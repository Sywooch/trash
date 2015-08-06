<?php
use dfs\modules\payments\models\PaymentsAccount;
use dfs\modules\payments\models\PaymentsOperations;
use likefifa\components\helpers\ListHelper;

/**
 * Class MastersController
 */
class MastersController extends SearchController
{

	public function forDefault()
	{
		return $this->forMasters();
	}

	public function getModelClass()
	{
		return 'LfMaster';
	}

	/**
	 * Фильтры
	 * Добавляет кэширование каталога мастеров и страницы мастера
	 *
	 * @return array
	 */
	public function filters()
	{
		return array(
			array(
				'COutputCache + map, mapPoints, loadData, moreWorks',
				'duration'=> Yii::app()->params["cacheTime"],
				'varyByParam'=> array($_SERVER["REQUEST_URI"]),
			),
		);
	}

	/**
	 * Увеличивает баланс мастера
	 * Выводит на экран для AJAX новый баланс
	 */
	public function actionPlus2000()
	{
		if (isset($_POST['master_id'])) {
			if ($master = LfMaster::model()->findByPk($_POST['master_id'])) {
				if (!$master->is_popup) {
					$master->is_popup = $master::IS_POPUP_RECEIVED;
					if ($master->saveAttributes(array('is_popup'))) {
						if (
						PaymentsAccount::model()->findByPk(PaymentsAccount::BONUS_ID)->creditAmount(
							$master->getAccount(),
							Yii::app()->params["bonusMaster"],
							false,
							PaymentsOperations::TYPE_BONUS,
							'Поступил бонус на сумму ' . Yii::app()->params["bonusMaster"] . ' рублей'
						)
						) {
							echo $master->getBalance();
						} else {
							$master->is_popup = 0;
							$master->saveAttributes(array('is_popup'));
						}
					}
				}
			}
		}
	}

	/**
	 * Мастер не принял подарок
	 */
	public function actionNotAcceptedGift()
	{
		if (isset($_POST['master_id'])) {
			if ($master = LfMaster::model()->findByPk($_POST['master_id'])) {
				if (!$master->is_popup) {
					$session = new CHttpSession;
					$session->open();
					$session['is_popup_denied'] = LfMaster::IS_POPUP_DENIED;
				}
			}
		}
	}

	/**
	 * Добавляет дополнительные критерии запроса
	 *
	 * @param string $action действие
	 * @param array  $params параметры
	 *
	 * @return array
	 */
	protected function getAdditionalCriteria($action, $params)
	{
		$criteria = array('with' => array());

		switch ($action) {
			case 'custom':
				$criteria['condition'] = 't.salon_id IS NULL';
				$criteria['with'][] = 'educations';

				break;
		}

		return $criteria;
	}

	/**
	 * Добавляет дополнительное условие для выборки мастеров
	 *
	 * @param string   $action
	 * @param string[] $params
	 *
	 * @return string[]
	 */
	protected function getAdditionalOrCriteria($action, $params)
	{
		$criteria = array();

		switch ($action) {
			case 'map':
				if (!$params['hasDeparture']) {
					list($controller) = Yii::app()->createController('/salons/custom');
					if ($controller && ($salonIds = $controller->getIds($action, $params))) {
						if ($params['service']) {

							$criteria['condition'][] =
								'group.id IN (' . ListHelper::buildIdList($params['service']->specialization->group) . ')';

							$criteria['with'][] = 'group';
						} else {
							if ($params['specialization']) {
								$criteria['condition'][] =
									'group.id IN (' . ListHelper::buildIdList($params['specialization']->group) . ')';
								$criteria['with'][] = 'group';
							}
						}
						$criteria['condition'][] = 't.salon_id IN (' . implode(',', $salonIds) . ')';
					}
				}
				break;

		}
		$criteria['condition'] =
			$criteria
				? implode(' AND ', $criteria['condition'])
				: '';
		return $criteria;
	}

