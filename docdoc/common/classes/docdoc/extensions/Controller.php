<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 02.09.14
 * Time: 11:42
 */

namespace dfs\docdoc\extensions;

/**
 * Class Controller
 *
 * @package dfs\docdoc\extensions
 */
class Controller extends \CController
{
	/**
	 * Рендерит json
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	public function renderJson(array $data)
	{
		foreach (\Yii::app()->log->routes as $route) {

			if ($route instanceof \CWebLogRoute) {
				// disable any weblogroutes
				$route->enabled = false;
			}
		}

		header("Content-type: application/json; charset=utf-8");

		echo json_encode($data);

		\Yii::app()->end();

		return true;
	}

	/**
	 * Развилка на рендер json
	 *
	 * @param string $view
	 * @param null   $data
	 * @param bool   $return
	 *
	 * @return string|void
	 */
	public function render($view, $data = null, $return = false)
	{
		if(\Yii::app()->request->getIsAjaxRequest()){
			return $this->renderJson((array)$data);
		} else {
			return parent::render($view, $data, $return);
		}
	}
} 
