<?php

use dfs\docdoc\reports\RequestCollection;

require_once dirname(__FILE__) . "/../include/header.php";
require_once dirname(__FILE__) . "/../lib/php/validate.php";
require_once dirname(__FILE__) . "/php/reportLib.php";
require_once dirname(__FILE__) . "/../lib/php/dateTimeLib.php";
require_once dirname(__FILE__) . "/../request/php/requestLib.php";
require_once dirname(__FILE__) . "/../lib/php/serviceFunctions.php";

$user = new user();
$user->checkRight4page(array('ADM', 'SAL', 'SOP'));

$report = new RequestCollection();

$request = \Yii::app()->request;

$report->
	setReportType($request->getQuery('ReportType', 'clinics'))->
	setRequestType($request->getQuery('RequestType'))->
	setCityId(getCityId())->
	setPeriod($request->getQuery('crDateShFrom', '01.' . date('m.Y')), $request->getQuery('crDateShTill', date('d.m.Y')));

if ($request->getQuery('crDateShFrom2') && $request->getQuery('crDateShTill2')) {
	$report->setAdmissionPeriod(
		$request->getQuery('crDateShFrom2'),
		$request->getQuery('crDateShTill2')
	);
}

if (!empty($_GET['Excel'])) {
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator('Docdoc.ru')->setTitle('Request count report');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Отчёт за период с ' . $report->getPeriodBegin() . ' по ' . $report->getPeriodEnd());
	$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');

	$sheet = $objPHPExcel->setActiveSheetIndex(0);
	$sheet->setTitle('Report');

	$TH = array(
		'font' => array(
			'name' => 'Arial',
			'size' => '10',
			'bold' => true,
			'color' => array('rgb' => '000000'),
		),
		'alignment'=>array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
		),
		'fill'=>array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('rgb' => '67bbbc'),
		) ,
		'borders' => array(
			'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN,
			),
		),
	);

	$requestTypes = RequestCollection::getRequestTypes();

	$n = 4;
	switch ($report->getReportType()) {
		case 'clinics':
			$sheet
				->setCellValue('A3', "ID клиники")
				->setCellValue('B3', "Клиника")
				->setCellValue('C3', "Количество записанных")
				->setCellValue('D3', "Тип");
			$sheet->getStyle('A3')->applyFromArray($TH);
			$sheet->getStyle('B3')->applyFromArray($TH);
			$sheet->getStyle('C3')->applyFromArray($TH);
			$sheet->getStyle('D3')->applyFromArray($TH);
			foreach ($report->getReportData() as $row) {
				$sheet
					->setCellValue('A' . $n, $row['clinic_id'])
					->setCellValue('B' . $n, $row['clinic_name'])
					->setCellValue('C' . $n, $row['count'])
					->setCellValue('D' . $n, $requestTypes[$row['type']]);
				$n++;
			}
			break;

		case 'diagnostics':
			$sheet
				->setCellValue('A3', "ID клиники")
				->setCellValue('B3', "Клиника")
				->setCellValue('C3', "КТ")
				->setCellValue('D3', "МРТ")
				->setCellValue('E3', "УЗИ и прочие")
				->setCellValue('F3', "Тип");
			$sheet->getStyle('A3')->applyFromArray($TH);
			$sheet->getStyle('B3')->applyFromArray($TH);
			$sheet->getStyle('C3')->applyFromArray($TH);
			$sheet->getStyle('D3')->applyFromArray($TH);
			$sheet->getStyle('E3')->applyFromArray($TH);
			$sheet->getStyle('F3')->applyFromArray($TH);
			foreach ($report->getReportData() as $row) {
				$sheet
					->setCellValue('A' . $n, $row['clinic_id'])
					->setCellValue('B' . $n, $row['clinic_name'])
					->setCellValue('C' . $n, $row['count_kt'])
					->setCellValue('D' . $n, $row['count_mrt'])
					->setCellValue('E' . $n, $row['count_other'])
					->setCellValue('F' . $n, $requestTypes[$row['type']]);
				$n++;
			}
			break;

		default:
			throw new Exception('Unknown report type');
			break;
	}

	$sheet->getColumnDimension('A')->setWidth(10);
	$sheet->getColumnDimension('B')->setWidth(60);
	$sheet->getColumnDimension('C')->setWidth(15);
	$sheet->getColumnDimension('D')->setWidth(15);
	$sheet->getColumnDimension('E')->setWidth(15);
	$sheet->getColumnDimension('F')->setWidth(15);

	$file = 'RequestCountReport_' . $report->getPeriodBegin() . '_' . $report->getPeriodEnd() . '.xls';

	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: max-age=0, must-revalidate, post-check=0, pre-check=0");
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Type: application/download");
	header("Content-Disposition: attachment;filename={$file}");
	header("Content-Transfer-Encoding: binary");

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
} else {
	pageHeader(dirname(__FILE__) . "/xsl/requestCount.xsl");

	$xmlString = '<srvInfo>';
	$xmlString .= $user->getUserXML();
	$xmlString .= '<CrDateShFrom>' . $report->getPeriodBegin() . '</CrDateShFrom>';
	$xmlString .= '<CrDateShTill>' . $report->getPeriodEnd() . '</CrDateShTill>';
	$xmlString .= '<CrDateShFrom2>' . $report->getAdmissionPeriodBegin() . '</CrDateShFrom2>';
	$xmlString .= '<CrDateShTill2>' . $report->getAdmissionPeriodEnd() . '</CrDateShTill2>';
	$xmlString .= '<RequestType>' . $report->getRequestType() . '</RequestType>';
	$xmlString .= '<ReportType>' . $report->getReportType() . '</ReportType>';
	$xmlString .= getCityXML();
	$xmlString .= '</srvInfo>';
	setXML($xmlString);

	$xmlString = '<dbInfo>';

	$xmlString .= '<RequestTypes>';
	foreach (RequestCollection::getRequestTypes() as $type => $title) {
		$xmlString .= '<Type type="' . $type . '">' . $title . '</Type>';
	}
	$xmlString .= '</RequestTypes>';

	$xmlString .= '<ReportTypes>';
	foreach (RequestCollection::getReportTypes() as $type => $title) {
		$xmlString .= '<Type type="' . $type . '">' . $title . '</Type>';
	}
	$xmlString .= '</ReportTypes>';

	$xmlString .= '<Reports>';
	foreach ($report->getReportData() as $row) {
		$xmlString .= '<Report>';
		foreach ($row as $field => $value) {
			$xmlString .= '<' . $field . '>' . $value . '</' . $field . '>';
		}
		$xmlString .= '</Report>';
	}
	$xmlString .= '</Reports>';

	$xmlString .= '</dbInfo>';
	setXML($xmlString);

	pageFooter();
}
