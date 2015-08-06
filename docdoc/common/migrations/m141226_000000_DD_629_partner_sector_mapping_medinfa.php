<?php

/**
 * Прописывает соответствия нашей специальности и специальность на сайте партнера medinfa
 *
 * @package migrations
 */
class m141226_000000_DD_629_partner_sector_mapping_medinfa extends CDbMigration
{

	/**
	 * Идентификатор партнера medinfa
	 */
	const PARTNER_ID = 41;

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 90,
				"sector_id"      => 74
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 4,
				"sector_id"      => 72
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 2,
				"sector_id"      => 68
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 92,
				"sector_id"      => 79
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 3,
				"sector_id"      => 69
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 6,
				"sector_id"      => 70
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 7,
				"sector_id"      => 71
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 40,
				"sector_id"      => 106
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 9,
				"sector_id"      => 72
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 10,
				"sector_id"      => 105
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 14,
				"sector_id"      => 73
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 53,
				"sector_id"      => 100
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 13,
				"sector_id"      => 74
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 95,
				"sector_id"      => 76
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 96,
				"sector_id"      => 104
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 15,
				"sector_id"      => 112
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 16,
				"sector_id"      => 75
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 17,
				"sector_id"      => 76
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 97,
				"sector_id"      => 100
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 18,
				"sector_id"      => 77
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 19,
				"sector_id"      => 78
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 20,
				"sector_id"      => 79
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 21,
				"sector_id"      => 80
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 22,
				"sector_id"      => 81
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 23,
				"sector_id"      => 81
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 39,
				"sector_id"      => 108
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 12,
				"sector_id"      => 82
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 24,
				"sector_id"      => 83
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 100,
				"sector_id"      => 77
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 11,
				"sector_id"      => 84
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 89,
				"sector_id"      => 70
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 25,
				"sector_id"      => 85
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 82,
				"sector_id"      => 86
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 26,
				"sector_id"      => 98
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 27,
				"sector_id"      => 89
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 28,
				"sector_id"      => 87
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 29,
				"sector_id"      => 102
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 103,
				"sector_id"      => 107
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 31,
				"sector_id"      => 110
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 32,
				"sector_id"      => 111
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 43,
				"sector_id"      => 75
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 33,
				"sector_id"      => 90
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 34,
				"sector_id"      => 91
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 106,
				"sector_id"      => 83
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 35,
				"sector_id"      => 93
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 5,
				"sector_id"      => 94
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 36,
				"sector_id"      => 95
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 41,
				"sector_id"      => 96
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 110,
				"sector_id"      => 96
			]
		);
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->delete("partner_sector_mapping", "partner_id = " . self::PARTNER_ID);
	}
}