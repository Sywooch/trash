<?php

/**
 * Файл класса m141204_000000_DD_629_partner_sector_mapping_153_one_more
 *
 * Прописывает соответствия нашей специальности и специальность на сайте партнера №153 для психиатра
 *
 * @author  Mikhail Vasilyev <mvasilyev@docdoc.ru>
 * @link    https://docdoc.atlassian.net/browse/DD-629
 * @package migrations
 */
class m141204_000000_DD_629_partner_sector_mapping_153_one_more extends CDbMigration
{

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
				"partner_id"     => 153,
				"partner_sector" => 298,
				"sector_id"      => 89
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
		$this->delete("partner_sector_mapping", "partner_id = 153 AND partner_sector = 298");
	}
}