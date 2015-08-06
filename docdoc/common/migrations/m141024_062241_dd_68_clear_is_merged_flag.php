<?php

class m141024_062241_dd_68_clear_is_merged_flag extends CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function up()
	{
		$this->execute("update api_doctor set is_merged = 0 where not exists(select * from doctor_4_clinic where doc_external_id = api_doctor.id) and is_merged");
	}

	/**
	 * @return bool|void
	 */
	public function down()
	{
		$this->execute("update api_doctor set is_merged = 0");
	}
}
