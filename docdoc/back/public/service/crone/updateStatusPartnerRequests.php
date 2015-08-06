<?php
use dfs\docdoc\objects\Rejection;

require_once dirname(__FILE__) . "/../../include/common.php";
require_once __DIR__ . "/../../lib/php/models/DocRequest.php";


echo "\r\n";
echo "- Start update statuses for request of partners";
echo "\r\n";

$statusNew = 0;
$statusRegistered = 1;
$statusDeclined = 2;

// Обновление статусов для заявок после 01.02.2014
$dateStart = 1391198400;

// Изменение статусов партнерских заявок на "Принята"
$sql = "SELECT DISTINCT t1.request_id AS id
        FROM request_partner t1
        INNER JOIN request t2 ON t2.req_id=t1.request_id
        WHERE
            t1.external_status={$statusNew}
            AND t2.req_created>{$dateStart}
            AND t2.req_status IN (
                " . DocRequest::STATUS_RECORD . ",
                " . DocRequest::STATUS_CAME . ",
                " . DocRequest::STATUS_REJECT . ",
                " . DocRequest::STATUS_REJECT_BY_PARTNER . "
            )";
$result = query($sql);

if (num_rows($result) > 0) {
	$ids = array();
	while ($row = fetch_object($result))
		$ids[] = $row->id;
	$ids = implode(',', $ids);
	$sql = "UPDATE request_partner SET external_status={$statusRegistered} WHERE request_id IN ($ids)";
	$result = query($sql);
	if ($result) {
		echo "-- update status to 'Accepted' for requests: $ids";
		echo "\r\n";
	}
}

// Изменение статусов партнерских заявок на "Новая"
$sql = "SELECT DISTINCT t1.request_id AS id
        FROM request_partner t1
        INNER JOIN request t2 ON t2.req_id=t1.request_id
        WHERE
            t1.external_status={$statusRegistered}
            AND t2.req_created>{$dateStart}
            AND t2.req_status IN (
                " . DocRequest::STATUS_PROCESS . ",
                " . DocRequest::STATUS_CALL_LATER . "
            )";
$result = query($sql);

if (num_rows($result) > 0) {
	$ids = array();
	while ($row = fetch_object($result))
		$ids[] = $row->id;
	$ids = implode(',', $ids);
	$sql = "UPDATE request_partner SET external_status={$statusNew} WHERE request_id IN ($ids)";
	$result = query($sql);
	if ($result) {
		echo "-- update status to 'New' for requests: $ids";
		echo "\r\n";
	}
}

// Изменение статусов партнерских заявок на "Отклонена"
$sql = "SELECT DISTINCT t1.request_id AS id
        FROM request_partner t1
        INNER JOIN request t2 ON t2.req_id=t1.request_id
        WHERE
            t2.req_created>{$dateStart}
            AND
            (
                t2.req_status = " . DocRequest::STATUS_REMOVED . "
                OR
                (
	                t2.req_status = " . DocRequest::STATUS_REJECT . "
	                AND
	                t2.reject_reason IN (" . implode(',', Rejection::getReasonsForDecline()) . ")
                )
            )";
$result = query($sql);

if (num_rows($result) > 0) {
	$ids = array();
	while ($row = fetch_object($result))
		$ids[] = $row->id;
	$ids = implode(',', $ids);
	$sql = "UPDATE request_partner SET external_status={$statusDeclined} WHERE request_id IN ($ids)";
	$result = query($sql);
	if ($result) {
		echo "-- update status to 'Rejected' for requests: $ids";
		echo "\r\n";
	}
}

echo "- End update";
