<div class="content-wrap content-pad-bottom">
	<div class="page-profile">
		<div class="prof-wrap">
			<?php $this->widget('application.components.likefifa.widgets.LfSalonLkTabsWidget', array(
				'currentTab' => 'masters',		
			)); ?>
						<div class="prof-cont">
					<?php $this->widget('application.components.likefifa.widgets.LfSalonLkMenuWidget', array(
					'actions' => array('masters' => 'Мастера салона', 'salonlk/masters/add' => 'Добавить мастера'),
					'currentAction' => $this->action->id,
					'model' => $model,
				)); ?>
				<div class="prof-rht salon-profile_res__masters" style="margin-top:-22px; padding-top:0;">
				<?php 
                $this->widget('zii.widgets.CListView', array(
                    'ajaxUpdate' => false,
                    'dataProvider'=>$dataProvider,
                    'viewData' => compact('specialization', 'service', 'hasDeparture','model', 'masters'),
                    'itemView'=>'_view',
                    'sortableAttributes'=>array(),
                    'template' => '{items} {pager}',
                    'emptyText' => '<p style="margin:40px 0 0 0;">Мастеров нет.</p>',
                   	'pager' => array(
                        'cssFile' => false,
                        'header' => '', 
                        'prevPageLabel' => '<',
                        'nextPageLabel' => '>',
                        'firstPageLabel'=> '',
                        'lastPageLabel'=> '',
                   		'maxButtonCount' => 8,
                    ),
                )); ?>
			</div>
		</div>
	</div>
</div>
</div>			