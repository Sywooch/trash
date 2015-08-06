<?php

/**
 * Class m141020_122216_DD_368_create_traffic_params
 */
class m141020_122216_DD_368_create_traffic_params extends CDbMigration
{
	/**
	 * Создание таблицы traffic_params
	 *
	 * @return bool|void
	 */
	public function up()
	{
		$this->execute(
			"CREATE TABLE `traffic_params_dict` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`name` VARCHAR(50) NOT NULL,
				`title` VARCHAR(255),
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8"
		);

		$this->execute(
			"CREATE TABLE `traffic_params` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`obj_id` INT(11) NOT NULL,
				`obj_type` TINYINT(4) NOT NULL,
				`param_id` INT(11) NOT NULL,
				`value` VARCHAR(255) NOT NULL,
				PRIMARY KEY (`id`),
				KEY `traffic_params_obj_id` (`obj_id`),
				KEY `traffic_params_obj_type` (`obj_type`),
				KEY `traffic_params_param_id` (`param_id`),
				CONSTRAINT `traffic_params_ibfk_1` FOREIGN KEY (`param_id`) REFERENCES `traffic_params_dict` (`id`) ON DELETE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8"
		);

		$this->insert('traffic_params_dict', ['id' => 1, 'name' => 'pid']);
		$this->insert('traffic_params_dict', ['id' => 2, 'name' => 'utm_source']);
		$this->insert('traffic_params_dict', ['id' => 3, 'name' => 'utm_medium']);
		$this->insert('traffic_params_dict', ['id' => 4, 'name' => 'utm_campaign']);
		$this->insert('traffic_params_dict', ['id' => 5, 'name' => 'utm_term']);
		$this->insert('traffic_params_dict', ['id' => 6, 'name' => 'utm_content']);
		$this->insert('traffic_params_dict', ['id' => 7, 'name' => 'referrer']);
	}

	/**
	 * @return bool|void
	 */
	public function down()
	{
		$this->dropTable('traffic_params');
		$this->dropTable('traffic_params_dict');
	}
}