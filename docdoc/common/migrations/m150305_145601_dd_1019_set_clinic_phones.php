<?php
use dfs\docdoc\models\ClinicModel;

class m150305_145601_dd_1019_set_clinic_phones extends CDbMigration
{
	public function safeUp()
	{
		$this->execute(
			"update clinic set phone = asterisk_phone where  (phone is null or phone = '') and (asterisk_phone is not null or asterisk_phone != '') ;
			update clinic set phone = (select number_p from clinic_phone where clinic_id = id and label in ('Многоканальный', 'Основной', 'Общий колл-центр', 'Регистратура', 'Единый', 'Круглосуточно') limit 1) where  (phone is null or phone = '');"
		);

		$this->execute("update clinic set phone='74955653293'  where id in(28, 407);"); //фейковые клиники разработки

		$command = Yii::app()->db->createCommand("select id, name, status, city.title from clinic join city on city.id_city = clinic.city_id where (phone is null or phone = '') and status not in (2, 5)");
		$res  = $command->queryAll();

		if($res){
			echo 'Непроставлены телефоны для стедующих клиник (' . count($res) . ' штук)', PHP_EOL;

			foreach($res as $r){
				echo sprintf("Клиниа id:%s, name:%s, status:%s, city:%s", $r['id'], $r['name'], ClinicModel::getStatusList()[$r['status']], $r['title']), PHP_EOL;
			}
		}
	}

	public function down()
	{
		//ничего
		return true;
	}
}