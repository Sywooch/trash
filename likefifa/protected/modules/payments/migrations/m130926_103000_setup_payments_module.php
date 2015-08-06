<?php
namespace dfs\modules\payments\migrations;

use dfs\modules\payments\models\PaymentsProcessor;
/**
 * Class m130926_112314_setup_payments_module
 *
 * Устанавливаем модуль оплаты
 *
 * @author Aleksey Parshukov <parshukovag@gmail.com>
 * @date 26.09.2013
 *
 * @see https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=1310733
 */
class m130926_103000_setup_payments_module extends \CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function up()
	{
		$this->createTable(
			'payments_account',
			array(
				'id' => 'pk',
				'amount_real' => 'BIGINT NOT NULL DEFAULT 0',
				'amount_fake' => 'BIGINT NOT NULL DEFAULT 0',
				'comment' => 'text',
			),
			'ENGINE=InnoDB AUTO_INCREMENT=10000 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci'
		);

		$this->createTable(
			'payments_processor',
			array(
				'id' => 'pk',
				'key'=> 'VARCHAR(64) NOT NULL',
				'account_id' => 'int',
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci'
		);

		$this->createIndex('key', 'payments_processor', 'key', true);
		$this->createIndex('account_id', 'payments_processor', 'account_id', true);
		$this->addForeignKey('account', 'payments_processor', 'account_id', 'payments_account', 'id');

		$this->createTable(
			'payments_invoice',
			array(
				'id' => 'CHAR(36) NOT NULL PRIMARY KEY',
				'create_date' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
				'amount_real' => 'BIGINT NOT NULL',
				'amount_fake' => 'BIGINT NOT NULL',
				'processor_id' => 'INTEGER NOT NULL',
				'account_to' => 'INTEGER NOT NULL',
				'message' => 'TEXT NOT NULL',
				'status' => 'INTEGER NOT NULL',
				'status_date' => 'TIMESTAMP NOT NULL',
				'email' => 'VARCHAR(255) NOT NULL',

			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci'
		);

		$this->createIndex('account_to', 'payments_invoice', 'account_to');
		$this->addForeignKey('account_to_FK', 'payments_invoice', 'account_to', 'payments_account', 'id');
		$this->addForeignKey('processor', 'payments_invoice', 'processor_id', 'payments_processor', 'id');

		return parent::up();
	}

	/**
	 * @return bool|void
	 */
	public function down()
	{
		$this->dropTable('payments_invoice');
		$this->dropTable('payments_processor');
		$this->dropTable('payments_account');

		return parent::down();
	}

	/**
	 * Создаём предварительнеые записи
	 *
	 * @return bool
	 */
	public function safeUp()
	{
		echo "    > Incerting Accounts\n";
		$robokassa_id = 1000;
		$this->getDbConnection()->createCommand("
			INSERT INTO `payments_account` (`id`, `comment`)
			VALUES
				(1, 'Система'),
				(2, 'Бонусы'),
				({$robokassa_id}, 'Робокасса')
		")->execute();

		echo "    > Incerting Processors\n";
		$ps = new PaymentsProcessor();
		$ps->account_id = $robokassa_id;
		$ps->key = "robokassa";
		$ps->save();

		return true;
	}
}