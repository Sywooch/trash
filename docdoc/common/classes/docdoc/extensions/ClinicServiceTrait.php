<?php
/**
 * Created by PhpStorm.
 * User: ndunaev
 * Date: 24.02.15
 * Time: 19:34
 */

namespace dfs\docdoc\extensions;
use dfs\docdoc\models\DiagnosticaModel;
use dfs\docdoc\models\SectorModel;

/**
 * Class ClinicServiceTrait
 *
 * Услуги клиники и разбивка этих услуг по столюцам и алфавиту
 *
 * @package dfs\docdoc\extensions
 */
trait ClinicServiceTrait {


	/**
	 * Получение всех услуг
	 *
	 * @param int $clinicId
	 * @return array
	 */
	public function getServices($clinicId, $withDiagnostic = true)
	{
		$services = [];

		$items = SectorModel::model()
			->byClinic($clinicId)
			->simple()
			->cache(86400)
			->findAll();
		foreach ($items as $item) {
			$services[] = [
				'id'    => $item->id,
				'name'  => $item->name,
				'type'  => 'spec',
			];
		}

		if ($withDiagnostic) {
			$items = DiagnosticaModel::model()
				->byClinic($clinicId)
				->onlyParents()
				->cache(86400)
				->findAll();
			foreach ($items as $item) {
				$services[] = [
					'id'    => $item->id,
					'name'  => $item->name,
					'type'  => 'diag',
				];
			}
		}

		return $this->getGroupsByColumn($services);
	}

	/**
	 * Получение элементов, сгруппированных по колонкам
	 *
	 * @param array $elements
	 * @param int $col
	 * @return array
	 */
	public function getGroupsByColumn($elements, $col = 3)
	{
		$data = [];

		$groups = $this->getGroupsByAlphabet($elements);
		$countInCol = ceil(count($groups) / $col);

		$i = 1;
		$cnt = 0;
		foreach ($groups as $key => $group) {
			if ($cnt >= $i * $countInCol) {
				$i++;
			}
			$data[$i][$key] = $group;
			$cnt++;
		}

		return $data;
	}

	/**
	 * Получение элементов, сгруппированных по алфавиту
	 *
	 * @param array $elements
	 * @return array
	 */
	public function getGroupsByAlphabet($elements)
	{
		$data = [];

		foreach ($elements as $item) {
			$firstChar = mb_strtoupper(mb_substr($item['name'], 0, 1));
			$data[$firstChar][] = $item;
		}

		ksort($data);

		return $data;
	}

} 