<?php

/**
 * Файл класса m141130_000000_DD_629_partner_sector_mapping_153
 *
 * Прописывает соответствия нашей специальности и специальность на сайте партнера №153
 *
 * @author  Mikhail Vasilyev <mvasilyev@docdoc.ru>
 * @link    https://docdoc.atlassian.net/browse/DD-629
 * @package migrations
 */
class m141130_000000_DD_629_partner_sector_mapping_153 extends CDbMigration
{

	/**
	 * Идентификатор партнера
	 */
	const PARTNER_ID = 153;

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
				"partner_sector" => 34,
				"sector_id"      => 72
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 212,
				"sector_id"      => 73
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 245,
				"sector_id"      => 91
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 215,
				"sector_id"      => 76
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 39,
				"sector_id"      => 70
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 195,
				"sector_id"      => 101
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 207,
				"sector_id"      => 71
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 111,
				"sector_id"      => 106
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 249,
				"sector_id"      => 91
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 37,
				"sector_id"      => 73
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 43,
				"sector_id"      => 74
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 45,
				"sector_id"      => 74
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 202,
				"sector_id"      => 112
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 47,
				"sector_id"      => 75
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 193,
				"sector_id"      => 76
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 250,
				"sector_id"      => 78
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 65,
				"sector_id"      => 80
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 53,
				"sector_id"      => 105
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 219,
				"sector_id"      => 81
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 235,
				"sector_id"      => 82
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 56,
				"sector_id"      => 90
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 222,
				"sector_id"      => 77
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 59,
				"sector_id"      => 84
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 241,
				"sector_id"      => 85
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 61,
				"sector_id"      => 85
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 297,
				"sector_id"      => 86
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 63,
				"sector_id"      => 91
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 67,
				"sector_id"      => 87
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 70,
				"sector_id"      => 102
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 55,
				"sector_id"      => 91
			]
		);

		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 72,
				"sector_id"      => 110
			]
		);

		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 75,
				"sector_id"      => 91
			]
		);

		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 76,
				"sector_id"      => 90
			]
		);

		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 192,
				"sector_id"      => 93
			]
		);

		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 78,
				"sector_id"      => 91
			]
		);

		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 210,
				"sector_id"      => 94
			]
		);

		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 81,
				"sector_id"      => 95
			]
		);

		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 211,
				"sector_id"      => 99
			]
		);

		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => 164,
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