	public function actionOpinionAjax()
	{
		if (isset($_POST['opinion_id'])) {
			$opinion_id = $_POST['opinion_id'];
			$cookie_name = 'likefifa_opinion_' . $opinion_id;

			if (!isset(Yii::app()->request->cookies[$cookie_name])) {

				$model = LfOpinion::model()->findByPk($opinion_id);

				/*$opinion_data = Yii::app()->db->createCommand()
					->select('*')
					->from('lf_opinion')
					->where('id=:id', array(':id'=> $opinion_id ))
					->queryRow();
				$yes = $opinion_data['yes'];
				$no = $opinion_data['no'];*/

				if (isset($_POST['yes'])) {
					$model->yes = $model->yes + 1;
				}
				if (isset($_POST['no'])) {
					$model->no = $model->no + 1;
				}
				if (!$model->tel) {
					$model->tel = 'не указан';
				}

				if ($model->save()) {

					Yii::app()->request->cookies[$cookie_name] = new CHttpCookie($cookie_name, 1);

					if (isset($_POST['yes'])) {
						echo $model->yes;
					}
					if (isset($_POST['no'])) {
						echo $model->no;
					}

				}

			}

		}
	}

	public function actionIndex($model)
	{
		if (!$model) {
			throw new CHttpException(404, 'Master not found');
		}

		$searchUrl =
			!empty(Yii::app()->session['searchUrl'])
			&& (
				empty($_SERVER['HTTP_REFERER'])
				|| (
					strpos($_SERVER['HTTP_REFERER'], '/masters/') !== false
					|| strpos($_SERVER['HTTP_REFERER'], '/master/') !== false
				)
			)
				? Yii::app()->session['searchUrl'] . '#master' . $model->id
				: null;

		$opinion = new LfOpinion;

		$this->pageTitle = $model->getFullName();
		$this->render('index', compact('model', 'opinion', 'searchUrl'));
	}

	protected function getModelPlurals()
	{
		return array(
			'мастер',
			'мастера',
			'мастеров',
		);
	}

	public function actionUpdateNewAppointment()
	{
		if (isset($_POST['master_id'])) {
			$master_id = $_POST['master_id'];
			$lf_appointment = Yii::app()->db->createCommand()
				->select('COUNT(id)')
				->from('lf_appointment')
				->where('master_id=:master_id AND status=:status', array(':master_id' => $master_id, ':status' => 0))
				->queryRow();
			echo $lf_appointment["COUNT(id)"];
		}
	}

