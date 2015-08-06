<?php

use likefifa\components\helpers\ListHelper;
use likefifa\components\application\ActiveRegion;

class ArticleController extends FrontendController
{

	/**
	 * Рейтинг мастера, ниже которого мастер не выводится в статьях
	 */
	const MIN_RATING = 3;

	/**
	 * Минимальное количество мастеров
	 */
	const MIN_COUNT = 3;

	/**
	 * Получает и выводит каталог статей
	 *
	 * @param string $sectionRewriteName url-имя раздела
	 *
	 * @return void
	 */
	public function actionIndex($sectionRewriteName = '')
	{
		$criteria = new CDbCriteria;
		$criteria->order = "t.id DESC";

		$sections = LfSpecialization::model()->findAll();
		$section = false;
		if ($sectionRewriteName) {
			if ($sectionRewriteName != 'other') {
				$section = $sectionRewriteName ? $this->loadSection($sectionRewriteName) : null;
				$criteria->condition = "article_section_id = :article_section_id";
				$criteria->params = array(":article_section_id" => $section->id);
			} else {
				$section = 'other';
				$criteria->condition = "article_section_id IS NULL";
			}
		}

		$dataProvider = new CActiveDataProvider("Article", array(
			'criteria'   => $criteria,
			'pagination' => array(
				'pageSize' => 10,
				'pageVar'  => 'page',
			),
		));

		$this->setTitle('Статьи')->setMetaDescription('')->setMetaKeywords('');

		$allArticlesCount = count(Article::model()->findAll());

		$this->render(
			'index',
			compact(
				'dataProvider',
				'sections',
				'section',
				'allArticlesCount'
			)
		);
	}

	/**
	 * Показывает статью
	 *
	 * @param string $articleRewriteName абривиатура статьи
	 *
	 * @return void
	 */
	public function actionView($articleRewriteName)
	{
		$article = $this->loadArticle($articleRewriteName);

		$this->setTitle($article->title ? : $article->name)->setMetaDescription($article->meta_description)
			->setMetaKeywords($article->meta_keywords);

		$articles = Article::model()->findAll();

		shuffle($articles);
		if (count($articles) > 3) {
			$articles = array_slice($articles, 0, 3);
		}

		$masters = LfMaster::model()->rand()->findAll($this->_getCriteriaForMasters($article));
		if (count($masters) < self::MIN_COUNT) {
			$masters = LfMaster::model()->rand()->findAll($this->_getCriteriaForMasters($article, true));
		}

		shuffle($masters);
		if (count($masters) > 5) {
			$masters = array_slice($masters, 0, 5);
		}
		$criteria = array();
		if ($article->services) {
			$criteria['condition'] = 'service_id IN (' . ListHelper::buildIdList($article->services) . ')';
		} elseif ($article->section) {
			$criteria['condition'] = 'specialization_id = ' . $article->section->id;
		}
		$works = LfWork::model()->rand()->findAll($criteria);
		shuffle($works);
		if (count($works) > 5) {
			$works = array_slice($works, 0, 5);
		}

		$this->render(
			'view',
			compact(
				'article',
				'articles',
				'masters',
				'works'
			)
		);
	}

	/**
	 * Получает критерии для поиска мастеров к статье
	 *
	 * @param Article $article  модель статьи
	 * @param bool    $showMore показывать ли больше мастеров
	 *
	 * @return CDbCriteria
	 */
	private function _getCriteriaForMasters($article, $showMore = false)
	{
		$criteria = new CDbCriteria;

		$criteria->condition = "t.rating >= :rating";
		$criteria->params["rating"] = self::MIN_RATING;
		$criteria->addInCondition(
			"t.city_id",
			ListHelper::buildPropList("id", Yii::app()->activeRegion->getModel()->cities)
		);

		if ($article->services && !$showMore) {
			$criteria->with = array("services");
			$criteria->addInCondition("services.id", ListHelper::buildPropList("id", $article->services));
		} else {
			if ($article->section) {
				$criteria->condition .= " AND services.specialization_id = :specialization_id";
				$criteria->params["specialization_id"] = $article->section->id;
				$criteria->with = array("services");
			}
		}

		return $criteria;
	}

	protected function loadSection($rewriteName)
	{
		$model =
			is_numeric($rewriteName)
				? LfSpecialization::model()->findByPk($rewriteName)
				: LfSpecialization::model()->find('t.rewrite_name = :rewriteName', compact('rewriteName'));

		if ($model === null) {
			throw new CHttpException(404, 'Раздел не найден.');
		}

		return $model;
	}

	protected function loadArticle($rewriteName)
	{
		$model =
			is_numeric($rewriteName)
				? Article::model()->findByPk($rewriteName)
				: Article::model()->find('t.rewrite_name = :rewriteName', compact('rewriteName'));

		if ($model === null) {
			throw new CHttpException(404, 'Статья не найдена.');
		}

		if (!empty($model)) {
			if ($model->disabled) {
				throw new CHttpException(404, 'Статья не найдена.');
			}
		}

		return $model;
	}

}