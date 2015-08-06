<?php
/**
 * @var LkController $this
 * @var LfMaster     $model
 */

$jcropDir = Yii::getPathOfAlias('application.vendors.tapmodo.jcrop');
Yii::app()->clientScript->registerScriptFile(
	Yii::app()->assetManager->publish("{$jcropDir}/js/jquery.color.js"),
	CClientScript::POS_BEGIN
);
Yii::app()->clientScript->registerScriptFile(
	Yii::app()->assetManager->publish("{$jcropDir}/js/jquery.Jcrop.min.js"),
	CClientScript::POS_BEGIN
);
Yii::app()->clientScript->registerCssFile(
	Yii::app()->assetManager->publish("{$jcropDir}/css/jquery.Jcrop.min.css")
);
Yii::app()->assetManager->publish("{$jcropDir}/css/Jcrop.gif");

$this->widget(
	'\likefifa\components\likefifa\widgets\lk\PaymentsWidget',
	array(
		'model' => $model,
	)
);
?>

<div class="form-group_radio" id="master-is-free">
	<?php echo LfHtml::checkBox(
		'is_free',
		$model->is_free,
		[
			'id'    => $model->id,
			'value' => 1,
			'label' => 'готов принимать заказы',
		]
	); ?>
</div>