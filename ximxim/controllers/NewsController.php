<?php

namespace controllers;

use components\Controller;
use models\CommentModel;
use models\NewsModel;
use Exception;
use models\TopicModel;
use components\App;

/**
 * Class NewsController
 *
 * @package controllers
 */
class NewsController extends Controller
{

	/**
	 * View dir
	 *
	 * @var string
	 */
	protected $viewsDir = "news";

	/**
	 * Displays news page
	 *
	 * @param integer $topicId topic ID
	 * @param integer $id      news ID
	 *
	 * @throws Exception
	 */
	public function actionView($topicId, $id)
	{
		$topicModel = TopicModel::model()->byId($topicId)->find();
		if (!$topicModel) {
			throw new Exception("Model not found");
		}

		$model = NewsModel::model()->byId($id)->find();
		if (!$model) {
			throw new Exception("Model not found");
		}

		$comments = CommentModel::model()->byNewsId($id)->ordered()->findAll();

		$this->render("view", ["model" => $model, "topicModel" => $topicModel, "comments" => $comments]);
	}

	/**
	 * Add news
	 *
	 * @param integer $topicId topic ID
	 *
	 * @return void
	 */
	public function actionAdd($topicId)
	{
		$model = new NewsModel();

		$post = App::getPost();
		if ($post) {
			$model->setAttributes($post);
			$model->topic_id = $topicId;
			if ($model->save()) {
				$this->redirect("/topic/{$topicId}/news/{$model->id}/");
			}
		}

		$this->render("forms", ["model" => $model, "title" => "Add news"]);
	}

	/**
	 * Edit news
	 *
	 * @param integer $topicId topic ID
	 * @param integer $id      news ID
	 *
	 * @throws Exception
	 */
	public function actionEdit($topicId, $id)
	{
		$model = NewsModel::model()->byId($id)->find();
		if (!$model) {
			throw new Exception("Model not found");
		}

		$post = App::getPost();
		if ($post) {
			$model->setAttributes($post);
			if ($model->save()) {
				$this->redirect("/topic/{$topicId}/news/{$model->id}/");
			}
		}

		$this->render("forms", ["model" => $model, "title" => "Edit news"]);
	}

	/**
	 * Delete news
	 *
	 * @param integer $topicId topic ID
	 * @param integer $id      news ID
	 *
	 * @throws Exception
	 */
	public function actionDelete($topicId, $id)
	{
		$model = NewsModel::model()->byId($id)->find();
		if (!$model) {
			throw new Exception("Model not found");
		}

		$model->delete();

		$this->redirect("/topic/{$topicId}/");
	}
}