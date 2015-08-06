<?php
/**
 * Created by PhpStorm.
 * User: atyutyunnikov
 * Date: 11.02.15
 * Time: 15:39
 */
//для errorLog который используется в астериск минетжере
define ("BASEDIR", ROOT_PATH . '/back/public/');

require_once ROOT_PATH . '/back/public/lib/asterisk/AsteriskManager.php';
require_once ROOT_PATH . '/back/public/lib/php/errorLog.php';

use dfs\common\components\console\Command;
use dfs\docdoc\models\RequestModel;
use dfs\docdoc\models\RequestHistoryModel;
use dfs\docdoc\models\MailQueryModel;
use dfs\docdoc\models\ClinicModel;

/**
 * Уведомление в клинику
 *
 * Class ClinicNotifyCommand
 */
class ClinicNotifyCommand extends Command
{
	/**
	 * @throws Exception
	 */
	public function actionIndex()
	{
		$this->log('Начнем уведомлять клиники о заявках на диагностику онлайн');

		$duration = \Yii::app()->params['DOnlineClinicNotifyDuration'];

		$requests = RequestModel::model()
			->byKind(RequestModel::KIND_DIAGNOSTICS)
			->inStatuses([RequestModel::STATUS_REMOVED], true)
			->withTypes([RequestModel::TYPE_ONLINE_RECORD])
			->notProcessed()
			->needToNotifyByAsterisk(date('c'), $duration)
			->with(
				[
					'clinic' => [
						'select' => 'clinic.phone',
						'joinType' => 'inner join',
					]
				]
			)->findAll();

		$this->log('Выбрано заявок:' . count($requests));

		if (count($requests)) {
			$ast = new Net_AsteriskManager();

			try {
				$ast->connect();
			} catch (PEAR_Exception $e) {
				throw new Exception("Ошибка подключения к астериску: {$e->getMessage()}");
			}

			foreach ($requests as $request) {
				$this->log('Обработка заявки ' . $request->req_id);

				if ($request->clinic->phone) {
					try {
						$ast->callAndPlayRecord($request->clinic->phone, 'diag-announcement');
						$request->addHistory('Отправлено уведомление о поступившей заявке на онлайн диагностику',
							RequestHistoryModel::LOG_TYPE_NOTIFY_BY_ASTERISK);
					} catch (PEAR_Exception $e) {
						$this->log($e->getMessage(), CLogger::LEVEL_ERROR);
					}
				} else {
					$this->log('У клиники ' . $request->clinic->id . ' не задан номер телефона');
				}

			}
		}

		$this->log('Работа скрипта закончена');
	}

	/**
	 * Ежемесячная сверка для клиник
	 */
	public function actionReconciliation()
	{
		$this->log('Ежемесячная сверка для клиник');

		$clinics = ClinicModel::model()
			->active()
			->findAll([
				'condition' => 't.email_reconciliation IS NOT NULL AND t.email_reconciliation != ""',
			]);

		foreach ($clinics as $clinic) {
			$this->log('[' . $clinic->id . '] ' . $clinic->name);

			MailQueryModel::model()->createMail('clinic_reconciliation', $clinic->email_reconciliation, [
				'clinic' => $clinic,
				'admin' => $clinic->getAdmin(),
			]);
		}

		$this->log('Работа скрипта закончена');
	}
}