<?php
/**
 * @author Aleksey Parshukov <parshukovag@gmail.com>
 * @date 26.09.2013
 *
 * @see https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=1310733
 *
 * Добавляет идентификатор аккаунта в таблицу мастеров
 */
class m130926_103911_account_for_LfMaster extends CDbMigration
{
	public function up()
	{
		$this->addColumn('lf_master', 'account_id', 'int');
		$this->addForeignKey('account_FK', 'lf_master', 'account_id', 'payments_account', 'id');
	}

	public function down()
	{
		$this->dropForeignKey('account_FK', 'lf_master');
		$this->dropColumn('lf_master', 'account_id');
	}
}