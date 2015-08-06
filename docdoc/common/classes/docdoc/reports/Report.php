<?php
/**
 * Created by PhpStorm.
 * User: ndunaev
 * Date: 23.09.14
 * Time: 10:24
 */

namespace dfs\docdoc\reports;

use PHPExcel;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Border;


class Report
{
	/**
	 * Заголовок отчёта
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * Параметры столбцов
	 *
	 * @var array
	 */
	protected $_fields = [];

	protected $_styleGeneral = array(
		'font' => array(
			'name'  => 'Arial',
			'size'  => '10',
			'color' => array('rgb' => '353535')
		)
	);

	protected $_styleTh = array(
		'font'      => array(
			'name'  => 'Arial',
			'size'  => '10',
			'bold'  => true,
			'color' => array('rgb' => '000000')
		),
		'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
		),
		'fill'      => array(
			'type'  => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('rgb' => '67bbbc')
		),
		'borders'   => array(
			'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		)
	);

	protected $_styleHead = array(
		'font'      => array(
			'name' => 'Arial',
			'size' => '16',
			'bold' => true
		),
		'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
		)
	);

	protected $_styleStrong = array(
		'font' => array(
			'bold' => true
		)
	);

	protected $_styleMoveRight3 = array(
		'alignment' => array(
			'indent' => '3'
		)
	);

	protected $_styleEven = array(
		'fill' => array(
			'type'  => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('rgb' => 'ffffff')
		)
	);

	protected $_styleOdd = array(
		'fill' => array(
			'type'  => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('rgb' => 'eefbfc')
		)
	);

	protected $_styleRed = array(
		'font' => array(
			'color' => array('rgb' => 'a81010')
		)
	);

	protected $_styleLeft = array(
		'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
		)
	);

	protected $_styleCenter = array(
		'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
		)
	);

	protected $_styleWb = array(
		'borders' => array(
			'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		)
	);

	/**
	 * Данные отчета
	 *
	 * @var array | null
	 */
	protected $_reportData = null;


	/**
	 * Получить данные отчёта
	 *
	 * @return array
	 */
	public function getReportData()
	{
		if ($this->_reportData === null) {
			$this->execute();
		}

		return $this->_reportData;
	}

	/**
	 * Сформировать excel-отчёт
	 *
	 * @param array | null  $fields
	 *
	 * @return \PHPExcel
	 */
	public function getExcelReport($fields = null)
	{
		return $this->excel($this->getReportData(), $fields);
	}


	/**
	 * Запуск формирования отчета
	 *
	 * @throws \CException
	 */
	public function execute()
	{
		throw new \CException('Report execute method not complete');
	}

	/**
	 * Генерация отчета
	 *
	 * @param array $data
	 * @param array | null  $fields
	 *
	 * @throws \Exception
	 * @return \PHPExcel
	 */
	public function excel($data, $fields = null)
	{
		set_time_limit(300);

		if (!is_array($fields)) {
			$fields = array_keys($this->_fields);
		} else {
			$fields = array_intersect($fields, array_keys($this->_fields));
		}

		$objPHPExcel = new PHPExcel();

		$objPHPExcel->getProperties()
			->setCreator("Docdoc.ru")
			->setTitle("Request list");

		$sheet = $objPHPExcel->setActiveSheetIndex(0);

		$sheet->setTitle('Report');
		$sheet->setSelectedCell('A1');

		$sheet->setCellValue('A1', $this->title);
		$sheet->getStyle('A1')->applyFromArray($this->_styleHead);

		$startNum = 3;

		$j = ord('A');
		$lastLetter = 'A';
		foreach ($fields as $name) {
			$field = $this->_fields[$name];
			$lastLetter = chr($j++);
			$sheet->setCellValue($lastLetter . $startNum, $field['title']);
			$sheet->getColumnDimension($lastLetter)->setWidth($field['width']);
		}

		$sheet->mergeCells('A1:' . $lastLetter . '1');

		$sheet->getStyle('A3:' . $lastLetter . '3')->applyFromArray($this->_styleTh);

		$i = 1;
		$n = $startNum;
		foreach ($data as $row) {
			$n++;

			$sheet->getStyle('A' . $n . ':' . $lastLetter . $n)->applyFromArray($this->_styleGeneral);

			$j = ord('A');
			foreach ($fields as $name) {
				$value = null;
				if (array_key_exists($name, $row)) {
					$value = $row[$name];
				}

				$handler =  "setColumnValue_" . $name;
				if (method_exists($this, $handler)) {
					$value = $this->$handler($row);
				}

				$letter = chr($j++);
				$sheet->setCellValue($letter . $n, $value);
			}

			if ($i % 2 == 0) {
				$sheet->getStyle('A' . $n . ':' . $lastLetter . $n)->applyFromArray($this->_styleOdd);
			}

			$sheet->getStyle('A' . $n . ':' . $lastLetter . $n)->applyFromArray($this->_styleWb);

			$i++;
		}

		$sheet->setCellValue('A' . ($n + 1), 'Дата формирования отчета: ' . date('d.m.Y'));
		$sheet->mergeCells('A' . ($n + 1) . ':' . $lastLetter . ($n + 1));

		$sheet->getStyle('A' . $startNum . ':' . $lastLetter . $n)->getAlignment()
			->setWrapText(true)
			->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

		return $objPHPExcel;
	}


	/**
	 * Конвертация даты в число
	 *
	 * @param string $date
	 * @param int $default
	 *
	 * @return int
	 */
	protected function convertDate($date, $default = 0)
	{
		return $date ? strtotime($date) : $default;
	}
}
