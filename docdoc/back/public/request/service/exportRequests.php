<?php
use dfs\docdoc\helpers\DomHelper;

require_once dirname(__FILE__) . "/../../include/common.php";
require_once __DIR__ . "/../../lib/php/RequestInterface.php";
require_once dirname(__FILE__) . "/../../request/php/requestLib.php";
require_once dirname(__FILE__) . "/../../lib/php/models/DocRequest.php";
require_once dirname(__FILE__) . "/../../lib/php/style4Excel.php";


$requestParams = getRequestParams();
$params = $requestParams['params'];
$params["withPager"] = false;
$filterParams = $requestParams['filters'];

$interface = new RequestInterface($filterParams['typeView']);
$user = new user();
$user->checkRight4page(array('ADM', 'SOP'), 'simple');

/* Получение XML */
$xmlString = "<Root>";
$xmlString .= "<Reports>";
$xmlString .= getStatus4RequestXML();
$xmlString .= getKinds4RequestXML();
$xmlString .= getType4RequestXML();
$xmlString .= getCityListXML();
$xmlString .= getSourceType4RequestXML();
$xmlString .= getRequestListXML($params, getCityId());
$xmlString .= "</Reports>";
$xmlString .= "</Root>";
/* Получение XML */

if (!empty($_GET["crDateShFrom"]) || !empty($_GET["crDateShTill"])) {
	$dateText = "Дата обращения";
	if (!empty($_GET["crDateShFrom"])) {
		$dateText .= " с " . $_GET["crDateShFrom"];
	}
	if (!empty($_GET["crDateShTill"])) {
		$dateText .= " по " . $_GET["crDateShTill"];
	}
} else {
	$dateText = "Все заявки";
}

/* Шапка таблицы */
$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("Docdoc.ru")->setTitle("Request analize by month");

$sheet = $objPHPExcel->setActiveSheetIndex(0);

$sheet->setCellValue('A1', "Список заявок");
$sheet->getStyle('A1')->applyFromArray($Head);
$sheet->mergeCells('A1:P1');
$sheet->setCellValue("A2", $dateText);
$sheet->mergeCells("A2:P2");

$currentRow = 3;
$nextRow = $currentRow + 1;

$columns = [
	'A' => [ 'title' => 'Id', 'width' => 10 ],
	'B' => [ 'title' => 'Создана', 'subColumns' => [
		'B' => [ 'title' => 'дата', 'width' => 10 ],
		'C' => [ 'title' => 'чч:мм', 'width' => 10 ],
	]],
	'D' => [ 'title' => 'Источник' ],
	'E' => [ 'title' => 'Вид' ],
	'F' => [ 'title' => 'Возможный источник заявок', 'width' => 30 ],
	'G' => [ 'title' => 'Клиент', 'width' => 50, 'subColumns' => [
		'G' => [ 'title' => 'фамилия имя', 'width' => 50 ],
		'H' => [ 'title' => 'телефон', 'width' => 20 ],
	]],
	'I' => [ 'title' => 'Клиника', 'width' => 50 ],
	'J' => [ 'title' => 'ФИО врача', 'width' => 50 ],
	'K' => [ 'title' => 'Специализация', 'width' => 25 ],
	'L' => [ 'title' => 'Прием', 'width' => 10 ],
	'M' => [ 'title' => 'Коментарий', 'width' => 100 ],
	'N' => [ 'title' => 'Оператор', 'width' => 30 ],
	'O' => [ 'title' => 'Прод.', 'width' => 18 ],
	'P' => [ 'title' => 'Статус', 'width' => 25 ],
];
$lastLetter = 'P';

if ($filterParams['typeView'] === 'partners') {
	$columns['Q'] = [ 'title' => 'Статус для партнёра', 'width' => 25 ];
	$columns['R'] = [ 'title' => 'Статус биллинга', 'width' => 25 ];
	$columns['S'] = [ 'title' => 'Стоимость для партнёра', 'width' => 25 ];
	$columns['T'] = [ 'title' => 'Город', 'width' => 25 ];
	$lastLetter = 'T';
}

foreach ($columns as $letter => $column) {
	$cell = $letter . $currentRow;
	$nextCell = $letter . $nextRow;

	if (!empty($column['subColumns'])) {
		$subLetters = array_keys($column['subColumns']);
		$nextCell = end($subLetters) . $currentRow;
	}

	$sheet->setCellValue($cell, $column['title']);
	$sheet->mergeCells($cell . ':' . $nextCell);
	$sheet->getStyle($cell)->applyFromArray($TH);
	$sheet->getStyle($nextCell)->applyFromArray($TH);

	if (isset($column['width'])) {
		$sheet->getColumnDimension($letter)->setWidth($column['width']);
	}

	if (!empty($column['subColumns'])) {
		foreach ($column['subColumns'] as $l => $c) {
			$subCell = $l . $nextRow;
			$sheet->setCellValue($subCell, $c['title']);
			if (isset($c['width'])) {
				$sheet->getColumnDimension($l)->setWidth($c['width']);
			}
			$sheet->getStyle($subCell)->applyFromArray($TH);
		}
	}
}
/* Шапка таблицы */

