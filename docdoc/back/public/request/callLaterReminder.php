<?php
require_once dirname(__FILE__) . "/../include/header.php";

$user = new user();
$user->checkRight4page(array('ADM', 'OPR', 'SOP'));
$userId = $user->idUser;

$typeView = isset($_GET['type']) ? $_GET['type'] : 'default';

pageHeader(dirname(__FILE__) . "/xsl/callLaterReminder.xsl", "noHead");

$xmlString = '<srvInfo>';
$xmlString .= "<TypeView>{$typeView}</TypeView>";
$xmlString .= $user->getUserXML();
$xmlString .= getCityXML();
$xmlString .= '</srvInfo>';
setXML($xmlString);


$xmlString = '<dbInfo>';

$sql = " SELECT
			t1.req_id as id, t1.call_later_time,
			t1.req_user_id as owner, t2.user_lname, t2.user_fname
		 FROM
		    request t1
		 LEFT JOIN `user` t2 ON (t2.user_id = t1.req_user_id)
		 WHERE
		    t1.req_status = 7
		    AND
		    t1.call_later_time is not null
		    AND
		    t1.call_later_time < ( UNIX_TIMESTAMP(NOW()) + 300)
		 ORDER BY t1.call_later_time ASC
		 LIMIT 3";
$result = query($sql);
if (num_rows($result) > 0) {
	$xmlString .= "<RequestList>";
	while ($row = fetch_object($result)) {
		$xmlString .= "<Element>";
		$xmlString .= "<Id>" . $row->id . "</Id>";
		$xmlString .= "<CallLaterDate>" . date("d.m.y", $row->call_later_time) . "</CallLaterDate>";
		$xmlString .= "<CallLaterTime>" . date("H:i", $row->call_later_time) . "</CallLaterTime>";
		$xmlString .= "<RemainTime>" . $row->call_later_time . "</RemainTime>";
		$xmlString .= "<Owner id=\"" . $row->owner . "\">" . $row->user_lname . " " . $row->user_fname . "</Owner>";
		$xmlString .= "</Element>";
	}
	$xmlString .= "</RequestList>";
}
$xmlString .= '</dbInfo>';
setXML($xmlString);


pageFooter("noHead");
