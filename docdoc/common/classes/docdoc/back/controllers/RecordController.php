<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 28.07.14
 * Time: 18:47
 */

namespace dfs\docdoc\back\controllers;

use CHttpException;
use dfs\docdoc\models\RequestRecordModel;
use dfs\docdoc\objects\record\RecordHandler;

/**
 * Загрузка записей
 *
 * Class RecordController
 *
 * @package dfs\docdoc\back\controllers
 */
class RecordController extends BackendController
{
	/**
	 * Возвращает правила доступа для контроллера
	 *
	 * @return array
	 */
	public function accessRules()
	{
		return [[ 'allow' ]];
	}


	/**
	 * Скачать запись
	 *
	 * @param int $id
	 *
	 * @throws \CHttpException
	 */
	public function actionDownload($id)
	{
		$record = $this->loadModel($id);

		$recordHandler = new RecordHandler($record);
		$file = $recordHandler->openFile();

		if(!is_resource($file)){
			throw new CHttpException(404, 'File not found');
		}

		$fileSize = $recordHandler->getFileSize();

		header("Accept-Ranges: bytes");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: " . $fileSize);
		header("Content-type: audio/mpeg");
		header('Content-Disposition: attachment; filename="' . $record->record . '"');

		fpassthru($file);
		exit;

	}

	/**
	 * Грузит RequestRecordModel
	 *
	 * @param int $id
	 *
	 * @return RequestRecordModel
	 * @throws \CHttpException
	 */
	public function loadModel($id)
	{
		$model = RequestRecordModel::model()->findByPk($id);

		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}

		return $model;
	}
} 
