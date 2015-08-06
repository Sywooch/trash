<?php

namespace dfs\docdoc\components\seo;
use Yii;

class SeoFactory {

	/**
	 * Фабрика, создающая объект PageModel или SEO
	 *
	 * @param string $controller
	 * @param string $action
	 * @param string $url
	 * @param int $id_city
	 *
	 * @return SeoInterface
	 *
	 * @throws \Exception
	 */
	static public function getSeo($controller, $action, $url, $id_city = 0)
	{
		//если seo для страницы переопределено в БД
		$pageSeo = (new PageSeo())->findByUrl($url, $id_city);
		if ($pageSeo !== null) {
			return $pageSeo;
		}

		$seo_class = 'dfs\docdoc\components\seo\\' . Yii::app()->params['siteName'] . '\\' . strtolower($controller) . '\\' . ucfirst($action) . 'Seo';
		$default_class = 'dfs\docdoc\components\seo\\' . Yii::app()->params['siteName'] . '\\' . strtolower($controller) . '\DefaultSeo';

		if (class_exists($seo_class)) {
			return new $seo_class();
		}

		if (class_exists($default_class)) {
			return new $default_class();
		}

		throw new SeoException(Yii::app()->params['siteName'] . ". Не найден SEO-класс для {$controller}->{$action}. Url = {$url}");
	}

} 