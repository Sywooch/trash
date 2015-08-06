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
					'htmlOptions' => array(
						'enctype' => 'multipart/form-data',
					),
				)); ?>
				<?php $this->widget('application.components.likefifa.widgets.LfLkMenuWidget', array(
					'actions' => $this->actions,
					'currentAction' => $this->action->id,
					'model' => $model,
				)); ?>


				<div class="prof-rht">
					<table>
						<tr>
							<td style="padding-right:15px;">
								<table>
									<tr>
										<td width="235" style="padding-bottom:30px;">
											<div class="prof-head-inp">Имя: *</div>
											<?php echo LfHtml::activeTextField($model,'name'); ?>
										</td>
										<td width="40">
										</td>
										<td width="235">
											<div class="prof-head-inp">Фамилия:</div>
											<?php echo LfHtml::activeTextField($model,'surname'); ?>
										</td>
									</tr>
									<tr>
										<td width="235" style="padding-bottom:30px;">
											<div class="prof-head-inp">Мобильный телефон: *</div>
											<?php echo LfHtml::activeTextField($model,'phone_cell'); ?>
											<div class="prof-note-inp" style="padding:2px 0 0 10px;">например, +7 (495) 123-45-55</div>
										</td>
										<td width="40">
										</td>
										<td width="235">
											<div class="prof-head-inp">E-mail: *</div>
											<?php echo LfHtml::activeTextField($model,'email'); ?>
											<?php echo $form->error($model,'email'); ?>
											<div class="prof-note-inp" style="padding:2px 0 0 10px;">например, info@likefifa.ru</div>
										</td>
									</tr>
								</table>
								<table>
									<tr>
										<td id="spec_select" width="200" style="padding-right:15px;">
											<div class="prof-head-inp" style="margin-bottom:16px;">Специализация: *</div>
											<?php
											echo LfHtml::checkBoxList(
												"LfMaster[groupIds]",
												CHtml::listData($model->masterGroups, 'group_id', 'group_id'),
												LfGroup::model()->getListItems()
											);
										 	?>
										</td>
										<td>
											<table>
												<tr>
													<td width="135">
														<div class="prof-head-inp">Опыт работы:</div>
														<?php echo LfHtml::activeDropDownList($model,'experience', LfMaster::getExperienceListItems()); ?>
														<?php echo $form->error($model,'experience'); ?>
													</td>
													<td width="165" style="padding-bottom:30px;">
														<div class="prof-note-inp" style="margin-top:18px; padding-left:20px;">укажите суммарный опыт работы (частная практика и работа в салоне)</div>
													</td>
												</tr>
												<tr>
													<td colspan="2">
														<div class="prof-head-inp">Дата рождения:</div>
														<table>
															<tr>
																<td>
																	<div style="width:80px;">
																		<?php echo LfHtml::activeDropDownList($model,'birth_day', range(1, 31)); ?>
																	</div>
																</td>
																<td style="padding-left:11px">
																	<div style="width:110px;">
																		<?php echo LfHtml::activeDropDownList($model,'birth_month', LfMaster::model()->getMonthListItems()); ?>
																	</div>
																</td>
																<td style="padding-left:11px">
																	<div style="width:80px;">
																		<?php echo LfHtml::activeDropDownList($model,'birth_year', range(1920, intval(date('Y')))); ?>
																	</div>
																</td>
															</tr>
														</table>
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
							<td>
								<div class="prof-iphoto">
									<div class="prof-iphoto_file ava-container">
										<div class="ava<?=$model->gender == LfMaster::GENDER_FEMALE ? ' female' : ''?> <?=$model->isUploaded() ?' load-img_loaded' : ''?>" id="load-img_wrap">
											<?php if ($model->isUploaded()) { ?>
												<img width="104" src="<?php echo $model->avatar(); ?>" />
												<?php echo LfHtml::loader(); ?>
												<div class="loader-overlay"></div>
												<?php
												echo CHtml::ajaxLink(
													"",
													$this->createUrl(
														"masters/rotateAvatar",
														array(
															"id"        => $model->id,
															"direction" => -90
														)
													),
													array(
														"beforeSend" => 'function(){
															$(".ava").find(".loader").show();
															$(".ava").find(".loader-overlay").show();
														}',
														"success" => 'function(data) {
															$(".ava").find(".loader").hide();
															$(".ava").find(".loader-overlay").hide();
															$(".ava").find("img").attr("src", data);
														}',
													),
													array(
														"class" => "rotate rotate-left",
														"id" => uniqid(),
														"title" => "Повернуть против часовой стрелки на 90 градусов",
														"live" => false,
													)
												);
												?>
												<?php
												echo CHtml::ajaxLink(
													"",
													$this->createUrl(
														"masters/rotateAvatar",
														array(
															"id"        => $model->id,
															"direction" => 90
														)
													),
													array(
														"beforeSend" => 'function(){
															$(".ava").find(".loader").show();
															$(".ava").find(".loader-overlay").show();
														}',
														"success" => 'function(data) {
															$(".ava").find(".loader").hide();
															$(".ava").find(".loader-overlay").hide();
															$(".ava").find("img").attr("src", data);
														}',
													),
													array(
														"class" => "rotate rotate-right",
														"id" => uniqid(),
														"title" => "Повернуть по часовой стрелке на 90 градусов",
														"live" => false,
													)
												);
												?>
											<?php } ?>
										</div>
										<?php if ($model->isUploaded('photo')):?>
											<span>изменить фото</span>
										<?php else:?>
											<span>добавить фото</span>
										<?php endif?>
										<?php echo $form->fileField($model,'photo'); ?>
									</div>
									<div id="load-img_name"></div>
									<?php echo $form->error($model,'photo'); ?>
									<div class="prof-note-inp" style="margin-bottom:30px;">Добавьте свою фотографию.<br/><strong>Важно!</strong> Клиент часто оценивает мастера по его внешнему виду, поэтому мы настоятельно рекомендуем загрузить Вашу фотографию хорошего качества.</div>

									<div class="prof-head-inp" style="margin-bottom:16px;">Пол:</div>
									<?php echo LfHtml::activeRadioButtonList($model,'gender', LfMaster::getGenderListItems()); ?>
									<?php echo $form->error($model,'gender'); ?>
								</div>
							</td>
						</tr>
						<tr>
						<td><p>* - поля, обязательные для заполнения</p><td>
						</tr>
					</table>
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