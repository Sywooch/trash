<?php
/**
 * Created by PhpStorm.
 * User: ndunaev
 * Date: 11.06.14
 * Time: 13:20
 */

namespace dfs\docdoc\components\seo\docdoc\index;

use dfs\docdoc\components\seo\AbstractSeo;


class DefaultSeo  extends AbstractSeo {

	public function seoInfo()
	{
		$this->setTitle('DocDoc - поиск врачей в ' . $this->params['city']['title_prepositional']);
		$this->setMetaKeywords("врач, врачи " . $this->params['city']['title_genitive'] . ", найти врача, поиск врачей");
		$this->setMetaDescription("DocDoc.ru – сервис по поиску врачей");
	}

} 