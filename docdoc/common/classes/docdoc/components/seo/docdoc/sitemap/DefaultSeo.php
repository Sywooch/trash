<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 17.06.14
 * Time: 11:02
 */

namespace dfs\docdoc\components\seo\docdoc\sitemap;

use dfs\docdoc\components\seo\AbstractSeo;

class DefaultSeo extends AbstractSeo
{
	/**
	 * создание Seo-информации
	 */
	public function seoInfo()
	{
		$this->setTitle('Карта сайта - DocDoc.ru');
	}
}
