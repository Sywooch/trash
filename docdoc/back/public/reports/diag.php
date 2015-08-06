<?php
require_once dirname(__FILE__) . "/../include/header.php";

define('CVS_SEPARATOR', ';');
define('MONTHS', '3');

$user = new user();
$user->checkRight4page(array('ADM', 'CNM', 'SCM'));

$result = query(
	"
		SELECT
			t1.id,
			t1.name,
			cd.title as contract
		FROM clinic t1
			LEFT JOIN diagnostica_settings ds ON ds.settings_id = t1.diag_settings_id
			LEFT JOIN contract_dict cd ON cd.contract_id = ds.contract_id
		WHERE t1.isDiagnostic='yes'
		ORDER BY t1.name
	"
);

$clinics = array();
foreach (fetch_all($result) as $c) {
	$clinics[$c['id']] = $c;
}

$clinicIds = join(array_keys($clinics), ',');
$monthTitles = array();
$now = new DateTime('now');
for ($i = 0; $i < MONTHS; $i++) {
	$thisMonthStart = new DateTime($now->format('Y-m-01'));
	$thisMonthStart->sub(new DateInterval(sprintf('P%sM', $i)));
	$thisMonthEnd = clone($thisMonthStart);
	$thisMonthEnd->add(new DateInterval('P1M'));
	$result = query(
		$q = "
		SELECT
			t1.id,
			COUNT(dr.req_id) as 'Calls',
			SUM(IF(dr.req_status = 0, 1, 0)) 'New',
			SUM(IF(dr.req_status = 1, 1, 0)) 'Process',
			SUM(IF(dr.req_status = 2, 1, 0)) 'Record',
			SUM(IF(dr.req_status = 3, 1, 0)) 'Go',
			SUM(IF(dr.req_status = 5, 1, 0)) 'Cancel',
			SUM(IF(dr.req_status = 5 AND dr.date_admission IS NOT NULL, 1, 0)) 'Cancel_with_data'
		FROM clinic t1
			LEFT JOIN request dr ON dr.clinic_id=t1.id
		WHERE
			t1.id IN ({$clinicIds})
			AND dr.req_created BETWEEN {$thisMonthStart->getTimestamp()} AND {$thisMonthEnd->getTimestamp()}
			AND kind=" . DocRequest::KIND_DIAGNOSTICS . "
		GROUP BY t1.id
	"
	);

	$monthTitles[$i] = array(
		'Calls'            => "{$thisMonthStart->format('M Y')} Звонки",
		'New'              => "{$thisMonthStart->format('M Y')} Новых",
		'Process'          => "{$thisMonthStart->format('M Y')} В обработке",
		'Record'           => "{$thisMonthStart->format('M Y')} Записанных",
		'Go'               => "{$thisMonthStart->format('M Y')} Дошедших",
		'Cancel'           => "{$thisMonthStart->format('M Y')} Отказ",
		'Cancel_with_data' => "{$thisMonthStart->format('M Y')} Отказ с датой приема",
	);

	foreach (fetch_all($result) as $row) {
		$clinics[$row['id']]['month'][$i] = $row;
	}
}
ksort($monthTitles, SORT_DESC);

// Titles
$head = array(
	'Название',
	'Контракт',
);
foreach ($monthTitles as $m) {
	foreach ($m as $title) {
		$head[] = $title;
	}
}

// heads
header('Content-type: text/csv');
header('Content-disposition: attachment;filename=diagnostica.csv');

// output

echo join($head, CVS_SEPARATOR);
echo "\n";

foreach ($clinics as $clinic) {
	if (!isset($clinic['name'])) {
		break;
	}

	$row = array(
		$clinic['name'],
		isset($clinic['contract']) ? $clinic['contract'] : 'неизвестно',
	);

	foreach ($monthTitles as $i => $m) {
		foreach ($m as $name => $title) {
			$row[] = isset($clinic['month'][$i][$name]) ? $clinic['month'][$i][$name] : 0;
		}
	}

	echo join($row, CVS_SEPARATOR);
	echo "\n";
}