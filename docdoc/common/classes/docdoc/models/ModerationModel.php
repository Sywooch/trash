<?php

namespace dfs\docdoc\models;

/**
 * Файл класса ModerationModel
 *
 * Содержит измененные данные по другим моделям, которые еще ждут модерацию
 *
 * @package dfs.docdoc.models
 *
 * @property int        $id           ID
 * @property int        $entity_class Класс связанной записи
 * @property int        $entity_id    Идентификатор связанной записи
 * @property array      $data         Данные
 * @property bool       $is_new       Новая запись
 * @property bool       $is_delete    Удалить запись
 */
class ModerationModel extends \CActiveRecord
{
	/**
	 * @var array
	 */
	public $data = [];

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return StreetModel the static model class
	 */
	public static function model($className=__CLASS__)
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
		return 'moderation';
	}

	/**
	 * Возвращает правила проверки для атрибутов модели
	 *
	 * @return string[]
	 */
	public function rules()
	{
		return array(
			array('entity_class, entity_id', 'required'),
		);
	}

	/**
	 * Возвращает связи между объектами
	 *
	 * @return string[]
	 */
	public function relations()
	{
		return array();
	}

	/**
	 * Действия после выборки
	 */
	protected function afterFind() {

		$this->data = json_decode($this->data, true);

		return parent::afterFind();
	}

	/**
	 * Действия перед сохранением
	 *
	 * @return bool
	 */
	protected function beforeSave() {

		$this->data = json_encode($this->data);

		return parent::beforeSave();
	}

	/**
	 * Действия после сохранением
	 */
	protected function afterSave() {

		$this->data = json_decode($this->data, true);

		return parent::afterSave();
	}

	/**
	 * Получить экземпляр ModerationModel для записи $record
	 *
	 * @param \CActiveRecord $record
	 *
	 * @return ModerationModel
	 */
	public static function getForRecord(\CActiveRecord $record)
	{
		$class = join('', array_slice(explode('\\', get_class($record)), -1));

		$moderation = ModerationModel::model()->findByAttributes([
				'entity_class' => $class,
				'entity_id' => $record->id,
			]);

		if (!$moderation) {
			$moderation = new ModerationModel();
			$moderation->entity_id = $record->id;
			$moderation->entity_class = $class;
		}

		return $moderation;
	}

	/**
	 * Сбросить изменения
	 *
	 * @param array | null $fields
	 *
	 * @return $this
	 */
	public function resetFields($fields = null)
	{
		if ($fields === null) {
			$this->data = [];
		} else {
			foreach ($fields as $field) {
				unset($this->data[$field]);
			}
		}

		return $this;
	}

	/**
	 * Применить данные к модели
	 *
	 * @param \CActiveRecord $record
	 * @param array | null   $fields
	 *
	 * @return $this
	 */
	public function applyChanges(\CActiveRecord $record, $fields = null)
	{
		$data = $fields === null ? $this->data : array_intersect_key($this->data, array_flip($fields));

		$relations = $record->relations();

		foreach ($data as $field => $value) {
			if (isset($relations[$field])) {
				$this->applyRelationValue($record, $field, $value, $relations[$field]);
			} else {
				$record->{$field} = $value;
			}
		}

		return $this;
	}

	/**
	 * Установить изменения для модерации и применить к $record
	 *
	 * @param \CActiveRecord $record
	 * @param array          $data
	 * @param bool           $validate
	 *
	 * @return bool
	 */
	public function saveChangeData(\CActiveRecord $record, $data, $validate = true)
	{
		$changeData = $data + $this->data;

		$relations = $record->relations();

		$oldData = [];
		if ($changeData) {
			foreach ($changeData as $field => $value) {
				if (isset($relations[$field])) {
					if (!$this->applyRelationValue($record, $field, $value, $relations[$field])) {
						unset($changeData[$field]);
					};
				} else {
					$oldData[$field] = $record->{$field};
					$record->{$field} = $value;
				}
			}

			if ($validate && !$record->validate(array_keys($changeData))) {
				return false;
			}
		}

		foreach ($oldData as $field => $value) {
			if ($record->{$field} == $value) {
				unset($changeData[$field]);
			}
		}

		$result = true;

		if ($changeData || $this->is_new || $this->is_delete) {
			$this->data = $changeData;
			$result = $this->save();
		} elseif (!$this->isNewRecord) {
			$result = $this->delete();
		}

		return $result;
	}

	/**
	 * Применить и сохранить изменения для $record
	 *
	 * @param \CActiveRecord $record
	 * @param null | array   $fields
	 * @param bool           $resetStatus
	 *
	 * @return bool
	 */
	public function saveWithRecordChanges(\CActiveRecord $record, $fields = null, $resetStatus = true)
	{
		if ($this->data) {
			if ($fields === null) {
				$fields = array_keys($this->data);
			}

			$relations = $record->relations();

			$fieldsRecord = [];
			$relationsValues = [];

			foreach ($fields as $field) {
				if (isset($relations[$field])) {
					$relationsValues[$field] = $record->{$field};
				} else {
					$fieldsRecord[] = $field;
				}
			}

			$this->applyChanges($record, $fields);

			if ($fieldsRecord && !$record->save(true, $fieldsRecord)) {
				return false;
			}

			foreach ($relationsValues as $field => $value) {
				$method = 'saveRelation' . ucfirst($field);
				if (method_exists($record, $method)) {
					$record->{$method}($record->{$field}, $value);
				}
			}

			$this->resetFields($fields);
		}

		if ($resetStatus) {
			$this->is_new = 0;
			$this->is_delete = 0;
		}

		return $this->data || $this->is_new || $this->is_delete ? $this->save() : $this->delete();
	}

	/**
	 * Изменить связанные элементы у записи
	 *
	 * @param \CActiveRecord $record
	 * @param string         $field
	 * @param mixed          $value
	 * @param array          $rel
	 *
	 * @return bool
	 */
	protected function applyRelationValue(\CActiveRecord $record, $field, $value, $rel)
	{
		$isChange = false;

		if (!empty($rel[0]) && !empty($rel[1])) {
			$class = $rel[1];

			switch ($rel[0]) {
				case \CActiveRecord::BELONGS_TO:
					$fieldRel = $rel[3];
					if ($record->{$fieldRel} != $value) {
						$isChange = true;
						$record->{$fieldRel} = $value;
						$record->{$field} = $class::model()->findByPk($value);
					}
					break;

				case \CActiveRecord::HAS_ONE:
					$oldValueObj = $record->{$field};
					$oldValue = $oldValueObj ? $oldValueObj->id : null;
					if ($oldValue != $value) {
						$isChange = true;
						$record->{$field} = $class::model()->findByPk($value);
					}
					break;

				case \CActiveRecord::HAS_MANY:
				case \CActiveRecord::MANY_MANY:
					$oldValueObj = $record->{$field};
					$newValueObj = is_array($value) ? $class::model()->findAllByPk($value) : [];
					$new = [];
					$old = [];
					foreach ($newValueObj as $item) {
						$new[] = $item->id;
					}
					if ($oldValueObj) {
						foreach ($oldValueObj as $item) {
							$old[] = $item->id;
						}
					}
					if (count($old) != count($new) || array_diff($old, $new)) {
						$isChange = true;
						$record->{$field} = $newValueObj;
					}
					break;
			}
		}

		return $isChange;
	}
}
