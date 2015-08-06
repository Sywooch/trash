<?php
use dfs\common\components\console\Command;
use dfs\docdoc\models\ApiClinicModel;
use dfs\docdoc\models\ApiDoctorModel;
use dfs\docdoc\models\DoctorClinicModel;
use dfs\docdoc\models\BookingModel;
use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\reports\BookingReport;
use dfs\docdoc\models\DoctorModel;
use dfs\docdoc\reports\DoctorWithScheduleReport;

/**
 * Команды для работы с интеграцинным шлюзом
 *
 *
 */
class ClinicApiCommand extends Command
{
	/**
	 * false потому что отчет можно генерить паралельно для нескольких клиник
	 *
	 * @var bool
	 */
	public $useAutoLock = false;

	/**
	 * Загрузка списка клиник из интеграционного шлюза
	 *
	 * @return void
	 */
	public function actionInstallFakeBooking()
	{
		$clinics = [
			'chudodoctor_1' => 55,
			'chudodoctor_2' => 249,
			'dobromed_1' => 1592,
			'dobromed_2' => 1607,
			'emc_1' => 1496,
			'emc_2' => 1498,
			'k31_1' => 748,
			'k31_2' => 2403,
			'onclinic_1' => 13,
			'onclinic_2' => 230,
			'wikimed_1' => 86,
			'abc_1' => 1930,
			'abc_2' => 2056,
			'delta_1' => 193,
			'delta_2' => 1848,
		];

		$this->log("Очищаем таблицы ");

		$this->log("api_clinic");
		Yii::app()->db->createCommand("DELETE FROM api_clinic")->execute();

		$this->log("api_doctor");
		Yii::app()->db->createCommand("DELETE FROM api_doctor")->execute();

		$this->actionLoadClinics();

		$apiClinics = ApiClinicModel::model()->findAll();

		$mergedClinics = [];
		foreach ($apiClinics as $apiClinic) {

			if (isset($clinics[$apiClinic->id])) {
				$clinic = ClinicModel::model()->findByPk($clinics[$apiClinic->id]);
				$clinic->updateByPk($clinics[$apiClinic->id], ['external_id' => $apiClinic->id]);
				$apiClinic->is_merged = 1;
				$apiClinic->save();
				$mergedClinics[$apiClinic->id] = $clinics[$apiClinic->id];
				unset($clinics[$apiClinic->id]);
				$this->log("Создана фейковая клиника {$apiClinic->id} - {$clinic->name}");
			} else {
				$this->log("!!!ИЗ API ПРИШЛА КЛИНИКА, ДЛЯ КОТОРОЙ НЕ ПРОПИСАНО СООТВЕТСВИЕ {$apiClinic->id} {$apiClinic->name}");
			}
		}

		//если из API не пришли какие-то клиники
		if (count($clinics)) {
			foreach ($clinics as $extId => $id) {
				$this->log("!!!!ИЗ API НЕ ПРИШЛА КЛИНИКА {$extId}");
			}
		}

		//делаем по 3 фейковых доктора в каждой клинике
		foreach ($mergedClinics as $extId => $id) {
			$doctors = DoctorModel::model()
				->inClinics([$id])
				->active();

			$cr = new CDbCriteria();
			$cr->compare(DoctorModel::model()->getTableAlias() . '.name', '<>FAKE', true);
			$cr->compare(DoctorModel::model()->getTableAlias() . '.name', $extId, true, 'OR');
			$doctors->getDbCriteria()->mergeWith($cr);

			$doctors = $doctors->findAll(['limit' => 3]);

			foreach ($doctors as $i => $d) {
				$d->name = $extId . " FAKE DOCTOR " . ($i + 1);
				$d->save(false);
				$this->log("Создан файковый врач {$d->name}. ID = {$d->id}");
			}
		}

		$this->actionLoadResources();

		$this->actionLoadSlots();
	}


