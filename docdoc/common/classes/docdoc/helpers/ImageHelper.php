<?php
/**
 * Created by PhpStorm.
 * User: atyutyunnikov
 * Date: 20.03.15
 * Time: 20:04
 */

namespace dfs\docdoc\helpers;

/**
 * Class ImageHelper
 *
 * @package dfs\docdoc\helpers
 */
class ImageHelper
{
	/**
	 * Добавить на картинку логотип докдок
	 *
	 * @param string $imageName полный путь к картинке
	 * @param string $position  слева или справа разместить логотип
	 */
	public static function addDocDocLogo($imageName, $position = 'left')
	{
		$logoImage = \Yii::app()->params['path']['upload'] . '/logoSq.gif';

		$x = $position == 'right' ? 90 : 0;

		$watermarkResource = imagecreatefromgif($logoImage);
		$imageResource = imagecreatefromjpeg($imageName);

		imagecopymerge($imageResource, $watermarkResource, $x, 178,  0, 0, 70, 25, 70);
		imagejpeg($imageResource, $imageName, 90);
	}
}