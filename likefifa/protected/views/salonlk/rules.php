<?php $this->pageTitle = 'Правила сотрудничества'; ?>
<div class="content-wrap content-pad-bottom">
	<div class="page-profile">
		<div class="prof-wrap">
			<?php $this->widget('application.components.likefifa.widgets.LfSalonLkTabsWidget', array(
				'currentTab' => 'rules',		
			)); ?>
			<div class="prof-cont" style="padding:30px;">
				<?php $this->renderPartial('/rules/salonlktext'); ?>
			</div>
		</div>
	</div>
</div>
