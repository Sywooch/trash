<?php

/**
 * m140217_000000_appointment_operator_comment class file.
 *
 * Комментарий оператора в заявках в БО
 *
 * @author   Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @see      https://docdoc.megaplan.ru/task/1003196/card/
 * @package  migrations
 */
class m140217_000000_appointment_operator_comment extends CDbMigration
{

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$criteria = new CDbCriteria;
		$criteria->condition = "version = :version";
		$criteria->params = array(":version" => "m170206_000000_appointment_operator_comment");
		$model = TblMigration::model()->find($criteria);
		if ($model) {
			$model->delete();
		} else {
			$this->addColumn("lf_appointment", "operator_comment", "VARCHAR(256)");
		}
	}

	/**
	 * Откатывает миграцию миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropColumn("lf_appointment", "operator_comment");
	}
}