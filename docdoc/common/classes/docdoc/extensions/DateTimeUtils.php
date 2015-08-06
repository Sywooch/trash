<?php

namespace dfs\docdoc\extensions;

/**
 * Class DateTimeUtils
 *
 * @package dfs\docdoc\extensions
 */
class DateTimeUtils
{
	public static $monthGenitive = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
	public static $month = ['январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь'];

	/**
	 * Человеческий вывод интервала времени
	 *
	 * @param string $time время h:m:s
	 *
	 * @return int
	 */
	public static function timeToSec($time)
	{
		$tm = explode(':', $time);

		switch (count($tm)) {
			case 1: return intval($tm[0]) * 3600;
			case 2: return intval($tm[0]) * 3600 + intval($tm[1]) * 60;
			case 3: return intval($tm[0]) * 3600 + intval($tm[1]) * 60 + intval($tm[2]);
		}

		return 0;
	}

	/**
	 * Дата в обычном формате
	 *
	 * @param int $time
	 *
	 * @return string
	 */
	public static function timeToDate($time)
	{
		return date('d', $time) . ' ' . self::$monthGenitive[date('n', $time) - 1] . ' ' . date('Y', $time);
	}
}