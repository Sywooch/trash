<?php

require_once dirname(__FILE__)."/../include/header.php";

initDomXML();

$session = Yii::app()->session;

$step = (isset($params['step']))? checkField($params['step'],"e","", false, array('proceed', 'step2')) : "";
$mode = (isset($session['registerMode']))? checkField($session['registerMode'],"e", 'doctor',  false, array('doctor', 'clinic')) : 'doctor';

$xmlString = '<dbInfo>';
$xmlString .= '<Step>'.$step.'</Step>';
$xmlString .= '<Mode>'.$mode.'</Mode>';
if ( isset($session['error']) ) {
	$xmlString .= '<Error>'.$session['error'].'</Error>';
}
if ( isset($session['registerName']) )
	$xmlString .= '<Name><![CDATA['.$session['registerName'].']]></Name>';
if ( isset($session['registerPhone']) )
	$xmlString .= '<Phone><![CDATA['.formatPhone($session['registerPhone']).']]></Phone>';
if ( isset($session['registerEmail']) )
	$xmlString .= '<Email><![CDATA['.$session['registerEmail'].']]></Email>';
if ( isset($session['registerClinicName']) )
	$xmlString .= '<ClinicName><![CDATA['.$session['registerClinicName'].']]></ClinicName>';
$xmlString .= '</dbInfo>';

setXML($xmlString);

Yii::app()->runController('page/old/template/register');
