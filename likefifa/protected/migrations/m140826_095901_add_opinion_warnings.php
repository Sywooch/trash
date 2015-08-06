<?php

/**
 * Меняет структуру для поиска накруток комментариев
 *
 * Class m140826_095901_add_opinion_warnings
 */
class m140826_095901_add_opinion_warnings extends CDbMigration
{
	public function up()
	{
		$this->addColumn(
			LfOpinion::model()->tableName(),
			'warning_level',
			"TINYINT(2) NOT NULL DEFAULT 0 COMMENT 'Помечает, является ли отзыв подозрительным'"
		);
		$this->addColumn(
			LfOpinion::model()->tableName(),
			'ga',
			"VARCHAR(45) NULL DEFAULT NULL COMMENT 'Кука Google Analytics' AFTER `ua`"
		);

		$this->createTable(
			'login_history',
			[
				'id'        => 'pk',
				'type'      => "ENUM('master','salon') NOT NULL DEFAULT 'master'",
				'entity_id' => 'INT(11) UNSIGNED NOT NULL',
				'date'      => 'DATETIME NOT NULL',
				'ip'        => 'VARCHAR(45) NULL DEFAULT NULL',
				'ua'        => 'VARCHAR(255) NULL DEFAULT NULL',
				'ga'        => "VARCHAR(45) NULL DEFAULT NULL COMMENT 'Кука Google Analytics'"
			]
		);
		$this->createIndex('lh_ipua', 'login_history', 'ip, ua');
		$this->createIndex('lh_ga', 'login_history', 'ga');
		$this->createIndex('lh_type_entity_id', 'login_history', 'type, entity_id');
	}

	public function down()
	{
		$this->dropColumn(
			LfOpinion::model()->tableName(),
			'warning_level'
		);
		$this->dropColumn(
			LfOpinion::model()->tableName(),
			'ga'
		);
		$this->dropTable('login_history');
	}
}