<?php
use likefifa\components\system\ActiveRecord;
use likefifa\models\CityModel;

/**
 * Файл класса LfGroup.
 *
 * Модель для таблицы "lf_group"
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @link    https://docdoc.megaplan.ru/task/1002975/card/
 * @package models
 *
 * @property int                $id              идентификатор
 * @property string             $name            название
 * @property string             $rewrite_name    абривиатура url
 * @property string             $genitive        название в родительном падеже
 * @property string             $genitive_one    название в родительном падеже в единственном числе
 *
 * @property LfSpecialization[] $specializations модели специальностей
 *
 * @method LfGroup ordered
 */
class LfGroup extends ActiveRecord
{

	/**
	 * Возвращает статическую модель указанного класса.
	 *
	 * @param string $className название класса
	 *
	 * @return LfGroup
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * Возвращает имя связанной таблицы базы данных
	 *
	 * @return string
	 */
	public function tableName()
	{
		return 'lf_group';
	}

	/**
	 * Возвращает правила проверки для атрибутов модели
	 *
	 * @return string[]
	 */
	public function rules()
	{
		return array(
			array('name, rewrite_name, genitive_one', 'required'),
			array('name, rewrite_name, genitive_one', 'length', 'max' => 256),
			array('id, name', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * Возвращает связи между объектами
	 *
	 * @return string[]
	 */
	public function relations()
	{
		return array(
			'specializations' => array(
				self::MANY_MANY,
				'LfSpecialization',
				'lf_group_specialization(group_id, specialization_id)'
			),
		);
	}

	/**
	 * Возвращает подписей полей
	 *
	 * @return string[]
	 */
	public function attributeLabels()
	{
		return array(
			'id'   => 'ID',
			'name' => 'Name',
		);
	}

	/**
	 * Получает список моделей. Используется при поиске
	 *
	 * @return CActiveDataProvider
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('name', $this->name, true);

		return new CActiveDataProvider(
			$this, array(
				'criteria' => $criteria,
			)
		);
	}

	/**
	 * Возвращает декларацию названных областей.
	 *
	 * @return string[]
	 */
	public function scopes()
	{
		return array(
			'ordered' => array(
				'order' => 'name ASC',
			),
		);
	}

	/**
	 * Получает список элементов
	 *
	 * @param bool $withEmpty использовать ли пустой первый элемент
	 *
	 * @return string[]
	 */
	public function getListItems($withEmpty = false)
	{
		$items = array();

		if ($withEmpty) {
			$items[''] = 'группа не выбрана';
		}

		$specializations = $this->ordered()->findAll();
		foreach ($specializations as $specialization) {
			$items[$specialization->id] = su::ucfirst($specialization->name);
		}

		return $items;
	}

	/**
	 * Получает первую специализацию
	 *
	 * @return LfSpecialization|null
	 */
	public function getFirstSpecialization()
	{
		return $this->specializations ? $this->specializations[0] : null;
	}

	/**
	 * Получает ссылку на специализацию
	 *
	 * @return string[]
	 */
	public function getSearchUrl()
	{
		$spec = $this->getFirstSpecialization();
		return Yii::app()->createUrl(
			'search/custom',
			array(
				'specialization' => $spec->getRewriteName(),
				'service'        => $spec->getFirstService()->getRewriteName(),
			)
		);
	}

	/**
	 * Получает идентификатор группы специализации
	 *
	 * @param string $name абривиатура url
	 *
	 * @return int|bool
	 */
	public function getIdByRewriteName($name)
	{
		if ($name) {
			$criteria = new CDbCriteria;
			$criteria->condition = "rewrite_name = :rewrite_name";
			$criteria->params = array(":rewrite_name" => $name);
			$model = $this->find($criteria);
			if ($model) {
				return $model->id;
			}
		}

		return false;
	}

	/**
	 * Получает название группы специализации
	 *
	 * @param string $name абривиатура url
	 *
	 * @return string
	 */
	public function getNameByRewriteName($name)
	{
		$pk = $this->getIdByRewriteName($name);
		if ($pk) {
			$model = $this->findByPk($pk);
			if ($model) {
				return $model->name;
			}
		}

		return null;
	}

	/**
	 * Получает ссылку для раздел
	 * Используется на главной странице
	 *
	 * @param CityModel $city
	 *
	 * @return string
	 */
	public function getLinkForMain($city = null)
	{
		$params = ["speciality" => $this->rewrite_name];
		if($city != null) {
			$params['city'] = $city->rewrite_name;
		}
		return Yii::app()->createUrl("masters/custom", $params);
	}

	/**
	 * Получает модель по абривиатуре
	 *
	 * @param string $name абривиатура url
	 *
	 * @return self|null
	 */
	public function getModelByRewriteName($name)
	{
		$pk = $this->getIdByRewriteName($name);
		if ($pk) {
			return $this->findByPk($pk);
		}

		return null;
	}

	/**
	 * Получает специальности
	 *
	 * @param string $rewriteName абривиатура url
	 *
	 * @return LfSpecialization[]|null
	 */
	public function getSpecializationsByRewriteName($rewriteName)
	{
		$model = $this->getModelByRewriteName($rewriteName);
		if ($model && $model->specializations) {
			return $model->specializations;
		}

		return null;
	}

	/**
	 * Получает идентификаторы услуг по данной специализации
	 *
	 * @return string[]
	 */
	public function getServicesIn()
	{
		$list = array();

		if (!$this->specializations) {
			return $list;
		}

		foreach ($this->specializations as $spec) {
			$list = array_merge($spec->getRelationIds("services"), $list);
		}

		return $list;
	}
}