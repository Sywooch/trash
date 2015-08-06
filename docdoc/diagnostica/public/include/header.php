<?php
	require_once 	dirname(__FILE__)."/common.php";

	
	function printHeader ($headerType = "") {
		header('Content-type: text/html; charset=UTF-8');
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache ");
		//header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		$xsl = new DOMDocument('1.0','UTF-8');
		$xsl->load(dirname(__FILE__)."/xsl/header.xsl");
		$proc = new XSLTProcessor;
		$proc->importStyleSheet($xsl);

		$doc = new DOMDocument('1.0','UTF-8');
		$node = $doc->createElement("root");
		$newnode = $doc->appendChild($node);
		
		
		$xmlString = '<?xml version="1.0" encoding="utf-8"?>';
		$xmlString .= '<srvInfo>';	
		$xmlString .= '<URL><![CDATA['.$_SERVER['REQUEST_URI'].']]></URL>';
		$xmlString .= '</srvInfo>';
	
		$xml2 = new DOMDocument('1.0','UTF-8');	$xml2->loadXML($xmlString);
		$addNode = $doc->importNode($xml2->documentElement, true);
		$newnode2 = $newnode->appendChild($addNode);

		$proc->setParameter('', 'GA', \Yii::app()->getParams()['ga-universal-id-diagnostic']);
		$proc->setParameter('', 'headerType', $headerType);
		$proc->setParameter('', 'stat', statisticKey);
		$processed = $proc->transformToXML($doc);
		$str = html_entity_decode ( $processed, ENT_NOQUOTES,'UTF-8');
		print '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'.$str;
	}
	
	function printFooter ($headerType = "") {
		global $statFooter;

		$xsl = new DOMDocument('1.0','UTF-8');
		$xsl->load(dirname(__FILE__)."/xsl/footer.xsl");
		$proc = new XSLTProcessor;
		$proc->importStyleSheet($xsl); 
		
		$doc = new DOMDocument('1.0','UTF-8');
		$node = $doc->createElement("root");
		$newnode = $doc->appendChild($node);
		   
		$proc->setParameter('', 'headerType', $headerType);
		$proc->setParameter('', 'stat', statisticKey);
		
		$processed = $proc->transformToXML($doc);
		$str = html_entity_decode ( $processed, ENT_NOQUOTES,'UTF-8');
		print $str;
	}

	
	
	
	
?>