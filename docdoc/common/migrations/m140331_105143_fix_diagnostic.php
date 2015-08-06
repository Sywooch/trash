<?php

/**
 * Class m140331_105143_fix_diagnostic
 *
 * Эспорт заявок на диагостику из старых заявок на врачей
 *
 * @link https://docdoc.megaplan.ru/task/1003391/card.html
 *
 */
class m140331_105143_fix_diagnostic extends CDbMigration {

	/**
	 * запросы, которые нужно выполнить при миграции
	 */
	public function safeUp(){

		//для всех заявок, где известен доктор, но пустая специализация врача, проставляем первую известную специализацию для этого врача
		$this->execute("UPDATE request
			SET req_sector_id = (
				SELECT
					sector_id
				FROM
					doctor_sector
				WHERE request.req_doctor_id = doctor_sector.doctor_id
				LIMIT 1)
			WHERE
				req_doctor_id>0
				AND req_sector_id<1");

		//заявка, которые являются заявками на диагностику и по которым есть запись
		//ставим статус "записан"
		$this->execute("UPDATE request
				SET
					kind=1, req_status=2
				WHERE req_id IN (
					99848,101614,99963,98657,101825,98278,98744,
					99681,98464,99603,97641,97641,99601,98566,98355,
					97667,98448,98065,98253,98121,97461,97100,96690,96141,
					95817,96265,96372,96080,95288,95329,93638,100522
				)");
	}

	/**
	 * при откате ничего не делаем
	 */
	public function safeDown()
	{

	}

}
