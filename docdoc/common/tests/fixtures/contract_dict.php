<?php

/**
 * Fixture для contract_dict
 *
 */

return [
	[
		"contract_id"  => 1,
		"title"        => "Оплата за дошедших (800/1200/1500)",
		"description"  => null,
		"isClinic"     => "yes",
		"isDiagnostic" => "no",
		"kind"         => 0
	],
	[
		"contract_id"  => 2,
		"title"        => "Оплата за записанных (600/1000)",
		"description"  => null,
		"isClinic"     => "yes",
		"isDiagnostic" => "no",
		"kind"         => 0
	],
	[
		"contract_id"  => 3,
		"title"        => "Оплата за звонки",
		"description"  => null,
		"isClinic"     => "no",
		"isDiagnostic" => "yes",
		"kind"         => 1
	],
	[
		"contract_id"  => 4,
		"title"        => "Оплата за запись на диагностику",
		"description"  => null,
		"isClinic"     => "no",
		"isDiagnostic" => "yes",
		"kind"         => 1
	],
	[
		"contract_id"  => 5,
		"title"        => "Плата за дошедших на диагностику",
		"description"  => null,
		"isClinic"     => "no",
		"isDiagnostic" => "yes",
		"kind"         => 1
	],
	[
		"contract_id"  => 6,
		"title"        => "Оплата за звонки на врачей по записи",
		"description"  => null,
		"isClinic"     => "yes",
		"isDiagnostic" => "no",
		"kind"         => 0
	],
	[
		"contract_id"  => 7,
		"title"        => "Диагностика. Онлайн-запись",
		"description"  => null,
		"isClinic"     => "no",
		"isDiagnostic" => "yes",
		"kind"         => 1
	]
];
