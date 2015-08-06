<?php


namespace dfs\docdoc\components\seo;
use dfs\docdoc\models\PageModel;
use Yii;


class PageSeo  extends AbstractSeo {

	/**
	 * SEO - переметры из БД
	 *
	 * @var PageModel
	 */
	private $_pageModel = null;

	/**
	 * Поиск записи в БД с SEO параметрами страницы
	 *
	 * @param string $url
	 * @param int $id_city
	 *
	 * @return PageSeo|null
	 */
	public function findByUrl($url, $id_city)
	{
		$seoRecords = PageModel::model()->findAllByUrl(Yii::app()->params->siteId, $url);

		if (!count($seoRecords)) {
			return null;
		}

		//если есть seo для этого города
		if (isset($seoRecords[$id_city])) {
			$this->_pageModel = $seoRecords[$id_city];
			return $this;
		}

		//если есть seo для всех городов
		if (isset($seoRecords[0])) {
			$this->_pageModel = $seoRecords[0];
			return $this;
		}

		return null;
	}

	/**
	 * генерация SEO-информации
	 */
	public function seoInfo()
	{
		$this->setHead($this->_pageModel->h1);
		$this->setMetaDescription($this->_pageModel->description);
		$this->setMetaKeywords($this->_pageModel->keywords);
		$this->setTitle($this->_pageModel->title);

		if (!empty($this->_pageModel->seo_text_top)) {
			$this->setSeoText(SeoInterface::SEO_TEXT_TOP, $this->_pageModel->seo_text_top);
		}

		if (!empty($this->_pageModel->seo_text_bottom)) {
			$this->setSeoText(SeoInterface::SEO_TEXT_BOTTOM, $this->_pageModel->seo_text_bottom);
		}
	}

} 