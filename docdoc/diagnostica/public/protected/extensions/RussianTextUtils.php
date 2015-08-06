<?php

class RussianTextUtils {
	
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
	);
	
	protected static $replaces = array();
	
	public static function caseForNumber($number, $words) {
		// $words = array('штука', 'штуки', 'штук');
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
	
	public function fileSize($size) {
		$units = array(
			1024 * 1024 * 1024		=> 'Гб',
			1024 * 1024 			=> 'Мб',
			1024 					=> 'кб',
			0						=> 'б',
		);
		
		$result = null;
		foreach ($units as $minValue => $unitName) {
			if (abs($size) >= $minValue) {
				$result = number_format($size / ($minValue ?: 1), 2).' '.$unitName;
				break;
			}
		}
		
		return $result;
	}
	
	public static function translit($str) {
		if (self::$replaces === array()) {
			$replacesUpper = array();
			foreach (self::$replacesLower as $rus => $lat) {
				$replacesUpper[mb_strtoupper($rus)] = ucfirst($lat);	
			}
		
			self::$replaces = array_merge(self::$replacesLower, $replacesUpper);
			self::$replacesLower = null;
		}
		
		return
			str_replace(
				array_keys(self::$replaces),
				array_values(self::$replaces),
				$str
			);
	}
	
}