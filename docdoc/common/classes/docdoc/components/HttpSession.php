<?php

namespace dfs\docdoc\components;


class HttpSession extends \CHttpSession
{
	public function open()
	{
		parent::open();

		// Костыль, для того чтобы не протухала сессия
		$this->add('time', time());
	}
}
