<?php
	use dfs\docdoc\models\RequestModel;


	require_once dirname(__FILE__)."/../include/header.php";
	require_once	dirname(__FILE__)."/php/requestLib.php";
	require_once dirname(__FILE__) . "/../lib/php/RequestInterface.php";

	$user = new user();
	$user -> checkRight4page(array('ADM','SOP'));
	$userId = $user -> idUser;

	pageHeader(dirname(__FILE__)."/xsl/setGroupAction.xsl","noHead");

	$xmlString = '<srvInfo>';
	$xmlString .= $user -> getUserXML();
	$xmlString .= getCityXML();
	$xmlString .= '</srvInfo>';
	setXML($xmlString);

	$xmlString = '<dbInfo>';

	$typeView = isset($_GET['typeView']) ? $_GET['typeView'] : null;

	switch ($typeView) {
		case RequestInterface::VIEW_PARTNERS:
			$xmlString .= '<StatusDict mode="requestDict">';
			foreach (RequestModel::getPartnerStatuses() as $key => $name) {
				if ($key) {
					$xmlString .= '<Element id="' . $key . '">' . $name . '</Element>';
				}
			}
			$xmlString .= '</StatusDict>';
			break;

		default:
			$xmlString .= getStatus4RequestXML();
			break;
	}

	$xmlString .= '<TypeView>' . $typeView . '</TypeView>';

	$xmlString .= '</dbInfo>';
	setXML($xmlString);

	pageFooter("noHead");
?>

