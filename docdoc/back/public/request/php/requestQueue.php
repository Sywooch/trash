<?php
function getQueueList () {
	$xml = "";

	$sql = "SELECT
				t1.SIP,
				DATE_FORMAT( t1.startTime,'%d.%m.%Y %H:%i') AS startTime,
				t1.user_id,
				t1.asteriskPool,
				concat(t2.user_fname, ' ', t2.user_lname) as userName,
				t2.user_login
			FROM `queue` t1
			LEFT JOIN `user` t2 ON (t1.user_id = t2.user_id)
			ORDER BY t1.SIP";
	//echo $sql."<br/>";
	$result = query($sql);
	if (num_rows($result) > 0 ) {
		$xml .= "<Queue>";
		while ($row = fetch_object($result)) {
			$xml .= "<Element sip=\"".$row -> SIP."\">";
			$xml .= "<User id=\"".$row ->user_id."\" login=\"".$row ->user_login."\" >".$row -> userName."</User>";
			$xml .= "<StartTime>".$row -> startTime."</StartTime>";
			$xml .= "</Element>";
		}
		$xml .= "</Queue>";
	}


	return $xml;
}
