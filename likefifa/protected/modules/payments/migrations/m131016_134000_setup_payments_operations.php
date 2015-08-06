<?php
namespace dfs\modules\payments\migrations;

/**
 * Class m131016_134000_setup_payments_operations
 *
 * Устанавливаем таблицу с логом операций
 *
 * @author Aleksey Parshukov <parshukovag@gmail.com>
 * @date 26.09.2013
 *
 * @see https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=1310733
 */
class m131016_134000_setup_payments_operations extends \CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function safeUp()
	{
		$this->createTable(
			'payments_operation',
			array(
				'id' => 'CHAR(36)',
				'create_date' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
				'amount_real' => 'BIGINT NOT NULL',
				'amount_fake' => 'BIGINT NOT NULL',
				'account_from' => 'INTEGER NOT NULL',
				'account_to' => 'INTEGER NOT NULL',
				'type' => 'INTEGER NOT NULL',
				'message' => 'TEXT NOT NULL',
				'income' => 'INTEGER NOT NULL',
				'invoice_id' => 'CHAR(36)',

				'PRIMARY KEY (`id`, `account_from`)',
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci'
		);

		$this->createIndex('create_date', 'payments_operation', 'create_date');
		$this->addForeignKey('account_to', 'payments_operation', 'account_to', 'payments_account', 'id');
		$this->addForeignKey('account_from', 'payments_operation', 'account_from', 'payments_account', 'id');
		$this->addForeignKey('invoice_id', 'payments_operation', 'invoice_id', 'payments_invoice', 'id');
	}

	/**
	 * @return bool|void
	 */
	public function safeDown()
	{
		$this->dropTable('payments_operation');
	}
}