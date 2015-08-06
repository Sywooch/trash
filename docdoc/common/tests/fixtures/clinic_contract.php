<?php

/**
 * Fixture для contract_dict
 *
 */

return [
	[
		//Оплата за дошедших (800/1200/1500)
		"id"  => 1,
		"clinic_id"    => 1,
		"contract_id"  => 1,
	],
	[
		//Оплата за записанных (600/1000)
		"id"  => 2,
		"clinic_id"    => 2,
		"contract_id"  => 2,
	],
	[
		//диагностика Оплата за звонки
		"id"  => 3,
		"clinic_id"    => 1,
		"contract_id"  => 3,
	],
	[
		//Оплата за запись на диагностику
		"id"  => 4,
		"clinic_id"    => 2,
		"contract_id"  => 4,
	],
	[
		//Плата за дошедших на диагностику
		"id"  => 5,
		"clinic_id"    => 3,
		"contract_id"  => 5,
	],
	[
		//Диагностика. Онлайн-запись
		"id"  => 6,
		"clinic_id"    => 1,
		"contract_id"  => 7,
	],
	[
		//Диагностика. Онлайн-запись
		"id"  => 7,
		"clinic_id"    => 3,
		"contract_id"  => 7,
	]
];
