<?php

/**
 * Файл класса m140812_073550_add_sms_types.
 */
class m140812_073550_add_sms_types extends CDbMigration
{
	/**
	 * Применяет миграцию
	 *
	 * @return bool|void
	 */
	public function up()
	{
		$this->execute("insert into SMStype values (6, 'Уведомление об изменении приема (БО)')");
		$this->execute("insert into SMStype values (7, 'Уведомление о недозвоне до пациента (БО)')");
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return bool|void
	 */
	public function down()
	{
		$this->execute("delete from SMStype where id_type IN (6, 7)");
	}

}