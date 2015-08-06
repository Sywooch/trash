<div class="content-wrap content-pad-bottom">
	<div class="page-profile">
		<div class="prof-wrap">
			<?php $this->widget('application.components.likefifa.widgets.LfSalonLkTabsWidget', array(
				'currentTab' => 'masters',		
			)); ?>
			<div class="prof-cont">

				<div id="education-template">
					<div>
						<input type="hidden" name="education[INDEX][id]" value="" class="education-id">
						<div class="prof-inp_marg">
							<div class="prof-head-inp">Профессиональное образование:</div>
							<div class="form-inp">
								<input type="text" value="" maxlength="512" name="education[INDEX][organization]" placeholder="Введите название школы/учебного центра/проф. курсов" class="education-organization">
							</div>
							<div style="padding:2px 0 0 10px;" class="prof-note-inp">например, "Московская школа визажистов"</div>
						</div>
						<div class="prof-inp_marg">
							<div class="prof-head-inp">Курс:</div>
							<div class="form-inp">
								<input type="text" value="" maxlength="512" name="education[INDEX][course]" placeholder="Введите название курса" class="education-course">
							</div>
							<div style="padding:2px 0 0 10px;" class="prof-note-inp">например, "курс профессионального макияжа"</div>
						</div>
						<div class="prof-inp_marg">
							<div class="prof-head-inp">Специализация:</div>
							<div class="form-inp">
								<input type="text" value="" maxlength="512" name="education[INDEX][specialization]" placeholder="Введите название полученной специализации" class="education-specialization">
							</div>
							<div style="padding:2px 0 0 10px;" class="prof-note-inp">например, "визажист"</div>
						</div>
						<div class="prof-inp_marg">
							<div class="prof-head-inp">Год окончания:</div>
							<div style="width:80px;">
								<div class=" form-inp">
									<input type="hidden" name="education[INDEX][graduation_year]" value="" id="inp-select-popup-graduation_year_INDEX" class="education-graduation_year">
									<div data-select-popup-id="select-popup-graduation_year_INDEX" class="form-select-over"></div>
									<div id="cur-select-popup-graduation_year_INDEX" class="form-select"></div>
									<div class="form-select-arr png"></div>
									<div id="select-popup-graduation_year_INDEX" class="form-select-popup" style="display: none;">
										<div class="form-select-popup-long">
											<?php foreach (range(1980, intval(date('Y'))) as $year): ?>
												<span class="item" data-value="<?php echo $year; ?>"><?php echo $year; ?></span>
											<?php endforeach; ?>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="delete-education">(<span>удалить</span> <strong>x</strong>)</div>
					</div>
				</div>
				<?php $form=$this->beginWidget('CActiveForm', array(
					'id'=>'master-form-salon',
					'enableAjaxValidation'=>true,
					'htmlOptions' => array(
						'enctype' => 'multipart/form-data',
					),
				)); ?>		
					<?php $this->widget('application.components.likefifa.widgets.LfSalonLkMenuWidget', array(
					'actions' => array('masters' => 'Мастера салона', 'salonlk/masters/add' => 'Добавить мастера'),
					'currentAction' => 'salonlk/masters/add',
					'model' => $model,
				)); ?>
				<div class="prof-rht">
					<table>
						<tr>
							<td>
								<div class="prof-head-inp">Фотография:</div>
								<div class="prof-iphoto_salon_new_master">
									<div class="prof-iphoto_file">
										<div class="ava<?php if($model->gender == LfMaster::GENDER_FEMALE){?> female<?php } ?><?php if ($model->isUploaded('photo')):?> load-img_loaded<?php endif?>" id="load-img_wrap">
											<?php if ($model->isUploaded('photo')):?>
											<img width="104" src="<?=$model->avatar().'?' . rand() ?>" />
											<?php endif?>
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
									<div class="prof-head-inp" style="margin-bottom:16px;">Пол:</div>
									<?php echo LfHtml::activeRadioButtonList($model,'gender', LfMaster::getGenderListItems()); ?>
									<?php echo $form->error($model,'gender'); ?>
								</div>
							</td>
							<td>
								<table>
									<tr>
										<td width="235">
											<div class="prof-inp_marg">
												<div class="prof-head-inp">Имя: *</div>
												<?php echo LfHtml::activeTextField($model,'name'); ?>
											</div>
											<div class="prof-inp_marg">
												<div class="prof-head-inp">Фамилия: *</div>
												<?php echo LfHtml::activeTextField($model,'surname'); ?>
											</div>
											<div class="prof-inp_marg">
												<div class="prof-head-inp">Специализация: *</div>
													<?php echo LfHtml::dropDownList('group',$model->group ? $model->group->id : (isset($_POST['group']) ? $_POST['group'] : null), $salonGroups, array('name' => 'group')); ?>
													<?php echo $form->error($model,'group_id'); ?>		
												<div class="prof-note-inp" style="padding:2px 0 0 10px;">например, стилист</div>
											</div>
										</td>
										<td width="30">
										</td>
										<td>
											<div class="prof-inp_marg">
												<div class="prof-head-inp">Дата рождения:</div>
												<table cellspacing="0">
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
											</div>
											<div class="prof-inp_marg" style="width:135px;">
												<div class="prof-head-inp">Опыт работы:</div>
												<?php echo LfHtml::activeDropDownList($model,'experience', LfMaster::getExperienceListItems()); ?>
												<?php echo $form->error($model,'experience'); ?>	
										</div>	
									</tr>
								</table>
							</td>
						</tr>
					</table>	
					<div style="padding-right:80px;">
						<div id="educations">

						</div>
						<script type="text/javascript">
							window.educations = <?php echo $model->educationsToJson(); ?>;
						</script>
						<div class="add-education-link"><a href="#" id="add-education">профессиональное образование</a></div>
						<div class="prof-inp_marg">
							<div class="prof-head-inp">Достижения мастера:</div>
							<div class="form-inp"><?php echo $form->textArea($model,'achievements',array("placeholder"=>"Здесь Вы можете указать свои достижения (возможно вы лауреат конкурса, обаладатель приза, имели публикации в журналах и т.п.)", "rows"=>"5")); ?><?php echo $form->error($model,'achievements'); ?></div>
						</div>
						<div class="prof-inp_marg">
							<div class="prof-head-inp">График работы:</div>
							<table cellspacing="0">
								<tr>
									<td>
										<p style="margin-bottom:3px; padding-left:7px;">Будни:</p>
										<table style="margin-bottom:12px;">
											<tr>
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
									</td>
									<td width="70">
									</td>
									<td>
										<p style="margin-bottom:3px; padding-left:7px;">Выходные:</p>
										<table>
											<tr>
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
									</td>
								</tr>
							</table>
						</div>
						<p>* - поля, обязательные для заполнения</p>
						<?php if ($model->id):?>
						<div class="prof-inp_marg">
							<div class="prof-head-inp">Фотография:</div>
							<a href="<?php echo yii::app()->createUrl('salonlk/editwork', array('master_id'=>$model->id));?>" class="prog-photo_link_det" style="margin-bottom:10px;">загрузить новую фотографию</a>
						</div>
						<?php else:?>
						<script>
						function addWork(data) {
							if(!data) {
								alert(data);
								if(!$("#LfMaster_name").val()) $("#LfMaster_name").parent().addClass("error");
								if(!$("#LfMaster_surname").val()) $("#LfMaster_surname").parent().addClass("error");
								if(!$("#inp-select-popup-group").val()) $("#inp-select-popup-group").parent().addClass("error");
							}
							else window.location = '../edit/'+data+'/addwork';
						}
						</script>
						<div class="prof-inp_marg">
							<div class="prof-head-inp">Фотография:</div>
							<?php echo CHtml::ajaxLink('загрузить новую фотографию', array('salonlk/ajax'), array(
									"data" => 'js:$("#master-form-salon").serialize()', 
    								"dataType" => 'json',
									'type'=>'post',
									"success" => 'addWork'
							), array('class'=>'prog-photo_link_det', 'style'=>'margin-bottom:10px;'));?>
						</div>						
						<?php endif?>					
					</div>
					<?php if ($model->works):?>
					<div class="prof-inp_marg">
						<div class="prof-head-inp">Фотогалерея мастера:</div>
						<div class="prof-photo_imgs prof-photo_imgs_salon">
							<?php $i=0; foreach ($model->works as $work): ?>
								<div class="item<?php if((($i++) % 4) === 0){ ?> first<?php } ?>">
									<div class="prof-photo_imgs_wrap">
										<a class="prof-photo_imgs_img" href="<?php echo yii::app()->createUrl('salonlk/editwork', array('master_id'=>$model->id, 'work_id'=>$work->id));?>"><img width="169" src="<?php echo $work->preview('small'); ?>" /></a>
										<a href="<?php echo yii::app()->createUrl('salonlk/deletework', array('master_id'=>$model->id, 'work_id'=>$work->id));?>" class="del"><span><i>удалить</i></span><img src="/i/profile/icon-del-photo.png" /></a>
									</div>
								</div>
							<?php endforeach?>
							<div class="clearfix"></div>
						</div>
					<?php endif?>
					</div>
					<input type='hidden' name='redirect_link' value='' class='redirect_link' />
					<div class="prof-btn_next" style="text-align:center; padding-top:0px;">
						<div class="button button-blue"><span>Сохранить мастера</span></div>
						<?php echo CHtml::submitButton('Сохранить мастера'); ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $this->endWidget(); ?>	
			</div>
		</div>
	</div>
</div>