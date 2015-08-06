<?php
/**
 * @var LkController $this
 * @var LfMaster     $model
 */

$this->renderPartial('_header', compact('model'));
?>
<?php $this->pageTitle = 'Правила сотрудничества'; ?>
<div class="content-wrap content-pad-bottom">
	<div class="page-profile">
		<div class="prof-wrap">
			<?php $this->widget(
				'application.components.likefifa.widgets.LfLkTabsWidget',
				array(
					'currentTab' => 'rules',
				)
			); ?>
			<div class="content-wrap content-pad-bottom rules_cont">
				<?php include(Yii::getPathOfAlias('webroot.protected.views.rules') . '/rules.php'); ?>
			</div>
		</div>
	</div>
</div>


