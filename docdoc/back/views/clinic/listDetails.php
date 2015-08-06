<?php
use dfs\docdoc\models\ClinicContractCostModel;

/**
 * @var dfs\docdoc\back\controllers\ClinicController $this
 * @var dfs\docdoc\models\ClinicContractCostModel    $model
 * @var CActiveDataProvider                          $dataProvider
 * @var string                                       $dateFrom
 * @var string                                       $dateTo
 * @var array                                        $periods
 * @var array                                        $params
 * @var int                                          $groupType
 * @var int                                          $totalNum
 * @var int                                          $totalSum
 */
$this->breadcrumbs = array(
	'Биллинг',
);
?>

<h1>Биллинг клиник за
	<?php

		echo CHtml::dropDownList('dateFrom', $dateFrom, $periods);
		echo ' группировка по контракту ' . CHtml::dropDownList('groupType', $groupType, [0 => 'группировать', 1 => "Нет"]);
	?>
</h1>

<a href="/2.0/clinicBilling/">Отчет биллинг</a>
<?php

$columns = [
	[
		'header' => 'ID',
		'class'    => 'CDataColumn',
		'value'    => '$data->tariff->clinic->id',
		'sortable' => true,
	],
	[
		'class'  => 'CDataColumn',
		'header' => 'Город',
		'value'  => '$data->tariff->clinic->clinicCity->title',
	],
	[
		'header' => 'Клиника',
		'class'    => 'CDataColumn',
		'value'    => '$data->tariff->clinic->short_name ?: $data->tariff->clinic->name',
		'sortable' => true,
	],
	[
		'header' => 'Контракт',
		'class'    => 'CDataColumn',
		'value'    => '$data->tariff->contract->title',
		'sortable' => true,
	],
	[
		'header' => 'Заявок за месяц',
		'class'    => 'CDataColumn',
		'value'    => '$data->getContractStatistics("totalNum", "' . $dateFrom . '", "' . $dateTo . '")',
		'sortable' => true,
	],
	[
		'header' => 'Сумма за месяц',
		'class'    => 'CDataColumn',
		'value'    => '$data->getContractStatistics("totalCost", "' . $dateFrom . '", "' . $dateTo . '")',
		'sortable' => true,
	],
];

if ($groupType > 0)
{
	$columns[] = [
		'header' => 'Шаг',
		'class'    => 'CDataColumn',
		'value'    => '$data->from_num',
		'sortable' => true,
	];

	$columns[] = [
		'header' => 'Стоимость',
		'class'    => 'CDataColumn',
		'value'    => '$data->cost',
		'sortable' => true,
	];


}

$columns[] =[
	'header' => 'Группа услуг',
	'class'    => 'CDataColumn',
	'value'    => '$data->contractGroup->name',
	'sortable' => true,
];

$columns[] =[
	'header' => 'Заявок в группе',
	'class'    => 'CDataColumn',
	'value'    => '$data->getContractStatistics("numForService", "' . $dateFrom . '", "' . $dateTo . '")',
	'sortable' => true,
];

$columns[] =[
	'header' => 'Стоимость для группы',
	'class'    => 'CDataColumn',
	'value'    => '$data->getContractStatistics("costForService", "' . $dateFrom . '", "' . $dateTo . '")',
	'sortable' => true,
];

$columns[] =[
	'header' => 'Текущий шаг',
	'class'    => 'CDataColumn',
	'value'    => '$data->getContractStatistics("currentStepNum", "' . $dateFrom . '", "' . $dateTo . '")',
	'sortable' => true,
];
$columns[] =[
	'header' => 'Стоимость тек. шага',
	'class'    => 'CDataColumn',
	'value'    => '$data->getContractStatistics("currentStepCost", "' . $dateFrom . '", "' . $dateTo . '")',
	'sortable' => true,
];
$columns[] =[
	'header' => 'След.шаг',
	'class'    => 'CDataColumn',
	'value'    => '$data->getContractStatistics("nextStepNum", "' . $dateFrom . '", "' . $dateTo . '")',
	'sortable' => true,
];
$columns[] =[
	'header' => 'Стоимость след. шага',
	'class'    => 'CDataColumn',
	'value'    => '$data->getContractStatistics("nextStepCost", "' . $dateFrom . '", "' . $dateTo . '")',
	'sortable' => true,
];
$columns[] =[
	'header'   => 'Осталось добежать',
	'class'    => 'CDataColumn',
	'value'    => '$data->getContractStatistics("leftToNextStep", "' . $dateFrom . '", "' . $dateTo . '")',
	'sortable' => true,
];
$columns[] =[
	'header'   => 'Профит',
	'class'    => 'CDataColumn',
	'value'    => '$data->getContractStatistics("profit", "' . $dateFrom . '", "' . $dateTo . '")',
	'sortable' => true,
];


$this->widget(
	'zii.widgets.grid.CGridView',
	array(
		'id'           => 'clinic-grid',
		'dataProvider' => $model->searchClinicsForBilling($params),
		'filter'       => $model,
		'columns'      => $columns,
	)
); ?>

<?php
if ($groupType == 0)
{
?>
	<div>Итого в биллинге: <b><?php  echo ClinicContractCostModel::$billingNum . " заявок на " . ClinicContractCostModel::$billingSum; ?> р.</b></div>
	<div>Контроль: <b><?php echo $totalNum . " заявок на " . $totalSum; ?> р.</b> (если кол-во/суммы различаются, значит, были заявки в клиниках, у которых удалили тариф)</div>
<?php
}
?>

<h2>Клиники с заявками, но без тарифов</h2>

<?php
$this->widget(
	'zii.widgets.grid.CGridView',
	array(
		'id'           => 'clinic-without-contracts-grid',
		'dataProvider' => $clinic->searchClinicsWithoutContracts($dateFrom, $dateTo),
		'columns'      => [
			'id',
			'name',
			[
				'name' => 'Количество заявок',
				'value' => 'count($data->requests)'
			],
			[
				'name' => 'Город',
				'value' => '$data->clinicCity->title'
			],
			[
				'name' => 'Тип',
				'value' => '$data->requests[0]->kind ? "Диагностика" : "Врачи"'
			],
		],
		'template' => '{items}',
		'htmlOptions' => [
			'style' => 'width: 400px;'
		]
	)
);
?>

<script type="text/javascript">
	$("[name=dateFrom]").change(function(){
		location.href = "/2.0/clinic/listDetails?dateFrom=" + $(this).val() + "&groupType=" + $("[name=groupType]").val();
	});

	$("[name=groupType]").change(function(){
		location.href = "/2.0/clinic/listDetails?groupType=" + $(this).val() + "&dateFrom=" + $("[name=dateFrom]").val();
	});
</script>
