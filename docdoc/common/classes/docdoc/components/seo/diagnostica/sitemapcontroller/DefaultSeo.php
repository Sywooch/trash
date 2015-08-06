<?php
/**
 * Created by PhpStorm.
 * User: ndunaev
 * Date: 11.06.14
 * Time: 12:25
 */

namespace dfs\docdoc\components\seo\diagnostica\sitemapcontroller;

use dfs\docdoc\components\seo\AbstractSeo;

class DefaultSeo extends AbstractSeo
{
	/**
	 * Генерация seo-информации
	 */
	public function seoInfo()
	{
		$this->setTitle('Карта сайта');
		$this->setHead('Карта сайта');
	}


}