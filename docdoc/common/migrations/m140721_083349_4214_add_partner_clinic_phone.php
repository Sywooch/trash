<?php

class m140721_083349_4214_add_partner_clinic_phone extends CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function safeUp()
	{
		$data = $this->getData();

		foreach($data as $item){
			$this->execute("insert into phone (number) values ('{$item['phone']}')");
			$this->execute("insert into clinic_partner_phone(partner_id, clinic_id, phone_id) values (16, {$item['clinic_id']}, (select id from phone where number='{$item['phone']}'))");
		}

	}

	/**
	 * @return bool|void
	 */
	public function safeDown()
	{
		$data = $this->getData();

		foreach($data as $item){
			$this->execute("delete from clinic_partner_phone where partner_id=16 and phone_id = (select id from phone where number='{$item['phone']}')");
			$this->execute("delete from phone where number='{$item['phone']}'");
		}
	}

	/**
	 * Массив с данными для миграции
	 *
	 * @return array
	 */
	protected function getData()
	{
		$data = [
			[
				'clinic_id' => 1,
				'phone' => '74993720347',
			],
			[
				'clinic_id' => 904,
				'phone' => '74993720334',
			],
			[
				'clinic_id' => 2,
				'phone' => '74993720284',
			],
			[
				'clinic_id' => 13,
				'phone' => '74993720256',
			],
			[
				'clinic_id' => 154,
				'phone' => '74993720207',
			],

		];

		return $data;
	}
}
