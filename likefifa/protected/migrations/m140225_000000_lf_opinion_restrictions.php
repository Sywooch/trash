<?php

/**
 * m140225_000000_lf_opinion_restrictions class file.
 *
 * Ограничения на отправку отзывов
 *
 * @author   Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @see      https://docdoc.megaplan.ru/task/1002976/card/
 * @package  migrations
 */
class m140225_000000_lf_opinion_restrictions extends CDbMigration
{

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function safeUp()
	{
		$this->addColumn("lf_opinion", "ip", "LONG NOT NULL");
		$this->addColumn("lf_opinion", "sid", "CHAR(184) NOT NULL");
		$this->addColumn("lf_opinion", "ua", "TEXT NOT NULL");
		$this->addColumn("lf_opinion", "appointment_id", "INT NOT NULL");

		$this->createIndex(
			"lf_opinion_appointment_id", "lf_opinion", "appointment_id"
		);
		$this->createIndex(
			"lf_opinion_tel", "lf_opinion", "tel"
		);
		$this->createIndex(
			"lf_appointment_phone", "lf_appointment", "phone"
		);
	}

	/**
	 * Откатывает миграцию миграцию
	 *
	 * @return void
	 */
	public function safeDown()
	{
		$this->dropIndex("lf_opinion_appointment_id", "lf_opinion");
		$this->dropIndex("lf_opinion_tel", "lf_opinion");
		$this->dropIndex("lf_appointment_phone", "lf_appointment");

		$this->dropColumn("lf_opinion", "ip");
		$this->dropColumn("lf_opinion", "sid");
		$this->dropColumn("lf_opinion", "ua");
		$this->dropColumn("lf_opinion", "appointment_id");
	}
}