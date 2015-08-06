<?php
require_once dirname(__FILE__)."/../include/common.php";

$spec = (isset($_GET['term'])) ? checkField($_GET['term'], "t", "") : '';

$specArr = array();
$sqlAdd = ' 1 = 1 ';

if(!empty($spec)){
    $sqlAdd .= " AND name LIKE '%".$spec."%'";
}

$sql = "SELECT id, name
        FROM sector
        WHERE ".$sqlAdd."
        ORDER BY name";
$result = query($sql);
if (num_rows($result) > 0) {
    while ($row = fetch_object($result)) {
        $specArr[] = $row->name;
    }
}

echo json_encode($specArr);

?>