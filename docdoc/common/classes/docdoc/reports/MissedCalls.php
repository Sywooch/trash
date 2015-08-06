<?php

namespace dfs\docdoc\reports;

use dfs\docdoc\objects\google\calls\MissedCallsRaw;
use PHPExcel;

/**
 * Class MissedCalls
 * @package dfs\docdoc\reports
 */
class MissedCalls extends Report
{
	/**
	 * Генерация отчета
	 *
	 * @param array $data
	 * @param array $fields
	 *
	 * @return PHPExcel
	 * @throws \Exception
	 */
	public function excel($data, $fields = [])
	{
		// meta
		$title = 'Отчет неудавшихся звонков в КЦ за ' . date('d.m.Y', strtotime('-1 day', time()));

		$meta = [
			'A' => ['title' => 'ID Клиники', 'width' => 15],
			'B' => ['title' => 'Название Клиники', 'width' => 40],
			'C' => ['title' => 'Подменный телефон', 'width' => 25],
			'D' => ['title' => 'Звонки', 'width' => 15],
			'E' => ['title' => 'Уникальные', 'width' => 15],
			'F' => ['title' => 'Успешные', 'width' => 15],
			'G' => ['title' => 'Не успешные', 'width' => 15],
			'H' => ['title' => '% не успешных', 'width' => 15]
		];

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Docdoc.ru")->setTitle($title);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $title);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->applyFromArray($this->_styleHead);
		$objPHPExcel->getActiveSheet()->mergeCells('A1:H1')->mergeCells('A2:H2');
		$objPHPExcel->getActiveSheet()->getStyle('A3:H3')->applyFromArray($this->_styleTh);

		foreach ($meta as $key => $value) {
			$objPHPExcel->getActiveSheet()
				->getColumnDimension($key)
				->setWidth($value['width']);

			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($key . '3', $value['title']);
		}

		$index  = 1;
		$offset = 3;

		foreach ($data as $row) {
			$objPHPExcel
				->setActiveSheetIndex(0)
				->setCellValue('A' . ($index + $offset), $row['clinic_id'])
				->setCellValue('B' . ($index + $offset), $row['clinic_name'])
				->setCellValue('C' . ($index + $offset), $row['clinic_phone'])
				->setCellValue('D' . ($index + $offset), $row['failed'] + $row['success'])
				->setCellValue('E' . ($index + $offset), $row['unique'])
				->setCellValue('F' . ($index + $offset), $row['success'])
				->setCellValue('G' . ($index + $offset), $row['failed'])
				->setCellValue('H' . ($index + $offset), round($row['failed'] / ($row['failed'] + $row['success']) * 100));

			$index++;
		}

		return $objPHPExcel;
	}
}
