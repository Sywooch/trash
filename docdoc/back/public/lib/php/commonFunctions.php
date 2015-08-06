<?php
use dfs\docdoc\models\QueueModel;

function XMLload($file,$doc) {
	$xml = new DOMDocument('1.0','UTF-8');
	$xml -> load($file);
	$addNode = $doc -> importNode($xml -> documentElement, true);	
	$node = $doc -> getElementsByTagName("root") ->item(0);
	$newnode = $node -> appendChild($addNode);
}


function xmlURL () {
	$xml = '';
	
	$URL = $_SERVER['PHP_SELF'];
	$URL = str_replace('.php','.htm',$URL);
	$xml = '<URL>'.$URL.'</URL>';
	$xml .= '<URL_SELF>'.$_SERVER['PHP_SELF'].'</URL_SELF>';
	$xml .= '<URI><![CDATA['.$_SERVER['REQUEST_URI'].']]></URI>';
	
	return $xml;
}	





function pageHeader($xslFile, $mode = '', $withHeader = true ) {
	global $proc, $doc;
	
	if (empty($_GET['debug']) || $_GET['debug'] != 'yes')  {
		if ( $withHeader ) { 
			printHeader ($mode); 
		} else {
			header('Content-type: text/html; charset=UTF-8');
		} 
	} else { 
		header('Content-type: text/xml; charset=UTF-8');
	}
	
	$xsl = new DOMDocument();
	$xsl->load($xslFile);
	$proc = new XSLTProcessor;
	$proc->importStyleSheet($xsl); 
	
	$doc = new DOMDocument('1.0','UTF-8');
	$node = $doc -> createElement("root");
	$rootNode = $doc -> appendChild($node); 
}



function setXML ($xmlIn) {
	global $proc, $doc;
	
	$xmlString = '<?xml version="1.0" encoding="UTF-8"?>';
	$xmlString .= $xmlIn;
	
	$xml = new DOMDocument('1.0','UTF-8');	
	$xml -> loadXML($xmlString);
	
	$addNode = $doc -> importNode($xml -> documentElement, true);
	$node = $doc -> getElementsByTagName("root") ->item(0);
	$newnode = $node -> appendChild($addNode);
}



function pageFooter ( $mode = '', $withHeader = true ) {
	global $proc, $doc;

	if ( debugMode == 'yes' ) { 
		$proc -> setParameter('', 'debug', "yes");
	}
	$processed = $proc->transformToXML($doc);
	
	if (empty($_GET['debug']) || $_GET['debug'] != 'yes')  {
		$str = html_entity_decode($processed, ENT_NOQUOTES, 'UTF-8');
		print $str;
		if ( $withHeader ) { printFooter ($mode); }
	} else {  
		$str = $doc->saveXML();	
		print $str;
	}

	Yii::app()->onEndRequest(new CEvent(null));
}

function setException ( $mess ) {
    echo htmlspecialchars(json_encode(array('status' => 'error', 'error' => $mess)), ENT_NOQUOTES);
    exit;
}

function setSuccess ( $redirectURL = false ) {
    echo htmlspecialchars(json_encode(array('status' => 'success', 'url' => $redirectURL)), ENT_NOQUOTES);
}

/**
 * Получаем XML с данными SIP
 *
 * @param $user
 *
 * @return string
 */
function getQueueUserXML($user)
{
	$xml = '';
	$queue = QueueModel::model()->byUser($user)->registered()->find();
	if (!is_null($queue)) {
		$xml .= "<Queue4User sip='{$queue->SIP}' name='{$queue->getName()}' userId='{$queue->user_id}' startTime='{$queue->startTime}' />";
	}

	return $xml;
}

/**
 * Получаем список доступных сип каналов
 *
 * @return string
 */
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
			WHERE t1.status = " . QueueModel::STATUS_REGISTERED . "
			ORDER BY t1.SIP";

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
