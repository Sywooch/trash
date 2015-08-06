<?php
	use dfs\docdoc\models\CityModel;
	use dfs\docdoc\models\RequestModel;

	require_once dirname(__FILE__)."/../include/header.php";
	require_once dirname(__FILE__)."/../lib/php/validate.php";
	require_once dirname(__FILE__)."/php/requestLib.php";

	$type = isset($_GET['typeView']) ? $_GET['typeView'] : 'default';
	$user = new user();
	$user -> checkRight4page(array('ADM','OPR','SOP', 'LIS'));

	pageHeader(dirname(__FILE__)."/xsl/doctorList.xsl","noHead");

	$id 		= ( isset($_GET["shDoctorId"]) ) ? checkField ($_GET["shDoctorId"], "i", 0) : 0;
	$status		= ( isset($_GET["status"]) ) ? checkField ($_GET["status"], "i", '') : '';
	$name		= ( isset($_GET["shDoctor"]) ) ? checkField ($_GET["shDoctor"], "t", '') : '';
	$phoneExt	= ( isset($_GET["shDoctorExt"]) ) ? checkField ($_GET["shDoctorExt"], "t", '') : '';
	$clinic		= ( isset($_GET["clinicId"]) ) ? checkField ($_GET["clinicId"], "i", '') : '';
	$sectorId	= ( isset($_GET["shSectorId"]) ) ? checkField ($_GET["shSectorId"], "i", '') : '';
	$sector		= ( isset($_GET["shSector"]) ) ? checkField ($_GET["shSector"], "t", '') : '';
	$startPage	= ( isset($_GET["startPage"]) ) ? checkField ($_GET["startPage"], "i") : 0;
	$departure	= ( isset($_GET["shHome"]) ) ? checkField ($_GET["shHome"], "i", 0) : 0;
	$doctorList = ( isset($_GET["doctorList"]) ) ? $_GET["doctorList"] : array();
	$doctorId	= ( isset($_GET["requestDoctorId"]) ) ? checkField ($_GET["requestDoctorId"], "i", '') : '';
	$workDate	= ( isset($_GET["doctorWorkDate"]) ) ? checkField ($_GET["doctorWorkDate"], "t", null) : null;
	$workHour	= ( isset($_GET["doctorWorkHour"]) ) ? checkField ($_GET["doctorWorkHour"], "i", 0) : 0;
	$workMin	= ( isset($_GET["doctorWorkMin"]) ) ? checkField ($_GET["doctorWorkMin"], "i", 0) : 0;
	$workToHour	= ( isset($_GET["doctorWorkToHour"]) ) ? checkField ($_GET["doctorWorkToHour"], "i", 23) : 23;
	$workToMin	= ( isset($_GET["doctorWorkToMin"]) ) ? checkField ($_GET["doctorWorkToMin"], "i", 59) : 59;
	$kidsReception = ( isset($_GET["shKidsReception"]) ) ? checkField ($_GET["shKidsReception"], "i", 0) : 0;
	$kidsAgeFrom	= ( isset($_GET["shKidsAgeFrom"]) ) ? checkField ($_GET["shKidsAgeFrom"], "i", 0) : 0;
	$kidsAgeTo	= ( isset($_GET["shKidsAgeTo"]) ) ? checkField ($_GET["shKidsAgeTo"], "i", 0) : 0;
	$districts  = (isset($_GET['shDistrict'])) ? $_GET['shDistrict'] : [];

	$workFrom = "";
	$workTo = "";

	if ($workDate !== null) {

		$workToHour =  (empty($workToHour)) ? 23 : $workToHour;

		$dt = explode('.', $workDate);
		if (count($dt) === 3) {
			$workFrom = date('Y-m-d H:i:s', mktime($workHour, $workMin, 0, $dt[1], $dt[0], $dt[2]));
			$workTo = date('Y-m-d H:i:s', mktime($workToHour, $workToMin, 0, $dt[1], $dt[0], $dt[2]));
		}
	}

	if ( !empty($doctorId) ) 
		array_push($doctorList, $doctorId);
	
	
	$metroList	= (isset ($_GET['shMetro']))? rtrim( trim($_GET['shMetro'] ), ',') : '';
	$metroList	= ( !empty($metroList) ) ? explode (",", $metroList) : array();
	
	$sortField	= ( isset($_GET["sortField"]) ) ? checkField ($_GET["sortField"], "t", '') : '';
	

	$xmlString = '<srvInfo>';
	$xmlString .= "<HostFront>".SERVER_FRONT."</HostFront>";
	$xmlString .= $user -> getUserXML();
	$xmlString .= "<TypeView>{$type}</TypeView>";
	
	$xmlString .= '<Id>'.$id.'</Id>';
	$xmlString .= '<ClinicId>'.$clinic.'</ClinicId>';
	$xmlString .= '<StartPage>'.$startPage.'</StartPage>';
	$xmlString .= '<Status>'.$status.'</Status>';
	$xmlString .= '<Name>'.$name.'</Name>';
	$xmlString .= '<PhoneExt>'.$phoneExt.'</PhoneExt>';
	$xmlString .= '<ShSector>'.$sector.'</ShSector>';
	$xmlString .= '<ShSectorId>'.$sectorId.'</ShSectorId>';
	$xmlString .= '<DoctorId>'.$doctorId.'</DoctorId>';
	if ( count($doctorList) > 0 ) {
		$xmlString .= '<DoctorList>';
		foreach ( $doctorList as $key => $data ) {
			$xmlString .= '<DoctorId>'.$data.'</DoctorId>';
		}
		$xmlString .= '</DoctorList>';
	}
	
	$xmlString .= getCityXML();
	$xmlString .= '</srvInfo>';
	setXML($xmlString);


	$params = array();
	if ( $id > 0 ) { $params['id'] 	= $id; }
	else {
		if ( !is_array($doctorList )) {
			array_push($doctorList, $doctorId);
		}
		
		$params['step'] 		= "100";
		$params['startPage']	= $startPage;
		$params['name']			= $name;
		$params['sector']		= $sectorId;
//		$params['status']		= $status;
		$params['departure']	= $departure;
		$params['doctorList']	= $doctorList;
		$params['phoneExt']		= $phoneExt;
		$params['clinicAvailable']= 'yes';
		$params['workFrom']		= $workFrom;
		$params['workTo']		= $workTo;
		$params['kidsReception'] = $kidsReception;
		$params['kidsAgeFrom']	= $kidsAgeFrom;
		$params['kidsAgeTo']    = $kidsAgeTo;
		$params['districts'] = $districts;
	}

	$xmlString = '<dbInfo>';

	$city = CityModel::model()->findByPk(getCityId());
	$host = 'http://' . $city->prefix . SERVER_FRONT;

	$metroIdList  = array();
	if ( count($metroList) > 0 ) {
		$metroIdList = getMetroIdList($metroList);
	}
	$params['metroList']	= $metroIdList;
	$xmlString .= getDoctorList4requestXML(
		$params,
		$city->id_city,
		Yii::app()->request->getQuery("requestId")
	);
	$xmlString .= getStatusDictXML();
	$xmlString .= '<DoctorHref>' . $host . '/doctor/</DoctorHref>';
	$xmlString .= '</dbInfo>';
	setXML($xmlString);

	pageFooter("noHead");

