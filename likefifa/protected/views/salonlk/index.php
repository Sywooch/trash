<?php
/**
 * @var LfSalon $model
 */
?>
<div class="content-wrap content-pad-bottom">
	<div class="page-profile">
		<div class="prof-wrap">
			<?php $this->widget('application.components.likefifa.widgets.LfSalonLkTabsWidget', array(
				'currentTab' => 'profile',		
			)); ?>
			<div class="prof-cont">
				<?php $form=$this->beginWidget('CActiveForm', array(
					'id'=>'master-form-personal',
					'enableAjaxValidation'=>false,
					'htmlOptions' => array(
						'enctype' => 'multipart/form-data',
					),
				)); ?>
				<?php $this->widget('application.components.likefifa.widgets.LfSalonLkMenuWidget', array(
					'actions' => $this->actions,
					'currentAction' => $this->action->id,
					'model' => $model,
				)); ?>
				<div class="prof-rht">
					<table>
						<tr>
							<td style="padding-left:30px;">
								<div class="prof-head-inp">Логотип:</div>
								<div class="prof-iphoto prof-iphoto_salon">
									<div class="prof-iphoto_file">
										<div class="ava<?=$model->isUploaded() ? ' load-img_loaded' : ''?>" id="load-img_wrap">
											<?php if ($model->isUploaded()):?>
											<img width="104" src="<?php echo $model->avatar(); ?>" />
											<?php endif?>
										</div>
										<?php if ($model->isUploaded()):?>
										<span>изменить лого</span>
										<?php else:?>
										<span>добавить лого</span>
										<?php endif?>
										<?php echo $form->fileField($model,'logo'); ?>
									</div>
									<div id="load-img_name"></div>
									<?php echo $form->error($model,'logo'); ?>
								</div>
							</td>
							
							<td>
								<table>
									<tr>
										<td width="235" style="padding-bottom:30px;">
											<div class="prof-head-inp">Название: *</div>
											<?php echo LfHtml::activeTextField($model,'name'); ?>
										</td>
										<td width="30">
										</td>
										<td width="235">
									<div class="prof-rating_info" style="margin-top: 18px;">
									<p style="margin-bottom: 5px;">Рейтинг: <span><?php echo $model->getRating(); ?></span></p>
									<div class="stars png" style="float:left;"><span style="width:<?php echo $model->getRatingPercent(); ?>%" class="png"></span></div>
									<span class="show-rating-popup">?</span>
									<div class="popup-note popup-rating">
										<div class="popup-close"></div>
										<div class="popup-note_cont">
											<p><strong>Заполнение данной информации, позволит Вам получить рейтинг</strong></p>
											<div class="stars"><span style="width:20%;"></span></div>
											<p>Название, логотип, телефон, e-mail, график работы, адрес</p>
											<div class="stars"><span style="width:40%;"></span></div>
											<p>Прайс-лист салона</p>
											<div class="stars"><span style="width:60%;"></span></div>
											<p>Фотографии салона (не менее 3-х шт.)</p>
											<div class="stars"><span style="width:80%;"></span></div>
											<p>Описание салона, заполненная информация о мастерах</p>
											<div class="stars"><span style="width:100%;"></span></div>
											<p>Положительные отзывы Ваших клиентов (не менее 5 шт.)</p>
											<br/>
											<strong>Спасибо!</strong>
										</div>
										<div class="popup-arr"></div>
									</div>
								</div>
										</td>
										
									</tr>
									<tr>
										<td width="235" style="padding-bottom:40px;">
											<div class="prof-head-inp">Телефон: *</div>
											<?php echo LfHtml::activeTextField($model,'phone'); ?>
											<div class="prof-note-inp" style="padding:2px 0 0 10px;">например, +7 (495) 123-45-55</div>
										</td>
										<td width="30">
										</td>
										<td width="235">
											<div class="prof-head-inp">E-mail: *</div>
											<?php echo LfHtml::activeTextField($model,'email'); ?>
											<div class="prof-note-inp" style="padding:2px 0 0 10px;">например, info@likefifa.ru</div>
										</td>
									</tr>
									<tr>
										<td colspan="3">
											<div style="width:380px;">
												<div class="prof-head-inp">График работы:</div>
												<table style="margin-bottom:12px;">
													<tr>
														<td width="100" class="prof-tbl-time-txt" style="font-size:14px; padding-left:10px;">Будни:</td>
														<td width="20" class="prof-tbl-time-txt">с</td>
														<td width="75">
															<?php echo LfHtml::activeDropDownList($model,'hrs_wd_from', range(0, 23));?>
														</td>
														<td width="20" class="prof-tbl-time-txt">до</td>
														<td width="75" style="padding-left:11px">
															<?php echo LfHtml::activeDropDownList($model,'hrs_wd_to', range(0, 23));?>	
														</td>
													</tr>
												</table>
												<table style="margin-bottom:12px;">
													<tr>
														<td width="100" class="prof-tbl-time-txt" style="font-size:14px; padding-left:10px;">Выходные:</td>
														<td width="20" class="prof-tbl-time-txt">с</td>
														<td width="75">
															<?php echo LfHtml::activeDropDownList($model,'hrs_we_from', range(0, 23));?>
														</td>
														<td width="20" class="prof-tbl-time-txt">до</td>
														<td width="75" style="padding-left:11px">
															<?php echo LfHtml::activeDropDownList($model,'hrs_we_to', range(0, 23));?>	
														</td>
													</tr>
												</table>
											</div>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					<p style="margin-left: 210px;"><br>* - поля, обязательные для заполнения</p>
				</div>
				<input type='hidden' name='redirect_link' value='' class='redirect_link' />
				<div class="clearfix"></div>
				<div class="prof-btn_next">
					<div class="button button-blue"><span>Сохранить</span></div>
					<?php echo CHtml::submitButton('Сохранить'); ?>
					<?php $this->endWidget(); ?>
				</div>
			</div>	
		</div>
	</div>
</div>