<?php
	require_once	dirname(__FILE__)."/../../include/common.php";
	require_once	dirname(__FILE__)."/../../lib/php/validate.php";
	require_once	dirname(__FILE__)."/../php/reportLib.php";
	require_once 	dirname(__FILE__)."/../../lib/php/xls.php";
	
	
	
	$crDateFrom	= ( isset($_GET["dateFrom"]) ) ? checkField ($_GET["dateFrom"], "t", date("d.m.Y")) : date("d.m.Y"); 
	$crDateTill	= ( isset($_GET["dateTill"]) ) ? checkField ($_GET["dateTill"], "t", date("d.m.Y")) : date("d.m.Y");
	

	$file="DiagnosticaCallReport_".$crDateFrom."_".$crDateTill.".csv";
	$filename = dirname(__FILE__)."/../../_reports/".$file;

	
	$params = array();
	$params['crDateFrom']	= $crDateFrom;
	$params['crDateTill']	= $crDateTill;
	
	$xmlString = '<?xml version="1.0" encoding="UTF-8"?>';
	$xmlString .= '<dbInfo>';
	$xmlString .= diagnosticaCallRepoetXML($params);
	$xmlString .= '</dbInfo>';
	
	$xsl = new DOMDocument();
	$xsl->load(dirname(__FILE__)."/../xsl/exportDiagnosticaCallReport.xsl");
	$proc = new XSLTProcessor;
	$proc->importStyleSheet($xsl);
	$doc = new DOMDocument('1.0','UTF-8');
	$node = $doc -> createElement("root");
	$rootNode = $doc -> appendChild($node);
	
	$xml = new DOMDocument('1.0','UTF-8');	
	$xml -> loadXML($xmlString);
	$addNode = $doc -> importNode($xml -> documentElement, true);
	$node = $doc -> getElementsByTagName("root") ->item(0);
	$newnode = $node -> appendChild($addNode);
	
	$processed = $proc->transformToXML($doc);

	$str = html_entity_decode($processed, ENT_NOQUOTES, 'UTF-8');
	
	
	$fp=fopen($filename,"w");
	fwrite($fp,$str);
	fclose($fp);
	
	header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");
    header("Content-Disposition: attachment; filename=\"{$filename}\"");
    header("Content-Transfer-Encoding: binary");
    
	print($str);
?>