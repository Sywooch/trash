<?php

namespace controllers;

use components\App;
use components\Controller;
use models\NewsModel;
use models\TopicModel;
use Exception;

/**
 * Class TopicController
 *
 * @package controllers
 */
class TopicController extends Controller
{

	/**
	 * View dir
	 *
	 * @var string
	 */
	protected $viewsDir = "topics";

	/**
	 * Displays topic page
	 *
	 * @param integer $id topic ID
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	public function actionView($id)
	{
		$model = TopicModel::model()->byId($id)->find();
		if (!$model) {
			throw new Exception("Model not found");
		}

		$newsList = NewsModel::model()->byTopicId($model->id)->findAll();

		$this->render("view", ["model" => $model, "newsList" => $newsList]);
	}

	/**
	 * Add new topic
	 *
	 * @param int $parentId parent ID of topic
	 *
	 * @return void
	 */
	public function actionAdd($parentId = 0)
	{
		$model = new TopicModel();

		$post = App::getPost();
		if ($post) {
			$model->setAttributes($post);
			$model->parent_id = $parentId;
			if ($model->save()) {
				$this->redirect("/topic/{$model->id}/");
			}
		}

		$this->render("forms", ["model" => $model, "title" => "Add topic"]);
	}

	/**
	 * Edit topic
	 *
	 * @param integer $id topic ID
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	public function actionEdit($id)
	{
		$model = TopicModel::model()->byId($id)->find();
		if (!$model) {
			throw new Exception("Model not found");
		}

		$post = App::getPost();
		if ($post) {
			$model->setAttributes($post);
			if ($model->save()) {
				$this->redirect("/topic/{$model->id}/");
			}
		}

		$this->render("forms", ["model" => $model, "title" => "Edit topic"]);
	}

	/**
	 * Delete topic
	 *
	 * @param integer $id topic ID
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	public function actionDelete($id)
	{
		$model = TopicModel::model()->byId($id)->find();
		if (!$model) {
			throw new Exception("Model not found");
		}

		$url = $model->parent_id ? "/topic/{$model->parent_id}/" : "/";

		$model->delete();

		$this->redirect($url);
	}
}