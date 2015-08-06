<?php
/**
 * @var ArticleController $this
 * @var Article           $model
 */

$this->beginWidget(
	'likefifa\components\system\admin\YbBox',
	[
		'title'      => $this->pageTitle,
		'headerIcon' => 'fa fa-file-text',
	]
);

/** @var TbActiveForm $form */
$form = $this->beginWidget(
	'likefifa\components\system\admin\YbActiveForm',
	[
		'id'          => 'article-form',
		'htmlOptions' => [
			'class'   => 'container-fluid',
			'enctype' => 'multipart/form-data'
		],
	]
);
?>
<div class="row">
	<div class="col-md-6">
		<?php echo $form->select2Group(
			$model,
			'article_section_id',
			[
				'widgetOptions' => [
					'data'    => LfSpecialization::model()->getListItems(),
					'options' => [
						'placeholder' => 'Специализация не выбрана',
					],
				],
			]
		); ?>

		<div class="form-group services-checkboxes">
			<?php echo $form->labelEx($model, 'services'); ?>
			<?php foreach (LfService::model()->findAll() as $service): ?>
				<div class="checkbox spec-<?php echo $service->specialization->id; ?>" style="display:none;">
					<?php echo CHtml::checkBox(
						'Article[services][]',
						in_array($service->id, $model->getRelationIds('services')),
						array('value' => $service->id)
					); ?>
					<?php echo $service->name; ?>
					<div class="clear"></div>
				</div>
			<?php endforeach; ?>
			<?php echo $form->error($service, 'services'); ?>
		</div>

		<?php echo $form->textFieldGroup($model, 'rewrite_name') ?>
		<?php echo $form->textFieldGroup($model, 'title') ?>
		<?php echo $form->textFieldGroup($model, 'meta_description') ?>
		<?php echo $form->textFieldGroup($model, 'meta_keywords') ?>
		<?php echo $form->textFieldGroup($model, 'name') ?>

		<?php echo $form->ckEditorGroup($model, 'description') ?>
		<?php echo $form->ckEditorGroup($model, 'text') ?>

		<?php echo $form->checkboxGroup($model, 'disabled') ?>
	</div>
</div>

<?php $this->widget(
	'booster.widgets.TbButton',
	[
		'buttonType' => 'submit',
		'context'    => 'primary',
		'label'      => $model->isNewRecord ? 'Создать' : 'Сохранить'
	]
); ?>
<?php
$this->endWidget();
$this->endWidget();
?>

<script type="text/javascript">
	var handleService = function () {
		var articleSection = $("#Article_article_section_id");
		var checkbox = '.spec-' + articleSection.val();
		if (articleSection.val()) {
			$(".services-checkboxes .checkbox").hide();
			$(checkbox).show();
		}
		else $(".checkbox").hide();
	};
	$("#Article_article_section_id").on('change', function () {
		$("input#Article_services").each(function () {
			$(this).removeAttr("checked");
		});
		handleService();
	});
	$(function () {
		handleService();
	});

</script>
