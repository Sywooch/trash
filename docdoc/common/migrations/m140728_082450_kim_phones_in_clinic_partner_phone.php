<?php

class m140728_082450_kim_phones_in_clinic_partner_phone extends CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function up()
	{
		$data = $this->getData();

		foreach ($data as $item) {
			$this->execute("insert into phone (number) values ('{$item['phone']}')");
			$this->execute("insert into clinic_partner_phone(partner_id, clinic_id, phone_id) values (1, {$item['clinic_id']}, (select id from phone where number='{$item['phone']}'))");
		}

	}

	/**
	 * @return bool|void
	 */
	public function down()
	{
		$data = $this->getData();

		foreach ($data as $item) {
			$this->execute("delete from clinic_partner_phone where partner_id=1 and phone_id = (select id from phone where number='{$item['phone']}')");
			$this->execute("delete from phone where number='{$item['phone']}'");
		}

	}

	/**
	 * Массив с данными для миграции
	 *
	 * @return array
	 */
	public function getData()
	{
		return [
			[
				'phone'     => '74993721766',
				'clinic_id' => 46,
			],
			[
				'phone'     => '74993721690',
				'clinic_id' => 13,
			],
			[
				'phone'     => '74952369301',
				'clinic_id' => 1293,
			],
			[
				'phone'     => '74993721697',
				'clinic_id' => 1592,
			],
			[
				'phone'     => '74952552649',
				'clinic_id' => 2,
			],
			[
				'phone'     => '74952369859',
				'clinic_id' => 904,
			],
			[
				'phone'     => '74993720753',
				'clinic_id' => 1,
			],
			[
				'phone'     => '74952552672',
				'clinic_id' => 86,
			],
			[
				'phone'     => '74952369712',
				'clinic_id' => 2056,
			],
			[
				'phone'     => '74952550897',
				'clinic_id' => 1419,
			],
			[
				'phone'     => '74952551894',
				'clinic_id' => 546,
			],
		];
	}

}
