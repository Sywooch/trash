<?php
use dfs\docdoc\models\DoctorClinicModel;

function getDoctorCountByClinicId ($clinicId) {
    $sqlAdd = '1=1';
    
    if( !empty($clinicId) )  {
        if ($clinicId >= 0  ) {
            $subSQL = "SELECT id FROM clinic WHERE id= ".$clinicId." OR parent_clinic_id=".$clinicId;
            $sqlAdd .= " AND t2.clinic_id IN (".$subSQL.") ";
        } else
            $sqlAdd .= " AND t2.clinic_id is null " ;
    }
    
    $sql = "SELECT
                COUNT(t1.id) AS cnt
            FROM doctor  t1
            LEFT JOIN doctor_4_clinic t2 ON (t1.id = t2.doctor_id and t2.type = " . DoctorClinicModel::TYPE_DOCTOR . ")
            WHERE ".$sqlAdd;
    //echo $sql;
    $result = query($sql);
    $item = fetch_object($result);
    
    return $item->cnt;
}

function cityListXML () {
    $xml = "";

    $sql = "SELECT id_city, title, rewrite_name
            FROM city";
    $result = query($sql);

	$cityId = Yii::app()->city->getCityId();

    $xml  = "<CityList>";
    while ($row = fetch_object($result)){
        if($row->id_city == $cityId)
            $selected = 1;
        else
            $selected = 0;
        $xml .= "<Element id='".$row->id_city."' selected='".$selected."'>".$row->title."</Element>";
    }
    $xml .= "</CityList>";

    return $xml;
}

function getCityInfo ($id) {
    $sql = "SELECT id_city, title, rewrite_name
            FROM city
            WHERE id_city=$id";
    $result = query($sql);
    $city = fetch_object($result);

    return $city;
}

function getPeopleCount () {
    $coeff = 2.5;
    $sql = "SELECT COUNT(*) AS cnt
            FROM request
            WHERE req_status<>4";
    $result = fetch_object(query($sql));

    return round($result->cnt * $coeff);
}

function sectorListXML () {
    $xml = "";

    $sql = "SELECT t5.id, t5.name, t5.rewrite_name FROM doctor_sector AS t1
            LEFT JOIN doctor_4_clinic AS t2 ON t2.doctor_id=t1.doctor_id and t2.type = " . DoctorClinicModel::TYPE_DOCTOR . "
            LEFT JOIN clinic AS t3 ON t3.id=t2.clinic_id
            LEFT JOIN doctor AS t4 ON t4.id=t1.doctor_id
            LEFT JOIN sector AS t5 ON t5.id=t1.sector_id
            WHERE t4.status=3
            AND t3.status=3
            AND t3.city_id=". Yii::app()->city->getCityId() ."
            GROUP BY t5.name
            ORDER BY name";
    $result = query($sql);

    if (num_rows($result) > 0) {
        $xml  .= "<SectorList>";

        while ($row = fetch_object($result))
            $sectors[] = $row;
        $firstChar = '';
        $sectorList = array();
        $i = 0;
        foreach($sectors as $sector) {
            if(mb_substr($sector->name,0,1) != mb_substr($sectors[$i+1]->name,0,1) && mb_substr($sector->name,0,1) != $firstChar)
                $i++;
            $sectorList[$i][] = $sector;
            $firstChar = mb_substr($sector->name,0,1);
        }

        foreach($sectorList as $group){
            $xml  .= '<Group char="'. mb_substr($group[0]->name, 0, 1) .'">';
            foreach($group as $item){
                $xml  .= '<Element id="'. $item->id .'">';
                $xml  .= '<Name>'. $item->name .'</Name>';
                $xml  .= '<RewriteName>'. $item->rewrite_name .'</RewriteName>';
                $xml  .= '</Element>';
            }
            $xml  .= '</Group>';
        }

        $xml .= "</SectorList>";
    }

    return $xml;
}

