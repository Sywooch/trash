<?php

/**
 * Class m140827_074926_DD_70_diagnostica_subtype
 */
class m140827_074926_DD_70_diagnostica_subtype extends CDbMigration
{
	/**
	 * Создаем таблицу подвидов диагностик
	 * @return bool|void
	 */
	public function up()
	{
		$this->execute(
			"CREATE TABLE `diagnostica_subtype` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`name` VARCHAR(255) NOT NULL,
				`diagnostica_id` INT(11) NOT NULL,
				`priority` TINYINT(3) DEFAULT 99,
				PRIMARY KEY (`id`),
				INDEX `fk_diagnostica_idx` (`diagnostica_id` ASC),
				CONSTRAINT `fk_diagnostica_id`
					FOREIGN KEY (`diagnostica_id`)
					REFERENCES `diagnostica` (`id`)
					ON DELETE CASCADE
					ON UPDATE CASCADE
			)
			ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='подвиды диагностик'"
		);

		$this->addColumn('diagnostica', 'sort_in_subtype', 'TINYINT(3) NULL DEFAULT 99');
		$this->addColumn('diagnostica', 'diagnostica_subtype_id', 'INT(11) NULL DEFAULT NULL');

		$this->execute("
			ALTER TABLE `diagnostica`
				ADD INDEX `fk_diagnostica_subtype_idx` (`diagnostica_subtype_id` ASC);
			ALTER TABLE `diagnostica`
			ADD CONSTRAINT `fk_diagnostica_subtype_id`
			  FOREIGN KEY (`diagnostica_subtype_id`)
			  REFERENCES `diagnostica_subtype` (`id`)
			  ON DELETE SET NULL
			  ON UPDATE CASCADE"
		);

		// Заполнение подвидов
		$this->insert('diagnostica_subtype', ['id' => 1, 'name' => 'МРТ головы', 'diagnostica_id' => 21, 'priority' => 1]);
		$this->insert('diagnostica_subtype', ['id' => 2, 'name' => 'МРТ суставов', 'diagnostica_id' => 21, 'priority' => 2]);
		$this->insert('diagnostica_subtype', ['id' => 3, 'name' => 'МРТ органов грудной клетки', 'diagnostica_id' => 21, 'priority' => 3]);
		$this->insert('diagnostica_subtype', ['id' => 4, 'name' => 'МРТ органов брюшной полости', 'diagnostica_id' => 21, 'priority' => 4]);
		$this->insert('diagnostica_subtype', ['id' => 5, 'name' => 'МРТ органов малого таза', 'diagnostica_id' => 21, 'priority' => 5]);
		$this->insert('diagnostica_subtype', ['id' => 6, 'name' => 'МРТ позвоночника', 'diagnostica_id' => 21, 'priority' => 6]);
		$this->insert('diagnostica_subtype', ['id' => 7, 'name' => 'МРТ мягких тканей', 'diagnostica_id' => 21, 'priority' => 7]);

		// Привязка диагностик к подвидам
		$this->update('diagnostica', ['diagnostica_subtype_id' => 1, 'sort_in_subtype' => 1], 'id = 55');
		$this->update('diagnostica', ['diagnostica_subtype_id' => 1, 'sort_in_subtype' => 2], 'id = 53');
		$this->update('diagnostica', ['diagnostica_subtype_id' => 1, 'sort_in_subtype' => 3], 'id = 54');
		$this->update('diagnostica', ['diagnostica_subtype_id' => 1, 'sort_in_subtype' => 4], 'id = 56');
		$this->update('diagnostica', ['diagnostica_subtype_id' => 1, 'sort_in_subtype' => 5], 'id = 57');
		$this->update('diagnostica', ['diagnostica_subtype_id' => 1, 'sort_in_subtype' => 6], 'id = 52');
		$this->update('diagnostica', ['diagnostica_subtype_id' => 1, 'sort_in_subtype' => 7], 'id = 184');
		$this->update('diagnostica', ['diagnostica_subtype_id' => 2, 'sort_in_subtype' => 1], 'id = 67');
		$this->update('diagnostica', ['diagnostica_subtype_id' => 2, 'sort_in_subtype' => 2], 'id = 191');
		$this->update('diagnostica', ['diagnostica_subtype_id' => 2, 'sort_in_subtype' => 3], 'id = 66');
		$this->update('diagnostica', ['diagnostica_subtype_id' => 2, 'sort_in_subtype' => 4], 'id = 68');
		$this->update('diagnostica', ['diagnostica_subtype_id' => 2, 'sort_in_subtype' => 5], 'id = 192');
		$this->update('diagnostica', ['diagnostica_subtype_id' => 2, 'sort_in_subtype' => 6], 'id = 69');
		$this->update('diagnostica', ['diagnostica_subtype_id' => 2, 'sort_in_subtype' => 7], 'id = 70');
		$this->update('diagnostica', ['diagnostica_subtype_id' => 2, 'sort_in_subtype' => 8], 'id = 63');
		$this->update('diagnostica', ['diagnostica_subtype_id' => 3, 'sort_in_subtype' => 1], 'id = 58');
		$this->update('diagnostica', ['diagnostica_subtype_id' => 3, 'sort_in_subtype' => 2], 'id = 155');
		$this->update('diagnostica', ['diagnostica_subtype_id' => 4, 'sort_in_subtype' => 1], 'id = 65');
		$this->update('diagnostica', ['diagnostica_subtype_id' => 4, 'sort_in_subtype' => 2], 'id = 61');
		$this->update('diagnostica', ['diagnostica_subtype_id' => 4, 'sort_in_subtype' => 3], 'id = 60');
		$this->update('diagnostica', ['diagnostica_subtype_id' => 4, 'sort_in_subtype' => 4], 'id = 188');
		$this->update('diagnostica', ['diagnostica_subtype_id' => 4, 'sort_in_subtype' => 5], 'id = 186');
		$this->update('diagnostica', ['diagnostica_subtype_id' => 4, 'sort_in_subtype' => 6], 'id = 187');
		$this->update('diagnostica', ['diagnostica_subtype_id' => 5, 'sort_in_subtype' => 1], 'id = 64');
		$this->update('diagnostica', ['diagnostica_subtype_id' => 6, 'sort_in_subtype' => 1], 'id = 189');
		$this->update('diagnostica', ['diagnostica_subtype_id' => 6, 'sort_in_subtype' => 2], 'id = 185');
		$this->update('diagnostica', ['diagnostica_subtype_id' => 6, 'sort_in_subtype' => 3], 'id = 62');
		$this->update('diagnostica', ['diagnostica_subtype_id' => 7, 'sort_in_subtype' => 1], 'id = 154');
		$this->update('diagnostica', ['diagnostica_subtype_id' => 7, 'sort_in_subtype' => 2], 'id = 59');
	}

	/**
	 * Откат миграции
	 *
	 * @return bool|void
	 */
	public function down()
	{
		$this->dropForeignKey('fk_diagnostica_subtype_id', 'diagnostica');
		$this->dropColumn('diagnostica', 'diagnostica_subtype_id');
		$this->dropColumn('diagnostica', 'sort_in_subtype');
		$this->dropTable('diagnostica_subtype');
	}

}