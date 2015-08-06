<?php

/**
 * Файл класса m141118_000001_DD_548_partner_sector_mapping_doktor
 *
 * Прописывает соответствия нашей специальности и специальность на сайте партнера doktor.ru
 *
 * @author  Mikhail Vasilyev <mvasilyev@docdoc.ru>
 * @link    https://docdoc.atlassian.net/browse/DD-548
 * @package migrations
 */
class m141118_000001_DD_548_partner_sector_mapping_doktor extends CDbMigration
{

	const PARTNER_ID = 155;

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
				"partner_sector" => "allergolog",
				"sector_id"      => 68
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => "anaesthetist",
				"sector_id"      => 107
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => "pi",
				"sector_id"      => 70
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => "gastro",
				"sector_id"      => 71
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => "hepatolog",
				"sector_id"      => 109
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => "ginecolog",
				"sector_id"      => 72
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => "dermatolog",
				"sector_id"      => 73
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => "dietolog",
				"sector_id"      => 74
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => "infect",
				"sector_id"      => 112
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => "heart",
				"sector_id"      => 75
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => "cosm",
				"sector_id"      => 76
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => "lor",
				"sector_id"      => 77
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => "mammolog",
				"sector_id"      => 78
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => "narko",
				"sector_id"      => 80
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => "neurology",
				"sector_id"      => 81
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => "onkolog",
				"sector_id"      => 82
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => "pediatr",
				"sector_id"      => 85
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => "plasurgeon",
				"sector_id"      => 86
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => "hemorr",
				"sector_id"      => 98
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => "psycho",
				"sector_id"      => 89
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => "phone",
				"sector_id"      => 87
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => "sexolog",
				"sector_id"      => 111
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => "vasa",
				"sector_id"      => 145
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => "stomatolog",
				"sector_id"      => 90
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => "gripp",
				"sector_id"      => 91
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => "travma",
				"sector_id"      => 83
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => "urolog",
				"sector_id"      => 93
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => "physio",
				"sector_id"      => 113
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => "varicoz",
				"sector_id"      => 94
			]
		);
		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => "surgeon",
				"sector_id"      => 95
			]
		);

		$this->insert(
			"partner_sector_mapping",
			[
				"partner_id"     => self::PARTNER_ID,
				"partner_sector" => "oculist",
				"sector_id"      => 84
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