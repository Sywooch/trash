<?php

/**
 * Файл класса m141205_000000_DD_649_partner_cost
 *
 * Делает возможным принятия NULL для service_id в таблице partner_cost
 * Удаляет UNSIGNED для city_id в таблице partner_cost для того, чтобы можно было записывать NULL
 * Добавляет внешний ключ для города
 *
 * @author  Mikhail Vasilyev <mvasilyev@docdoc.ru>
 * @link    https://docdoc.atlassian.net/browse/DD-649
 * @package migrations
 */
class m141205_000000_DD_649_partner_cost extends CDbMigration
{

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->execute("ALTER TABLE partner_cost MODIFY service_id INT");
		$this->execute("ALTER TABLE partner_cost MODIFY city_id INT");
		$this->addForeignKey("partner_cost_city_id", "partner_cost", "city_id", "city", "id_city");
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->execute("ALTER TABLE partner_cost MODIFY service_id INT NOT NULL");
		$this->dropForeignKey("partner_cost_city_id", "partner_cost");
		$this->execute("ALTER TABLE partner_cost MODIFY city_id INT(10) UNSIGNED");
	}
}