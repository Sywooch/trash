<?php

class m140723_102651_add_test_replaced_phones extends CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function up()
	{
		$this->execute("insert into phone (number) values('74959661867')");
		$this->execute("insert into clinic_partner_phone (clinic_id, partner_id, phone_id) values  (450, 14, (select id from phone where number='74959661867'))");

		$this->execute("update clinic set asterisk_phone='74957836927' where id=450");
		$this->execute("update clinic set asterisk_phone_2='74959661293' where id=450");
		$this->execute("update clinic set phone='74955653293' where id=450"); //офисный телефон
	}

	/**
	 * @return bool|void
	 */
	public function down()
	{
		$this->execute("delete from clinic_partner_phone where phone_id = (select id from phone where number='74959661867') and partner_id=14 and clinic_id=450");
		$this->execute("delete from phone where number='74959661867'");
		$this->execute("update clinic set asterisk_phone_2=null, asterisk_phone = null, phone=null where id=450");
	}
}
