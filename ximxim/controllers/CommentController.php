<?php

namespace controllers;

use components\Controller;
use models\CommentModel;
use components\App;
use Exception;

/**
 * Class CommentController
 *
 * @package controllers
 */
class CommentController extends Controller
{

	/**
	 * View dir
	 *
	 * @var string
	 */
	protected $viewsDir = "comments";

	/**
	 * Add new comment
	 *
	 * Used of AJAX
	 * Displays JSON
	 *
	 * @return void
	 */
	public function actionAdd()
	{
		$model = new CommentModel();

		$status = false;
		$html = "";

		$post = App::getPost();
		if ($post) {
			$model->setAttributes($post);
			if ($model->save()) {
				$status = true;
				$html =
					$this->renderPartial(
						"/comments/_comment",
						["comment" => CommentModel::model()->byId($model->id)->find()],
						true
					);
			}
		}

		echo json_encode(["status" => $status, "html" => $html]);
		exit();
	}

	/**
	 * Delete comment
	 *
	 * @param int $id comment ID
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	public function actionDelete($id)
	{
		$model = CommentModel::model()->byId($id)->with(["newsModel"])->find();
		if (!$model) {
			throw new Exception("Model not found");
		}

		$model->delete();

		$this->redirect("/topic/{$model->newsModel->topic_id}/news/{$model->newsModel->id}/");
	}
}