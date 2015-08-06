<?php

namespace dfs\docdoc\extensions;

/**
 * Class TextUtils
 * @package dfs\docdoc\models
 */
class TextUtils
{
	/**
	 * @var array
	 */
	static private $translit = array(
		'а' => 'a',
		'б' => 'b',
		'в' => 'v',
		'г' => 'g',
		'д' => 'd',
		'е' => 'e',
		'ё' => 'yo',
		'ж' => 'zh',
		'з' => 'z',
		'и' => 'i',
		'й' => 'y',
		'к' => 'k',
		'л' => 'l',
		'м' => 'm',
		'н' => 'n',
		'о' => 'o',
		'п' => 'p',
		'р' => 'r',
		'с' => 's',
		'т' => 't',
		'у' => 'u',
		'ф' => 'f',
		'х' => 'h',
		'ц' => 'ts',
		'ч' => 'ch',
		'ш' => 'sh',
		'щ' => 'sht',
		'ъ' => 'a',
		'ы' => 'y',
		'ь' => '',
		'э' => 'e',
		'ю' => 'yu',
		'я' => 'ya',
		'А' => 'A',
		'Б' => 'B',
		'В' => 'V',
		'Г' => 'G',
		'Д' => 'D',
		'Е' => 'E',
		'Ё' => 'YO',
		'Ж' => 'Zh',
		'З' => 'Z',
		'И' => 'I',
		'Й' => 'Y',
		'К' => 'K',
		'Л' => 'L',
		'М' => 'M',
		'Н' => 'N',
		'О' => 'O',
		'П' => 'P',
		'Р' => 'R',
		'С' => 'S',
		'Т' => 'T',
		'У' => 'U',
		'Ф' => 'F',
		'Х' => 'H',
		'Ц' => 'Ts',
		'Ч' => 'Ch',
		'Ш' => 'Sh',
		'Щ' => 'Sht',
		'Ъ' => 'A',
		'ы' => 'Y',
		'Ь' => '',
		'Э' => 'E',
		'Ю' => 'Yu',
		'Я' => 'Ya',
	);

	/**
	 * Метод для замены окончания
	 *
	 * @param string $word
	 * @param array $endings
	 * @return mixed
	 */
	public static function replaceEnding($word, $endings)
	{
		foreach ($endings as $endingFrom => $endingTo) {
			if (preg_match('/' . $endingFrom . '$/u', $word)) {
				return preg_replace('/' . $endingFrom . '$/u', $endingTo, $word);
			}
		}
	}

	/**
	 * Парсинг слов
	 *
	 * @param string $words
	 * @param bool $many
	 * @param string $callback
	 * @return mixed
	 */
	public static function parseWords($words, $many, $callback)
	{
		return preg_replace_callback(
			'/([a-zа-яё]+)/u',
			function ($matches) use ($callback, $many) {
				return call_user_func("self::{$callback}", $matches[1], $many);
			},
			$words
		);
	}

	/**
	 * Слово в предложном падеже
	 *
	 * @param string $word
	 * @return mixed
	 */
	public static function wordPrepositional($word)
	{
		return self::replaceEnding($word, array(
			"уг" => "уге",
			"ый" => "ом",
			"о" => "о",
		));
	}

	/**
	 * Удаление из названия всех символов кроме букв и цифр
	 *
	 * @param string $name
	 * @param string $replace
	 * @param array | null $clearWords
	 *
	 * @return string
	 */
	public static function reductionName($name, $replace = '', $clearWords = null)
	{
		$text = mb_strtolower($name);

		if ($clearWords !== null) {
			foreach ($clearWords as $word) {
				$text = preg_replace('/' . $word . '([^a-яё\w\d]|$)/', ' ', $text);
			}
		}

		return preg_replace('/([^А-Яa-яЁё\w\d])/i', $replace, $text);
	}


	/**
	 * Получение транслитерации названия для использования в url
	 *
	 * @param string $text
	 *
	 * @return string
	 */
	public static function rewriteName($text)
	{
		return mb_strtolower(
			trim(
				preg_replace(
					'/([^\w\d-])+/i',
					'_',
					str_replace(array_keys(self::$translit), self::$translit, $text)
				),
				'_'
			)
		);
	}

	/**
	 * Формы слов в зависимости от количества
	 *
	 * $words = array('штука', 'штуки', 'штук');
	 *
	 * @param int $number
	 * @param String[] $words array('штука', 'штуки', 'штук');
	 *
	 * @return string
	 */
	public static function caseForNumber($number, $words) {

		$number = $number % 100;

		if ($number >= 11 && $number <= 14) return $words[2];

		$number = $number % 10;
		if ($number === 1) {
			return $words[0];
		}
		elseif ($number > 1 && $number < 5) {
			return $words[1];
		}
		else {
			return $words[2];
		}
	}

	/**
	 * Человеческий вывод интервала времени
	 *
	 * @param int $time время в секундах
	 *
	 * @return string
	 */
	public static function timePeriod($time)
	{
		if ($time < 60) {
			return "$time " . self::caseForNumber($time, [ 'секунда', 'секунды', 'секунд' ]);
		}

		$time = intval($time / 60);
		if ($time < 60) {
			return "$time " . self::caseForNumber($time, [ 'минута', 'минуты', 'минут' ]);
		}

		$time = intval($time / 60);
		if ($time < 60) {
			return "$time " . self::caseForNumber($time, [ 'час', 'часа', 'часов' ]);
		}

		$time = intval($time / 24);
		if ($time < 24) {
			return "$time " . self::caseForNumber($time, [ 'день', 'дня', 'дней' ]);
		}
	}

	/**
	 * Уникальное название для значений при построении sql-запросов
	 *
	 * @return string
	 */
	public static function getUniqueValueName()
	{
		static $number = 1;

		return 'value' . $number++;
	}

	/**
	 * Форматирование рейтинга
	 *
	 * @param mixed $value
	 *
	 * @return string
	 */
	public static function ratingFormat($value)
	{
		$m = intval($value);
		$s = 0;
		if ($m > 10) {
			$m = 10;
		} else {
			$s = intval(($value - $m) * 10);
		}

		return [
			'main' => $m,
			'sub' => $s,
		];
	}

	/**
	 * Форматировать записи по алфавиту и разбить по колонкам
	 *
	 * @param array $records
	 * @param int $countColumns
	 *
	 * @return array
	 */
	public static function formatItemsByAlphabet($records, $countColumns = 0)
	{
		$groups = [];

		foreach ($records as $record) {
			$letter = mb_strtoupper(mb_substr($record->name, 0, 1, 'utf-8'), 'utf-8');
			$groups[$letter][] = $record;
		}

		if (!$countColumns) {
			return $groups;
		}

		$countInColumns = floor((count($groups) + count($records)) / $countColumns);
		$columns = [];

		$c = 0;
		$n = 0;
		foreach ($groups as $letter => $items) {
			if ($n > $countInColumns) {
				$c++;
				$n = 0;
			}
			$columns[$c][$letter] = $items;
			$n += count($items) + 1;
		}

		return $columns;
	}

	/**
	 * Обрезка длинной строки
	 *
	 * @param string $str
	 * @param array $params
	 *
	 * @return string
	 */
	public static function cutString($str, $params = [])
	{
		return (isset($params['maxCharacters']) && mb_strlen($str) > $params['maxCharacters'])
			? mb_substr($str, 0, $params['maxCharacters'] - 1) . '...'
			: $str;
	}
}