<?php
/**
 * @var LkController $this
 * @var LfMaster     $model
 */

use likefifa\extensions\image\Image;

$this->renderPartial('_header', compact('model'));
?>

<div class="content-wrap content-pad-bottom">
	<div class="page-profile">
		<div class="prof-wrap">
			<?php $this->widget('application.components.likefifa.widgets.LfLkTabsWidget', array(
				'currentTab' => 'profile',		
			)); ?>
			<div class="prof-cont">
				<?php $this->widget('application.components.likefifa.widgets.LfLkMenuWidget', array(
					'actions' => $this->actions,
					'currentAction' => $this->action->id,
					'model' => $model,		
				)); ?>
				<div class="prof-rht">
					<div class="prof-note-important">
						<p class="ico png"><strong>Требования к фотографиям:</strong></p>
							<strong>1.</strong> На фотографиях должна быть изображена Ваша работа.<br/>
							<strong>2.</strong> Фотографии должны быть хорошего качества.<br/>
							<strong>3.</strong> Запрещается выкладывать фотографии с указанием контактных данных (номер телефона, e-mail, Skype, ICQ, ссылки на сайт и социальные сети и т.д.)<br/>
						<br/><p><strong><i>Обратите внимание! Вы можете разместить не более 50 фотографий. Выбирайте только лучшие работы!</i></strong></p>
						<br/><p><strong><i>Новые возможности:</i></strong></p>
						<div>
							<strong>4.</strong> Отмечайте Ваши лучшие фотоработы галочкой &quot;Добавить в топ&quot;. Данные работы будут показываться в Вашей краткой анкете, а так же выше в нашей фотогалереи. Тем самым клиенты смогут чаще увидеть лучшие работы.<br/>
							<strong>5.</strong> Редактируйте Ваши фотографии. При загрузке фотографий Вы можете обрезать их, сделав акцент на нужную часть работы.<br/>
							<strong>6.</strong> Фото, набравшиеся наибольшее количество лайков из соцсетей, показываются на главной странице сайта!<br/>
						</div>
					</div>
					<div class="prof-head-inp">Фотография:</div>
					<a href="./add/" class="prog-photo_link_det">загрузить новые фотографии</a>
					<?php if ($model->works):?>
						<div class="prof-head-inp">Моя фотогалерея:</div>
						<div class="prof-photo_imgs">
							<?php $i=0; foreach ($model->works as $work): ?>
								<div class="item<?php if((($i++) % 4) === 0){ ?> first<?php } ?>">
									<div class="prof-photo_imgs_wrap" id="prof-photo-<?php echo $work->id; ?>">
										<a
											class="prof-photo_imgs_img"
											href="./edit/<?php echo $work->id ?>/"
											title="Редактировать работу">
											<img
												class="lk-work-<?php echo $work->id; ?>"
												width="180"
												src="<?php echo $work->preview('small', true, Image::WIDTH) . '?' . rand(); ?>"/>
											<span class="prof-photo_add_over"><span>Изменить фотографию</span></span>
										</a>
										<a href="./delete/<?php echo $work->id?>/" class="del"><span><i>удалить</i></span><img src="/i/profile/icon-del-photo.png" /></a>
										<div class="b-btn_top__work<?php echo $work->is_main ? ' b-btn_top__work-add' : '' ?>"
											 onclick="return toggleTop10(this);" data-id="<?php echo $work->id ?>">
											<div class="b-btn_top__work-ico"></div>
											<div class="b-btn_top__work-txt">ТОП <span class="b-top__num">10</span>
											</div>
										</div>
									</div>
								</div>
							<?php endforeach?>
							<div class="clearfix"></div>
						</div>
					<?php endif?>
				</div>
				<input type='hidden' name='redirect_link' value='' class='redirect_link' />
				<div class="clearfix"></div>
				<div class="prof-btn_next">
					<a href="<?php echo $this->createUrl('schedule'); ?>" class="button button-blue"><span>Сохранить</span></a>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(function() {
		initCardLikes();
	});
</script>
