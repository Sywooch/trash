<?php

namespace components;

use models\TopicModel;

/**
 * Class Controller
 *
 * Component to inherit controllers
 * Contains a basic function for controllers
 *
 * @package components
 */
class Controller
{

	/**
	 * Layout name
	 *
	 * @var string
	 */
	protected $layout = "page";

	/**
	 * View dir
	 *
	 * @var string
	 */
	protected $viewsDir = "common";

	/**
	 * This method first calls renderPartial to render the view (called content view).
	 * It then renders the layout view which may embed the content view at appropriate place.
	 * In the layout view, the content view rendering result can be accessed via variable $content.
	 * At the end, it calls processOutput to insert scripts and dynamic contents if they are available.
	 *
	 * @param string $viewFile name of the view to be rendered
	 * @param array  $data     data to be extracted into PHP variables and made available to the view script
	 * @param bool   $isReturn whether the rendering result should be returned instead of being displayed to end users.
	 *
	 * @return void|string
	 */
	protected function render($viewFile, $data = [], $isReturn = false)
	{
		$path = $this->getViewsRootDir() . "layouts/{$this->layout}.php";

		$content = $this->renderPartial($viewFile, $data, true);

		if (!$isReturn) {
			require($path);
		}

		ob_start();
		ob_implicit_flush(false);
		require($path);
		return ob_get_clean();
	}

	/**
	 * This method differs from render() in that it does not apply a layout to the rendered result.
	 * It is thus mostly used in rendering a partial view, or an AJAX response.
	 *
	 * @param string $viewFile name of the view to be rendered
	 * @param array  $data     data to be extracted into PHP variables and made available to the view script
	 * @param bool   $isReturn whether the rendering result should be returned instead of being displayed to end users.
	 *
	 * @return void|string
	 */
	protected function renderPartial($viewFile, $data = [], $isReturn = false)
	{
		$path = $this->getViewsRootDir();
		if ($viewFile[0] !== "/") {
			$path .= $this->viewsDir . "/";
		} else {
			$viewFile = substr($viewFile, 1);
		}
		$path .= "{$viewFile}.php";

		extract($data, EXTR_OVERWRITE);

		if (!$isReturn) {
			require($path);
		}

		ob_start();
		ob_implicit_flush(false);
		require($path);
		return ob_get_clean();
	}

	/**
	 * Gets path of views dir
	 *
	 * @return string
	 */
	protected function getViewsRootDir()
	{
		return App::$config["rootDir"] . "/views/";
	}

	/**
	 * Gets HTML of topic tree
	 *
	 * @return string
	 */
	public function getTopicTree()
	{
		$list = [];
		$models = TopicModel::model()->orderByParentId()->findAll();

		foreach ($models as $model) {
			$list[$model->parent_id][] = [
				"id"    => $model->id,
				"title" => $model->title,
			];
		}

		return $this->createTopicTree($list);
	}

	/**
	 * Creates topic tree
	 *
	 * @param array $list     list of topics
	 * @param int   $parentId topic parent id
	 *
	 * @return string
	 */
	public function createTopicTree($list, $parentId = 0)
	{
		$html = "";
		if (!empty($list[$parentId])) {
			$html .= $this->renderPartial(
				"/topics/_topic_tree",
				[
					"items" => $list[$parentId],
					"list"  => $list
				],
				true
			);
		}

		return $html;
	}

	/**
	 * URL redirect
	 *
	 * @param string $url URL
	 *
	 * @return void
	 */
	public function redirect($url)
	{
		header("Location: {$url}");
		exit;
	}
}