function getSpecializationListXML() {
    $xml = "";

    $sql = "SELECT id, name FROM sector ORDER BY name";
    $result = query($sql);
    if (num_rows($result) > 0) {
        $xml .= "<SpecializationList>";
        while ($row = fetch_object($result)) {
            $xml .= "<Element id='".$row->id."'>";
            $xml .= "<Name>".$row->name."</Name>";
            $xml .= "</Element>";
        }
        $xml .= "</SpecializationList>";
    }
    return $xml;
}

function illnessListXML ($sectorId = null) {
    $xml  = "";

    if($sectorId !== null)
        $sqlAddCond = " AND sector_id=$sectorId";
    else
        $sqlAddCond = "";
    $sql = "SELECT id, name, rewrite_name
            FROM illness
            WHERE is_hidden=0 ".$sqlAddCond."
            ORDER BY name
            LIMIT 7";
    $result = query($sql);
    if (num_rows($result) > 0) {
        $xml  .= "<IllnessList>";
        while ($row = fetch_object($result)){
            $xml .= "<Element id='".$row->id."'>";
            $xml .= "<Name>".$row->name."</Name>";
            $xml .= "<RewriteName>".$row->rewrite_name."</RewriteName>";
            $xml .= "</Element>";
        }
        $xml  .= "</IllnessList>";
    }

    return $xml;
}

function isMobileBrowser() {
    $useragent=$_SERVER['HTTP_USER_AGENT'];

    if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
        return true;
    else
        return false;
}

function getCurrentClinicXML($currentClinicId) {
    $xml = '';

    $currentClinicId = (int)$currentClinicId;
    $sql = "SELECT t1.id, t1.name
            FROM clinic t1
            WHERE t1.id=".$currentClinicId."
            LIMIT 1";
    $result = query($sql);
    $clinic = fetch_object($result);
    $xml .= "<Clinic id='".$clinic->id."'>".$clinic->name."</Clinic>";

    return $xml;
}

function getClinicListXML($adminId, $currentClinicId) {
    $xml = '';

    $adminId = (int)$adminId;
    $sql = "SELECT t1.id, t1.name, t1.parent_clinic_id
            FROM clinic t1
            WHERE t1.parent_clinic_id IN (SELECT clinic_id FROM admin_4_clinic WHERE clinic_admin_id=".$adminId.")
                OR t1.id IN (SELECT clinic_id FROM admin_4_clinic WHERE clinic_admin_id=".$adminId.")";
    $result = query($sql);
    $xml .= '<ClinicList>';
    while ($row = fetch_object($result)){
        if($row->id == $currentClinicId)
            $xml .= '<Element id="'.$row->id.'" selected="1">'.$row->name.'</Element>';
        else
            $xml .= '<Element id="'.$row->id.'" selected="0">'.$row->name.'</Element>';
    }
    $xml .= '</ClinicList>';

    return $xml;
}

function getMonthInRus($number) {
    switch($number){
        case 1:  $month = 'Январь'; break;
        case 2:  $month = 'Февраль'; break;
        case 3:  $month = 'Март'; break;
        case 4:  $month = 'Апрель'; break;
        case 5:  $month = 'Май'; break;
        case 6:  $month = 'Июнь'; break;
        case 7:  $month = 'Июль'; break;
        case 8:  $month = 'Август'; break;
        case 9:  $month = 'Сентябрь'; break;
        case 10: $month = 'Октябрь'; break;
        case 11: $month = 'Ноябрь'; break;
        case 12: $month = 'Декабрь'; break;
    }

    return $month;
}

function getLastMonthsListXML($number = 3) {
    $xml = '';

    if($number > 0){
        $xml .= '<MonthList>';
        for($i=($number-1); $i>-1; $i--){
            $currentTime = mktime(0,0,0,date("m")-$i,1,date("Y"));
            $xml .= '<Element>';
            $xml .= '<Name>'.getMonthInRus(date('n', $currentTime)).'</Name>';
            $xml .= '<BeginDate>'.date('d-m-Y', $currentTime).'</BeginDate>';
            $xml .= '<EndDate>'.date('d-m-Y', mktime(0,0,0,date("m")-$i+1,0,date("Y"))).'</EndDate>';
            $xml .= '</Element>';
        }
        $xml .= '</MonthList>';
    }

    return $xml;
}

?>
