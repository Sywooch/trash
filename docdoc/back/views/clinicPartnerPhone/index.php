<?php

use dfs\docdoc\back\controllers\ClinicPartnerPhoneController;
use dfs\docdoc\models\ClinicPartnerPhoneModel;
use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\PartnerModel;
use dfs\docdoc\extensions\GroupGridView;

/**
 * @var ClinicPartnerPhoneModel      $model
 * @var ClinicPartnerPhoneController $this
 */
?>

<?php
$this->breadcrumbs = array(
	'Телефоны партнеров для клиник',
);

$this->menu = array(
	array('label' => 'Добавить', 'url' => array('create')),
);

?>

	<div class="span-5 last">
		<div id="sidebar">
			<?php
			$this->beginWidget(
				'zii.widgets.CPortlet',
				array(
					'title' => 'Действия',
				)
			);
			$this->widget(
				'zii.widgets.CMenu',
				array(
					'items'       => $this->menu,
					'htmlOptions' => array('class' => 'operations'),
				)
			);
			$this->endWidget();
			?>
		</div>
	</div>

	<h1>Телефоны партнеров для клиник</h1>

<?php
$this->widget(
	GroupGridView::class,
	array(
		'id'           => 'clinic-partner-phone-grid',
		'dataProvider' => $model->search(),
		'filter'       => $model,
		'ajaxUpdate'   => false,
		'mergeColumns' => array('partner_id'),
		'columns'      => array(
			array(
				'class'  => 'CDataColumn',
				'name'   => 'clinic_id',
				'type'   => 'raw',
				'filter' => CHtml::listData(
					ClinicModel::model()->active()->onlyClinic()->ordered()->findAll(),
					'id',
					'name'
				),
				'value'  => '"<a href=\"" .
					Yii::app()->controller->createUrl(
						"",
						array(CHtml::activeName($data, "clinic_id") => $data->clinic_id)
					)
					. "\"
					title=\"Выбрать все записи по данной клинике\"
					>" . $data->clinic->name . "</a>"',
			),
			array(
				'class'  => 'CDataColumn',
				'name'   => 'partner_id',
				'type'   => 'raw',
				'filter' => CHtml::listData(PartnerModel::model()->ordered()->findAll(), 'id', 'name'),
				'value'  => '"<a href=\"" .
					Yii::app()->controller->createUrl(
						"",
						array(CHtml::activeName($data, "partner_id") => $data->partner_id)
					)
					. "\"
					title=\"Выбрать все записи по данному партнеру\"
					>" . $data->partner->name . "</a>"',
			),
			array(
				'class' => 'CDataColumn',
				'name'  => 'phoneNumber',
				'type'  => 'raw',
				'value' => '$data->phone->getPhone()->prettyFormat("+7 ")',
			),
			array(
				'header'  => 'Телефон клиники',
				'type'  => 'raw',
				'value' => '$data->clinic->phone',
			),
			array(
				'class'    => 'CButtonColumn',
				'template' => '{update}{delete}',
				'buttons'  => array(
					'update' => array(
						'url' => 'Yii::app()->controller->createUrl(
								"update",
								array("id" => $data->clinic_id, "partner_id" => $data->partner_id)
							)',
					),
					'delete' => array(
						'url' => 'Yii::app()->controller->createUrl(
								"delete",
								array("id" => $data->clinic_id, "partner_id" => $data->partner_id)
							)',
					),
				),
			),
		),
	)
);
?>