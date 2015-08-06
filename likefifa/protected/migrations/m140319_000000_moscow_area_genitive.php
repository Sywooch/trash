<?php

/**
 * m140319_000000_moscow_area_genitive class file.
 *
 * Склонение города в родительный падеж и предложный падежы
 *
 * @author   Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @see      https://docdoc.megaplan.ru/task/1003365/card/
 * @package  migrations
 */
class m140319_000000_moscow_area_genitive extends CDbMigration
{

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->addColumn("moscow_area", "genitive", "VARCHAR(64)");
		$this->addColumn("moscow_area", "prepositional", "VARCHAR(64)");

		$cities = MoscowArea::model()->findAll();
		if ($cities) {
			foreach ($cities as $model) {
				$genitive = $this->_getNewFormText($model->name, 1);
				if (!$genitive) {
					$genitive = $model->name;
				}
				$this->update("moscow_area", array("genitive" => $genitive), "id = :id", array("id" => $model->id));

				$prepositional = $this->_getNewFormText($model->name, 5);
				if (!$prepositional) {
					$prepositional = $model->name;
				}
				$this->update(
					"moscow_area",
					array("prepositional" => $prepositional),
					"id = :id",
					array("id" => $model->id)
				);

				$model->save();
			}
		}
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropColumn("moscow_area", "genitive");
		$this->dropColumn("moscow_area", "prepositional");
	}

	/**
	 * Склонение слов по падежам. С использованием api Яндекса
	 *
	 * @var string  $text    текст
	 * @var integer $numForm нужный падеж. Число от 0 до 5
	 *
	 * @return - вернет false при неудаче. При успехе вернет нужную форму слова
	 */
	protected function _getNewFormText($text, $numForm)
	{
		$urlXml = "http://export.yandex.ru/inflect.xml?name=" . urlencode($text);
		$result = @simplexml_load_file($urlXml);
		if ($result) {
			$arrData = array();
			foreach ($result->inflection as $one) {
				$arrData[] = (string)$one;
			}
			if (!empty($arrData[$numForm])) {
				return $arrData[$numForm];
			}
		}

		return false;
	}
}