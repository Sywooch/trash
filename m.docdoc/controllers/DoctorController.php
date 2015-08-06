<?php

use dfs\components\Controller;

/**
 * Class SearchController
 */
class DoctorController extends Controller
{
	/**
	 * Полная информация о враче
	 *
	 * @param string $alias
	 *
	 * @throws CHttpException
	 */
	public function actionDetail($alias)
	{
		$session = new CHttpSession;
		$session->open();

		$apiDto = new ApiDto();

		if (!$doctor = $apiDto->getDoctorByAlias($alias)) {
			throw new CHttpException(404, 'Доктор не найден');
		}

		$host = Yii::app()->city->getMainSiteUrl();

		$city = Yii::app()->city->getModel();
		$clinic = $doctor->getFirstClinic();

		$eventParams = [
			'Spec'       => $doctor->getAllSpecialityString(),
			'Clinic'     => $clinic ? $clinic->getName() : null,
			'Metro'      => $doctor->getAllStationsString(),
			'Area'       => null,
			'Name'       => $doctor->getName(),
			'Amount'     => $doctor->getPrice(),
			'Price'      => $doctor->getSpecialPrice() > 0 ? $doctor->getSpecialPrice() : $doctor->getPrice(),
			'Discount'   => $doctor->getSpecialPrice() > 0,
			'Reviews'    => count($doctor->getReviews()),
			'Rating'     => $doctor->getRating(),
			'Experience' => intval($doctor->getExperienceYear()),
			'Awards'     => $doctor->getDegree(),
			'Photo'      => $doctor->getImg() != '',
			'City'       => $city->getName(),
			'DocID'      => $doctor->getId(),
			'ClinID'     => $clinic ? $clinic->getId() : null,
			'Url'        => $host . '/doctor/' . $doctor->getAlias(),
			'PhotoUrl'   => $doctor->getImg(),
		];
		Yii::app()->mixpanel->addTrack('DoctorPage', $eventParams);

		$refURL = Yii::app()->session['back_url_search'];

		if (!$refURL) {
			$stations = $doctor->getStations();
			$specialities = $doctor->getSpecialities();
			$station = isset($stations[0]) ? $stations[0] : null;
			$speciality = isset($specialities[0]) ? $specialities[0] : null;

			if ($speciality) {
				$refURL = '/doctor/' . $speciality->getAlias() . '/' . ($station ? $station->getAlias() . '/' : '');
			} elseif ($station) {
				$refURL = '/search/stations/' . $station->getId() . '/';
			}
		}

		$this->render(
			'detail',
			[
				'doctor' => $doctor,
				'city'   => $city,
				'refURL' => $refURL,
			]
		);
	}

	/**
	 * Отправить заявку
	 */
	public function actionRequest()
	{
		$apiDto = new ApiDto();

		$doctorId = isset($_GET['doctor']) ? $_GET['doctor'] : '';

		if (!$doctor = $apiDto->getDoctorById($doctorId)) {
			throw new CHttpException(404, 'Доктор не найден');
		}

		if (!$doctor->isActive()) {
			$this->redirect($this->createUrl("doctor/detail", ["alias" => $doctor->getAlias()]));
		}

		$this->render('request', [
				'doctor' => $doctor,
				'clinic' => $doctor->getFirstClinic(),
				'city'   => Yii::app()->city->getModel()
			]);
	}

	/**
	 * Отправить заявку
	 */
	public function actionRequestSend()
	{
		$apiDto = new ApiDto();

		$requestModel = new RequestModel();
		$requestModel->setName(Yii::app()->request->getPost('name'));
		$requestModel->setPhone(Yii::app()->request->getPost('phone'));
		$requestModel->setDoctor(Yii::app()->request->getPost('doctor'));
		$requestModel->setComment(Yii::app()->request->getPost('comment'));
		$requestModel->setClinic(Yii::app()->request->getPost('clinic'));

		$requestModel = $apiDto->sendRequest($requestModel);

		$this->layout = false;
		header('Content-type: application/json');
		echo json_encode([
				'status'  => $requestModel->getStatus(),
				'message' => $requestModel->getMessage(),
				'created' => time(),
			]);
		Yii::app()->end();
	}
} 