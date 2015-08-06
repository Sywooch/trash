<?php

use dfs\docdoc\components\Version;

require_once dirname(__FILE__) . "/../lib/php/user.class.php";
require_once dirname(__FILE__) . "/common.php";
require_once dirname(__FILE__) . "/../lib/php/commonFunctions.php";
require_once dirname(__FILE__) . "/../lib/php/pager.php";


function printHeader($headerType = "")
{
	header('Content-type: text/html; charset=UTF-8');
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache ");
	//header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

	$xsl = new DOMDocument();
	$xsl->load(dirname(__FILE__) . "/xsl/header.xsl");
	$proc = new XSLTProcessor;
	$proc->importStyleSheet($xsl);

	$doc = new DOMDocument('1.0', 'UTF-8');
	$node = $doc->createElement("root");
	$newnode = $doc->appendChild($node);

	$panel = (isset(Yii::app()->session['panel'])) ? Yii::app()->session['panel'] : 'open';

	$xmlString = '<?xml version="1.0" encoding="utf-8"?>';
	$xmlString .= '<srvInfo>';
	$user = new user();
	$xmlString .= $user->getUserXML();
	$xmlString .= "<ServerAddr>" . $_SERVER['SERVER_ADDR'] . "</ServerAddr>";
	$xmlString .= getCityListXML();
	$xmlString .= "<Panel>" . $panel . "</Panel>";
	$xmlString .= getCityXML();
	$xmlString .= '<URL><![CDATA[' . $_SERVER['REQUEST_URI'] . ']]></URL>';

	$version = new Version();
	$xmlString .= "<Version>{$version->getCurrent()}</Version>>";

	$xmlString .= '<jqueryPath>' . (empty($GLOBALS['jqueryPath']) ? '/lib/js/jquery-1.4.4.min.js' : $GLOBALS['jqueryPath']). '</jqueryPath>';

	$xmlString .= getQueueUserXML($user->idUser);
	$xmlString .= '</srvInfo>';

	$xml2 = new DOMDocument('1.0', 'UTF-8');
	$xml2->loadXML($xmlString);
	$addNode = $doc->importNode($xml2->documentElement, true);
	$newnode2 = $newnode->appendChild($addNode);

	XMLload(dirname(__FILE__) . "/xml/leftMenu.xml", $doc);

	$proc->setParameter('', 'headerType', $headerType);
	$processed = $proc->transformToXML($doc);
	$str = html_entity_decode($processed, ENT_NOQUOTES, 'UTF-8');
	print $str;
}

function printFooter($headerType = "")
{
	global $statFooter;

	$xsl = new DOMDocument();
	$xsl->load(dirname(__FILE__) . "/xsl/footer.xsl");
	$proc = new XSLTProcessor;
	$proc->importStyleSheet($xsl);

	$doc = new DOMDocument('1.0', 'UTF-8');
	$node = $doc->createElement("root");
	$newnode = $doc->appendChild($node);

	$proc->setParameter('', 'headerType', $headerType);
	$proc->setParameter('', 'footer', $statFooter);
	$processed = $proc->transformToXML($doc);
	$str = html_entity_decode($processed, ENT_NOQUOTES, 'UTF-8');
	print $str;
}