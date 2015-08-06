<?php

/**
 * Мобильное API
 *
 * Class Api_1_0Controller
 */
class Api_1_0Controller extends ApiController
{

	/**
	 * Отправляем результат работы клиенту
	 *
	 * @param mixed  $data
	 * @param int    $code
	 * @param string $type
	 * @param array  $headers
	 */
	protected function sendResponse($data, $code = 200, $type = 'application/json', $headers = array())
	{
		if (!headers_sent()) {
			header('HTTP/1.1 ' . ($code ?: 200));
			if ($type) {
				header('Content-Type: ' . $type);
			}
			if ($headers) {
				foreach ($headers as $headerName => $headerValue) {
					header($headerName . ': ' . $headerValue);
				}
			}
			if ($this->getLoggedMaster()) {
				header('Session: ' . session_id());
			}
		}

		if (is_object($data) && method_exists($data, 'toArray')) {
			$data = $data->toArray();
		}

		// fix to php 5.3
		$options = defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0;
		echo json_encode($data, $options);
	}

	protected function handleError($code, $message)
	{
		$this->sendResponse(array('error' => $message), $code);
	}

	protected function getSessionConfig()
	{
		return array(
			'cookieMode'              => 'none',
			'sessionName'             => 'session',
			'useTransparentSessionID' => true,
		);
	}

	protected function checkAuthorization()
	{
		if (!$this->getLoggedMaster()) {
			throw new CHttpException(403, 'You must be logged in');
		}
		return $this;
	}

	protected function checkRequest($allowedTypes)
	{
		if (is_string($allowedTypes)) {
			$allowedTypes = array($allowedTypes);
		}
		if (!in_array($_SERVER['REQUEST_METHOD'], $allowedTypes)) {
			throw new CHttpException('Must be ' . implode(', ', $allowedTypes) . ' request');
		}
		return $this;
	}

	protected function validate($model)
	{
		if (!$model->validate()) {
			throw new CHttpException(401, implode(', ', array_keys($model->getErrors())));
		}

		return true;
	}

	protected function runSubaction($object = null)
	{
		$indexMethod = $this->action->id . 'Index';
		if ($object === null) {
			$this->$indexMethod();
		} elseif (is_numeric($object)) {
			$this->$indexMethod((int)$object);
		} elseif (($method = $this->action->id . su::ucfirst($object)) && method_exists($this, $method)) {
			$this->$method();
		} else {
			throw new CHttpException(404, 'No such object ' . $object);
		}
	}

	protected function masterAuth()
	{
		$this->checkRequest('POST');

		$form = new MasterLoginForm;
		$form->attributes = $_POST;

		if ($form->validate() && $form->login() && ($master = $this->getLoggedMaster())) {
			$token = !empty($_POST['token']) ? $_POST['token'] : null;
			$master->applyNotificationToken($token);
			$this->sendResponse($master->toArray());
		} else {
			throw new CHttpException(403, 'Wrong email or password');
		}
	}

	protected function masterLogout()
	{
		if ($this->getLoggedMaster()) {
			Yii::app()->user->logout();
		}
		$this->sendResponse('OK');
	}

	protected function masterIndex()
	{
		$this->checkRequest(array('GET', 'POST'))->checkAuthorization();

		$master = $this->getLoggedMaster();

		switch ($_SERVER['REQUEST_METHOD']) {
			case 'POST':
				$master->scenario = 'API1.0Index';
				$master->attributes = $_POST;
				if ($this->validate($master)) {
					$master->save();
				}
				break;
		}

		$this->sendResponse($master->toArray());
	}

	protected function masterAvatar()
	{
		$this->checkRequest('POST')->checkAuthorization();

		$master = $this->getLoggedMaster();

		$master->scenario = 'API1.0Avatar';
		$master->uploadFile('photo', 'data');
		if ($this->validate($master)) {
			$master->save();
		}

		$this->sendResponse($master->toArray());
	}

	public function actionMaster($object = null)
	{
		$this->runSubaction($object);
	}

