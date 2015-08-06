<?php
namespace dfs\components;

use CController;
use Yii;

/**
 * Class Controller
 *
 * @package dfs\components
 */
class Controller extends CController
{
	public $layout = 'main';

	/**
	 * Получает идентификатор выбранной специальности
	 *
	 * @return int
	 */
	public function getActiveSpecId()
	{
		/**
		 * @var \SpecialityModel $sessionSpec
		 */
		$sessionSpec = Yii::app()->session["select_spec"];
		if (!$sessionSpec) {
			return 0;
		}

		return $sessionSpec->getId();
	}

	/**
	 * Получает идентификатор выбранного метро
	 *
	 * @return int
	 */
	public function getActiveMetroId()
	{
		/**
		 * @var \MetroModel $sessionMetro
		 */
		$sessionMetro = Yii::app()->session["select_metro"];
		if (!$sessionMetro) {
			return 0;
		}

		return $sessionMetro->getId();
	}

	/**
	 * Получает идентификатор выбранного района
	 *
	 * @return int
	 */
	public function getActiveDistrictId()
	{
		/**
		 * @var \dfs\models\DistrictModel $sessionDistrict
		 */
		$sessionDistrict = Yii::app()->session["selectDistrict"];
		if (!$sessionDistrict) {
			return 0;
		}

		return $sessionDistrict->getId();
	}
} 