	/**
	 * Загрузка списка клиник из интеграционного шлюза
	 *
	 *
	 * @return void
	 */
	public function actionLoadClinics()
	{
		$this->lock('actionLoadClinics');

		$this->log("Начало загрузки клиник......");

		$clinics = ApiClinicModel::model()->loadClinicsFromApi();
		ApiClinicModel::model()->saveClinicsFromApi($clinics);

		$idList = array_map(
			function ($x) {
				return $x->clinicId;
			},
			$clinics
		);

		ApiClinicModel::model()->disableByPk($idList, true);

		$this->log("Загружено " . count($clinics) . " клиник, все остальные выключены");
	}

	/**
	 * Загрузка докторов из всех клиник
	 *
	 * @return void
	 */
	public function actionLoadResources()
	{
		$this->lock('actionLoadResources');

		$this->log("Начало загрузки ресурсов......");

		$dataProvider = new \CActiveDataProvider(
			ApiClinicModel::class,
			[
				'criteria' => [
					'scopes' => ['enabled'],
				]
			]
		);

		$iterator = new \CDataProviderIterator($dataProvider, 500);

		$this->log("Получено активных клиник, имеющих API: " . $iterator->getTotalItemCount());

		$clinicIdList = [];

		$total = [
			ApiDoctorModel::TYPE_DOCTOR => 0,
			ApiDoctorModel::TYPE_CABINET => 0,
		];

		foreach ($iterator as $c) {
			/** @var  ApiClinicModel $c */
			$clinicIdList[] = $c->id;
			$doctorIdList = [];

			try {
				$resources = $c->loadResourcesFromApi();

				foreach($resources as $r){
					$doctorIdList[$r->resourceType][] = $r->resourceId;
				}
			} catch (Exception $e) {
				$this->log("Клиника {$c->id}. Ошибка загрузки врачей: " . $e->getMessage(), CLogger::LEVEL_ERROR);
				continue;
			}

			ApiDoctorModel::model()->saveResourcesFromApi($resources);

			//дисаблю докторов которых нет в апи для конкретной клиники
			foreach($doctorIdList as $type => $idList){
				ApiDoctorModel::model()->disableByClinicAndDoctors($c->id, $type, $idList, true);

				$this->log("Клиника {$c->id}. Загружено " . count($idList) . ($type == ApiDoctorModel::TYPE_DOCTOR ? " врачей" : " кабинетов"));
				$total[$type] += count($idList);
			}
		}

		//дисаблю докторов для дисабленных клиник
		ApiDoctorModel::model()->disableByClinic($clinicIdList, true);

		$this->log("Всего загружено " . $total[ApiDoctorModel::TYPE_DOCTOR] . " врачей");
		$this->log("Всего загружено " . $total[ApiDoctorModel::TYPE_CABINET] . " кабинетов");
		$this->log("Загрузка ресурсов закончена");
	}

	/**
	 * Загрузка слотов
	 *
	 * @return void
	 */
	public function actionLoadSlots()
	{
		$this->lock('actionLoadSlots');

		$this->log("Начало загрузки слотов......");

		$dayInterval = Yii::app()->params['booking']['loadDays'];

		$criteria = DoctorClinicModel::model()
			->needUpdate(date('c'))
			->getDbCriteria();

		$total = DoctorClinicModel::model()->count($criteria);
		$this->log("Всего найдено в doctor_4_clinic: $total");

		$criteria->limit = $limit = 200;

		while($total > 0) {
			$doctors = DoctorClinicModel::model()->findAll($criteria);

			$this->log("Выбрано " . count($doctors) . " докторов/кабинетов для загрузки слотов:");

			foreach ($doctors as $d) {
				try {
					$slots = $d->loadSlots(date('Y-m-d 00:00'), date('Y-m-d 23:59', strtotime("+{$dayInterval} day")));
					$result = $d->saveSlotsFromApi($slots);

					$this->log("Загрузка слотов для врача: {$d->id}:");
					$this->log("   всего - {$result['total']}, из них:");
					$this->log("       создано новых слотов {$result['new']}");
					$this->log("       удалено из-за изменения расписания {$result['delete']}");
					$this->log("       существующих слотов {$result['exists']}");
					$this->log("       проигнорировано слотов в прошлом {$result['inLast']}");
				} catch (Exception $e) {
					$d->last_slots_update = date('c');
					$d->save();
					$this->log("Ошибка загрузки слотов для доктора: {$d->id}. Ошибка:{$e->getMessage()}", CLogger::LEVEL_ERROR);
				}
			}

			$total -= $limit;
		}

		$this->log("Загрузка слотов завершена");
	}

