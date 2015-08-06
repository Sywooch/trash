<?php

use dfs\docdoc\models\RatingStrategyModel;


function setXML ($xmlIn) {
	global $proc, $doc;
	
	$xmlString = '<?xml version="1.0" encoding="UTF-8"?>';
	$xmlString .= $xmlIn;

	libxml_use_internal_errors(true);
	$xml = new DOMDocument('1.0','UTF-8');
	$xml->recover = true;
	libxml_clear_errors();
	$xml -> loadXML($xmlString);
	
	$addNode = $doc -> importNode($xml -> documentElement, true);
	$node = $doc -> getElementsByTagName("root") ->item(0);
	$newnode = $node -> appendChild($addNode);
}


function initDomXML ( ) {
	global $doc;

	$doc = new DOMDocument('1.0','UTF-8');
	$node = $doc -> createElement("root");
	$rootNode = $doc -> appendChild($node);

}


function setException ( $mess ) {
    echo htmlspecialchars(json_encode(array('status' => 'error', 'error' => $mess)), ENT_NOQUOTES);
    exit;
}

function setSuccess ( $redirectURL = false ) {
    echo htmlspecialchars(json_encode(array('status' => 'success', 'url' => $redirectURL)), ENT_NOQUOTES);
}

/**
 * Преобразует массив в XML
 *
 * @param array $data
 * @param bool  $forceKeyNumeric
 *
 * @return string
 */
function arrayToXML($data, $forceKeyNumeric = false) {
    $xml = '';
    
    if(array_key_exists(0, $data) || $forceKeyNumeric) {

        foreach($data as $item){
            if(isset($item['Id']))
                $xml .= '<Element id="'.$item['Id'].'">';
            else
                $xml .= '<Element>';
	
	        if (isset($item) && count($item) > 0 && is_array($item) )
		        foreach ( $item as $attr => $val ) {
		            $attr = ucfirst($attr);
		            if  (is_array($val)){
		                $xml .= '<'.$attr.'>'.arrayToXML($val).'</'.$attr.'>';
		            } else {
		                if(is_numeric($val))
		                    $xml .= '<'.$attr.'>'.$val.'</'.$attr.'>';
		                else
		                    $xml .= '<'.$attr.'><![CDATA['.$val.']]></'.$attr.'>';
		            }
		        }
	        
	        $xml .= '</Element>';
        }
        
    } else {
    	if (isset($data ) && count($data ) > 0 )
	        foreach($data as $attr => $val){
	            $attr = ucfirst($attr);
	            if(is_array($val)){
	                $xml .= '<'.$attr.'>'.arrayToXML($val).'</'.$attr.'>';
	            } else {
	                if(is_numeric($val))
	                    $xml .= '<'.$attr.'>'.$val.'</'.$attr.'>';
	                else
	                    $xml .= '<'.$attr.'><![CDATA['.$val.']]></'.$attr.'>';
	            }
	        }
    }
    
    return $xml;
}


	function setExeption ( $mess ) {
		echo htmlspecialchars(json_encode(array('error' => $mess)), ENT_NOQUOTES);
		exit;
	}

/**
 * Инициализация событий mixpanel через js
 */
function mixpanelJsInit() {
	$mixpanel = Yii::app()->mixpanel;
	$xml = '<mixpanel>';
	$xml .= '<partnerId>' . (int)Yii::app()->referral->id . '</partnerId>';
	$xml .= '<ratingStrategy>' . (int)Yii::app()->rating->getId(RatingStrategyModel::FOR_DOCTOR) . '</ratingStrategy>';
	$xml .= '<init token="' . $mixpanel->getToken() . '"/>';
	foreach ($mixpanel->getTracks() as $name => $params) {
		$xmlValue = htmlspecialchars(json_encode($params), ENT_XML1, 'UTF-8');
		$xml .= '<track name="' . $name . '">' . $xmlValue . '</track>';
	}
	$xml .= '</mixpanel>';
	setXML($xml);
}
