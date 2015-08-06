<?php
namespace dfs\docdoc\extensions;

/**
 * Class WebApplication
 *
 * Нужен для переопределения error handler, чтобы в продакшете не останавливать выполнение
 * Используется в YiiAppRunner::create
 */
class WebApplication extends \CWebApplication
{
	use ErrorHandlerTrait;

	/**
	 * Переопределяем beforeControllerAction, чтобы назначить имя транзакции для NewRelic
	 *
	 * @param \CController $controller
	 * @param \CAction $action
	 *
	 * @return bool
	 */
	public function beforeControllerAction($controller, $action)
	{
		$route = 'yii/' . \Yii::app()->getParams()['appName'];
		$route = $route . '/' . $controller->id . '/' . $action->id;
		if (!\Yii::app()->params['skipNewRelicTransactionName']) {
			\Yii::app()->newRelic->nameTransaction($route);
		}
		return parent::beforeControllerAction($controller, $action);
	}
}
