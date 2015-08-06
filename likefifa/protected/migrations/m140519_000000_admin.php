<?php

use likefifa\models\AdminModel;

/**
 * m140519_000000_admin class file.
 *
 * Создает таблицы с администраторами и их правами
 *
 * @author   Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @see      https://docdoc.megaplan.ru/task/1002402/card/
 * @package  migrations
 */
class m140519_000000_admin extends CDbMigration
{

	/**
	 * Применяет миграцию
	 *
	 * @return bool
	 */
	public function safeUp()
	{
		$this->createTable(
			"admin",
			array(
				"id"       => "pk",
				"login"    => "VARCHAR(128) NOT NULL",
				"password" => "CHAR(40) NOT NULL",
				"group_id" => "INT NOT NULL",
				"name"     => "VARCHAR(64) NOT NULL",
			),
			"ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci"
		);

		$this->createIndex("admin_group_id", "admin", "group_id");

		$this->createTable(
			"admin_controller",
			array(
				"id"           => "pk",
				"name"         => "VARCHAR(128) NOT NULL",
				"rewrite_name" => "VARCHAR(128) NOT NULL",
			),
			"ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci"
		);

		$this->insert(
			"admin",
			array(
				"login"    => "admin",
				"password" => AdminModel::getSha1Password("Hkfe83Fg32A"),
				"group_id" => 1,
				"name"     => "Администратор",
			)
		);

		$this->insert(
			"admin",
			array(
				"login"    => "mike",
				"password" => AdminModel::getSha1Password("mypasS77"),
				"group_id" => 1,
				"name"     => "Михаил Васильев",
			)
		);

		$this->insert("admin_controller", array("name" => "Ветки метро", "rewrite_name" => "undergroundLine"));
		$this->insert("admin_controller", array("name" => "Станции метро", "rewrite_name" => "undergroundStation"));
		$this->insert("admin_controller", array("name" => "Города", "rewrite_name" => "moscowArea"));
		$this->insert("admin_controller", array("name" => "Группы", "rewrite_name" => "group"));
		$this->insert("admin_controller", array("name" => "Направления", "rewrite_name" => "sector"));
		$this->insert("admin_controller", array("name" => "Специалиазции", "rewrite_name" => "specialization"));
		$this->insert("admin_controller", array("name" => "Услуги", "rewrite_name" => "service"));
		$this->insert("admin_controller", array("name" => "SEO-блоки", "rewrite_name" => "seoText"));
		$this->insert("admin_controller", array("name" => "Статьи", "rewrite_name" => "article"));
		$this->insert("admin_controller", array("name" => "Мастера", "rewrite_name" => "master"));
		$this->insert("admin_controller", array("name" => "Салоны", "rewrite_name" => "salon"));
		$this->insert("admin_controller", array("name" => "Отзывы", "rewrite_name" => "opinion"));
		$this->insert("admin_controller", array("name" => "Работы", "rewrite_name" => "work"));
		$this->insert("admin_controller", array("name" => "Фото салона", "rewrite_name" => "salonPhoto"));
		$this->insert("admin_controller", array("name" => "Заявки", "rewrite_name" => "appointment"));
		$this->insert("admin_controller", array("name" => "Рассылка", "rewrite_name" => "mailing"));
		$this->insert("admin_controller", array("name" => "Администраторы", "rewrite_name" => "admins"));

		$this->addColumn("lf_appointment", "admin_id", "INT");
		$this->addForeignKey("lf_appointment_admin_id", "lf_appointment", "admin_id", "admin", "id");

		$operators = Yii::app()->db->createCommand()
			->select('*')
			->from('lf_operator')
			->queryAll();
		foreach ($operators as $operator) {
			$this->insert(
				"admin",
				array(
					"name"     => $operator["name"],
					"group_id" => AdminModel::GROUP_OPERATOR,
					"login"    => "operator_" . $operator["id"],
					"password" => AdminModel::getSha1Password("operator_" . $operator["id"]),
				)
			);
			$newAdmin = Yii::app()->db->createCommand()
				->select('id')
				->from('admin')
				->order("id DESC")
				->queryRow();
			$this->update(
				"lf_appointment",
				array(
					"admin_id" => $newAdmin["id"],
				),
				"operator_id = :operator_id",
				array("operator_id" => $operator["id"])
			);
		}

		$this->dropTable("lf_operator");
		$this->dropColumn("lf_appointment", "operator_id");
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return bool
	 */
	public function safeDown()
	{
		$this->dropIndex("admin_group_id", "admin");

		$this->dropForeignKey("lf_appointment_admin_id", "lf_appointment");

		$this->dropTable("admin");
		$this->dropTable("admin_controller");

		$this->addColumn("lf_appointment", "operator_id", "INT NOT NULL");
		$this->createTable(
			"lf_operator",
			array(
				"id"   => "pk",
				"name" => "VARCHAR(128) NOT NULL",
			),
			"ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci"
		);

		$this->dropColumn("lf_appointment", "admin_id");
	}
}