<?php

/**
 * m140917_130504_DD_40_update_diagnostic_seotext
 * изменение сео-текстов с применением параметра город
 */
class m140917_130504_DD_40_update_diagnostic_seotext extends CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function up()
	{
		$this->execute("UPDATE diagnostica SET meta_description = REPLACE(meta_description, 'Москва', '{city}') WHERE meta_description LIKE '%Москва%'");
		$this->execute("UPDATE diagnostica SET meta_description = REPLACE(meta_description, 'Москве', '{cityInPrepositional}') WHERE meta_description LIKE '%Москве%'");
		$this->execute("UPDATE diagnostica SET meta_description = REPLACE(meta_description, 'Москвы', '{cityInGenitive}') WHERE meta_description LIKE '%Москвы%'");
	}

	/**
	 * @return bool|void
	 */
	public function down()
	{
		$this->execute("UPDATE diagnostica SET meta_description = REPLACE(meta_description, '{city}', 'Москва') WHERE meta_description LIKE '%{city}%'");
		$this->execute("UPDATE diagnostica SET meta_description = REPLACE(meta_description, '{cityInPrepositional}', 'Москве') WHERE meta_description LIKE '%{cityInPrepositional}%'");
		$this->execute("UPDATE diagnostica SET meta_description = REPLACE(meta_description, '{cityInGenitive}', 'Москвы') WHERE meta_description LIKE '%{cityInGenitive}%'");
	}
}