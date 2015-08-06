<?php

namespace likefifa\components\system;

use CDbCriteria;

/**
 * Class DbCriteria
 *
 * @package likefifa\components
 */
class DbCriteria extends CDbCriteria
{
	/**
	 * Adds a between condition to the {@link condition} property.
	 *
	 * @param string $column
	 * @param string $valueStart
	 * @param string $valueEnd
	 * @param string $operator
	 *
	 * @return $this|CDbCriteria
	 */
	public function addBetweenCondition($column, $valueStart, $valueEnd, $operator = 'AND')
	{
		if ($valueStart === '' && $valueEnd === '') {
			return $this;
		}

		$condition = '';

		$paramStart = self::PARAM_PREFIX . self::$paramCount++;
		$paramEnd = self::PARAM_PREFIX . self::$paramCount++;
		if (!empty($valueStart)) {
			$this->params[$paramStart] = $valueStart;
			$condition = "{$column} >= {$paramStart}";
		}
		if (!empty($valueEnd)) {
			$this->params[$paramEnd] = $valueEnd;
			$condition .= (empty($condition) ? '' : ' AND ') . "{$column} <= {$paramEnd}";
		}

		return $this->addCondition($condition, $operator);
	}
} 