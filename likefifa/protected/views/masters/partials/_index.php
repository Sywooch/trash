<?php
/**
 * @var MastersController $this
 * @var boolean           $opinionSent
 * @var LfMaster          $model
 * @var LfOpinion         $opinion
 */
?>

<div>
	<div class="det-left">
		<div class="det-left_ph">
			<?php if ($model->isUploaded()): ?>
				<?= CHtml::image($model->avatar(), $model->getFullName(), ['width' => 97]) ?>
			<?php else: ?>
				<?php if ($model->gender == LfMaster::GENDER_FEMALE): ?>
					<div class="det-left_noph"></div>
				<?php else: ?>
					<div class="det-left_noph det-left_noph_male"></div>
				<?php endif; ?>
			<?php endif; ?>
			<div class="det-left_rating">
				<div class="stars png"><span style="width:<?= $model->getRatingPercent() ?>%" class="png"></span></div>
				Рейтинг: <span><?= $model->getRating() ?></span>
				<span class="show-rating-popup">?</span>

				<div class="popup-note popup-rating popup-rating-card popup-rating-card-index">
					<div class="popup-note_cont">
						<p>Рейтинг специалистов формируется на основе отзывов клиентов о качестве работы мастера.</p>
					</div>
					<div class="popup-arr"></div>
				</div>
			</div>
		</div>
		<div class="det-left_c">
			<?php if ($model->salon): ?>
				<div class="det-left_place">
					мастер салона
					<?= CHtml::link('&laquo;' . $model->salon->name . '&raquo;', $model->salon->getProfileUrl()) ?>
				</div>
			<?php endif; ?>
			<h1><?= CHtml::encode($model->getFullName()) ?></h1>

			<div class="det-left_spec">
				<?= $model->getActualSpecsConcatenated() ?>
			</div>
			<?php if ($model->canShow()): ?>
				<div
					class="button button-blue btn-appointment"
					<?php if ($model->salon): ?>
						data-salon-id="<?= $model->salon->id ?>"
					<?php else: ?>
						data-id="<?= $model->id ?>"
					<?php endif; ?>
					data-full="1"
					data-gatype="click-click_on_large"
					><span>Записаться</span></div>
			<?php endif; ?>
			<div class="det-left_txt">
				<?php if ($model->getExperienceName()): ?>
					<p>
						Опыт работы – <?= $model->getExperienceName() ?>.
					</p>
				<?php endif; ?>
				<?php if ($model->educations): ?>
					<p>
					Профессиональное образование:<br/>
					<?php $i = 0;
					foreach ($model->educations as $education): ?>
						<?php if ($i++ == 1): ?><div class="det-left_txt_f"><?php endif; ?>
						<?php echo
							$education->organization .
							($education->course ? ', ' . $education->course : '') .
							($education->specialization ? ', ' . $education->specialization : '') .
							($education->graduation_year ? ' (' . $education->graduation_year . ')' : ''); ?>
						<br/>
					<?php endforeach; ?>
					</p>

				<?php endif; ?>
				<?php if (count($model->educations) == 1 || !$model->educations): ?>
				<div class="det-left_txt_f"><?php endif; ?>
					<?php if ($model->achievements): ?>
					<p><?= nl2br($model->achievements) ?></p>
				</div>
				<a href="#" class="det-left_txt_f_l">подробнее</a>
				<?php else: ?>
			</div>
		<?php endif; ?>
		</div>
		<div style="padding-right:40px">
			<?php
			$this->renderPartial(
				"//partials/_prices",
				[
					"data"           => $model,
					"prices"         => LfPrice::model()->getPrices($model),
					'service'        => null,
					'specialization' => null,
					'all'            => false,
				]
			);
			?>
		</div>
	</div>
