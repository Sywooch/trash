<?php
abstract class ApiController extends Controller {
	
	protected $loggedMaster = null;
	protected $loggedSalon = null;

	/**
	 * Текущий зарегистрированный мастер из сессии
	 *
	 * @return LfMaster|null
	 */
	protected function getLoggedMaster()
	{
		if ($this->loggedMaster === null && Yii::app()->user->getState('masterLoggedInPublic')) {
			$this->loggedMaster = LfMaster::model()->findByPk(Yii::app()->user->getId());
		}

		return $this->loggedMaster;
	}

	/**
	 * Текущий зарегистрированный салон из сессии
	 *
	 * @return LfSalon|null
	 */
	protected function getLoggedSalon()
	{
		if ($this->loggedSalon === null && Yii::app()->user->getState('salonLoggedInPublic')) {
			$this->loggedSalon = LfSalon::model()->findByPk(Yii::app()->user->getId());
		}

		return $this->loggedSalon;
	}
	
	/**
	 * Переопределить обработчик ошибок, чтобы
	 * иметь возможность перехватывать их
	 * @return ApiController
	 */
	protected function overrideErrorHandler() {
		Yii::app()->setComponents(array(
				'errorHandler' => array(
						'errorAction' => '/'.Yii::app()->controller->id.'/error',
				),
		));
		
		return $this;
	}
	
	/**
	 * Отключить вывод лога на странице
	 * @return ApiController
	 */
	protected function disableWebLog() {
		if (Yii::app()->hasComponent('log')) {
			foreach (Yii::app()->log->getRoutes() as $route) {
				if ($route instanceof CWebLogRoute) $route->enabled = false;
			}
		}
		
		return $this;
	}
	
	/**
	 * Настроить параметры сессии и инициализировать её.
	 * @return #ApiController
	 */
	protected function setupSession() {
		if (!session_id()) {
			Yii::app()->setComponents(array('session' => $this->getSessionConfig()));
			Yii::app()->session->open();
		}
		
		return $this;
	}
	
	/**
	 * Метод, возвращающий конфиг компонента session.
	 */
	abstract protected function getSessionConfig();
	
	/**
	 * Метод, которому будет передаваться описание
	 * перехваченной через errorHandler ошибки
	 * @param int $code
	 * @param string $message
	 */
	abstract protected function handleError($code, $message);
	
	public function beforeAction($action) {
		$this->
			overrideErrorHandler()->
			disableWebLog()->
			setupSession();
		
		return parent::beforeAction($action);
	}
	
	public function actionError() {
		$this->handleError(
			Yii::app()->errorHandler->error['code'], 
			Yii::app()->errorHandler->error['message']
		);
	}
	
	public function missingAction($actionID) {
		$this->
			disableWebLog()->
			handleError(404, 'Type '.$actionID.' is missing');
	}
	
	public function actionIndex() {
		$this->handleError(404, 'Type is missing');
	}
	
}