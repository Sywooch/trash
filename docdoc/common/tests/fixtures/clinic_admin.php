<?php

return array(
	'enabled'=>array(
		'clinic_admin_id' =>1,
		'email' => 'enable@email.com',
		'fname' => 'Тест',
		'lname' => 'Тестов',
		'mname' => 'Тестович',
		'phone' => '',
		'cell_phone' => '',
		'passwd' => md5('qwerty'),
		'admin_comment' => '',
		'status' => 'enable',
	),
	'disabled'=>array(
		'clinic_admin_id' =>2,
		'email' => 'disable@email.com',
		'fname' => 'Неактив',
		'lname' => 'Неактивный',
		'mname' => 'Неактивнович',
		'phone' => '',
		'cell_phone' => '',
		'passwd' => md5('qwerty'),
		'admin_comment' => '',
		'status' => 'disable',
	),
);