<?php
/**
 * @var string $content
 */

printHeader();

Yii::app()
	->clientScript
	->registerScriptFile(CHtml::asset(ROOT_PATH . '/back/public/js/tiny_mce/jquery.tinymce.js'), CClientScript::POS_END)
	->registerScriptFile(CHtml::asset(ROOT_PATH . '/back/public/js/tinymce.js'), CClientScript::POS_END)
;

?>

<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/admin/main.css?up" />
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/admin/form.css?3" />


<div class="container">

	<div id="content">
		<?php echo $content; ?>
	</div><!-- content -->

	<div class="span-5 last">
		<div id="sidebar">
			<?php
			$this->beginWidget('zii.widgets.CPortlet', array(
					'title'=>'Действия',
				));
			$this->widget('zii.widgets.CMenu', array(
					'items'=>$this->menu,
					'htmlOptions'=>array('class'=>'operations'),
				));
			$this->endWidget();
			?>
		</div><!-- sidebar -->
	</div>

</div>

<?php

printFooter();
