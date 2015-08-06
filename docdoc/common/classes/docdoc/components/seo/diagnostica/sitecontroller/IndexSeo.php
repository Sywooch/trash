<?php

namespace dfs\docdoc\components\seo\diagnostica\sitecontroller;

use dfs\docdoc\components\seo\AbstractSeo;

/**
 * Сео для главной страницы диагностических центров
 *
 * Class IndexSeo
 * @package dfs\docdoc\components\seo\diagnostica\sitecontroller
 */
class IndexSeo extends AbstractSeo
{

	/**
	 *
	 */
	public function seoInfo()
	{
		$city = $this->params['city'];
		$this->setTitle("Диагностика DocDoc - поиск диагностических центров {$city['title_genitive']}");
		$this->setMetaDescription("Здесь Вы сможете найти клинику, которая делает диагностические исследования в вашем районе. Сделать диагностику в {$city['title_prepositional']} с DocDoc.ru - теперь просто!");
	}

}