<?php

/**
 * Class m150202_082936_DD_867_index
 */
class m150202_082936_DD_867_index extends CDbMigration
{
	/**
	 * Новый индекс для биллинга
	 *
	 * @return bool|void
	 */
	public function up()
	{
		$this->execute("ALTER TABLE `request`
			ADD INDEX `billing_in_clinic_kind_admission`
			(`clinic_id` ASC, `date_admission` ASC, kind ASC, billing_status ASC);");

		$this->execute("ALTER TABLE `request`
			ADD INDEX `billing_in_clinic_kind_record`
			(`clinic_id` ASC, `date_record` ASC, kind ASC, billing_status ASC);");

	}

	/**
	 * @return bool
	 */
	public function down()
	{
		$this->execute("ALTER TABLE `request`
			DROP INDEX `billing_in_clinic_kind_admission`");

		$this->execute("ALTER TABLE `request`
			DROP INDEX `billing_in_clinic_kind_record`");
	}
}