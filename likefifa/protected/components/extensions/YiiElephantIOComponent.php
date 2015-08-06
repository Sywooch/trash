<?php


namespace likefifa\components\extensions;

use CApplicationComponent;
use ElephantIO\Client;
use Yii;

/**
 * Класс для работы с elephant.io
 *
 * Class YiiElephantIOComponent
 *
 * @package likefifa\components\extensions
 */
class YiiElephantIOComponent extends CApplicationComponent
{
	/**
	 * @var string
	 */
	public $host;

	/**
	 * @var integer|string
	 */
	public $port;

	/**
	 * @var integer|string
	 */
	public $sslPort;

	/**
	 * @var int in miliseconds
	 */
	public $handshakeTimeout = 3000;

	public function init()
	{
		parent::init();
		include_once Yii::getPathOfAlias('application.vendors.wisembly') . DIRECTORY_SEPARATOR . implode(
				DIRECTORY_SEPARATOR,
				array(
					'elephant.io',
					'lib',
					'ElephantIO',
					'Client.php'
				)
			);
	}

	/**
	 * @param null $host
	 * @param null $port
	 *
	 * @return Client
	 */
	public function createClient($host = null, $port = null)
	{
		if (!isset($host)) {
			$host = $this->host;
		}

		if (!isset($port)) {
			$port = $this->port;
		}

		return new Client(
			sprintf('http://%s:%s', $host, $port),
			'socket.io',
			1,
			false,
			false
		);
	}

	/**
	 * @param string $event event name in current namespace
	 * @param mixed  $data  event data
	 * @param mixed  $data,...
	 *
	 */
	public function emit($event, $data)
	{
		$elephant = $this->createClient();
		$elephant->setHandshakeTimeout($this->handshakeTimeout);
		$elephant->init();
		$elephant->emit($event, $data);
		$elephant->close();
	}
}