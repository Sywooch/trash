<?php

use dfs\docdoc\models\CityModel;

/**
 * @var dfs\docdoc\back\controllers\ClinicController $this
 * @var dfs\docdoc\models\ClinicModel                $model
 * @var CActiveDataProvider                          $dataProvider
 * @var string                                       $dateFrom
 * @var string                                       $dateTo
 * @var array                                        $periods
 */
$this->breadcrumbs = array(
	'Биллинг',
);
?>

<h1>Биллинг клиник за
	<?php
		echo CHtml::dropDownList('dateFrom', $dateFrom, $periods);
	?>
</h1>

<a href="/2.0/clinic/listDetails">Подробный отчет</a>

<?php $this->widget(
	'zii.widgets.grid.CGridView',
	array(
		'id'           => 'clinic-grid',
		'dataProvider' => $model->searchClinicsForBilling(),
		'filter'       => $model,
		'columns'      => array(
			'id',
			[
				'class'    => 'CDataColumn',
				'name'     => 'city_id',
				'value'    => '$data->clinicCity->title',
				'filter'   =>  CHtml::listData(CityModel::model()->active()->ordered()->findAll(), 'id_city', 'title')
			],
			[
				'class'    => 'CDataColumn',
				'name'     => 'short_name',
				'value'    => '$data->short_name ?: $data->name',
			],
			array(
				'header' => 'Тариф / Кол-во заявок / Сумма',
				'class'  => 'CDataColumn',
				'type'   => 'raw',
				'filter' => null,
				'value'  => '$this->grid->owner->widget(
								"\dfs\docdoc\back\widgets\ClinicTariffWidget",
								[
									"dateFrom" =>"' .  $dateFrom . '",
									"dateTo" =>"' .  $dateTo . '",
									"clinic" => $data
								],
								true
							)',
			),
		),
	)
); ?>

<script type="text/javascript">
	$("[name=dateFrom]").change(function(){
		location.href = "/2.0/clinic/list?dateFrom=" + $(this).val();
	});
</script>
