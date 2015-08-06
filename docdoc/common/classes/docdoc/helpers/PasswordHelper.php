<?php

namespace dfs\docdoc\helpers;


/**
 * Класс для работы с паролями
 *
 * @package dfs\docdoc\helpers
 */
class PasswordHelper
{
	public static function generate($length)
	{
		$array = [];
		$friendlychars = '01234567890aAbBcCdDeEfFgGhHiIjJkKlLmMnNoOpPqQrRsStTuUvVwWxXyYzZ';

		srand((double) microtime() * 1000000);

		for ($i=0; $i < 63; $i++) {
			$array[$i] = $friendlychars[$i];
		}

		$pass = '';
		for ($i=0; $i < $length; $i++) {
			$pass .= $array[rand(0, count($array) - 1)];
		}

		return $pass;
	}
} 
