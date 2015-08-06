<?php
/**
 * @var MasterSearchController $this
 * @var CActiveDataProvider    $dataProvider
 * @var array                  $params
 */
?>
<script type="text/javascript"
		src="<?php echo Yii::app()->getBaseUrl(); ?>/js/jquery.mousewheel.js?<?php echo RELEASE_MEDIA; ?>"></script>
<script type="text/javascript" src="<?php echo Yii::app()->getBaseUrl(
); ?>/js/jquery.jscrollpane.min.js?<?php echo RELEASE_MEDIA; ?>"></script>

<?php
$this->breadcrumbs = [
	'Подбор мастера'
];

$this->beginWidget('likefifa\components\system\admin\YbBox', ['title' => false]);
?>

<form action="" method="GET" class="quick-search-main form-inline" id="search-main">
	<?php echo CHtml::hiddenField(
		'appointment_id',
		Yii::app()->request->getQuery('appointment_id')
	) ?>
	<?php echo CHtml::hiddenField(
		'specialization',
		Yii::app()->request->getQuery('specialization'),
		[
			'id' => 'specialization',
		]
	) ?>
	<?php echo CHtml::hiddenField(
		'service',
		Yii::app()->request->getQuery('service'),
		[
			'id' => 'service',
		]
	) ?>
	<div class="form-group">
		<div class="suggest-container">
			<?php echo CHtml::textField(
				'query',
				Yii::app()->request->getQuery('query'),
				[
					'autocomplete' => 'off',
					'id'           => 'search-suggest',
					'placeholder'  => 'Укажите услугу',
					'class'        => 'form-control',
				]
			) ?>
		</div>
	</div>

	<div class="form-group">
		<?php $this->widget(
			'booster.widgets.TbSelect2',
			array(
				//'id' => 'masterSearchMetroResult',
				'asDropDownList' => false,
				'name'    => 'stations',
				'value' => Yii::app()->request->getQuery('stations'),
				'options' => [
					'width' => '300px',
					'placeholder' => 'Станции метро',
					'multiple'    => true,
					'ajax'        => [
						'url'      => $this->createUrl('ajax/metroSuggest'),
						'dataType' => 'json',
						'data' => 'js:function(term) {return {term:term};}',
						'results'  => 'js:masterSearchMetroResult',
					],
					'initSelection' => 'js:masterSearchMetroResultInit',
					'escapeMarkup' => 'js:function (m) { return m; }',
				]
			)
		); ?>
	</div>

	<div class="form-group">
		<?php echo CHtml::textField(
			'price-filter',
			Yii::app()->request->getQuery('price-filter'),
			[
				'id'          => 'price-filter',
				'placeholder' => 'Стоимость',
				'class'       => 'form-control',
			]
		) ?>
	</div>

	<?php
	$this->widget(
		'booster.widgets.TbButton',
		array(
			'buttonType' => 'submit',
			'context'    => 'primary',
			'label'      => 'Применить'
		)
	);
	echo '&nbsp;';
	$this->widget(
		'booster.widgets.TbButton',
		array(
			'buttonType' => 'link',
			'url'        => ['index'],
			'context'    => 'default',
			'label'      => 'Сбросить'
		)
	);
	?>
</form>
<?php
$this->endWidget();

$this->beginWidget(
	'likefifa\components\system\admin\YbBox',
	[
		'title'      => 'Поиск мастеров (' . $dataProvider->getTotalItemCount() . ')',
		'headerIcon' => 'fa fa-search',
	]
);

$this->widget(
	'likefifa\components\system\admin\YbGridView',
	[
		'id'           => 'masters-grid',
		'dataProvider' => $dataProvider,
		'template'     => '{items} {pager}',
		'columns'      => [
			'id',
			[
				'name'  => 'Имя мастера',
				'type'  => 'raw',
				'value' => '"<span class=\"master-is-free status-".$data->is_free."\" title=\"".($data->is_free ? "готов" : "не готов")." принимать заказы\"></span> " . CHtml::link($data->getFullName(), $data->getProfileUrl(true), ["target" => "_blank"]) .
				"&nbsp;" . Yii::app()->controller->getAppointmentLink($data)',
			],
			'phone_cell',
			'rating',
			[
				'name'  => 'balance',
				'type'  => 'raw',
				'value' => '$data->getBalance() . " р."',
			],
			[
				'name'  => 'Станция метро',
				'type'  => 'raw',
				'value' => '$data->undergroundStation ? $data->undergroundStation->name . " (<span style=\"color: #".$data->undergroundStation->undergroundLine->color."\">" . $data->undergroundStation->undergroundLine->name . "</span>)" : ""'
			],
			[
				'name'  => 'Стоимость услуги',
				'type'  => 'raw',
				'value' => !$params['service']
					? ''
					:
					'$data->getPriceForService(' .
					$params["service"]->id .
					')->price ? $data->getPriceForService(' .
					$params["service"]->id .
					')->getPriceFormatted() . "р." : ""',
			],
			[
				'class'    => 'CButtonColumn',
				'template' => '{addAppointment}',
				'buttons'  => [
					'addAppointment' => [
						'label'   => 'Создать заявку',
						'url'     => 'Yii::app()->createUrl("/admin/appointment/create", ["master_id" => $data->id])',
						'options' => [
							'style' => 'white-space: nowrap;'
						]
					]
				],
			],
		],
	]
);
$this->endWidget();
?>

<script type="text/javascript"
		src="<?php echo Yii::app()->homeUrl; ?>js/jquery.jsonSuggest.js?<?php echo RELEASE_MEDIA; ?>"></script>
<script type="text/javascript">
	var ajaxRequest;
	var suggest;
	$(function () {
		suggest = new SearchSuggest();
		suggest.formId = 'search-main';
		suggest.callback = function () {
			ajaxRequest = $('#search-main').serialize();
			$.fn.yiiGridView.update(
				'masters-grid',
				{data: ajaxRequest}
			);
		};
		suggest.initSpec('search-suggest');
		suggest.initMetro('metro-suggest');
	});
</script>