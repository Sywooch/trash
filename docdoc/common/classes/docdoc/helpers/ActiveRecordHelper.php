<?php

namespace dfs\docdoc\helpers;


/**
 * Class ActiveRecordHelper
 *
 * @package dfs\docdoc\helpers
 */
class ActiveRecordHelper
{
	/**
	 * Вычислить расхождение между новыми и старыми элементами
	 *
	 * @param \CActiveRecord[] $new
	 * @param \CActiveRecord[] $old
	 *
	 * @return array
	 */
	static public function arrayRecordsDiff($new, $old)
	{
		$add = [];
		$delete = [];

		if (is_array($new)) {
			foreach ($new as $item) {
				$add[$item->id] = $item;
			}
		}

		if (is_array($old)) {
			foreach ($old as $item) {
				$id = $item->id;
				if (isset($add[$id])) {
					unset($add[$id]);
				} else {
					$delete[$id] = $item;
				}
			}
		}

		return [ 'add' => $add, 'delete' => $delete ];
	}

	/**
	 * Сгруппировать записи по полю и разбить по колонкам
	 *
	 * @param array $records
	 * @param string $field
	 * @param int $countColumns
	 * @param int $correction
	 *
	 * @return array
	 */
	static public function groupItemsByField($records, $field, $countColumns = 0, $correction = 0)
	{
		$groups = [];

		foreach ($records as $record) {
			$groups[$record->{$field}][] = $record;
		}

		if (!$countColumns) {
			return $groups;
		}

		$countInColumns = floor((count($groups) + count($records)) / $countColumns - $correction);
		$columns = [];

		$c = 0;
		$n = 0;
		foreach ($groups as $group => $items) {
			if ($n > $countInColumns && $c < ($countColumns - 1)) {
				$c++;
				$n = 0;
			}
			$columns[$c][$group] = $items;
			$n += count($items) + 1;
		}

		return $columns;
	}
} 
