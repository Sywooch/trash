<?php

use dfs\docdoc\models\DoctorModel;

require_once dirname(__FILE__) . "/../../lib/php/user.class.php";
require_once dirname(__FILE__) . "/../../include/common.php";
require_once dirname(__FILE__) . "/../../lib/php/validate.php";
require_once dirname(__FILE__) . "/../../lib/php/imgLib.php";

$widthMed = 160; // Ширина превью
$heightMed = 218; // Высота превью

$report = "";

$user = new user();
$user->checkRight4page(array('ADM', 'OPR', 'SOP', 'CNM', 'ACM'), 'simple');
$userId = $user->idUser;

$id = (isset($_GET['id'])) ? checkField($_GET['id'], "i", 0) : '0';
$markPos = (isset($_GET['markPos'])) ? checkField($_GET['markPos'], "t", 'left') : 'left';

$x = (isset($_GET['x'])) ? checkField($_GET['x'], "i", 0) : '0';
$y = (isset($_GET['y'])) ? checkField($_GET['y'], "i", 0) : '0';
$w = (isset($_GET['w'])) ? checkField($_GET['w'], "i", $widthMed) : $widthMed;
$h = (isset($_GET['h'])) ? checkField($_GET['h'], "i", $heightMed) : $heightMed;

if ($w == 0) {
	$w = $widthMed;
}
if ($h == 0) {
	$w = $heightMed;
}

$doctor = $id > 0 ? DoctorModel::model()->findByPk($id) : null;

if ($doctor !== null) {
	if (!empty($_GET['delete'])) {
		if (!$doctor->deleteImage()) {
			$report = array("status" => "error", "error" => "Ошибка при сохранении в БД");
		} else {
			$report = array("status" => "success");
		}

		echo htmlspecialchars(json_encode($report), ENT_NOQUOTES);
		exit;
	} else {
		try {
			if ($doctor->saveImage(null, $x, $y, $w, $h, $markPos == 'right')) {
				$report = array("status" => "success");
			} else {
				$errors = [];

				foreach ($doctor->getErrors() as $doctorErrors) {
					foreach ($doctorErrors as $error) {
						$errors[] = $error;
					}
				}

				$report = array("status" => "error", "error" => $errors);
			}
		} catch (Exception $e) {
			$report = array("status" => "error", "error" => $e->getMessage());
		}
	}
} else {
	$report = array("status" => "error", "error" => "Неверный идентификатор доктора");
}

echo htmlspecialchars(json_encode($report), ENT_NOQUOTES);
exit;
