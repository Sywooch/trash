<?php
use dfs\docdoc\models\ClinicBillingModel;

/**
 * Fixture для contract_dict
 *
 */

return [
	[
		'id'                 => 1,
		'clinic_id'          => 1,
		'billing_date'       => '2015-01-01',
		'clinic_contract_id' => 1,
		'status'             => ClinicBillingModel::STATUS_OPEN,
		'start_sum'          => 1000,
		'start_requests'     => 5,
		'agreed_sum'         => 1000,
		'agreed_requests'    => 5,
		'recieved_sum'       => 0,
	],
	[
		'id'                 => 2,
		'clinic_id'          => 1,
		'billing_date'       => '2015-02-01',
		'clinic_contract_id' => 1,
		'status'             => ClinicBillingModel::STATUS_OPEN,
		'start_sum'          => 1000,
		'start_requests'     => 5,
		'agreed_sum'         => 1000,
		'agreed_requests'    => 5,
		'recieved_sum'       => 500,
	]
];
