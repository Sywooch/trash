<?php

require_once dirname(__FILE__)."/../../include/common.php";
require_once __DIR__ . "/../../lib/php/models/DocRequest.php";

define('CLINIC_CONTRACT', 6);

$month = date('n');
$year = date('Y');
$timeStart = mktime(0, 0, 0, $month, 1, $year);
$timeEnd = mktime(23, 59, 59, $month + 1, 0, $year);

/**
 * @todo В апреле нужно будет убрать условие по t0.clinic_id=t1.id !!!
 */
$sql = "SELECT
            t1.id,
            (
                SELECT COUNT(DISTINCT(t0.req_id))
                FROM request_record t
                INNER JOIN request t0 ON t0.req_id=t.request_id
                LEFT JOIN clinic t3 ON (t3.id=t0.clinic_id)
                WHERE
                    (
                        EXISTS (
                            SELECT record_id
                            FROM request_record
                            WHERE
                                request_id=t0.req_id
                                AND clinic_id=t1.id
                                AND NOT (isAppointment='no' OR clinic_id=t3.parent_clinic_id)
                        )
                        OR t0.clinic_id=t1.id
                    )
                    AND t.clinic_id=t1.id
                    AND t0.req_created BETWEEN {$timeStart} AND {$timeEnd}
                    AND t0.req_status NOT IN (" . implode(',', DocRequest::getExcludeStatuses()) . ")
            ) AS calls,
            (
                SELECT COUNT(*)
                FROM request_record t
                WHERE t.clinic_id=t1.id
                    AND t.year={$year}
                    AND t.month={$month}
                    AND t.isAppointment='yes'
            ) AS appointments
        FROM clinic t1
        INNER JOIN clinic_settings t2 ON t2.settings_id=t1.settings_id
        WHERE t2.contract_id=" . CLINIC_CONTRACT;
$result = query($sql);
while ($row = fetch_array($result)) {
	$ctr = $row['calls'] ? round($row['appointments'] / $row['calls'], 2) : 0;
	$sql = "INSERT INTO ctr SET
                 value={$ctr}, clinic_id={$row['id']}, year={$year}, month={$month}
            ON DUPLICATE KEY UPDATE value={$ctr}";
	query($sql);
}
