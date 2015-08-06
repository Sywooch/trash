<?php
namespace dfs\docdoc\reports;

use dfs\docdoc\objects\google\calls\MissedCalls;
use SebastianBergmann\Exporter\Exception;

/**
 * Class MissedCallsReport
 *
 * Отчет, список неудачных звонков в Call Center
 * @package dfs\docdoc\reports
 */
final class MissedCallsReport extends MissedCallsRawReport
{
	/**
	 * @var array
	 */
	private $_calls = array();

	/**
	 * Получение модели
	 *
	 * @return \dfs\docdoc\objects\google\BigQuery|MissedCalls
	 */
	public function getBqModel()
	{
		return new MissedCalls();
	}

	/**
	 * Генерация отчета
	 *
	 * @return array
	 */
	public function generate()
	{
		$this->_calls = $this->loadCalls();

		foreach ($this->getData() as $row) {
			$this->addData($row);
		}

	}

	/**
	 * Данные для отчета
	 *
	 * @return array
	 */
	public function getData()
	{
		$result= [];

		foreach ($this->_calls as $item) {

			$replacedPhonesKey = join('_', explode(', ', $item['clinic_phone']));
			$key = (is_null($item['clinic_id']) ? 'not_found' : $item['clinic_id']) . $replacedPhonesKey;

			if (!isset($rows[$key])) {
				$rows[$key] = [
					'date'  => date('Y-m-d', strtotime($item['start_time'])),
					'clinic_id' => $item['clinic_id'],
					'clinic_name' => $item['clinic_name'],
					'clinic_phone' => $item['clinic_phone'],
					'success' => 0,
					'failed' => 0,
				];
			}

			$rows[$key]['numbers'][$item['ani']] = true;

			if ($item['is_lost'] == 'True') {
				$rows[$key]['failed']++;
			} else {
				$rows[$key]['success']++;
			}
		}

		// Сортировка
		uasort($rows, function($a, $b) {
			if ($a['failed'] == $b['failed']) {
				$percentA = $a['failed'] / ($a['failed'] + $a['success']) * 100;
				$percentB = $b['failed'] / ($b['failed'] + $b['success']) * 100;

				if ($percentA == $percentB) {
					$totalA = $a['failed'] + $a['success'];
					$totalB = $b['failed'] + $b['success'];

					if ($totalA == $totalB) {
						return 0;
					}

					return ($totalA > $totalB) ? -1 : 1;
				}

				return ($percentA > $percentB) ? -1 : 1;
			}

			return ($a['failed'] > $b['failed']) ? -1 : 1;

		});

		// Формируем результат
		foreach ($rows as $row) {
			$tmp = array_merge($row, [
				'unique' => count($row['numbers']),
				'percentage_of_failed' => round($row['failed'] / ($row['failed'] + $row['success']) * 100),
				'calls' => $row['failed'] + $row['success'],
			]);
			unset($tmp['numbers']);
			$result[] = $tmp;
		}

		return $result;
	}

	/**
	 * Установка звонков
	 *
	 * @param $calls
	 */
	public function setCalls($calls)
	{
		$this->_calls = $calls;
	}
}