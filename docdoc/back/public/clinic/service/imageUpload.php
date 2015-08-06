<?php

require_once dirname(__FILE__) . "/../../include/common.php";
require_once dirname(__FILE__) . "/../lib/FileUploader.php";
require_once dirname(__FILE__)."/../../lib/php/imgLib.php";
require_once dirname(__FILE__)."/../../lib/php/validate.php";

$clinicId = (isset($_REQUEST['id'])) ? checkField($_REQUEST['id'], "i", 0) : '0';

$allowedExtensions = array("jpg", "gif", "png", "tif");
$sizeLimit = 5 * 1024 * 1024;
define ("widthPrv",550);
define ("heightPrv",748);
define ("widthMin",160);
define ("heightMin",218);
define ("SRCpath","src/");

$uploader = new FileUploader($allowedExtensions, $sizeLimit, $_GET['qqfile'], $clinicId);
$response = $uploader->handleUpload(Path4Upload . 'clinic/logo/', true);

echo htmlspecialchars(json_encode($response), ENT_NOQUOTES);
