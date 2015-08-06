<?php
namespace dfs\docdoc\extensions;

use \Yii;
/**
 *
 * расширение класса \CActiveRecord
 *
 * Добавляет функциональность по получению результата обработки модели в виде массива
 *
 *
 */
class AdvancedCActiveRecord extends \CActiveRecord
{
	/**
	 * Первоначальное значение аттрибутов
	 *
	 * @var []
	 */
	protected $_old_attr = [];

	/**
	 * Инициализация объекта
	 */
	public function init()
	{
		$this->resetChanges();

		parent::init();
	}

	/**
	 * Сброс флагов изменения свойств модели
	 */
	public function resetChanges()
	{
		$this->_old_attr = $this->getAttributes();
	}

	/**
	 * После сохранения
	 */
	public function afterSave()
	{
		$this->resetChanges();

		parent::afterSave();
	}

	/**
	 * Выполнение действий после выборки
	 */
	public function afterFind()
	{
		$this->resetChanges();

		parent::afterFind();
	}

	/**
	 * Изменилось ли значение аттрибута
	 *
	 * @param string $attr
	 *
	 * @return bool
	 */
	public function isChanged($attr)
	{
		return $this->_old_attr[$attr] != $this->$attr;
	}

	/**
	 * Получить значение атрибута до изменения
	 *
	 * @param string $attr
	 *
	 * @return mixed
	 */
	public function getOldValue($attr)
	{
		return $this->_old_attr[$attr];
	}

	/**
	 * Возвращает результат выполнения запроса в виде массива. Н еработает с реляцинной AR
	 *
	 * @throws \Exception метод не может обрабатывать релдяцинной запрос
	 *
	 * @param \CDbCriteria $criteria the query criteria
	 * @param boolean $all whether to return all data
	 * @return array
	 */
	protected function queryArray($criteria,$all=false)
	{
		$this->beforeFind();
		$this->applyScopes($criteria);

		if(empty($criteria->with)) {
			if(!$all)
				$criteria->limit=1;
			$command=$this->getCommandBuilder()->createFindCommand($this->getTableSchema(),$criteria,$this->getTableAlias());
			return $all ? $command->queryAll() : $command->queryRow();
		} else {
			throw new \Exception('Метод queryArray не может исользоваться для реляционных запросов. Необходимо использовать методы find()');
		}
	}


	/**
	 * Возвращает многомерный key->value  массив атрибутов объекта, полученных`findAll`
	 * НЕ СОЗДАЕТ ЭКЗЕМПЛЯРЫ ОБЪЕКТОВ
	 *
	 * @example
	 * <code>
	 *     //создаем criteria
	 *     $critera = new CDbCriteria();
	 *
	 *     // добавляем условия
	 *
	 *     // или мерджим с существующими условиями
	 *     $model->getDbCriteria()->mergeWith($criteria);
	 *
	 *     // получаем результат в виде массива
	 *     $results = $model->queryAll();
	 * </code>
	 *
	 * @param mixed $condition query condition or criteria.
	 * If a string, it is treated as query condition (the WHERE clause);
	 * If an array, it is treated as the initial values for constructing a {@link CDbCriteria} object;
	 * Otherwise, it should be an instance of {@link CDbCriteria}.
	 * @param array $params parameters to be bound to an SQL statement.
	 * This is only used when the first parameter is a string (query condition).
	 * In other cases, please use {@link CDbCriteria::params} to set parameters.
	 * @return array
	 */
	public function queryAll($condition='',$params=array())
	{
		Yii::trace(get_class($this).'.queryAll()','system.db.ar.CActiveRecord');
		$criteria=$this->getCommandBuilder()->createCriteria($condition, $params);
		return $this->queryArray($criteria,true);
	}


	/**
	* Возвращем одну строку в виде массива
	*
	* @param mixed $condition query condition or criteria.
	* If a string, it is treated as query condition (the WHERE clause);
	* If an array, it is treated as the initial values for constructing a {@link CDbCriteria} object;
	* Otherwise, it should be an instance of {@link CDbCriteria}.
	 * @param array $params parameters to be bound to an SQL statement.
	 * This is only used when the first parameter is a string (query condition).
	 * In other cases, please use {@link CDbCriteria::params} to set parameters.
	 * @return array the record found. Null if no record is found.
	 */
	public function queryRow($condition='',$params=array())
	{
		Yii::trace(get_class($this).'.find()','system.db.ar.CActiveRecord');
		$criteria=$this->getCommandBuilder()->createCriteria($condition, $params);
		return $this->queryArray($criteria);
	}

}