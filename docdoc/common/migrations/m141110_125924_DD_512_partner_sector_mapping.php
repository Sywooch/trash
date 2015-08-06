<?php
use dfs\docdoc\models\SectorModel;
use dfs\docdoc\models\PartnerSectorMappingModel;
/**
 * маппинг специальностей на сайте партнера и у нас
 */
class m141110_125924_DD_512_partner_sector_mapping extends CDbMigration
{
	private $_medportalId = 137;

	/**
	 * @return bool|void
	 */
	public function up()
	{
		$this->execute("
			CREATE TABLE `partner_sector_mapping` (
				`id` INT NOT NULL AUTO_INCREMENT,
				`partner_id` INT(11) NOT NULL,
				`sector_id` INT(11) NOT NULL,
				`partner_sector` VARCHAR(50) NOT NULL,
				PRIMARY KEY (`id`),
				INDEX `partner_sector` (`partner_sector` ASC),
				INDEX `partner_id` (`partner_id` ASC)
			)  ENGINE=InnoDB DEFAULT CHARSET=utf8"
		);

		$mapping = [
			"analysis" => "vrach_uzi",
			"andrology" => "androlog",
			"procreation" => "akusher",
			"besplodie" => "ginekolog",
			"vessels" => "flebolog",
			"otolaryngology" => "lor_otolaringolog",
			"gastroenterology" => "gastroenterolog",
			"gynaecology" => "ginekolog",
			"ophthalmology" => "okulist",
			"nutrition" => "dietolog",
			"allergology" => "allergolog",
			"infection" => "infektsionist",
			"cardiology" => "kardiolog",
			"dermatology" => "dermatolog",
			"cosmetology" => "kosmetolog",
			"mammology" => "mammolog",
			"narcology" => "narkolog",
			"neurology" => "nevrolog",
			"oncology" => "onkolog",
			"orthopedy" => "ortoped",
			"pediatrics" => "pediatr",
			"plasurgery" => "plasticheskij_hirurg",
			"venerology" => "venerolog",
			"proctology" => "proktolog",
			"psychiatry" => "psihiatr",
			"psychology" => "psiholog",
			"pulmonology" => "pulmonolog",
			"rheumatology" => "revmatolog",
			"sexology" => "seksolog",
			"stomatology" => "stomatolog",
			"urology" => "urolog",
			"surgery" => "hirurg",
			"endocrinology" => "endokrinolog",
		];

		foreach ($mapping as $k => $v) {
			$sector = SectorModel::model()->byRewriteName($v)->find();
			$m = new PartnerSectorMappingModel();
			$m->partner_id = $this->_medportalId;
			$m->sector_id = $sector->id;
			$m->partner_sector = $k;
			$m->save();
		}
	}

	/**
	 * @return bool|void
	 */
	public function down()
	{
		$this->dropTable('partner_sector_mapping');
	}
}
