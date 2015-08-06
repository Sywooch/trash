<?php

/**
 * Fixture для clinic_contract_cost
 *
 */

return [
	//ко всем 0
	[
		"id"  => 1,
		"service_id"   => null,
		"cost"         => 500,
		"clinic_contract_id"     => 1,
		"from_num" => 0,
		'group_uid' => 1, //все специальности
	],
	//ко всем, начиная с 3
	[
		"id"  => 2,
		"service_id"   => null,
		"cost"         => 700,
		"clinic_contract_id"     => 1,
		"from_num" => 3,
		'group_uid' => 1,
	],
	//к окулисту от 0
	[
		"id"  => 3,
		"service_id"   => 84,
		"cost"         => 600,
		"clinic_contract_id"     => 1,
		"from_num" => 0,
		'group_uid' => 3,
	],
	//к окулисту от 2
	[
		"id"  => 4,
		"service_id"   => 84,
		"cost"         => 800,
		"clinic_contract_id"     => 1,
		"from_num" => 2,
		'group_uid' => 3,
	],
	//на всю диагностику от 0
	[
		"id"  => 5,
		"service_id"   => null,
		"cost"         => 150,
		"clinic_contract_id"     => 6,
		"from_num" => 0,
		'group_uid' => 2,
	],
	//на всю диагностику от 2
	[
		"id"  => 6,
		"service_id"   => null,
		"cost"         => 200,
		"clinic_contract_id"     => 6,
		"from_num" => 2,
		'group_uid' => 2,
	],
	//на КТ/МРТ от 0
	[
		"id"  => 7,
		"service_id"   => 11,
		"cost"         => 250,
		"clinic_contract_id"     => 6,
		"from_num" => 0,
		'group_uid' => 4,
	],
	//на КТ/МРТ от 0
	[
		"id"  => 8,
		"service_id"   => 11,
		"cost"         => 300,
		"clinic_contract_id"     => 6,
		"from_num" => 2,
		'group_uid' => 4,
	],
	//на КТ/МРТ от 0
	[
		"id"  => 15,
		"service_id"   => 11,
		"cost"         => 400,
		"clinic_contract_id"     => 6,
		"from_num" => 3,
		'group_uid' => 4,
	],
	//на звонки за диагностику
	[
		"id"  => 9,
		"service_id"   => null,
		"cost"         => 50,
		"clinic_contract_id"     => 3,
		"from_num" => 0,
		'group_uid' => 2,
	],
	//запись ко врачам
	[
		"id"  => 10,
		"service_id"   => null,
		"cost"         => 400,
		"clinic_contract_id"     => 2,
		"from_num" => 0,
		'group_uid' => 1,
	],
	//на uzi от 0
	[
		"id"  => 12,
		"service_id"   => 1,
		"cost"         => 123,
		"clinic_contract_id"     => 6,
		"from_num" => 0,
		'group_uid' => 6,
	],
	//на всю диагностику от 0
	[
		"id"  => 20,
		"service_id"   => null,
		"cost"         => 150,
		"clinic_contract_id"     => 5,
		"from_num" => 0,
		'group_uid' => 2,
	],
	//на всю диагностику от 2
	[
		"id"  => 21,
		"service_id"   => null,
		"cost"         => 200,
		"clinic_contract_id"     => 5,
		"from_num" => 2,
		'group_uid' => 2,
	],
	//на КТ/МРТ от 0
	[
		"id"  => 22,
		"service_id"   => 11,
		"cost"         => 250,
		"clinic_contract_id"     => 5,
		"from_num" => 0,
		'group_uid' => 4,
	],
	//на КТ/МРТ от 0
	[
		"id"  => 23,
		"service_id"   => 11,
		"cost"         => 300,
		"clinic_contract_id"     => 5,
		"from_num" => 2,
		'group_uid' => 4,
	],
	//на КТ/МРТ от 0
	[
		"id"  => 24,
		"service_id"   => 11,
		"cost"         => 400,
		"clinic_contract_id"     => 5,
		"from_num" => 3,
		'group_uid' => 4,
	],
];
