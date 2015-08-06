<div class="content-wrap content-pad-bottom">
	<div class="page-profile">
		<div class="prof-wrap">
			<?php $this->widget('application.components.likefifa.widgets.LfSalonLkTabsWidget', array(
				'currentTab' => 'profile',		
			)); ?>
			<div class="prof-cont">
				<?php $this->widget('application.components.likefifa.widgets.LfSalonLkMenuWidget', array(
					'actions' => $this->actions,
					'currentAction' => $this->action->id,
					'model' => $model,		
				)); ?>
				<div class="prof-rht">
					<div class="prof-note-important">
						<p class="ico png"><strong>Требования к фотографиям:</strong></p>
							<strong>1.</strong> Вы можете размещать любые фотографии Вашего салона (интерьер, оборудование, мастера за работой и т.д.). Фотографии работ мастеров салона Вы можете добавить в анкете мастера. Для этого нажмите на кнопку "загрузить новую фотографию" и обязательно выберите раздел и подраздел перед сохранением.<br/>
							<strong>2.</strong> Фотографии должны быть хорошего качества.<br/>
						<br/><p><strong><i>Почему необходимо выкладывать фотографии своих работ:</i></strong></p>
						<div>
							<strong>1.</strong> Фотографии салона и работ мастеров повышают Ваш рейтинг на сайте.<br/>	
							<strong>2.</strong> Красивые фотографии привлекают новых клиентов.<br/>		
							<strong>3.</strong> Каждый клиент может "лайкнуть" понравившуюся ему фотографию.<br/>
							<strong>4.</strong> Самые популярные фотографии будут показываться на главной страничке сайта.
						</div>
					</div>
					<div class="prof-head-inp">Фотографии салона:</div>
					<div class="prof-photo_imgs prof-photo_imgs_salon">
					<?php if ($model->photo):?>
						
						<div class="prof-photo_imgs">
							<?php $i=0; foreach ($model->photo as $photo): ?>
								<div class="item<?php if((($i++) % 4) === 0){ ?> first<?php } ?>">
									<div class="prof-photo_imgs_wrap">
										<a class="prof-photo_imgs_img" href="./edit/<?php echo $photo->id?>/"><img width="169" src="<?php echo $photo->preview('big'); ?>" /></a>
										<a href="./delete/<?php echo $photo->id?>/" class="del"><span><i>удалить</i></span><img src="/i/profile/icon-del-photo.png" /></a>
									</div>
								</div>
							<?php endforeach?>
						</div>
					<?php endif?>
						<div class="clearfix"></div>
					</div>
					<a class="prog-photo_link_det" href="./add/">загрузить новую фотографию</a>
					<div class="prof-head-inp" style="border-top: 1px solid #DEDEDE; padding-top:20px;">Фотогалерея работ салона:</div>
					<div class="prof-photo_imgs prof-photo_imgs_salon">
					<?php if (!empty($works)):?>
						
						<div class="prof-photo_imgs">
							<?php $i=0; foreach ($works as $work): ?>
								<div class="item<?php if((($i++) % 4) === 0){ ?> first<?php } ?>">
									<div class="prof-photo_imgs_wrap">
										<a rel="prettyPhoto[gallery]" class="prof-photo_imgs_img" href="<?php echo $work->preview('full'); ?>"><img width="180" src="<?php echo $work->preview('small'); ?>" /></a>
									</div>
								</div>
							<?php endforeach?>
						</div>
					<?php endif?>
						<div class="clearfix"></div>
					</div>
					
				</div>
				<input type='hidden' name='redirect_link' value='' class='redirect_link' />
				<div class="clearfix"></div>
				<div class="prof-btn_next">
					<a href="<?php echo $this->createUrl('password'); ?>" class="button button-blue"><span>Сохранить</span></a>
				</div>
			</div>
		</div>
	</div>
</div>