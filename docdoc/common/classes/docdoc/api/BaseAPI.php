<?php

namespace dfs\docdoc\api;

use CException;

/**
 * Базовый класс API
 */
class BaseAPI
{
	public $params;
	public $method;
	public $dataType = 'json';
	public $jsonCyrillicMode = false;
	public $result = array();
	protected $_version = '1.0';
	public $log = 'api.log';

	/**
	 * Доступные методы
	 *
	 * @var array
	 */
	public $methods = [];

	public function __construct($params = array())
	{
		$this->params = $params;
		$this->dataType = $params['dataType'];

		if ($this->prepareData())
			return false;
		unset($this->params['dataType']);

		return true;
	}

	/**
	 * Получение методов
	 *
	 * @return array
	 */
	public function getMethods()
	{
		return $this->methods;
	}

	/**
	 * Запуск методов API
	 * @return string
	 */
	public function run()
	{
		//установка стратегии сортировки
		\Yii::app()->rating->setFromConfig();

		$this->callMethod();

		if ($this->dataType == 'json') {
			$data = self::convertToJSON($this->result, $this->jsonCyrillicMode);
			$data = str_replace('\/', '/', $data);
			$this->addLog($data);
		} else
			$data = self::convertToXML($this->result);


		return $data;
	}

	/**
	 * Конвертация из массива в JSON
	 *
	 * @param array $array
	 * @param boolean $cyrillic
	 *
	 * @return string
	 */
	static function convertToJSON($array, $cyrillic = false)
	{

		$str = json_encode($array);

		if ($cyrillic) {

			$arrReplaceUtf = array('\u0410', '\u0430', '\u0411', '\u0431', '\u0412', '\u0432',

				'\u0413', '\u0433', '\u0414', '\u0434', '\u0415', '\u0435', '\u0401', '\u0451', '\u0416',

				'\u0436', '\u0417', '\u0437', '\u0418', '\u0438', '\u0419', '\u0439', '\u041a', '\u043a',

				'\u041b', '\u043b', '\u041c', '\u043c', '\u041d', '\u043d', '\u041e', '\u043e', '\u041f',

				'\u043f', '\u0420', '\u0440', '\u0421', '\u0441', '\u0422', '\u0442', '\u0423', '\u0443',

				'\u0424', '\u0444', '\u0425', '\u0445', '\u0426', '\u0446', '\u0427', '\u0447', '\u0428',

				'\u0448', '\u0429', '\u0449', '\u042a', '\u044a', '\u042b', '\u044b', '\u042c', '\u044c',

				'\u042d', '\u044d', '\u042e', '\u044e', '\u042f', '\u044f');

			$arrReplaceCyr = array('А', 'а', 'Б', 'б', 'В', 'в', 'Г', 'г', 'Д', 'д', 'Е', 'е',

				'Ё', 'ё', 'Ж', 'ж', 'З', 'з', 'И', 'и', 'Й', 'й', 'К', 'к', 'Л', 'л', 'М', 'м', 'Н', 'н', 'О', 'о',

				'П', 'п', 'Р', 'р', 'С', 'с', 'Т', 'т', 'У', 'у', 'Ф', 'ф', 'Х', 'х', 'Ц', 'ц', 'Ч', 'ч', 'Ш', 'ш',

				'Щ', 'щ', 'Ъ', 'ъ', 'Ы', 'ы', 'Ь', 'ь', 'Э', 'э', 'Ю', 'ю', 'Я', 'я');

			$str = str_replace($arrReplaceUtf, $arrReplaceCyr, $str);

		}

		return $str;

	}

