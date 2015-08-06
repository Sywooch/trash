<?php

namespace dfs\docdoc\api\rest;

use dfs\docdoc\models\DoctorClinicModel;
use dfs\docdoc\models\DoctorModel;
use dfs\docdoc\models\RequestModel;

	/*
	 * To change this template, choose Tools | Templates
	 * and open the template in the editor.
	 */

/**
 * Description of api_v1
 *
 * @author Danis
 */
class API_v101 extends API_v100
{

	public $jsonCyrillicMode = true;

	/**
	 * Получение методов
	 *
	 * @return array
	 */
	public function getMethods() {
		$methods = parent::getMethods();
		$methods['dc'] = 'dcView';
		$methods['dc/list'] = 'dcList';
		$methods['dc/count'] = 'dcCount';

		return $methods;
	}

	/**
	 * Получение списка диагностических центров
	 *
	 * @return array
	 */
	protected function dcList()
	{
		$this->params['isDiagnostic'] = 'yes';

		return parent::clinicList();
	}

	/**
	 * Получение списка клиник
	 *
	 * @return array
	 */
	protected function clinicList()
	{
		$params = $this->params;

		$params['isClinic'] = 'yes';
		$params['withDoctors'] = true;

		return $this->_clinicList($params);
	}

	// Получение кол-ва диагностических центров
	protected function dcCount()
	{
		$params = $this->params;
		$params['isDiagnostic'] = 'yes';
		$count = \Clinic::getCount($params);
		$total = \Clinic::getCount(
			array(
				'city'         => $params['city'],
				'isDiagnostic' => 'yes',
			)
		);

		return array('Total' => $total, 'ClinicSelected' => $count);
	}

	// Получение кол-ва клиник
	protected function clinicCount()
	{
		$params = $this->params;
		$params['isClinic'] = 'yes';
		$params['withDoctors'] = true;
		$count = \Clinic::getCount($params);
		$total = \Clinic::getCount(
			array(
				'city'        => $params['city'],
				'isClinic'    => 'yes',
				'withDoctors' => true
			)
		);

		return array('Total' => $total, 'ClinicSelected' => $count);
	}

	protected function dcView()
	{
		return parent::clinicView();
	}

	/**
	 * маппинг доктора в json
	 *
	 * @param DoctorModel $row
	 *
	 * @return array
	 */
	protected function doctorMapping($row)
	{
		$data = parent::doctorMapping($row);

		$data['Description'] = self::clearText($row->text, true);
		$education = $row->getEducation();
		$educationText = '';
		if (!empty($education)) {
			$educationText = "<ul><li>" . implode('</li><li>', $education) . "</li></ul>";
		}

		$data['TextEducation'] = $educationText ?: self::clearText($row->text_education, true);
		$data['TextAssociation'] = self::clearText($row->text_association, true);
		$data['TextDegree'] = self::clearText($row->text_degree, true);
		$data['TextSpec'] = self::clearText($row->text_spec, true);
		$data['TextCourse'] = self::clearText($row->text_course, true);
		$data['TextExperience'] = self::clearText($row->text_experience, true);

		return $data;
	}



	protected function clinicView()
	{
		$params = $this->params;
		$data = array();

		$id = intval($params['id']);
		$clinic = new \Clinic($id);

		$clinic = $clinic->data;
		if (!empty($clinic)) {
			if ($clinic['isClinic'] == 'yes') {
				$data['Id'] = $clinic['id'];
				$data['Name'] = $clinic['name'];
				$data['ShortName'] = $clinic['short_name'];
				$data['RewriteName'] = $clinic['rewrite_name'];
				$data['Url'] = $clinic['url'];
				$data['City'] = $clinic['city'];
				$data['Street'] = $clinic['street'];
				$data['Description'] = $clinic['description'];
				$data['House'] = $clinic['house'];
				if (!empty($clinic['asterisk_phone'])) {
					$data['Phone'] = '+' . $clinic['asterisk_phone'];
				} else {
					$data['Phone'] = '+' . $clinic['phone'];
				}
				$data['Logo'] = "http://docdoc.ru/upload/kliniki/logo/" . $clinic['logoPath'];

				$sql = "SELECT t1.id
                        FROM doctor t1
                        INNER JOIN doctor_4_clinic t2 ON t2.doctor_id=t1.id and t2.type = " . DoctorClinicModel::TYPE_DOCTOR . "
                        WHERE t1.status=3 AND t2.clinic_id=" . $clinic['id'];
				$result = query($sql);
				while ($row = fetch_object($result)) {
					$data['Doctors'][] = $row->id;
				}
			}
		}

		return array('Clinic' => array($data));
	}

}
