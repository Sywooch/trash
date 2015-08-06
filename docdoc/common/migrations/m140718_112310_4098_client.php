<?php

/**
 * Файл класса m140718_112310_4098_client
 *
 * Удалеине старых клиентов, сброс клиентов в таблице с заявками, добыаление нового столбца
 */
class m140718_112310_4098_client extends CDbMigration
{
	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->execute("TRUNCATE TABLE client");
		$this->execute("UPDATE request SET clientId = NULL");

		$this->execute("ALTER TABLE `client`
			ADD COLUMN `registered_in_mixpanel` tinyint(1) DEFAULT 0
			COMMENT 'флаг, был ли клиент зарегистрирован в микспанели'");

		//уникальный индекс
		$this->createIndex('phone_idx', 'client', 'phone', true);

		$this->execute("
		ALTER TABLE `request`
			ADD INDEX `fk_client_id_idx` (`clientId` ASC);
		ALTER TABLE `request`
		ADD CONSTRAINT `fk_client_id`
		  FOREIGN KEY (`clientId`)
		  REFERENCES `client` (`clientId`)
		  ON DELETE SET NULL
		  ON UPDATE CASCADE");


		$this->dropColumn('client', 'cell_phone');
		$this->dropColumn('client', 'reg_code');
		$this->dropColumn('client', 'status');
		$this->dropColumn('client', 'comment');


	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropColumn('client', 'registered_in_mixpanel');
		$this->addColumn('client', 'cell_phone', 'varchar(50)');
		$this->addColumn('client', 'reg_code', 'varchar(50)');
		$this->addColumn('client', 'status', 'varchar(50)');
		$this->addColumn('client', 'comment', 'varchar(50)');
		$this->dropIndex('phone_idx', 'client');
		$this->dropForeignKey('fk_client_id', 'request');
		$this->dropIndex('fk_client_id_idx', 'request');
	}
}