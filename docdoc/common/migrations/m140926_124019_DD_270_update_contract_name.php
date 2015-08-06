<?php

/**
 * m140926_124019_DD_270_update_contract_name
 * человеческие название тарифов
 */
class m140926_124019_DD_270_update_contract_name extends CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function up()
	{
		$this->execute("
			UPDATE contract_dict SET contract_id = 1, title = 'Врачи. Оплата за дошедших', description = null, isClinic = 'yes', isDiagnostic = 'no', kind = 0 WHERE contract_id = 1;
			UPDATE contract_dict SET contract_id = 2, title = 'Врачи. Оплата за запись', description = null, isClinic = 'yes', isDiagnostic = 'no', kind = 0 WHERE contract_id = 2;
			UPDATE contract_dict SET contract_id = 3, title = 'Диагностика. Оплата за звонки', description = null, isClinic = 'no', isDiagnostic = 'yes', kind = 1 WHERE contract_id = 3;
			UPDATE contract_dict SET contract_id = 4, title = 'Диагностика. Оплата за запись', description = null, isClinic = 'no', isDiagnostic = 'yes', kind = 1 WHERE contract_id = 4;
			UPDATE contract_dict SET contract_id = 5, title = 'Диагностика. Оплата за дошедших', description = null, isClinic = 'no', isDiagnostic = 'yes', kind = 1 WHERE contract_id = 5;
			UPDATE contract_dict SET contract_id = 6, title = 'Врачи. Оплата за звонки', description = null, isClinic = 'yes', isDiagnostic = 'no', kind = 0 WHERE contract_id = 6;
			UPDATE contract_dict SET contract_id = 7, title = 'Диагностика. Онлайн-запись', description = null, isClinic = 'no', isDiagnostic = 'yes', kind = 1 WHERE contract_id = 7;
			");

	}

	/**
	 * @return bool|void
	 */
	public function down()
	{

	}
}