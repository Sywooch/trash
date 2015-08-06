<?php

/**
 * Создает таблицу алиасов для услуг
 */
class m140926_102127_add_service_aliases extends CDbMigration
{
	public function up()
	{
		$this->execute(
			"CREATE TABLE `lf_service_aliases` (
			  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			  `specialization_id` INT(10) NULL DEFAULT NULL,
			  `service_id` INT(10) NULL DEFAULT NULL,
			  `alias` VARCHAR(255) NOT NULL,
			  PRIMARY KEY (`id`),
			  INDEX `lf_service_aliases_service_id_idx` (`service_id` ASC),
			  INDEX `lf_service_aliases_spec_id_idx` (`specialization_id` ASC),
			  CONSTRAINT `lf_service_aliases_service_id`
				FOREIGN KEY (`service_id`)
				REFERENCES `lf_service` (`id`)
				ON DELETE CASCADE
				ON UPDATE CASCADE,
			  CONSTRAINT `lf_service_aliases_spec_id`
				FOREIGN KEY (`specialization_id`)
				REFERENCES `lf_specialization` (`id`)
				ON DELETE CASCADE
				ON UPDATE CASCADE);"
		);
		$this->insert(
			'admin_controller',
			[
				'name'         => 'Алиасы услуг',
				'rewrite_name' => 'serviceAliases',
				'sort'         => '0',
				'col_group'    => 'Прочее',
				'icon'         => 'cubes'
			]
		);
	}

	public function down()
	{
		$this->dropTable('lf_service_aliases');
		$this->delete('admin_controller', 'rewrite_name = :rewrite_name', [':rewrite_name' => 'serviceAliases']);
	}
}