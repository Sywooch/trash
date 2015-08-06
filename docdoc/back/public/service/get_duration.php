<?php
set_time_limit(300);

require_once dirname(__FILE__) . "/../include/common.php";
require_once dirname(__FILE__) . "/../lib/php/audioServiceFunctions.php";
require_once dirname(__FILE__) . "/../lib/php/dateTimeLib.php";

$path = \Yii::app()->params['phone_providers']['download_dir'] . "/diagnostica/";

$sql = "
		SELECT 
			t1.diag_req_id,
			t1.record_id,
			t1.file_name
		FROM diag_request_record t1
		WHERE
			t1.duration = 0
			OR
			t1.duration IS NULL";

$result = query($sql);
while ($row = fetch_object($result)) {

	$filename = $row->file_name;
	$parceArray = explode("_", $filename);
	$dateArray = explode("-", $parceArray[0]);

	$dir = $path . "etc/";
	if (count($dateArray) == 3) {
		$dir = $path . $dateArray[0] . "/" . $dateArray[1] . "/" . $dateArray[2] . "/";
	}
	$filename = $dir . $filename;

	$file = null;

	$duration = getDuration($filename);
	$sql2 = "update diag_request_record set duration = '" . $duration . "' where record_id = " . $row->record_id;
	$result2 = query($sql2);
	echo $row->diag_req_id . " " . $row->record_id . " " . fromFormatedTimeIntoSec($duration) . "<br>";

}
