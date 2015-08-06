<?php

/**
 * @author Danis Gilmanov, <dgilmanov@docdoc.ru>
 * @task 3433
 *
 * Удаляем старые таблицы заявок по диагностике и звонкам на врачей
 */
class m140331_135428_3433_drop_unused_requests extends CDbMigration
{
	public function up()
	{
		$this->dropTable('diag_request_history');
		$this->dropTable('diag_request_record');
		$this->dropTable('diag_request');
		$this->dropTable('doc_request_history');
		$this->dropTable('doc_request_record');
		$this->dropTable('doc_request');
	}

	public function down()
	{
		echo "m140331_135428_3433_drop_unused_requests does not support migration down.\n";
		return false;
	}

}