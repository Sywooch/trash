<?php

namespace likefifa\components\helpers;

use CActiveRecord;

/**
 * Class ListHelper
 *
 * Класс для работы с коллекциями
 *
 * @package likefifa\components\helpers
 */
class ListHelper
{
	/**
	 * Возвращает коллекцию, состоящую из значений указанного атрибута
	 *
	 * @param string        $prop
	 * @param CActiveRecord $items
	 * @param bool          $unique
	 *
	 * @return array
	 */
	public static function buildPropList($prop, $items, $unique = true)
	{
		if (!$items || !is_array($items)) {
			return array();
		}

		$list = array();
		foreach ($items as $item) {
			$list[] = $item->$prop;
		}

		if ($unique) {
			$list = array_unique($list);
		}

		return $list;
	}

	/**
	 * Возвращает список идентификаторов
	 *
	 * @param CActiveRecord[] $items
	 * @param bool            $unique
	 *
	 * @return string
	 */
	public static function buildIdList($items, $unique = true)
	{
		return implode(',', self::buildPropList('id', $items, $unique));
	}

	/**
	 * Возвращает список имен
	 *
	 * @param CActiveRecord[] $items
	 * @param bool            $unique
	 *
	 * @return string
	 */
	public static function buildNameList($items, $unique = true)
	{
		return implode(', ', self::buildPropList('name', $items, $unique));
	}
} 