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
			
				<div id="education-template">
					<div>
						<input class="education-id" type="hidden" value="" name="education[INDEX][id]" />
						<div class="prof-inp_marg">
							<div class="prof-head-inp">Профессиональное образование:</div>
							<div class="form-inp">
								<input class="education-organization" placeholder="Введите название школы/учебного центра/проф. курсов" name="education[INDEX][organization]" type="text" maxlength="512" value="">
							</div>
							<div class="prof-note-inp" style="padding:2px 0 0 10px;">например, "Московская школа визажистов"</div>
						</div>
						<div class="prof-inp_marg">
							<div class="prof-head-inp">Курс:</div>
							<div class="form-inp">
								<input class="education-course" placeholder="Введите название курса" name="education[INDEX][course]" type="text" maxlength="512" value="">
							</div>
							<div class="prof-note-inp" style="padding:2px 0 0 10px;">например, "курс профессионального макияжа"</div>
						</div>
						<div class="prof-inp_marg">
							<div class="prof-head-inp">Специализация:</div>
							<div class="form-inp">
								<input class="education-specialization" placeholder="Введите название полученной специализации" name="education[INDEX][specialization]" type="text" maxlength="512" value="">
							</div>
							<div class="prof-note-inp" style="padding:2px 0 0 10px;">например, "визажист"</div>
						</div>
						<div class="prof-inp_marg">
							<div class="prof-head-inp">Год окончания:</div>
							<div style="width:80px;">
								<div class=" form-inp">
									<input class="education-graduation_year" type="hidden" id="inp-select-popup-graduation_year_INDEX" value="" name="education[INDEX][graduation_year]" />
									<div class="form-select-over" data-select-popup-id="select-popup-graduation_year_INDEX"></div>
									<div class="form-select" id="cur-select-popup-graduation_year_INDEX"></div>
									<div class="form-select-arr png"></div>
									<div class="form-select-popup" id="select-popup-graduation_year_INDEX">
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
					<div style="padding-right:80px;">
						
						
						<div id="educations"></div>
						<script type="text/javascript">
							window.educations = <?php echo $model->educationsToJson(); ?>;
						</script>
						
						<div class="add-education-link"><a id="add-education" href="#">профессиональное образование</a></div>
						
						<div class="prof-inp_marg">
							<div class="prof-head-inp">Ваши достижения:</div>
							<div class="form-inp"><?php echo $form->textArea($model,'achievements',array("placeholder"=>"Здесь Вы можете указать свои достижения (возможно вы лауреат конкурса, обладатель приза, имели публикации в журналах и т.п.)", "rows"=>"5")); ?><?php echo $form->error($model,'achievements'); ?></div>
						</div>
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