	static function utf8InWin($s)
	{
		$s = str_replace("'", '"', $s);
		$s = strtr($s, array(
			'\xd0\xb0' => "а", '\xd0\x90' => "А",
			'\xd0\xb1' => "б", '\xd0\x91' => "Б",
			'\xd0\xb2' => "в", '\xd0\x92' => "В",
			'\xd0\xb3' => "г", '\xd0\x93' => "Г",
			'\xd0\xb4' => "д", '\xd0\x94' => "Д",
			'\xd0\xb5' => "е", '\xd0\x95' => "Е",
			'\xd1\x91' => "ё", '\xd0\x81' => "Ё",
			'\xd0\xb6' => "ж", '\xd0\x96' => "Ж",
			'\xd0\xb7' => "з", '\xd0\x97' => "З",
			'\xd0\xb8' => "и", '\xd0\x98' => "И",
			'\xd0\xb9' => "й", '\xd0\x99' => "Й",
			'\xd0\xba' => "к", '\xd0\x9a' => "К",
			'\xd0\xbb' => "л", '\xd0\x9b' => "Л",
			'\xd0\xbc' => "м", '\xd0\x9c' => "М",
			'\xd0\xbd' => "н", '\xd0\x9d' => "Н",
			'\xd0\xbe' => "о", '\xd0\x9e' => "О",
			'\xd0\xbf' => "п", '\xd0\x9f' => "П",
			'\xd1\x80' => "р", '\xd0\xa0' => "Р",
			'\xd1\x81' => "с", '\xd0\xa1' => "С",
			'\xd1\x82' => "т", '\xd0\xa2' => "Т",
			'\xd1\x83' => "у", '\xd0\xa3' => "У",
			'\xd1\x84' => "ф", '\xd0\xa4' => "Ф",
			'\xd1\x85' => "х", '\xd0\xa5' => "Х",
			'\xd1\x86' => "ц", '\xd0\xa6' => "Ц",
			'\xd1\x87' => "ч", '\xd0\xa7' => "Ч",
			'\xd1\x88' => "ш", '\xd0\xa8' => "Ш",
			'\xd1\x89' => "щ", '\xd0\xa9' => "Щ",
			'\xd1\x8a' => "ъ", '\xd0\xaa' => "Ъ",
			'\xd1\x8b' => "ы", '\xd0\xab' => "Ы",
			'\xd1\x8c' => "ь", '\xd0\xac' => "Ь",
			'\xd1\x8d' => "э", '\xd0\xad' => "Э",
			'\xd1\x8e' => "ю", '\xd0\xae' => "Ю",
			'\xd1\x8f' => "я", '\xd0\xaf' => "Я",
		));

		return $s;
	}


	/**
	 * Конвертация из массива в XML
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	static function convertToXML($data)
	{
		$xml = '';

		if (count($data) > 0) {
			foreach ($data as $key => $val) {
				if (is_numeric($key)) {
					$xml .= "<Element>";
					self::convertToXML($val);
					$xml .= "</Element>";
				}
				if (is_array($val)) {
					$xml .= "<" . $key . ">";
					self::convertToXML($val);
					$xml .= "</" . $key . ">";
				} else {
					$xml .= "<" . $key . ">" . $val . "</" . $key . ">";
				}
			}

		}

		return $xml;

	}

	/**
	 * Служебная функция - очистка строки от cсимволов форматирования
	 * @param $content
	 * @param bool $withTags
	 *
	 * @return float|int|mixed|string
	 */
	static function clearText($content, $withTags = false)
	{

		$content = str_replace(array("\r", "\n"), ' ', $content);
		if (!$withTags) {
			$content = str_replace("&quot;", '', $content);
			$content = checkField($content, "t", '');
		}

		return $content;
	}

	/**
	 * Преобразование данных для последующей обработки
	 *
	 * @return bool
	 */
	protected function prepareData()
	{
		if ($this->dataType == 'json') {
			$this->params['rawData'] = self::utf8InWin($this->params['rawData']);
			$this->params['data'] = json_decode($this->params['rawData']);
		} else {
			$this->params['data'] = $this->params['rawData'];
		}

		unset($this->params['rawData']);

		return true;
	}

