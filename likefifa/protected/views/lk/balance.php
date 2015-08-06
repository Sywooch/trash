<?php
/**
 * @var LkController $this
 * @var LfMaster     $model
 */

$this->renderPartial('_header', compact('model'));
?>
<?php $this->pageTitle = 'Как пополнить баланс'; ?>
<div class="content-wrap content-pad-bottom">
	<div class="page-profile">
		<div class="prof-wrap">
			<?php $this->widget(
				'application.components.likefifa.widgets.LfLkTabsWidget',
				array(
					'currentTab' => 'balance',
				)
			); ?>
			<div class="content-wrap content-pad-bottom rules_cont lk-balance-info">
				<?php $this->renderPartial("../partials/_balance"); ?>
			</div>
		</div>
	</div>
</div>