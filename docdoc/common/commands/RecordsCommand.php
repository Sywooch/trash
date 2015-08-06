<?php
use dfs\common\components\console\Command;
use dfs\docdoc\objects\call\Provider;
use dfs\docdoc\objects\call\ProviderInterface;
use dfs\docdoc\models\RequestModel;
use dfs\docdoc\models\RequestRecordModel;
use dfs\docdoc\objects\record\RecordHandler;

/**
 * Class RecordsCommand
 */
class RecordsCommand extends Command
{
	/**
	 * Загружает вавки с ftp серверов
	 *
	 * @param string|null $date возможность переназначить дату загрузки
	 *
	 * @return int|void
	 */
	public function actionLoad($date = null)
	{
		is_null($date) && $date = date('Y-m-d');

		$accounts = Provider::getAll();

		foreach ($accounts as $account) {
			try {
				list($copy, $try, $total) = $account->loadFiles($date);
				$this->log("{$account->getName()}: copied - {$copy}, read - {$try}, total - {$total} files");

			} catch (Exception $e) {
				$this->log("{$account->getName()}: {$e->getMessage()}");
			}
		}
	}

	/**
	 * Добавляет записи в нашу базу, проставля партнера город клинику request->kind
	 *
	 * @param int|null $date время относительно которого парсить
	 */
	public function actionParse($date = null)
	{
		if(is_null($date)){
			$dateFrom = date('Y-m-d H:i:s', time() - RequestModel::DIFF_TIME_FOR_MERGED_REQUEST);
			$dateTo = date('Y-m-d H:i:s');
			$dateToDateDir = date('c');
		} else {
			$dateFrom = date('Y-m-d 00:00', strtotime($date));
			$dateTo = date('Y-m-d 23:59', strtotime($dateFrom));
			$dateToDateDir = date('c', strtotime($date));
		}

		$logger = Yii::getLogger();

		$accounts = Provider::getAll();

		foreach ($accounts as $account) {
			$logger->log('Current provider ' . $account->getName());

			$account->setDateDir($dateToDateDir);

			$currentDir = \Yii::app()->params['phone_providers']['download_dir'] .
				$account->getLocalPathPrefix() .
				$account->getDateDir();

			$notParsedFiles = $account->getNotInsertedFilesInInterval($dateFrom, $dateTo);
			$logger->log('Количество не обработанных файлов = ' . count($notParsedFiles));

			foreach ($notParsedFiles as $file) {
				$logger->log('Текущий файл ' . $currentDir . DIRECTORY_SEPARATOR . $file);

				$record = $account->createRecord($file);

				if ($record->clinic_id) {
					$logger->log("Найдена клиника id=" . $record->clinic_id);
				} else {
					$logger->log("Клиника не найдена");
				}

				if ($record->replaced_phone) {
					$logger->log("Найден подменный телефон " . $record->replaced_phone);
				} else {
					$logger->log("Запись без подменного телефона");
				}

				if ($record->getCallerPhone()) {
					$logger->log('Телефон звонящего ' . $record->getCallerPhone());
				} else {
					$logger->log("Телефон звонящего не определен");
				}

				if ($record->getDestinationPhone()) {
					$logger->log("Телефон клиники(или мб чего другого) " . $record->getDestinationPhone());
				} else {
					$logger->log("Не определился номер клиники");
				}

				$request = RequestModel::saveByRecord($record);

				if ($request->partner_id) {
					$logger->log("Обнаружен партнер " . $request->partner_id);
				}

				if ($request->kind == RequestModel::KIND_DIAGNOSTICS) {
					$logger->log("Заявка на диагностику");
				} elseif (!is_null($request->kind) && $request->kind == RequestModel::KIND_DOCTOR) {
					$logger->log("Заявка на врача");
				}

				if ($request->req_id) {
					//и не поймешь был инс
					$logger->log("Запись добавлена к заявке id= " . $request->req_id);
				} else {
					$logger->log("Заявка не создана: " . $request->req_id);
				}

				foreach ($request->getErrors() as $errors) {

					foreach ($errors as $e) {
						$logger->log($e . ' for record ' . $record->record);
					}

				}
			}
		}

	}

	/**
	 * Устанавливает продолжительность записи
	 *
	 * @param int $days
	 */
	public function actionSetDuration($days = 2)
	{
		$logger = Yii::getLogger();

		$records = RequestRecordModel::model()
			->between(date('Y-m-d H:i:s', time() - $days * 86400), date('Y-m-d H:i:s'))
			->hasEmptyDuration()
			->findAll();

		$logger->log('Количество записей с 0 длительностью = ' . count($records));

		foreach ($records as $record) {
			$recordHandler = new RecordHandler($record);
			try {
				$record->duration = $recordHandler->getDuration();

				// Корректируем время записи на время начала разговора для записей с астериска
				if ($record->source == RequestRecordModel::SOURCE_DEFAULT) {
					$record->crDate = date('Y-m-d H:i:s', strtotime($record->crDate) - $record->duration);
					$logger->log("Скорректировано время разговора начала для записи #{$record->record_id}");
				}

				if ($record->save()) {
					$logger->log("Установлена длительность для записи #{$record->record_id}");
				}

				foreach ($record->getErrors() as $errors) {

					foreach ($errors as $e) {
						$logger->log($e . ' for record ' . $record->record);
					}

				}
			} catch (CException $e) {
				$logger->log($e->getMessage());
			}
		}
	}

	/**
	 * Конвертирование wav -> mp3
	 */
	public function actionConvert()
	{
		require_once ROOT_PATH . "/back/public/lib/php/serviceFunctions.php";

		$providers = Provider::getAll();

		$pathList = array_map(
			function (ProviderInterface $x) {
				return [
					'local_path' => \Yii::app()->params['phone_providers']['download_dir'] . $x->getLocalPathPrefix(),
				];
			},
			$providers
		);

		$month = date("m");
		$year = date("Y");
		$day = date("d");

		foreach ($pathList as $key => $array) {

			$path = $array['local_path'];

			$dir = $path . DIRECTORY_SEPARATOR . $year
				. DIRECTORY_SEPARATOR . $month
				. DIRECTORY_SEPARATOR . $day;

			if (is_dir($dir)) {
				$directories = scandir($dir);
				Yii::log("Scan folder: {$dir}");

				$i = 1;

				foreach ($directories as $file) {
					if ($file === '..' || $file === '.') {
						continue;
					}

					$fileInfo = pathinfo($file);
					$fullFileName = $dir . DIRECTORY_SEPARATOR . $file;

					Yii::log("Сейчас обрабатывается " . $i++ . "/" . (count($directories) - 2));

					if (is_file($fullFileName)
						&& $fileInfo['extension'] === 'wav'
						&& !file_exists($dir . "/" . $fileInfo['filename'] . ".mp3")
					) {
						Yii::log("Found file: {$fullFileName}");

						if (convertWavToMp3($fullFileName)) {
							unlink($fullFileName);
							Yii::log('Удален локальный файл ' . $fullFileName);
						}

					} else {
						Yii::log("File {$file} was converted alredy(or not a file or ext != wav)");
					}
				}
			}
		}
	}
} 
