<?php

/**
 * Class m141106_092349_dd_449_update_requests_set_kind_1_where_partner_id_in_47_and_43
 */
class m141106_092349_dd_449_update_requests_set_kind_1_where_partner_id_in_47_and_43 extends CDbMigration
{
	/**
	 * После решения этой задачи нужно все заявки без записи партнеров mrtportal и mrtrus обозначить как "диагностика".
	 * @link https://docdoc.atlassian.net/browse/DD-449
	 * @return bool|void
	 */
	public function up()
	{
		$this->execute("update request set kind=1  where partner_id in (43, 47) and kind=0 and date_admission is null;");
	}

	public function down()
	{
		//pass не ревернуть
	}
}
