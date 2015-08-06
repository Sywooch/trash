<?php

namespace dfs\docdoc\components;

/**
 * Class AppController
 *
 * @package dfs\docdoc\components
 */
abstract class AppController extends \CController
{
	/**
	 * Отдача json-данных клиенту
	 *
	 * @param array $data
	 */
	protected function renderJSON($data)
	{
		header('Content-type: application/json');
		echo \CJSON::encode($data);

		foreach (\Yii::app()->log->routes as $route) {
			if($route instanceof \CWebLogRoute) {
				$route->enabled = false; // disable any weblogroutes
			}
		}
		\Yii::app()->end();
	}

	/**
	 * Рендер для старых xsl-шаблонов
	 *
	 * @param string $template
	 * @param string $file
	 * @param bool   $isRoot
	 *
	 * @return string
	 */
	public function renderXsl($template, $file = null, $isRoot = false)
	{
		global $doc;

		if ($file === null) {
			$file = $template . '.xsl';
		}

		$xslDom = new \DOMDocument();

		if ($isRoot) {
			$xslDom->load(ROOT_PATH . $this->_pathToXsl . $file);
		} else {
			$xml = '<?xml version="1.0"  encoding="utf-8"?>
			<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
				<xsl:import href="' . ROOT_PATH . $this->_pathToXsl . $file . '"/>
				<xsl:template match="/root">
					<xsl:call-template name="'. $template . '"/>
				</xsl:template>
			</xsl:transform>';

			$xslDom->loadXML($xml);
		}

		$proc = new \XSLTProcessor;
		$proc->importStyleSheet($xslDom);

		$this->xslParameters($proc);

		return html_entity_decode($proc->transformToXML($doc), ENT_NOQUOTES, 'UTF-8');
	}

	/**
	 * Установка параметров для xsl шаблонов
	 *
	 * @param \XSLTProcessor $proc
	 */
	protected function xslParameters(\XSLTProcessor $proc)
	{
		if (debugMode == 'yes') {
			$proc->setParameter('', 'debug', 'yes');
		}
	}

	/**
	 * Сформировать сообщение об ошибках в записи (для показа в js-алерте)
	 *
	 * @param \CActiveRecord $record
	 * @param string         $defaultMessage
	 *
	 * @return string
	 */
	public static function buildErrorMessageByRecord(\CActiveRecord $record, $defaultMessage = '')
	{
		$msg = '';
		foreach ($record->getErrors() as $errors) {
			$msg .= implode(". \n", $errors);
		}

		return $msg ?: $defaultMessage;
	}

	public static function getInternalReferer()
	{
		if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) !== false) {
			return $_SERVER['HTTP_REFERER'];
		}

		return null;
	}

	/**
	 * Вывод Excel отчета
	 *
	 * @param \PHPExcel $objPHPExcel
	 */
	public function renderExcel(\PHPExcel $objPHPExcel)
	{
		$file = "RequestReport4Clinic_" . date("YmdHisu") . ".xls";

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: max-age=0, must-revalidate, post-check=0, pre-check=0");
		header('Content-Type: application/vnd.ms-excel');
		header("Content-Type: application/download");
		header("Content-Disposition: attachment; filename={$file}");
		header("Content-Transfer-Encoding: binary");

		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		\Yii::app()->end();
	}
}
