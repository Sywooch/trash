<?php
/**
 * @var LfOpinionsWidget $this
 */
?>
<div class="det-line_sep">
	<span>Отзывы <?php if ($this->model->opinions): ?>(<?php echo count(
			$this->model->opinions
		); ?>)<?php endif; ?></span>
</div>
<div class="det-com_form">
	<div class='opinion-container det-com_form_success_window_container'>
		<div id="opinion-popup" class="popup-success" style="display: block; top: 448.5px; margin-left: -175px;">
			<div class="popup-close"></div>
			<div class="popup-success_head">Ваш отзыв принят.</div>
			<div class="popup-success_txt">Ваш отзыв отправлен на модерацию и будет опубликован в ближайшее время.
			</div>
			<div class="popup-success_thx">Спасибо!</div>
		</div>
	</div>

	<div class="head">Оставьте свой отзыв</div>
	<?php $form = $this->beginWidget(
		'CActiveForm',
		array(
			'id' => 'new-opinion',
			'enableAjaxValidation' => false,
			'action' => $this->model->getProfileUrl() . '#opinion',
			'htmlOptions' => array(),
		)
	); ?>
	<?php echo LfHtml::activeTextField(
		$this->opinion,
		'name',
		array('placeholder' => 'Ваше имя', 'class' => 'name')
	); ?>
	<?php echo LfHtml::activeTextField($this->opinion, 'tel', array('placeholder' => 'Телефон', 'class' => 'tel')); ?>
	<div class='det-com_form_number'>
		<a href='#' title='Наш портал публикует только проверенные отзывы от клиентов, записавшихся через наш сайт.
			Ваш телефон необходим для подтверждения Вашего отзыва.'>Зачем нам Ваш номер</a><sup>?</sup>
	</div>
	<div class='stars_c'>
		<div class='rating'>
			<div class="det-com_form_rating">
				<div class="stars png">
					<?php for ($i = 1; $i < 6; $i++) { ?>
						<span class="png" title="<?php echo $this->opinion->getRatingValue($i); ?>"></span>
					<?php } ?>
				</div>
				Довольны ли Вы результатом
			</div>
			<?php echo LfHtml::activeHiddenField($this->opinion, 'rating'); ?>
		</div>
		<div class='quality'>
			<div class="det-com_form_rating">
				<div class="stars png">
					<?php for ($i = 1; $i < 6; $i++) { ?>
						<span class="png" title="<?php echo $this->opinion->getQualityValue($i); ?>"></span>
					<?php } ?>
				</div>
				Качество обслуживания
			</div>
			<?php echo LfHtml::activeHiddenField($this->opinion, 'quality'); ?>
		</div>
		<div class='ratio'>
			<div class="det-com_form_rating">
				<div class="stars png">
					<?php for ($i = 1; $i < 6; $i++) { ?>
						<span class="png" title="<?php echo $this->opinion->getRatioValue($i); ?>"></span>
					<?php } ?>
				</div>
				Соотношение цена/качество
			</div>
			<?php echo LfHtml::activeHiddenField($this->opinion, 'ratio'); ?>
		</div>
	</div>
	<div class='is_more'>
		<?php
		if (isset($_POST['LfOpinion']['is_more'])) {
			$is_more = $_POST['LfOpinion']['is_more'];
		} else {
			$is_more = 1;
		}
		?>
		<div class="is_more_container">
			<?php echo LfHtml::radioButtonList(
				'LfOpinion[is_more]',
				$is_more,
				array('1' => 'Да', '0' => 'Нет')
			); ?>
		</div>
		Пошли бы Вы <?php echo $this->where; ?> еще?
	</div>
	<?php echo LfHtml::activeTextField(
		$this->opinion,
		'advantages',
		array('placeholder' => 'Достоинства', 'class' => 'advantages')
	); ?>
	<?php echo LfHtml::activeTextField(
		$this->opinion,
		'disadvantages',
		array('placeholder' => 'Недостатки', 'class' => 'disadvantages')
	); ?>
	<?php echo LfHtml::activeTextArea(
		$this->opinion,
		'text',
		array('placeholder' => 'Напишите здесь свой отзыв', 'rows' => 7, 'class' => 'text')
	); ?>
	<div class="det-com_form_btn"><input type="submit" value="Оставить отзыв"/>

		<div class="button button-blue"><span>Оставить отзыв</span></div>
	</div>
	<?php $this->endWidget(); ?>
