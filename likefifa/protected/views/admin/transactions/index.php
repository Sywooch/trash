<?php
use likefifa\models\forms\PaymentsOperationsAdminFilter;

/**
 * @var PaymentsOperationsAdminFilter $model
 * @var AdminsController              $this
 * @var integer                       $sum
 */

$dataProvider = $model->search();

$this->breadcrumbs = array(
	'Транзакции',
);

$this->beginWidget('likefifa\components\system\admin\YbBox', ['title' => false]);
/** @var TbActiveForm $form */
$form = $this->beginWidget(
	'booster.widgets.TbActiveForm',
	[
		'method' => 'get',
		'id'     => 'filter-form',
		'type'   => 'inline',
	]
);

echo $form->dropDownListGroup(
	$model,
	'account_from',
	[
		'widgetOptions' => [
			'data'        => [
				'0' => 'Мастера',
				'1000' => 'LikeFifa',
				'1' => 'Заплатили мастера'
			],
			'htmlOptions' => [
				'empty' => 'Вид транзакций',
			]
		]
	]
);

echo $form->dateRangeGroup(
	$model,
	'create_date',
	[
		'widgetOptions' => [
			'options'     => [
				'format' => 'DD.MM.YYYY',
			],
			'htmlOptions' => [
				'style' => 'min-width: 180px; width: 180px;',
			],
			'callback'    => 'js:function(start, end) {
				$("#' . CHtml::modelName($model) . '_createdFrom").val(start.format("DD.MM.YYYY"));
				$("#' . CHtml::modelName($model) . '_createdTo").val(end.format("DD.MM.YYYY"));
			}'
		],
		'prepend'       =>
			'<i class="glyphicon glyphicon-calendar" data-toggle="tooltip" title="' .
			$model->getAttributeLabel('created') .
			'"></i>'
	]
);
echo $form->hiddenField($model, 'createdFrom');
echo $form->hiddenField($model, 'createdTo');

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

$this->endWidget();
$this->endWidget();
?>
	<p>
		<strong>Баланс расчетного счета:</strong>
		<?php echo Yii::app()->numberFormatter->format('#,##0', PaymentsOperationsAdminFilter::getRealAmount()) ?> руб.
		/
		<strong>Заплатили всего мастера:</strong>
		<?php echo Yii::app()->numberFormatter->format('#,##0', PaymentsOperationsAdminFilter::getTotalFakeAmount()) ?> руб.
	</p>
	<p>
		<strong>Заплатили мастера:</strong>
		<?php echo Yii::app()->numberFormatter->format('#,##0', $model->getFakeAmount()) ?> руб.
	</p>
<?php
$this->beginWidget(
	'likefifa\components\system\admin\YbBox',
	[
		'title'      => 'Транзакции (' . $dataProvider->getTotalItemCount() . ' на сумму ' . Yii::app()->numberFormatter->format('#,##0', $sum) . ' руб.)',
		'headerIcon' => 'fa fa-money',
	]
);
$this->widget(
	'likefifa\components\system\admin\YbGridView',
	array(
		'id'           => 'transactions-grid',
		'dataProvider' => $dataProvider,
		'columns'      => array(
			'id',
			array(
				'name'   => 'master',
				'type'   => 'raw',
				"filter" => false,
				'value'  => 'LfMaster::model()->getBoLinkByAccountId($data->getUserAccountId())',
			),
			array(
				'name'   => 'create_date',
				'type'   => 'raw',
				"filter" => false,
				'value'  => '$data->getFormatDate()',
			),
			'amount_real',
			'amount_fake',
			array(
				'name'   => 'type',
				'type'   => 'raw',
				"filter" => false,
				'value'  => '$data->getType()',
			),
			'message',
			'invoice_id',
			array(
				'name'   => 'invoice_status',
				'type'   => 'raw',
				"filter" => false,
				'value'  => '$data->getInvoiceStatus()',
			),
			array(
				'name'   => 'invoice_email',
				'type'   => 'raw',
				"filter" => false,
				'value'  => '$data->getInvoiceEmail()',
			),
		),
	)
);

$this->endWidget();