	public function actionGetNewAppointment()
	{
		if (isset($_POST['master_id'])) {
			$master_id = $_POST['master_id'];
			$lf_appointment = Yii::app()->db->createCommand()
				->select('id')
				->from('lf_appointment')
				->where('master_id=:master_id AND status=:status', array(':master_id' => $master_id, ':status' => 0))
				->order('id DESC')
				->queryRow();
			if ($lf_appointment) {
				$data = LfAppointment::model()->findByPk($lf_appointment['id']);

				if ($data->status == 0 || $data->status == 60 || $data->status == 10) {
					$status = 'new';
				} elseif ($data->status == 40) {
					$status = 'apply';
				} elseif ($data->status == 20 || $data->status == 30) {
					$status = 'cancel';
				} else {
					$status = 'completed';
				}

				echo
					'
										<tr>
											<td class="first" style="' .
					($data->oneHourLeft
						? "width:40px; text-align: center; background-color: #fae4f7; border-color: #fae4f7; border-bottom-color: #dad8db;"
						: "width:40px; text-align: center;") .
					'">' .
					$data->id .
					'</td>
											<td style="' .
					($data->oneHourLeft
						? "width:90px; text-align: center; background-color: #fae4f7; border-color: #fae4f7; border-bottom-color: #dad8db;"
						: "width:90px; text-align: center;") .
					'">' .
					date("d.m.y", $data->getNumericCreated()) .
					'</td>
											<td><div style="' .
					($data->oneHourLeft
						? "width:130px; background-color: #fae4f7; border-color: #fae4f7; border-bottom-color: #dad8db; white-space: pre-wrap; white-space: -moz-pre-wrap; white-space: -pre-wrap; white-space: -o-pre-wrap; word-wrap: break-word;"
						: "width:130px; white-space: pre-wrap; white-space: -moz-pre-wrap; white-space: -pre-wrap; white-space: -o-pre-wrap; word-wrap: break-word;") .
					'">' .
					$data->name .
					'</div></td>
											<td style="' .
					($data->oneHourLeft
						? "width:120px; text-align: center; white-space: nowrap; background-color: #fae4f7; border-color: #fae4f7; border-bottom-color: #dad8db;"
						: "width:120px; text-align: center; white-space:nowrap;") .
					'">' .
					$data->phone .
					'</td>
											<td style="' .
					($data->oneHourLeft
						? "width:130px; text-align: center; font-weight:bold; white-space:nowrap; background-color: #fae4f7; border-color: #fae4f7; border-bottom-color: #dad8db;"
						: "width:130px; text-align: center; font-weight:bold; white-space:nowrap;") .
					'">' .
					($data->getPrice() ? $data->getPriceFormatted() . " руб." : "") .
					'</td>
											<td style="' .
					($data->oneHourLeft
						? "width:130px; background-color: #fae4f7; border-color: #fae4f7; border-bottom-color: #dad8db;"
						: "width:130px;") .
					'">' .
					($data->service
						? $data->service->name
						: ($data->specialization ? $data->specialization->name
							: "")) .
					'</td>
										';

				if ($status == 'apply') {
					echo
						'
												<td style="' .
						($data->oneHourLeft
							? "width:110px; background-color: #fae4f7; border-color: #fae4f7; border-bottom-color: #dad8db;"
							: "width:110px;") .
						'">' .
						date("H:i d.m.Y", $data->date) .
						'<br>
													<div style="position:relative">
														<div class="popup-apply popup-note"></div>
														<a href="#" class="apply-button" data-id="' .
						$data->id .
						'" data-status="apply">редактировать</a>
													</div>
												</td>
											';
				} elseif ($status == 'cancel' || $status == 'completed') {
					echo
						'
												<td style="' .
						($data->oneHourLeft
							? "width:110px; background-color: #fae4f7; border-color: #fae4f7; border-bottom-color: #dad8db;"
							: "width:110px;") .
						'">' .
						date("H:i <br>d.m.Y", $data->NumericChanged) .
						'</td>
											';
				} else {
					echo
						'
												<td style="' .
						($data->oneHourLeft
							? "width:130px; background-color: #fae4f7; border-color: #fae4f7; border-bottom-color: #dad8db;"
							: "width:110px;") .
						'">Актуально до<br>' .
						date("H:i d.m.y", $data->NumericCreated + 2 * 3600) .
						'</td>
											';
				}

				echo
					'
											<td><div style="width: 120px;">' .
					($data->departure
						? "<div style=\"word-wrap: break-word; width: 120px;\"><div class=\"prof-appointment-check\">Да</div>$data->address</div>"
						: "<div style=\"width: 120px;\">Нет</div>") .
					'</div></td>

											<td style="' .
					($data->oneHourLeft
						? "background-color: #fae4f7; border-color: #fae4f7; border-bottom-color: #dad8db;" : "") .
					'" class="button-column">
												<div style="position:relative">
													<div class="popup-abuse popup-note popup-apply"></div>
													<a class="apply-button" data-id="' .
					$data->id .
					'" data-status="new" title="принять" href="#">принять</a>
												</div>
												<div style="position:relative">
													<div class="popup-abuse popup-note popup-cancel"></div>
													<a class="cancel-button" data-id="' .
					$data->id .
					'" data-status="new" title="отклонить" href="#">отклонить</a>
												</div>
											</td>

										</tr>
									';
			}
		}
	}

	/**
	 * Получает количество записей и выводит на экран
	 *
	 * @return void
	 */
	public function actionCount()
	{
		$params = $this->getSearchParams();
		$this->getCount($params);
	}

	/**
	 * Переворачивает аватарку
	 * Выводит на экран ссылку на новое изображение
	 *
	 * @return void
	 */
	public function actionRotateAvatar()
	{
		$id = Yii::app()->request->getQuery("id");
		$direction = Yii::app()->request->getQuery("direction");
		if ($id && $direction) {
			$model = LfMaster::model()->findByPk($id);
			if ($model) {
				echo $model->rotateAvatar($direction);
			}
		}
	}

	/**
	 * Перенаправляет со старых страниц мастеров на новые
	 * с /master на /masters
	 *
	 * @return void
	 */
	public function actionOldMaster()
	{
		$rewriteName = Yii::app()->request->getQuery("rewriteName");
		if ($rewriteName) {
			$url = $this->createUrl("masters/index", compact("rewriteName"));
			$this->redirect($url);
		}
	}

	/**
	 * Загружает все работы мастера
	 *
	 * @param integer $id
	 *
	 * @throws CHttpException
	 */
	public function actionMoreWorks($id) {
		$model = LfMaster::model()->with(['works'])->findByPk($id);
		if(!$model) {
			throw new CHttpException(404, 'Мастер не найден');
		}

		for($i = 5; $i < count($model->works) && isset($model->works[$i]); $i++) {
			$this->renderPartial(
				'partials/_work',
				[
					'data'  => $model->works[$i],
					'model' => $model,
					'index' => $i
				]
			);
		}
	}
}