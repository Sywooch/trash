<?php
/**
 * Created by PhpStorm.
 * User: ndunaev
 * Date: 04.06.14
 * Time: 20:25
 */

namespace dfs\docdoc\api;


class JsonRpcException extends \Exception
{
	const PARSE_ERROR = -32700;
	const INVALID_REQUEST = -32600;
	const METHOD_NOT_FOUND = -32601;
	const INVALID_PARAMS = -32602;
	const INTERNAL_ERROR = -32603;

	/**
	 * @var mixed
	 */
	private $data = null;

	/**
	 * @param string $message
	 * @param int    $code
	 * @param null   $data
	 */
	public function __construct($message, $code, $data = null)
	{
		$this->data = $data;
		parent::__construct($message, $code);
	}

	/**
	 * Получение массива информации по ошибке
	 *
	 * @return array
	 */
	public function getErrorAsArray()
	{
		$result = array(
			'code'    => $this->getCode(),
			'message' => $this->getMessage(),
		);
		if ($this->data !== null) {
			$result['data'] = $this->data;
		}
		return $result;
	}

	/**
	 * @return string
	 */
	public function getErrorMessage()
	{
		if($this->data){
			$msg = "{$this->data} ({$this->message})";
		} else {
			$msg = $this->message;
		}

		return $msg;
	}
}
