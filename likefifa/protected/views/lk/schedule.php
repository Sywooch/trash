<?php
/**
 * @var LkController $this
 * @var LfMaster     $model
 */

$this->renderPartial('_header', compact('model'));
?>

<div class="content-wrap content-pad-bottom">
	<div class="page-profile">
		<div class="prof-wrap">
			<?php $this->widget('application.components.likefifa.widgets.LfLkTabsWidget', array(
				'currentTab' => 'profile',		
			)); ?>
			<div class="prof-cont">
				<?php $form=$this->beginWidget('CActiveForm', array(
					'id'=>'master-form-personal',
					'enableAjaxValidation'=>false,
					'htmlOptions' => array(	),
				)); ?>
				<?php $this->widget('application.components.likefifa.widgets.LfLkMenuWidget', array(
					'actions' => $this->actions,
					'currentAction' => $this->action->id,
					'model' => $model,	
				)); ?>
				<div class="prof-rht">
					<div style="width:300px;">
						<div class="prof-head-inp" style="margin-bottom:15px;">График работы:</div>
						<p style="margin-bottom:3px; padding-left:7px;">Будни:</p>
						<table style="margin-bottom:12px;">
							<tr>
								<td class="prof-tbl-time-txt" width="20">с</td>
								<td width="75">
									<?php echo LfHtml::activeDropDownList($model,'hrs_wd_from', range(0, 23));?>
								</td>
								<td class="prof-tbl-time-txt" width="20">до</td>
								<td style="padding-left:11px" width="75">
									<?php echo LfHtml::activeDropDownList($model,'hrs_wd_to', range(0, 23));?>
								</td>
							</tr>
						</table>
						<p style="margin-bottom:3px; padding-left:7px;">Выходные:</p>
						<table>
							<tr>
								<td class="prof-tbl-time-txt" width="20">с</td>
								<td width="75">
									<?php echo LfHtml::activeDropDownList($model,'hrs_we_from', range(0, 23));?>
								</td>
								<td class="prof-tbl-time-txt" width="20">до</td>
								<td style="padding-left:11px" width="75">
									<?php echo LfHtml::activeDropDownList($model,'hrs_we_to', range(0, 23));?>
								</td>
							</tr>
						</table>
					</div>
				</div>
				<input type='hidden' name='redirect_link' value='' class='redirect_link' />
				<div class="clearfix"></div>
				<div class="prof-btn_next">
					<div class="button button-blue"><span>Сохранить</span></div>
					<?php echo CHtml::submitButton('Сохранить'); ?>
				</div>
				<?php $this->endWidget(); ?>
			</div>
		</div>
	</div>
</div>