	/**
	 * Вызов метода API по его названию
	 *
	 * @return bool
	 */
	protected function callMethod()
	{
		try {
			$method = $this->getMethodName();
			$params = $this->getRequestParams();

			if (!method_exists($this, $method)) {
				throw new CException('Неправильная строка запроса');
			}
			$this->result = call_user_func(array('self', $method), $params);
		} catch (CException $e) {
			$this->result = $this->getError($e->getMessage());
		}

		return true;
	}

	/**
	 * Получение статуса и сообщения об ошибке
	 *
	 * @param $msg
	 *
	 * @return array
	 */
	protected function getError($msg)
	{
		$error = array('status' => 'error', 'message' => $msg);

		return $error;
	}

	/**
	 * Логгирование
	 *
	 * @param $msg
	 */
	protected function addLog($msg)
	{
		if (isset($_SERVER['REMOTE_ADDR'])) {
			$msg = '[' . $_SERVER['REMOTE_ADDR'] . '] ' . $this->method . ' - ' . $msg;
		}

		$request = '';
		$headers = $this->getRequestHeader();
		if (!empty($headers)) {
			$request .= '. Request - ';
			foreach ($headers as $header => $value) {
				$request .= "$header: $value \n";
			}

			$request .= file_get_contents("php://input");
		}

		new \commonLog($this->log, $msg . $request);
	}

	protected function getRequestHeader()
	{
		if (!function_exists('apache_request_headers')) {
			$arh = array();
			$rx_http = '/\AHTTP_/';
			foreach ($_SERVER as $key => $val) {
				if (preg_match($rx_http, $key)) {
					$arh_key = preg_replace($rx_http, '', $key);
					// do some nasty string manipulations to restore the original letter case
					// this should work in most cases
					$rx_matches = explode('_', $arh_key);
					if (count($rx_matches) > 0 and strlen($arh_key) > 2) {
						foreach ($rx_matches as $ak_key => $ak_val) $rx_matches[$ak_key] = ucfirst($ak_val);
						$arh_key = implode('-', $rx_matches);
					}
					$arh[$arh_key] = $val;
				}
			}
			return ($arh);
		}
	}

	/**
	 * Определяет вызываемый метод
	 *
	 * @return bool
	 */
	private function getMethodName()
	{
		$path = parse_url($this->params['query'], PHP_URL_PATH);

		foreach ($this->getMethods() as $alias => $method) {
			if (preg_match('~^/(' . $alias . ')(/)?(.*)?$~', $path)) {
				return $this->method = $method;
			}
		}

		return $this->method;
	}

	/**
	 * Получение параметров запроса
	 *
	 * @return array
	 *
	 * @throws \CException
	 */
	private function getRequestParams()
	{
		$params = [];

		$methodAlias = array_search($this->method, $this->getMethods());
		if (!$methodAlias) {
			throw new CException('Неправильная строка запроса');
		}
		$path = str_replace('/' . $methodAlias, '', parse_url($this->params['query'], PHP_URL_PATH));
		$path = trim($path, '/');

		// todo Нужно убрать костыль для методов просмотра врача/клиники
		if (in_array($this->method, ['doctorView', 'clinicView'])) {
			$params['id'] = $path;
		} elseif ($this->method == 'doctorByAlias') {
			$params['alias'] = $path;
		} elseif (!empty($path)) {
			$data = explode('/', $path);
			if (strpos($path, '/') && count($data) % 2 == 0) {
				$i = 0;
				while ($i < count($data)) {
					$params[$data[$i]] = $data[$i + 1];
					$i = $i + 2;
				}
			} else {
				throw new CException('Неправильная строка запроса');
			}
		}

		$query = parse_url($this->params['query'], PHP_URL_QUERY);
		if (!empty($query)) {
			parse_str($query, $params);
		}

		return $this->params = array_merge($this->params, $params);
	}

	/**
	 * Результат работы АПИ
	 * Без заголовков
	 *
	 * @return string
	 */
	public function getRowResult()
	{
		return self::run();
	}
}