</div>
<div class="det-com">
	<?php if (!$this->model->opinions): ?>
		<p>Нет ни одного отзыва.</p>
	<?php endif; ?>
	<?php foreach ($this->model->opinions as $i => $opinion): ?>
		<div class="det-com_item">
			<div class="det-com_author">
				<p><?php echo $opinion->name; ?>, <span class='date'><?php echo $opinion->getCreated(); ?></span></p>
			</div>
			<div class="det-com_body">
				<div class='stars_container'>
					<div class="det-com_rating det-com_rating_right">
						<div class='label'>цена/качество</div>
						<div
							class="stars png"
							title="<?php echo $opinion->getRatioValue($opinion->ratio); ?>"
							>
							<span
								style="width:<?php echo 100 * $opinion->ratio / 5; ?>%"
								class="png"
								></span>
						</div>
					</div>
					<div class="det-com_rating">
						<div class='label'>результат</div>
						<div
							class="stars png"
							title="<?php echo $opinion->getRatingValue($opinion->rating); ?>"
							>
							<span
								style="width:<?php echo 100 * $opinion->rating / 5; ?>%"
								class="png"
								></span>
						</div>
					</div>
					<div class="det-com_rating">
						<div class='label'>обслуживание</div>
						<div
							class="stars png"
							title="<?php echo $opinion->getQualityValue($opinion->quality); ?>"
							>
							<span
								style="width:<?php echo 100 * $opinion->quality / 5; ?>%"
								class="png"
								></span>
						</div>
					</div>
				</div>
				<div class='adv_disadv'>
					<?php if ($opinion->advantages) { ?>
						<div class='adv'>
							<span></span>
							<?php echo nl2br(htmlspecialchars($opinion->advantages)); ?>
						</div>
					<?php } ?>
					<?php if ($opinion->disadvantages) { ?>
						<div class='disadv'>
							<span></span>
							<?php echo nl2br(htmlspecialchars($opinion->disadvantages)); ?>
						</div>
					<?php } ?>
				</div>
				<?php echo nl2br(htmlspecialchars($opinion->text)); ?>
				<div class='is_useful'>
					<div class='question'>
						<div class='label'>Вы считаете этот отзыв полезным?</div>
						<span class='yes'>
							<a href='#' opinion='<?php echo $opinion->id; ?>'>Да</a>
							<span><?php echo (int)$opinion->yes; ?></span> /
						</span>
						<span class='no'>
							<a href='#' opinion='<?php echo $opinion->id; ?>'>Нет</a>
							<span><?php echo (int)$opinion->no; ?></span></span>
						<?php if ($opinion->is_more) { ?>
							<span class='is_more' title='Еще раз пойдет <?php echo $this->where; ?>'></span>
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>

		<?php if ($this->limit > -1 && $this->limit == $i + 1) {
			break;
		} ?>
	<?php endforeach; ?>

	<?php if ($this->limit != -1 && count($this->model->opinions) > $this->limit): ?>
		<p style="text-align:center; font-style:italic; margin-left:-20px;">
			<a href="javascript:void(0);" class="shop-all-opinions" data-target="down">
				Показать еще <?php echo count($this->model->opinions) - $this->limit ?>
				<?php
				echo su::caseForNumber(count($this->model->opinions) - $this->limit, ['отзыв', 'отзыва', 'отзывов'])
				?>
			</a>
		</p>
	<?php endif; ?>
	<?php if($this->limit == -1 && count($this->model->opinions) > 3): ?>
		<p style="text-align:center; font-style:italic; margin-left:-20px;">
			<a href="javascript:void(0);" class="shop-all-opinions" data-target="up">
				Свернуть
			</a>
		</p>
	<?php endif; ?>
</div>

<script type="text/javascript">
	$(function () {
		var submitProcess = false;

		$('.shop-all-opinions').on('click', function () {
			$.get(document.location.href, {allOpinions: $(this).data('target') == 'down' ? 1 : 0}, function (data) {
				var div = $('<div></div>');
				div.append(data);
				$('.det-com').replaceWith(div.find('.det-com'));
			}, 'html');
			return false;
		});

		$('#new-opinion').on('submit', function () {
			if (submitProcess == true)
				return false;
			submitProcess = true;

			$(this).ajaxSubmit({
				url: homeUrl + '<?php echo $this->controller->id ?>/createOpinion/<?php echo $this->model->id ?>/',
				success: function (data, statusText, xhr, $form) {
					submitProcess = false;

					$($form).find('.error').removeClass('error');

					if (typeof(data['success']) != 'undefined' && data['success'] == true) {
						$('#new-opinion').clearForm();
						$("#overlay").show();
						$('#popup').html($('#opinion-popup').html());
						showPopup('popup-success');
					} else {
						for (var n in data) {
							$('#LfOpinion_' + n).parent().addClass('error');
						}
					}
				},
				dataType: 'json'
			});

			return false;
		});
	});
</script>
