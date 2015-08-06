<?php

use dfs\docdoc\models\DiagnosticaModel;

/**
 * @package migrations
 */
class m150224_000001_DD_825_diagnostica extends CDbMigration
{

	/**
	 * @return void
	 */
	public function up()
	{
		$this->alterColumn('SMSQuery', 'message', 'VARCHAR(512) NOT NULL');
		$this->addColumn('diagnostica', 'accusative_name', 'VARCHAR(512) NOT NULL');
		$this->addColumn('diagnostica', 'genitive_name', 'VARCHAR(512) NOT NULL');

		$criteria = new CDbCriteria();
		$criteria->condition = "t.parent_id = 0";
		foreach (DiagnosticaModel::model()->findAll($criteria) as $model) {
			$this->update(
				"diagnostica",
				[
					"accusative_name" => $model->reduction_name ? $model->reduction_name : $model->name
				],
				"id = {$model->id}"
			);
		}
		$this->update(
			"diagnostica",
			["accusative_name" => "Компьютерную томографию"],
			"id = 19"
		);
		$this->update(
			"diagnostica",
			["accusative_name" => "Эхокардиографию (ЭХОКГ)"],
			"id = 90"
		);
		$this->update(
			"diagnostica",
			["accusative_name" => "Флюорографию грудной клетки"],
			"id = 116"
		);
		$this->update(
			"diagnostica",
			["accusative_name" => "Функциональную диагностику"],
			"id = 132"
		);
		$this->update(
			"diagnostica",
			["accusative_name" => "Бронхоскопию"],
			"id = 143"
		);
		$this->update(
			"diagnostica",
			["accusative_name" => "Гастроскопию"],
			"id = 144"
		);
		$this->update(
			"diagnostica",
			["accusative_name" => "Денситометрию"],
			"id = 156"
		);

		$criteria = new CDbCriteria();
		$criteria->condition = "t.parent_id != 0";
		foreach (DiagnosticaModel::model()->findAll($criteria) as $model) {
			$this->update(
				"diagnostica",
				[
					"genitive_name" => $model->name
				],
				"id = {$model->id}"
			);
		}
		$this->update(
			"diagnostica",
			["genitive_name" => "эхоэнцефалографии (Эхо-ЭГ)"],
			"id = 133"
		);
		$this->update(
			"diagnostica",
			["genitive_name" => "реоэнцефалографии (РЭГ)"],
			"id = 134"
		);
		$this->update(
			"diagnostica",
			["genitive_name" => "электрокардиографии (ЭКГ)"],
			"id = 135"
		);
		$this->update(
			"diagnostica",
			["genitive_name" => "суточного ЭКГ мониторирования (по Холтеру)"],
			"id = 136"
		);
		$this->update(
			"diagnostica",
			["genitive_name" => "ЭКГ-пробы с дозированной физической нагрузкой (велоэргометрия или тредмил-тест)"],
			"id = 137"
		);
		$this->update(
			"diagnostica",
			["genitive_name" => "ЭКГ-пробы с дозированной физической нагрузкой (велоэргометрия или тредмил-тест)"],
			"id = 137"
		);
		$this->update(
			"diagnostica",
			["genitive_name" => "эзофагогастродуоденоскопии (ЭФГДС)"],
			"id = 139"
		);
		$this->update(
			"diagnostica",
			["genitive_name" => "колоноскопии"],
			"id = 140"
		);
		$this->update(
			"diagnostica",
			["genitive_name" => "ректороманоскопии"],
			"id = 141"
		);
		$this->update(
			"diagnostica",
			["genitive_name" => "трахеобронхоскопии"],
			"id = 142"
		);
		$this->update(
			"diagnostica",
			["genitive_name" => "гистероскопии"],
			"id = 175"
		);
		$this->update(
			"diagnostica",
			["genitive_name" => "кольпоскопии"],
			"id = 176"
		);
		$this->update(
			"diagnostica",
			["genitive_name" => "артроскопии"],
			"id = 177"
		);
		$this->update(
			"diagnostica",
			["genitive_name" => "цистоскопии"],
			"id = 178"
		);
		$this->update(
			"diagnostica",
			["genitive_name" => "электроэнцефалографии (ЭЭГ)"],
			"id = 179"
		);
		$this->update(
			"diagnostica",
			["genitive_name" => "суточного мониторирования АД"],
			"id = 180"
		);
		$this->update(
			"diagnostica",
			["genitive_name" => "суточного мониторирования АД+ЭКГ"],
			"id = 181"
		);
		$this->update(
			"diagnostica",
			["genitive_name" => "спирометрии"],
			"id = 182"
		);
	}

	/**
	 * @return void
	 */
	public function down()
	{
		$this->dropColumn('diagnostica', 'accusative_name');
		$this->dropColumn('diagnostica', 'genitive_name');
	}
}