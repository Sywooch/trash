<?php

/**
 * m140414_000000_delete_docdoc class file.
 *
 * Удаляет таблицы в БД от ДокДока.
 *
 * @author   Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @see      https://docdoc.megaplan.ru/task/1003491/card/
 * @package  migrations
 */
class m140414_000000_delete_docdoc extends CDbMigration
{

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->_deleteIndexesAndForeignKeys();
		$this->_deleteTables();
	}

	/**
	 * Удаляет все индексы и ключи из таблиц от ДокДока
	 *
	 * @return void
	 */
	private function _deleteIndexesAndForeignKeys()
	{
		if (Yii::app()->db->schema->getTable("clinic_admin")) {
			$this->dropIndex("email", "clinic_admin");
		}
		if (Yii::app()->db->schema->getTable("clinic_phone")) {
			$this->dropIndex("clinicId", "clinic_phone");
		}
		if (Yii::app()->db->schema->getTable("diagnostic_center_address")) {
			$this->dropIndex("underground_station_id", "diagnostic_center_address");
		}
		if (Yii::app()->db->schema->getTable("diagnostic_center_diagnostica")) {
			$this->dropIndex("diagnostic_center_id", "diagnostic_center_diagnostica");
			$this->dropIndex("diagnostica_id", "diagnostic_center_diagnostica");
		}
		if (Yii::app()->db->schema->getTable("diagnostic_center_image")) {
			$this->dropIndex("diagnostic_center_id", "diagnostic_center_image");
		}
		if (Yii::app()->db->schema->getTable("diagnostica")) {
			$this->dropIndex("parent_id", "diagnostica");
		}
		if (Yii::app()->db->schema->getTable("district_underground_station2")) {
			$this->dropIndex("district_moscow_id", "district_underground_station2");
		}
		if (Yii::app()->db->schema->getTable("doctor")) {
			$this->dropForeignKey("doctor_ibfk_17", "doctor");
			$this->dropIndex("clinic_id", "doctor");
			$this->dropIndex("addNumber", "doctor");
		}
		if (Yii::app()->db->schema->getTable("doctor_address")) {
			$this->dropForeignKey("doctor_address_ibfk_1", "doctor_address");
			$this->dropForeignKey("doctor_address_ibfk_2", "doctor_address");
			$this->dropIndex("doctor", "doctor_address");
			$this->dropIndex("underground_station", "doctor_address");
		}
		if (Yii::app()->db->schema->getTable("doctor_appointment")) {
			$this->dropForeignKey("doctor_appointment_ibfk_1", "doctor_appointment");
			$this->dropIndex("doctor", "doctor_appointment");
		}
		if (Yii::app()->db->schema->getTable("doctor_opinion")) {
			$this->dropForeignKey("doctor_opinion_ibfk_1", "doctor_opinion");
			$this->dropIndex("doctor", "doctor_opinion");
		}
		if (Yii::app()->db->schema->getTable("doctor_request")) {
			$this->dropForeignKey("doctor_request_ibfk_1", "doctor_request");
			$this->dropForeignKey("doctor_request_ibfk_2", "doctor_request");
			$this->dropIndex("doctor", "doctor_request");
			$this->dropIndex("sector", "doctor_request");
		}
		if (Yii::app()->db->schema->getTable("doctor_request_address")) {
			$this->dropForeignKey("doctor_request_address_ibfk_1", "doctor_request_address");
			$this->dropForeignKey("doctor_request_address_ibfk_2", "doctor_request_address");
			$this->dropIndex("underground_station_id", "doctor_request_address");
		}
		if (Yii::app()->db->schema->getTable("doctor_sector")) {
			$this->dropForeignKey("doctor_sector_ibfk_1", "doctor_sector");
			$this->dropForeignKey("doctor_sector_ibfk_2", "doctor_sector");
			$this->dropIndex("doctor_sector_ibfk_1", "doctor_sector");
		}
		if (Yii::app()->db->schema->getTable("doctor_shedule_week")) {
			$this->dropIndex("doctor_id", "doctor_shedule_week");
		}
		if (Yii::app()->db->schema->getTable("img_clinic")) {
			$this->dropIndex("clinicId", "img_clinic");
		}
		if (Yii::app()->db->schema->getTable("log_back_user")) {
			$this->dropIndex("code", "log_back_user");
			$this->dropIndex("crDate", "log_back_user");
		}
		if (Yii::app()->db->schema->getTable("net_city")) {
			$this->dropIndex("country_id", "net_city");
			$this->dropIndex("name_ru", "net_city");
			$this->dropIndex("name_en", "net_city");
		}
		if (Yii::app()->db->schema->getTable("net_city_ip")) {
			$this->dropIndex("city_id", "net_city_ip");
			$this->dropIndex("ip", "net_city_ip");
		}
		if (Yii::app()->db->schema->getTable("net_country")) {
			$this->dropIndex("code", "net_country");
			$this->dropIndex("name_en", "net_country");
			$this->dropIndex("name_ru", "net_country");
		}
		if (Yii::app()->db->schema->getTable("net_country_ip")) {
			$this->dropIndex("country_id", "net_country_ip");
			$this->dropIndex("ip", "net_country_ip");
		}
		if (Yii::app()->db->schema->getTable("net_euro")) {
			$this->dropIndex("country_id", "net_euro");
			$this->dropIndex("ip", "net_euro");
		}
		if (Yii::app()->db->schema->getTable("net_ru")) {
			$this->dropIndex("city_id", "net_ru");
			$this->dropIndex("ip", "net_ru");
		}
		if (Yii::app()->db->schema->getTable("promo_text_zone")) {
			$this->dropForeignKey("promo_text_zone_ibfk_1", "promo_text_zone");
			$this->dropForeignKey("promo_text_zone_ibfk_2", "promo_text_zone");
			$this->dropIndex("promo_text_zone_ibfk_2", "promo_text_zone");
		}
		if (Yii::app()->db->schema->getTable("promo_zone")) {
			$this->dropIndex("code", "promo_zone");
		}
		if (Yii::app()->db->schema->getTable("request_record")) {
			$this->dropIndex("request_id", "request_record");
		}
		if (Yii::app()->db->schema->getTable("request_station")) {
			$this->dropIndex("request_id", "request_station");
			$this->dropIndex("station_id", "request_station");
		}
		if (Yii::app()->db->schema->getTable("sector_seo_text_sector")) {
			$this->dropForeignKey("sector_seo_text_sector_ibfk_1", "sector_seo_text_sector");
			$this->dropForeignKey("sector_seo_text_sector_ibfk_2", "sector_seo_text_sector");
			$this->dropIndex("sector_seo_text_sector_ibfk_2", "sector_seo_text_sector");
		}
		if (Yii::app()->db->schema->getTable("srbac_itemchildren")) {
			$this->dropIndex("child", "srbac_itemchildren");
		}
	}

	/**
	 * Удаляет все таблицы от ДокДока
	 *
	 * @return void
	 */
	private function _deleteTables()
	{
		if (Yii::app()->db->schema->getTable("academic_degree")) {
			$this->dropTable("academic_degree");
		}
		if (Yii::app()->db->schema->getTable("admin_4_clinic")) {
			$this->dropTable("admin_4_clinic");
		}
		if (Yii::app()->db->schema->getTable("area")) {
			$this->dropTable("area");
		}
		if (Yii::app()->db->schema->getTable("article_section")) {
			$this->dropTable("article_section");
		}
		if (Yii::app()->db->schema->getTable("category_dict")) {
			$this->dropTable("category_dict");
		}
		if (Yii::app()->db->schema->getTable("city_dict")) {
			$this->dropTable("city_dict");
		}
		if (Yii::app()->db->schema->getTable("client")) {
			$this->dropTable("client");
		}
		if (Yii::app()->db->schema->getTable("clinic")) {
			$this->dropTable("clinic");
		}
		if (Yii::app()->db->schema->getTable("clinic_address")) {
			$this->dropTable("clinic_address");
		}
		if (Yii::app()->db->schema->getTable("clinic_admin")) {
			$this->dropTable("clinic_admin");
		}
		if (Yii::app()->db->schema->getTable("clinic_phone")) {
			$this->dropTable("clinic_phone");
		}
		if (Yii::app()->db->schema->getTable("degree_dict")) {
			$this->dropTable("degree_dict");
		}
		if (Yii::app()->db->schema->getTable("diagnostic_center")) {
			$this->dropTable("diagnostic_center");
		}
		if (Yii::app()->db->schema->getTable("diagnostic_center_address")) {
			$this->dropTable("diagnostic_center_address");
		}
		if (Yii::app()->db->schema->getTable("diagnostic_center_diagnostica")) {
			$this->dropTable("diagnostic_center_diagnostica");
		}
		if (Yii::app()->db->schema->getTable("diagnostic_center_image")) {
			$this->dropTable("diagnostic_center_image");
		}
		if (Yii::app()->db->schema->getTable("diagnostica")) {
			$this->dropTable("diagnostica");
		}
		if (Yii::app()->db->schema->getTable("diagnostica4clinic")) {
			$this->dropTable("diagnostica4clinic");
		}
		if (Yii::app()->db->schema->getTable("district_underground_station2")) {
			$this->dropTable("district_underground_station2");
		}
		if (Yii::app()->db->schema->getTable("doctor")) {
			$this->dropTable("doctor");
		}
		if (Yii::app()->db->schema->getTable("doctor_4_clinic")) {
			$this->dropTable("doctor_4_clinic");
		}
		if (Yii::app()->db->schema->getTable("doctor_address")) {
			$this->dropTable("doctor_address");
		}
		if (Yii::app()->db->schema->getTable("doctor_appointment")) {
			$this->dropTable("doctor_appointment");
		}
		if (Yii::app()->db->schema->getTable("doctor_opinion")) {
			$this->dropTable("doctor_opinion");
		}
		if (Yii::app()->db->schema->getTable("doctor_request")) {
			$this->dropTable("doctor_request");
		}
		if (Yii::app()->db->schema->getTable("doctor_request_address")) {
			$this->dropTable("doctor_request_address");
		}
		if (Yii::app()->db->schema->getTable("doctor_sector")) {
			$this->dropTable("doctor_sector");
		}
		if (Yii::app()->db->schema->getTable("doctor_shedule_week")) {
			$this->dropTable("doctor_shedule_week");
		}
		if (Yii::app()->db->schema->getTable("education")) {
			$this->dropTable("education");
		}
		if (Yii::app()->db->schema->getTable("education_4_doctor")) {
			$this->dropTable("education_4_doctor");
		}
		if (Yii::app()->db->schema->getTable("education_dict")) {
			$this->dropTable("education_dict");
		}
		if (Yii::app()->db->schema->getTable("illness")) {
			$this->dropTable("illness");
		}
		if (Yii::app()->db->schema->getTable("img_clinic")) {
			$this->dropTable("img_clinic");
		}
		if (Yii::app()->db->schema->getTable("log")) {
			$this->dropTable("log");
		}
		if (Yii::app()->db->schema->getTable("log_ami")) {
			$this->dropTable("log_ami");
		}
		if (Yii::app()->db->schema->getTable("log_back_user")) {
			$this->dropTable("log_back_user");
		}
		if (Yii::app()->db->schema->getTable("log_dict")) {
			$this->dropTable("log_dict");
		}
		if (Yii::app()->db->schema->getTable("log_sms")) {
			$this->dropTable("log_sms");
		}
		if (Yii::app()->db->schema->getTable("net_city")) {
			$this->dropTable("net_city");
		}
		if (Yii::app()->db->schema->getTable("net_city_ip")) {
			$this->dropTable("net_city_ip");
		}
		if (Yii::app()->db->schema->getTable("net_country")) {
			$this->dropTable("net_country");
		}
		if (Yii::app()->db->schema->getTable("net_country_ip")) {
			$this->dropTable("net_country_ip");
		}
		if (Yii::app()->db->schema->getTable("net_euro")) {
			$this->dropTable("net_euro");
		}
		if (Yii::app()->db->schema->getTable("net_ru")) {
			$this->dropTable("net_ru");
		}
		if (Yii::app()->db->schema->getTable("price_range")) {
			$this->dropTable("price_range");
		}
		if (Yii::app()->db->schema->getTable("promo_text")) {
			$this->dropTable("promo_text");
		}
		if (Yii::app()->db->schema->getTable("promo_text_zone")) {
			$this->dropTable("promo_text_zone");
		}
		if (Yii::app()->db->schema->getTable("promo_zone")) {
			$this->dropTable("promo_zone");
		}
		if (Yii::app()->db->schema->getTable("rank_dict")) {
			$this->dropTable("rank_dict");
		}
		if (Yii::app()->db->schema->getTable("request")) {
			$this->dropTable("request");
		}
		if (Yii::app()->db->schema->getTable("request_history")) {
			$this->dropTable("request_history");
		}
		if (Yii::app()->db->schema->getTable("request_record")) {
			$this->dropTable("request_record");
		}
		if (Yii::app()->db->schema->getTable("request_shedule")) {
			$this->dropTable("request_shedule");
		}
		if (Yii::app()->db->schema->getTable("request_station")) {
			$this->dropTable("request_station");
		}
		if (Yii::app()->db->schema->getTable("right_4_user")) {
			$this->dropTable("right_4_user");
		}
		if (Yii::app()->db->schema->getTable("sector_seo_text")) {
			$this->dropTable("sector_seo_text");
		}
		if (Yii::app()->db->schema->getTable("sector_seo_text_sector")) {
			$this->dropTable("sector_seo_text_sector");
		}
		if (Yii::app()->db->schema->getTable("sedule_exeption")) {
			$this->dropTable("sedule_exeption");
		}
		if (Yii::app()->db->schema->getTable("shedule_appointment")) {
			$this->dropTable("shedule_appointment");
		}
		if (Yii::app()->db->schema->getTable("shedule_rules")) {
			$this->dropTable("shedule_rules");
		}
		if (Yii::app()->db->schema->getTable("srbac_assignments")) {
			$this->dropTable("srbac_assignments");
		}
		if (Yii::app()->db->schema->getTable("srbac_itemchildren")) {
			$this->dropTable("srbac_itemchildren");
		}
		if (Yii::app()->db->schema->getTable("srbac_items")) {
			$this->dropTable("srbac_items");
		}
		if (Yii::app()->db->schema->getTable("street_dict")) {
			$this->dropTable("street_dict");
		}
		if (Yii::app()->db->schema->getTable("underground_station_4_clinic")) {
			$this->dropTable("underground_station_4_clinic");
		}
		if (Yii::app()->db->schema->getTable("user")) {
			$this->dropTable("user");
		}
		if (Yii::app()->db->schema->getTable("user_right_dict")) {
			$this->dropTable("user_right_dict");
		}
	}
}