<?php
/**
 * @var IndexController $this
 * @var BoIndexReport   $report
 * @var integer         $amountSum
 * @var integer[]       $mastersRegData
 * @var integer[]       $appointmentsData
 * @var integer[]       $opinionsData
 */
use likefifa\models\forms\BoIndexReport;

?>

<div class="row">

	<div class="col-lg-3 col-sm-6 col-xs-6 col-xxs-12">
		<div class="smallstat box">
			<div class="boxchart-overlay blue">
				<div class="boxchart"><?php echo implode(',', $mastersRegData) ?></div>
			</div>
			<span class="title">Мастера</span>
			<span class="value">
				<?php echo Yii::app()->numberFormatter->format('#,##0', LfMaster::model()->active()->count()) ?>
			</span>
			<a href="<?php echo $this->createUrl('/admin/master/index') ?>" class="more">
				<span>Посмотреть</span>
				<i class="fa fa-chevron-right"></i>
			</a>
		</div>
	</div>
	<!--/col-->

	<div class="col-lg-3 col-sm-6 col-xs-6 col-xxs-12">
		<div class="smallstat box">
			<div class="linechart-overlay red">
				<div class="linechart"><?php echo implode(',', $appointmentsData) ?></div>
			</div>
			<span class="title">Заявки</span>
			<span class="value">
				<?php echo Yii::app()->numberFormatter->format('#,##0', LfAppointment::model()->count()) ?>
			</span>
			<a href="<?php echo $this->createUrl('/admin/appointment/index') ?>" class="more">
				<span>Посмотреть</span>
				<i class="fa fa-chevron-right"></i>
			</a>
		</div>
	</div>
	<!--/col-->

	<div class="col-lg-3 col-sm-6 col-xs-6 col-xxs-12">
		<div class="smallstat box">
			<i class="fa fa-rub green"></i>
			<span class="title">Заработок</span>
			<span class="value">
				<?php echo Yii::app()->numberFormatter->format('#,##0', $amountSum) ?>
			</span>
			<a href="<?php echo $this->createUrl(
				'/admin/transactions/index',
				['likefifa_models_forms_PaymentsOperationsAdminFilter[account_from]' => 1000]
			) ?>" class="more">
				<span>Посмотреть</span>
				<i class="fa fa-chevron-right"></i>
			</a>
		</div>
	</div>
	<!--/col-->

	<div class="col-lg-3 col-sm-6 col-xs-6 col-xxs-12">
		<div class="smallstat box">
			<div class="boxchart-overlay yellow">
				<div class="boxchart"><?php echo implode(',', $opinionsData) ?></div>
			</div>
			<span class="title">Отзывы</span>
			<span class="value"><?php echo Yii::app()->numberFormatter->format(
					'#,##0',
					LfOpinion::model()->count()
				) ?></span>
			<a href="<?php echo $this->createUrl(
				'/admin/opinion/index'
			) ?>" class="more">
				<span>Посмотреть</span>
				<i class="fa fa-chevron-right"></i>
			</a>
		</div>
	</div>
	<!--/col-->

</div>

<div class="row">
	<div class="col-lg-12">
		<div id="main-chart"></div>
	</div>
</div>

<?php
$this->beginWidget(
	'likefifa\components\system\admin\YbBox',
	[
		'id'          => 'reports-block',
		'title'       => 'Анализ обращений',
		'headerIcon'  => 'fa fa-eye',
		'htmlOptions' => [
			'style' => 'margin-top: 20px;'
		]
	]
);
/** @var TbActiveForm $form */
$form = $this->beginWidget(
	'likefifa\components\system\admin\YbActiveForm',
	[
		'method'               => 'get',
		'type'                 => 'inline',
		'enableAjaxValidation' => true,
		'action'               => ['/admin/index/index'],
		'clientOptions'        => [
			'validateOnSubmit' => true,
			'validateOnChange' => false,
		],
	]
);
echo $form->dateRangeGroup(
	$report,
	'date',
	[
		'widgetOptions' => [
			'options' => [
				'format' => 'DD.MM.YYYY',
			],
		]
	]
);
$this->widget(
	'booster.widgets.TbButton',
	[
		'buttonType' => 'submit',
		'context'    => 'success',
		'label'      => 'Сгенерировать'
	]
);
$this->endWidget();

