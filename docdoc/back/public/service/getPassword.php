<?php
	require_once dirname(__FILE__) . "/../include/common.php";

	echo dfs\docdoc\helpers\PasswordHelper::generate(8);
