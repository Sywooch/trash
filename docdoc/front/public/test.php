<?php

require_once 	dirname(__FILE__)."/include/common.php";



$sql = "SELECT t5.id, t5.name, t5.rewrite_name FROM doctor_sector AS t1
            LEFT JOIN doctor_4_clinic AS t2 ON t2.doctor_id=t1.doctor_id
            LEFT JOIN clinic AS t3 ON t3.id=t2.clinic_id
            LEFT JOIN doctor AS t4 ON t4.id=t1.doctor_id
            LEFT JOIN sector AS t5 ON t5.id=t1.sector_id
            WHERE t4.status=3
            AND t3.status=3
            AND t3.city_id=1
            GROUP BY t5.name
            ORDER BY t5.name";

$result = query($sql);
echo num_rows($result);

$xml = "";
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

ECHO $xml;
?>