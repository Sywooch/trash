<?php

namespace likefifa\components\system;

use CActiveRecord;

class ActiveRecord extends CActiveRecord
{
	/**
	 * Значения для комбиков Да/Нет
	 *
	 * @var array
	 */
	public static $yesNoList = [
		0 => 'Нет',
		1 => 'Да',
	];

	/**
	 * Возвращает коллекцию PK связи
	 *
	 * @param $relation
	 *
	 * @return int[] array
	 */
	public function getRelationIds($relation)
	{
		$ids = [];
		foreach ($this->$relation as $item) {
			if (is_object($item) || is_numeric($item)) {
				// if model attributes assigned through $model->attributes relation array will contains ids only
				$ids[] = is_object($item) ? $item->id : intval($item);
			}
		}

		return $ids;
	}

	/**
	 * Возвращает название модели без namespace
	 * @return string
	 */
	public function resolveClassName()
	{
		$class = get_class($this);
		if (strpos($class,'\\')!==false)
		{
			$namespace = explode('\\',$class);
			return $namespace[count($namespace)-1]; //get last element - model name
		}
		return $class;
	}
} 