	public function actionServices($object = null)
	{
		$this->checkRequest('GET')->checkAuthorization();

		$master = $this->getLoggedMaster();

		$this->sendResponse(LfSpecialization::model()->getFullTree($object === 'all' ? null : $master));
	}

	protected function worksIndex($id = null)
	{
		$this->checkRequest(array('GET', 'POST'))->checkAuthorization();

		$master = $this->getLoggedMaster();

		if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
			if ($id) {
				$work = LfWork::model()->find('id = ? AND master_id = ?', array($id, $master->id));
				if (!$work) {
					throw new CHttpException('Work ' . $id . ' not found');
				}
				$work->scenario = 'update';
			} else {
				$work = new LfWork;
				$work->master_id = $master->id;
				$work->scenario = 'create';
			}
		}

		switch ($_SERVER['REQUEST_METHOD']) {
			case 'PUT':
			case 'POST':
				$work->attributes = $_POST;
				$work->uploadFile('image', 'data');
				$this->validate($work);
				$work->save();
				break;

			case 'DELETE':
				$work->delete();
				break;
		}

		$master = $master->toArray();
		$this->sendResponse($master['works']);
	}

	public function actionWorks($object = null)
	{
		$this->runSubaction($object);
	}

	/**
	 * Конструктор DateTime (и strtotime) для даты, отформатированной по RFC822 определяет пояс MSK как +0300 вместо +0400
	 * При этом, если в DateTime передавать часовой пояс отдельно, всё определяется нормально.
	 *
	 * @param string $date
	 */
	protected function parseRFC822Date($date)
	{
		$dateParts = date_parse($date);
		if (isset($dateParts['tz_abbr'])) {
			$dt = new DateTime($date, new DateTimeZone(timezone_name_from_abbr($dateParts['tz_abbr'])));
		} else {
			$dt = new DateTime($date);
		}

		return (int)$dt->format('U');
	}

	/**
	 * Главная страница мастера
	 *
	 * @param int $id идентификатор мастера
	 *
	 * @throws CHttpException
	 */
	protected function reqsIndex($id = null)
	{
		$this->checkRequest(array('GET', 'POST'))->checkAuthorization();

		$master = $this->getLoggedMaster();
		if ($master == null) {
			$logMessage = "MASTER NOT FOUND.\r\nAction: reqs; id: " . $id . ';
			GET: ' . serialize($_GET) . ';
			POST: ' . serialize($_POST) . ';
			COOKIE: ' . serialize($_COOKIE);
			Yii::log($logMessage, 'warning', 'system.web.ApiController');
			throw new CHttpException(403, 'You must be logged in');
		}

		switch ($_SERVER['REQUEST_METHOD']) {
			case 'POST':
				$appointment = LfAppointment::model()->find(
					'id = :id AND master_id = :master_id',
					array(
						':id'        => $id,
						':master_id' => $master->id
					)
				);
				if (!$appointment) {
					throw new CHttpException('Request ' . $id . ' not found');
				}

				$appointment->scenario = 'api';
				$appointment->attributes = $_POST;
				$this->validate($appointment);
				$appointment->touch()->save();
				break;
		}

		$since =
			!empty($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $this->parseRFC822Date($_SERVER['HTTP_IF_MODIFIED_SINCE'])
				: null;

		$appointments = array();
		foreach ($master->appointments as $appointment) {
			if (!$since || $appointment->numericCreated >= $since || $appointment->numericChanged >= $since ||
				$appointment->id == $id
			) {
				$appointments[] = $appointment->toArray();
			}
		}

		header_remove('Expires');
		$this->sendResponse(
			$appointments,
			count($appointments) ? 200 : 304,
			'application/json',
			array(
				'Last-Modified' => gmdate('D, d M Y H:i:s') . ' GMT',
				//'Cache-Control' => 'public',
			)
		);
	}

	public function actionReqs($object = null)
	{
		$this->runSubaction($object);
	}

}