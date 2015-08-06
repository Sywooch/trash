<?php

use dfs\docdoc\back\controllers\PartnerController;
use dfs\docdoc\models\PartnerModel;
use dfs\docdoc\models\CityModel;
use dfs\docdoc\extensions\CheckBoxColumn;
use dfs\docdoc\models\QueueModel;

/**
 * @var PartnerModel $model
 * @var PartnerController $this
 */
?>

<?php
$this->breadcrumbs = [
	'Партнеры',
];

$this->menu = [
	['label' => 'Добавить партнера', 'url' => ['create']],
	['label' => 'Стоимости заявок для партнера', 'url' => $this->createUrl("partnerCost/index")]
];

?>

<h1>Партнеры</h1>

<?php
$this->widget(
	'zii.widgets.grid.CGridView',
	[
		'id'           => 'partner-grid',
		'dataProvider' => $model->search(),
		'filter'       => $model,
		'ajaxUpdate'   => false,
		'columns'      => [
			"id",
			"name",
			"login",
			"contact_name",
			"contact_phone",
			"contact_email",
			[
				'class'  => CDataColumn::class,
				'name'   => 'city_id',
				'type'   => 'raw',
				'filter' => CHtml::listData(CityModel::model()->findAll(), 'id_city', 'title'),
				'value'  => '$data->getCityTitle()',
			],
			[
				'class'  => CDataColumn::class,
				'type'  => 'raw',
				'name'   => 'request_kind',
				'value'  => '"<div style=\"width: 20px; height: 20px; margin: auto; background: url(/img/icon/req_kind_$data->request_kind.png) no-repeat\"></div>"',
			],
			[
				'class'  => CheckBoxColumn::class,
				'name'   => 'use_special_price',
				'filter' => [1 => 'Да', 0 => 'Нет'],
				'selectableRows' => 0,
				'checked' => '$data->use_special_price',
			],
			[
				'class'  => CheckBoxColumn::class,
				'name'   => 'offer_accepted',
				'filter' => [1 => 'Да', 0 => 'Нет'],
				'checked' => '$data->offer_accepted',
				'selectableRows' => 0,
			],
			[
				'class'  => CheckBoxColumn::class,
				'name'   => 'send_sms',
				'filter' => [1 => 'Да', 0 => 'Нет'],
				'checked' => '$data->send_sms',
				'selectableRows' => 0,
			],
			[
				'class'  => CheckBoxColumn::class,
				'name'   => 'show_watermark',
				'filter' => [1 => 'Да', 0 => 'Нет'],
				'checked' => '$data->show_watermark',
				'selectableRows' => 0,
			],
			[
				'class' => CDataColumn::class,
				'name'  => 'offer_accepted_timestamp',
				'type'  => 'raw',
				'value' => '$data->offer_accepted_timestamp',
			],
			[
				'class' => CDataColumn::class,
				'name'  => 'phone_queue',
				'type'  => 'raw',
				'filter' => QueueModel::getQueueNames(),
				'value' => '$data->phone_queue',
			],
			[
				'name'        => 'phoneNumbers',
				'type'        => 'raw',
				'filter'      => false,
				'value'       => '$data->getPhoneNumbers()',
				'htmlOptions' => ['style' => 'width: 105px;'],
			],
			[
				'class'  => CDataColumn::class,
				'header' => 'Заявки партнера',
				'type'   => 'raw',
				'value'  => 'CHtml::link("Заявки", "/request/partner.htm?partner[]=" . $data->id)',
			],
			[
				'class'  => CDataColumn::class,
				'name'   => 'phones_for_clinic',
				'type'   => 'raw',
				'filter' => false,
				'value'  => '"<a href=\"" .
					Yii::app()->createUrl(
						"clinicPartnerPhone/index", array(
							CHtml::activeName(new dfs\docdoc\models\ClinicPartnerPhoneModel, "partner_id") => $data->id
						)
					)
					. "\">Перейти в справочник</a>"',
			],
			[
				'class'  => CDataColumn::class,
				'name'   => 'widget',
				'type'   => 'raw',
				'filter' => false,
				'value'  => '"<a href=\"" .
					Yii::app()->createUrl(
						"partnerWidget/index", array(
							CHtml::activeName(new dfs\docdoc\models\PartnerWidgetModel, "partner_id") => $data->id
						)
					)
					. "\">Виджеты</a>"',
			],
			[
				'class'    => 'CButtonColumn',
				'template' => '{update}',
			],
		],
	]
);
?>