	/**
	 * Проверка статусов брони в клиниках
	 */
	public function actionBookingCheck()
	{
		$this->lock('actionBookingCheck');

		$this->log("Начало проверки броней......");

		$statuses = [
			BookingModel::STATUS_NEW,
			BookingModel::STATUS_ACCEPTED,
			BookingModel::STATUS_APPROVED,
		];

		$dataProvider = new \CActiveDataProvider(
			BookingModel::class,
			[
				'criteria' => [
					'scopes' => ['inStatus' => [$statuses]],
				]
			]
		);

		$iterator = new \CDataProviderIterator($dataProvider, 100);

		if ($iterator->getTotalItemCount()) {
			$this->log('Найдено ' . $iterator->getTotalItemCount() . ' броней');

			$data = [
				'total'   => 0,
				'success' => 0,
				'error'   => 0,
			];

			foreach ($iterator as $i) {
				/** @var BookingModel $i */
				try {
					if ($i->reloadFromApi()) {
						$data['success']++;
					} else {
						$data['error']++;
						$this->log('Ошибка для брони #' . $i->id . ' ' . var_export($i->getErrors(), true));
					}
				} catch (Exception $e) {
					$data['error']++;
					$this->log('Ошибка для брони #' . $i->id . ' ' . $e->getMessage());
				}

				$data['total']++;
			}

			$this->log("Обработано: {$data['total']}, Успешно:{$data['success']}, С ошибками:{$data['error']}");
		} else {
			$this->log('Не найдено броней для проверки');
		}

		$this->log("Проверка статусов брони закончена");
	}

	/**
	 * Обновление поля has_slots для тех, у кого нет расписания
	 */
	public function actionUpdateHasSlots()
	{
		$this->lock('actionUpdateHasSlots');

		try {
			$this->log("Начало обновления doctor_4_clinic.has_slots......");
			$count = DoctorClinicModel::model()->updateHasSlots();
			$this->log("Обновлено $count строк");
		} catch (Exception $e) {
			$this->log("Ошибка обновления: " . $e->getMessage());
		} finally {
			$this->log("Обновление doctor_4_clinic.has_slots закончено");
		}
	}

	/**
	 * Отчет по букингу
	 *
	 */
	public function actionReport()
	{
		$this->lock('actionReport');

		$reportBuilder = new BookingReport();
		$reportBuilder->generate(date('c'));

		foreach ($reportBuilder->getData() as $report) {
			$log = [
				'Id головной клиники: ' . $report['clinic_id'],
				'Имя клиники: ' . $report['clinic_name'],
				'Дата: ' . $report['date'],
				'Филиалов в клинике: ' . $report['branches_bo'],
				'Филиалов из апи: ' . $report['branches_api'],
				'Врачей в бо: ' . $report['doctors_bo'],
				'Врачей в api: ' . $report['doctors_api'],
				'Смержено врачей:' . $report['doctors_merged'],
				'Активных слотов: ' . $report['active_slots'],
				'Заявок: ' . $report['request_count'],
				'Бронирований: ' . $report['booking_count']
			];
			$this->log(implode(PHP_EOL, $log));
		}
		$reportBuilder->insertIntoBigQuery();


		$reportBuilder = new DoctorWithScheduleReport();
		$reportBuilder->generate(date('c'));
		$this->log(print_r($reportBuilder->getData(), 1));
		$reportBuilder->insertIntoBigQuery();

	}
} 
