<?php
use dfs\docdoc\models\DoctorClinicModel;

require_once dirname(__FILE__)."/../include/common.php";

$doctor = (isset($_GET['term'])) ? checkField($_GET['term'], "t", "") : '';
$clinic = (isset($_GET['clinicId'])) ? checkField($_GET['clinicId'], "i", 0) : 0;

$docArr = array();
$sqlAdd = ' 1 = 1 ';

if(!empty($doctor)){
    $sqlAdd .= " AND name LIKE '%".$doctor."%'";

    if($clinic > 0){
        $subSQL = "SELECT id FROM clinic WHERE id= ".$clinic." OR parent_clinic_id=".$clinic;
        $sqlAdd .= " AND t2.clinic_id IN (".$subSQL.")";
    }

    $sql = "SELECT id, name
            FROM doctor t1
            LEFT JOIN doctor_4_clinic t2 ON t2.doctor_id=t1.id and t2.type = " . DoctorClinicModel::TYPE_DOCTOR . "
            WHERE ".$sqlAdd."
            ORDER BY name";
    $result = query($sql);
    if (num_rows($result) > 0) {
        while ($row = fetch_object($result)) {
            $docArr[] = $row->name;
        }
    }
}

echo json_encode($docArr);

?>
