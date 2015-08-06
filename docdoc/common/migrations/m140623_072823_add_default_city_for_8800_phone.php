<?php

class m140623_072823_add_default_city_for_8800_phone extends CDbMigration
{
	public function up()
	{
		$this->execute("insert into `city` (title, rewrite_name, prefix, title_genitive, title_prepositional, has_diagnostic, is_active)
		 values('Другие', 'default', '', 'Других', 'Другим', 0, 0);");
	}

	public function down()
	{
		$this->execute("delete from `city` where rewrite_name='default'");
	}
}
