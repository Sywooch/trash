<?php

/**
 * Class m141107_143511_dd_453_traffic_params_dict_add_google_client
 */
class m141107_143511_dd_453_traffic_params_dict_add_google_client extends CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function up()
	{
		$this->execute("insert into traffic_params_dict (name, title) values ('_ga_cl', 'clientId по версии google analytics')");
	}

	/**
	 * @return bool|void
	 */
	public function down()
	{
		$this->execute("delete from traffic_params_dict where name='_ga_cl'");
	}
}