if ($report->isFill()) {
	$appointmentReport = $report->getAppointmentData();
	?>
	<h5>Заявки</h5>
	<div class="row">
		<div class="col-md-4">
			<?php
			$this->widget(
				'likefifa\components\system\admin\YbGridView',
				[
					'exportButtons' => [],
					'dataProvider'  => new CArrayDataProvider($appointmentReport['data'], ['keyField' => 's0']),
					'columns'       => $appointmentReport['columns1']
				]
			);
			?>
		</div>
		<div class="col-md-8">
			<?php
			$this->widget(
				'likefifa\components\system\admin\YbGridView',
				[
					'exportButtons' => [],
					'dataProvider'  => new CArrayDataProvider($appointmentReport['data'], ['keyField' => 's0']),
					'columns'       => $appointmentReport['columns2']
				]
			);
			?>
		</div>
	</div>

	<div class="row">
		<div class="col-md-5">
			<?php
			$this->widget(
				'likefifa\components\system\admin\YbGridView',
				[
					'exportButtons' => [],
					'dataProvider'  => new CArrayDataProvider($appointmentReport['data'], ['keyField' => 's0']),
					'columns'       => $appointmentReport['columns3']
				]
			);
			?>
		</div>
	</div>
	<hr/>
	<div class="row">
		<div class="col-md-5">
			<h5>Мастера</h5>
			<?php
			$mastersReport = $report->getMastersData();
			$this->widget(
				'likefifa\components\system\admin\YbGridView',
				[
					'exportButtons' => [],
					'dataProvider'  => new CArrayDataProvider($mastersReport['data'], ['keyField' => 'total']),
					'columns'       => $mastersReport['columns']
				]
			);
			?>
		</div>
	</div>
	<hr/>
	<div class="row">
		<div class="col-md-5">
			<h5>Отзывы</h5>
			<?php
			$opinionsReport = $report->getOpinionsData();
			$this->widget(
				'likefifa\components\system\admin\YbGridView',
				[
					'exportButtons' => [],
					'dataProvider'  => new CArrayDataProvider($opinionsReport['data'], ['keyField' => 'total']),
					'columns'       => $opinionsReport['columns']
				]
			);
			?>
		</div>
	</div>
<?php
}
$this->endWidget();
?>

<script src="<?php echo Yii::app()->theme->getBaseUrl() ?>/js/jquery.easy-pie-chart.min.js"></script>
<script src="<?php echo Yii::app()->getBaseUrl() ?>/js/admin/highstock/highstock.js"></script>

<script type="text/javascript">
	$(function () {
		$.get(homeUrl + 'admin/index/mainChart', function (data) {
			$('#main-chart').highcharts('StockChart', {
				chart: {
					height: 500,
					zoomType: 'x'
				},

				rangeSelector: {
					inputEnabled: $('#container').width() > 480,
					selected: 1,
					buttons: [
						{
							type: 'week',
							count: 1,
							text: '1н'
						},
						{
							type: 'month',
							count: 1,
							text: '1м'
						},
						{
							type: 'month',
							count: 3,
							text: '3м'
						},
						{
							type: 'month',
							count: 6,
							text: '6м'
						},
						{
							type: 'ytd',
							text: 'нг'
						},
						{
							type: 'year',
							count: 1,
							text: '1г'
						},
						{
							type: 'all',
							text: 'все'
						}
					]
				},

				title: {
					text: 'Показатели'
				},

				tooltip: {
					style: {
						width: '200px'
					},
					valueDecimals: 0
				},

				xAxis: {
					type: 'date'
				},

				series: [
					{
						name: 'Регистрации мастеров',
						data: data['masters']
					},
					{
						name: 'Регистрации салонов',
						data: data['salons']
					},
					{
						name: 'Новые заявки',
						data: data['appointments']
					},
					{
						name: 'События',
						type: 'flags',
						data: data['events']
					}
				],

				legend: {
					enabled: true
				}
			});
		}, 'json');
	});
</script>