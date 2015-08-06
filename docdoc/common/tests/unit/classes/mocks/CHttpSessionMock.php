<?php

namespace dfs\tests\mocks;


/**
 * Class CHttpSessionMock
 */
class CHttpSessionMock extends \CHttpSession
{
	public $_SESSION = [];

	public function init() {
		if (isset($_SESSION)) {
			$this->_SESSION = $_SESSION;
		}
	}
	public function getUseCustomStorage() { return false; }
	public function open() {}
	public function close() {}
	public function destroy() {}
	public function getIsStarted() { return true; }
	public function getSessionID() { return 0; }
	public function setSessionID($value) {}
	public function regenerateID($deleteOldSession=false) {}
	public function getSessionName() { return ''; }
	public function setSessionName($value) {}
	public function getSavePath() { return ''; }
	public function setSavePath($value) {}
	public function getCookieParams() { return []; }
	public function setCookieParams($value) {}
	public function getCookieMode() { return 'allow'; }
	public function setCookieMode($value) {}
	public function getGCProbability() { return 1; }
	public function setGCProbability($value) {}
	public function getUseTransparentSessionID() { return false; }
	public function setUseTransparentSessionID($value) {}
	public function getTimeout() { return 0; }
	public function setTimeout($value) {}

	public function getIterator()
	{
		return new CHttpSessionIteratorMock($this);
	}

	public function getCount()
	{
		return count($this->_SESSION);
	}

	public function getKeys()
	{
		return array_keys($this->_SESSION);
	}

	public function get($key,$defaultValue=null)
	{
		return isset($this->_SESSION[$key]) ? $this->_SESSION[$key] : $defaultValue;
	}

	public function itemAt($key)
	{
		return isset($this->_SESSION[$key]) ? $this->_SESSION[$key] : null;
	}

	public function add($key,$value)
	{
		$this->_SESSION[$key]=$value;
	}

	public function remove($key)
	{
		if(isset($this->_SESSION[$key]))
		{
			$value=$this->_SESSION[$key];
			unset($this->_SESSION[$key]);
			return $value;
		}
		else
			return null;
	}

	public function clear()
	{
		foreach(array_keys($this->_SESSION) as $key)
			unset($this->_SESSION[$key]);
	}

	public function contains($key)
	{
		return isset($this->_SESSION[$key]);
	}

	public function toArray()
	{
		return $this->_SESSION;
	}

	public function offsetExists($offset)
	{
		return isset($this->_SESSION[$offset]);
	}

	public function offsetGet($offset)
	{
		return isset($this->_SESSION[$offset]) ? $this->_SESSION[$offset] : null;
	}

	public function offsetSet($offset,$item)
	{
		$this->_SESSION[$offset]=$item;
	}

	public function offsetUnset($offset)
	{
		unset($this->_SESSION[$offset]);
	}
}

/**
 * Class CHttpSessionIteratorMock
 */
class CHttpSessionIteratorMock implements \Iterator
{
	/**
	 * @var array list of keys in the map
	 */
	private $_keys;
	/**
	 * @var mixed current key
	 */
	private $_key;

	/**
	 * @var array
	 */
	private $_session = [];

	/**
	 * Constructor.
	 * @param array the data to be iterated through
	 */
	public function __construct($session)
	{
		$this->_session = $session->_SESSION;
		$this->_keys=array_keys($this->_session);
	}

	/**
	 * Rewinds internal array pointer.
	 * This method is required by the interface Iterator.
	 */
	public function rewind()
	{
		$this->_key=reset($this->_keys);
	}

	/**
	 * Returns the key of the current array element.
	 * This method is required by the interface Iterator.
	 * @return mixed the key of the current array element
	 */
	public function key()
	{
		return $this->_key;
	}

	/**
	 * Returns the current array element.
	 * This method is required by the interface Iterator.
	 * @return mixed the current array element
	 */
	public function current()
	{
		return isset($this->_session[$this->_key])?$this->_session[$this->_key]:null;
	}

	/**
	 * Moves the internal pointer to the next array element.
	 * This method is required by the interface Iterator.
	 */
	public function next()
	{
		do
		{
			$this->_key=next($this->_keys);
		}
		while(!isset($this->_session[$this->_key]) && $this->_key!==false);
	}

	/**
	 * Returns whether there is an element at current position.
	 * This method is required by the interface Iterator.
	 * @return boolean
	 */
	public function valid()
	{
		return $this->_key!==false;
	}
}
