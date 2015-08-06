<?php

class RemindBehavior extends CActiveRecordBehavior
{
	public function generatePassword()
	{
		$chars = '0123456789qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
		$len = rand(8, 12);
		$s = '';
		for ($i = 0; $i < $len; $i++) {
			$s .= $chars[rand(0, strlen($chars) - 1)];
		}

		return $s;
	}

	public function setPassword($password)
	{
		$this->owner->password = $password;
	}
}