/* Заявки */
$doc = new DOMDocument('1.0', 'UTF-8');
if ($doc->loadXML($xmlString)) {
	$xml = new SimpleXMLElement($xmlString);
} else {
	echo "не XML";
}

$LineList = $xml->Reports->RequestList;
$i = 0;
$currentRow = $nextRow + 1;

if ($LineList) {
	if (count($LineList->Element) > 0) {
		$statuses = DocRequest::getStatusNames();

		foreach ($LineList->Element as $item) {
			$commentText = "";
			foreach ($item->CommentList->Element as $comment) {
				if ($comment->Type == \dfs\docdoc\models\RequestHistoryModel::LOG_TYPE_COMMENT) {
					$commentText = $comment->Text;
					break;
				}
			}

			$sheet->getStyle('A' . ($i + $currentRow) . ":" . $lastLetter . ($i + $currentRow))
				->applyFromArray(
					$general
				);
			$sheet
				->setCellValue('A' . ($i + $currentRow), $item->Id)
				->setCellValue('B' . ($i + $currentRow), strval($item->CrDate))
				->setCellValue('C' . ($i + $currentRow), strval($item->CrTime))
				->setCellValue(
					'D' . ($i + $currentRow),
					DomHelper::searchElt($xml->Reports->SourceTypeDict, "id", strval($item->SourceType))
				)
				->setCellValue(
					'E' . ($i + $currentRow),
					DomHelper::searchElt($xml->Reports->KindDict, "id", strval($item->Kind))
				)
				->setCellValue(
					'F' . ($i + $currentRow),
					DomHelper::searchElt($xml->Reports->TypeDict, "id", strval($item->Type))
				)
				->setCellValue('G' . ($i + $currentRow), strval($item->Client))
				->setCellValue('H' . ($i + $currentRow), strval($item->ClientPhone))
				->setCellValue('I' . ($i + $currentRow), strval($item->ClinicName))
				->setCellValue('J' . ($i + $currentRow), strval($item->Doctor))
				->setCellValue('K' . ($i + $currentRow), strval($item->SpecName))
				->setCellValue('L' . ($i + $currentRow), strval($item->AppointmentDate))
				->setCellValue('M' . ($i + $currentRow), strval($commentText))
				->setCellValue('N' . ($i + $currentRow), strval($item->Owner))
				->setCellValue('O' . ($i + $currentRow), strval($item->Duration))
				->setCellValue(
					'P' . ($i + $currentRow),
					DomHelper::searchElt($xml->Reports->StatusDict, "id", strval($item->Status))
				);

			if ($filterParams['typeView'] === 'partners') {
				$cityName = "";
				foreach ($xml->Reports->CityList->Element as $city) {
					if (intval($city->Id) == intval($item->CityId)) {
						$cityName = strval($city->Name);
					}
				}
				$sheet
					->setCellValue('Q' . ($i + $currentRow), strval($item->PartnerStatus))
					->setCellValue('R' . ($i + $currentRow), strval($item->BillingStatus))
					->setCellValue('S' . ($i + $currentRow), strval($item->PartnerCost))
					->setCellValue('T' . ($i + $currentRow), $cityName);
			}

			if ($i % 2 == 0) {
				$sheet
					->getStyle('A' . ($i + $currentRow) . ":" . $lastLetter . ($i + $currentRow))
					->applyFromArray($odd);
			}

			$sheet
				->getStyle('A' . ($i + $currentRow) . ":" . $lastLetter . ($i + $currentRow))
				->applyFromArray($wb);

			$i++;
		}

	}
}
/* Заявки */

/* Подвал таблицы */
$sheet->setCellValue('A' . ($i + $currentRow), strval("Всего заявок: {$i}"));
$sheet->mergeCells("A" . ($i + $currentRow) . ":" . $lastLetter . ($i + $currentRow));
/* Подвал таблицы */

/* Сохранение файла */
$sheet->setTitle('Report');

$file = "requests.xls";
$filename = dirname(__FILE__) . "/../../_reports/" . $file;

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save($filename);
chmod($filename, FILE_MODE);

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: max-age=0, must-revalidate, post-check=0, pre-check=0");
header('Content-Type: application/vnd.ms-excel');
header("Content-Type: application/download");
header("Content-Disposition: attachment;filename={$file}");
header("Content-Transfer-Encoding: binary");

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
/* Сохранение файла */