</div>
<div class="det-right">
	<div class="det-right_map">
		<script src="<?php echo Yii::app()->homeUrl; ?>js/map-card.js?<?php echo RELEASE_MEDIA; ?>"
				type="text/javascript"></script>
		<script type="text/javascript">
			<?php if($model->salon)	{$center = json_encode($model->salon->map_lat && $model->salon->map_lng ? array($model->salon->map_lat, $model->salon->map_lng) : array(55.75150546844201, 37.616654052733395));
}
			elseif($model->undergroundStation && !$model->add_street) {$center = json_encode(array(55.7558, 37.6178));
}
			else {$center = json_encode($model->map_lat && $model->map_lng ? array($model->map_lat, $model->map_lng) : array(55.75150546844201, 37.616654052733395));
} ?>
			<?php if($model->salon) {$zoom = json_encode($model->salon->map_lat && $model->salon->map_lng ? 15 : 11);
}
			else {$zoom = json_encode($model->map_lat && $model->map_lng ? 15 : 11);
} ?>

			var map = null;
			$(document).ready(function () {
				map = new CardMap();
				map.center = <?php echo $center ?>;
				map.zoom = <?php echo $zoom ?>;
				map.defaultCenter = [55.7558, 37.6178];
				map.balloonContent = <?php echo CJavaScript::encode('м.' . $model->getFullAddress())?>;
				map.metro = "Москва м. " + "<?php echo isset($model->undergroundStation->name) ? $model->undergroundStation->name: '';?>";
				map.completeCallback = function () {
					initCardLikes();
				};
				map.init();
			});
		</script>
		<div id="ya-map" style="height: 200px;"></div>
	</div>
	<?php if ($address = $model->getFullAddress()): ?>
		<p><strong>Адрес:</strong> <?php if ($model->undergroundStation): ?><span
				class="icon-metro png metro-l_<?php echo $model->undergroundStation->undergroundLine->id; ?>"></span><?php endif; ?> <?php echo $address; ?>
		</p>
	<?php endif ?>
	<?php if ($model->has_departure): ?>
		<div class="master-dep">
			<strong>Возможен выезд</strong>
			<span class="show-rating-popup">?</span>

			<div class="popup-note popup-rating popup-depart-card">
				<div class="popup-note_cont">
					<p>Обратите внимание, что стоимость услуги с выездом может отличаться от указанной в анкете.
						Уточняйте конечную стоимость у мастера.</p>
				</div>
				<div class="popup-arr"></div>
			</div>
		</div>
	<?php endif ?>
	<div class="time">
		<p><span><?php if ($model->hrs_wd_from) { ?>с <?php echo $model->hrs_wd_from;
				}
				if ($model->hrs_wd_to) {
					?> до <?php echo $model->hrs_wd_to;
				}
				if ($model->hrs_wd_to || $model->hrs_wd_from){
				?></span>будни:</p><?php } ?>
		<p><span><?php if ($model->hrs_we_from) { ?>с <?php echo $model->hrs_we_from;
				}
				if ($model->hrs_we_to) {
					?> до <?php echo $model->hrs_we_to;
				}
				if ($model->hrs_we_to || $model->hrs_we_from){
				?></span>выходные:</p><?php } ?>
	</div>
	<div class="soc-btn">
		<div class="soc-btn_item" style="width:120px;">
			<div class="fb-like" data-colorscheme="light" data-layout="button_count" data-action="like"
				 data-show-faces="true" data-send="false"></div>
		</div>
		<div class="soc-btn_item" style="width:150px;">

			<div id="vk_like"></div>
		</div>
		<div class="soc-btn_item" style="width:110px;">
			<a href="https://twitter.com/share" class="twitter-share-button" data-lang="ru">Твитнуть</a>
			<script>!function (d, s, id) {
					var js, fjs = d.getElementsByTagName(s)[0];
					if (!d.getElementById(id)) {
						js = d.createElement(s);
						js.id = id;
						js.src = "//platform.twitter.com/widgets.js";
						fjs.parentNode.insertBefore(js, fjs);
					}
				}(document, "script", "twitter-wjs");</script>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="det-right_complain">
		<div class="popup-note popup-abuse"></div>
		<a class="abuse-link" href="#" data-id="<?php echo $model->id; ?>">пожаловаться</a>
	</div>
</div>
<div class="clearfix"></div>


<?php if (count($model->works) > 0): ?>
	<div id="works" class="det-works">
		<div class="det-line_sep"><span>Работы мастера (<?php echo count($model->works); ?>)</span></div>

		<?php
		for ($i = 0; $i < 5 && isset($model->works[$i]); $i++) {
			$this->renderPartial(
				'partials/_work',
				[
					'data'  => $model->works[$i],
					'model' => $model,
					'index' => $i
				]
			);
		}
		?>

		<?php if (count($model->works) > 5): ?>
			<div id="det-works_full" class="det-works_full_close"></div>
			<div style="text-align:center; font-style:italic; margin-left:-20px;">
				<a href="#" class="det-works_switch_open" data-master-id="<?php echo $model->id ?>">
					показать еще
				</a>
				<a href="#" class="det-works_full_link det-works_switch_open">свернуть &uarr;</a>
			</div>
		<?php endif; ?>
	</div>
<?php endif; ?>


<a name="opinion"></a>
<?php
$this->widget(
	'application.components.likefifa.widgets.LfOpinionsWidget',
	array(
		'model'   => $model,
		'opinion' => $opinion,
		'where'   => 'к мастеру',
		'limit'   => 3
	)
);
?>
<div class="clearfix"></div>
</div>