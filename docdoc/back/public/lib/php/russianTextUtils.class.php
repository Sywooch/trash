<?php

class RussianTextUtils
{

	protected static $replacesLower = array(
		'а' => 'a',
		'б' => 'b',
		'в' => 'v',
		'г' => 'g',
		'д' => 'd',
		'е' => 'e',
		'ё' => 'e',
		'ж' => 'zh',
		'з' => 'z',
		'и' => 'i',
		'й' => 'j',
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
		'щ' => 'sh',
		'ъ' => '',
		'ы' => 'i',
		'ь' => '',
		'э' => 'e',
		'ю' => 'ju',
		'я' => 'ya',
		' ' => '_'
	);

	protected static $replaces = array();

	public static function caseForNumber($number, $words)
	{
		// $words = array('штука', 'штуки', 'штук');
		$number = $number % 100;

		if ($number >= 11 && $number <= 14) return $words[2];

		$number = $number % 10;
		if ($number === 1) {
			return $words[0];
		} elseif ($number > 1 && $number < 5) {
			return $words[1];
		} else {
			return $words[2];
		}
	}

	/**
	 * Метод транслитирации строки c удалением лишних символов
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	public static function translit($str)
	{
		$str = trim(mb_strtolower($str));
		if (self::$replaces === array()) {
			$replacesUpper = array();
			foreach (self::$replacesLower as $rus => $lat) {
				$replacesUpper[mb_strtoupper($rus)] = ucfirst($lat);
			}

			self::$replaces = array_merge(self::$replacesLower, $replacesUpper);
			self::$replacesLower = null;
		}

		$str = str_replace(
			array_keys(self::$replaces),
			array_values(self::$replaces),
			$str
		);

		return preg_replace('/[^_a-z0-9]/', "", $str);
	}

	public static function wordInNominative($word, $many = false)
	{
		return self::parseWords($word, $many, 'wordNominative');
	}

	public static function wordInGenitive($word, $many = false)
	{
		return self::parseWords($word, $many, 'wordGenitive');
	}

	public static function wordInDative($word, $many = false)
	{
		return self::parseWords($word, $many, 'wordDative');
	}

	public static function wordInPrepositional($word, $many = false)
	{
		return self::parseWords($word, $many, 'wordPrepositional');
	}

	static function replaceEnding($word, $endings)
	{
		foreach ($endings as $endingFrom => $endingTo) {
			if (preg_match('/' . $endingFrom . '$/u', $word)) {
				return preg_replace('/' . $endingFrom . '$/u', $endingTo, $word);
			}
		}
	}

	static function wordNominative($word, $many = false)
	{
		if (!$many) return $word;

		return self::replaceEnding($word, array(
			'р' => 'ры',
			'г' => 'ги',
			'т' => 'ты',
			'д' => 'ды',
			'ий' => 'ие',
			'ый' => 'ые',
			'эко' => 'эко',
			'узи' => 'узи'
		));
	}

	static function wordGenitive($word, $many = false)
	{
		if (!$many) {
			return self::replaceEnding($word, array(
				'кт' => 'кт',
				'а' => 'ы',
				'р' => 'ра',
				'г' => 'га',
				'т' => 'та',
				'д' => 'да',
				'ий' => 'ого',
				'ый' => 'ого',
				'ое' => 'ого',
				'ие' => 'ия',
				'ия' => 'ии',
				'ая' => 'ой',
				'н' => 'н',
				'з' => 'за',
				'ень' => 'ня',
				'ь' => 'и',
				'ит' => 'ита',
				'ки' => 'ек',
				'ой' => 'ого',
				'ы' => 'ы',
				'эко' => 'эко',
				'узи' => 'узи',
				'ния' => 'нии',
				'во' => 'ва',
				'ж' => 'жа',
				'ые' => 'ых',
				'ни' => 'ней',
			));
		} else {
			return self::replaceEnding($word, array(
				'ие' => 'их',
				'ые' => 'ых',
				'цы' => 'ц',
				'ения' => 'ений',
				'ва' => 'ва',
				'ии' => 'ии',
				'ки' => 'к',
				'и' => 'и',
				'жа' => 'жа',
				'ой' => 'ой',
				'р' => 'ров',
				'г' => 'гов',
				'т' => 'тов',
				'д' => 'дов',
				'ий' => 'их',
				'ый' => 'ых',
				'эко' => 'эко',
				'узи' => 'узи',
				'ния' => 'нии',
				'ы' => 'ов',
			));
		}
	}

	static function wordDative($word, $many = false)
	{
		if (!$many) {
			return self::replaceEnding($word, array(
				'кт' => 'кт',
				'р' => 'ру',
				'з' => 'зу',
				'г' => 'гу',
				'т' => 'ту',
				'д' => 'ду',
				'ий' => 'ому',
				'ый' => 'ому',
				'эко' => 'эко',
				'узи' => 'узи'
			));
		} else {
			return self::replaceEnding($word, array(
				'р' => 'рам',
				'г' => 'гам',
				'т' => 'там',
				'д' => 'дам',
				'ий' => 'им',
				'ый' => 'ым',
				'эко' => 'эко',
				'узи' => 'узи'
			));
		}
	}

	static function wordPrepositional($word, $many = false)
	{
		if (!$many) {
			return self::replaceEnding($word, array(
				'а' => 'е',
				'г' => 'ге',
				'т' => 'т',
				"уг" => "уге",
				"ый" => "ом",
				"о" => "о",
			));
		} else {
			return self::replaceEnding($word, array());
		}
	}

	static function parseWords($words, $many, $callback)
	{
		return preg_replace_callback(
			'/([a-zа-яё]+)/u',
			function ($matches) use ($callback, $many) {
				return call_user_func("self::{$callback}", $matches[1], $many);
			},
			$words
		);
	}

}