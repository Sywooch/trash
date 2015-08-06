<?php

/**
 * Class m141119_115542_dd_543_update_sms_type_title
 */
class m141119_115542_dd_543_update_sms_type_title extends CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function up()
	{
		$this->execute("update SMStype set title='Создание заявки' where id_type=2;");
		$this->execute("insert into SMStype(id_type, title) VALUES (8, 'Валидация телефона');");
		$this->execute("insert into SMStype(id_type, title) VALUES (0, 'Не задан');");
	}

	/**
	 * @return bool|void
	 */
	public function down()
	{
		$this->execute("update SMStype set title='Заявка с сайта (ФО)' where id_type=2;");
		$this->execute("delete from SMStype where id_type=8 and title='Валидация телефона';");
		$this->execute("delete from SMStype where id_type=0 and title='Не задан';");
	}
}
