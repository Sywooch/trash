<?php

/**
 * Изменяет таблицу services
 *
 * Class m140724_120541_change_services
 */
class m140724_120541_change_services extends CDbMigration
{
	/**
	 * Услуги, которым нужно оставить "от"
	 *
	 * @var array
	 */
	private $_fromServices = [
		'Блеск-тату',
		'Исправление татуировок',
		'Татуировки хной',
		'Татуировки',
		'Удаление татуировок',
		'Временные татуировки'
	];

	public function safeUp()
	{
		$this->addColumn('lf_service', 'price_from', 'TINYINT(1) NOT NULL DEFAULT 0');
		$this->addColumn('lf_service', 'unit', 'VARCHAR(45) NULL DEFAULT NULL');

		$criteria = new CDbCriteria;
		$criteria->addInCondition('name', $this->_fromServices);
		LfService::model()->updateAll(['price_from' => 1], $criteria);

		$this->execute("update lf_price set price_from = 0 where service_id not in (select id from lf_service where price_from = 1)");
	}

	public function safeDown()
	{
		$this->dropColumn('lf_service', 'price_from');
		$this->dropColumn('lf_service', 'unit');

		LfService::model()->updateAll(['price_from' => 0]);
	}
}