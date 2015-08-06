<?php

namespace dfs\docdoc\api\feed;

/**
 * Class YmlFeed2
 *
 * @package dfs\docdoc\api\feed
 *
 *
 */
class YmlFeed2 extends YmlFeed
{
	const CATEGORY_CLINIC = 1000000;
	const CATEGORY_DOCTOR = 2000000;

	/**
	 * получение id для специальности
	 *
	 * @param int $id
	 *
	 * @return string
	 */
	public function getSectorId($id)
	{
		return 1000 + $id;
	}

	/**
	 * получение id для специальности
	 *
	 * @param int $id
	 *
	 * @return string
	 */
	public function getClinicId($id)
	{
		return self::CATEGORY_CLINIC + $id;
	}

	/**
	 * получение id для специальности
	 *
	 * @param array $params
	 *
	 * @return string
	 */
	public function getDoctorId(array $params)
	{
		return self::CATEGORY_DOCTOR + $params['doctor'];
	}

	public function getCategoryClinic()
	{
		return self::CATEGORY_CLINIC;
	}

	public function getCategoryDoctor()
	{
		return self::CATEGORY_DOCTOR;
	}
}
