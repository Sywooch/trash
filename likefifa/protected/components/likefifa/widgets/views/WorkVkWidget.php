<?php
use likefifa\components\likefifa\widgets\WorkVkWidget;

/**
 * @var WorkVkWidget $this
 * @var string       $socialImageLink
 * @var string       $socialLink
 * @var string       $socialTitle
 */
?>

<div class="work-social-likes"
	 data-image="<?php echo $socialImageLink; ?>"
	 data-url="<?php echo $socialLink; ?>"
	 data-social-title="<?php echo CHtml::encode($socialTitle) ?>"
	 data-description="Хотите такой же? LikeFifa.ru — лучшие мастера красоты. Выбери своего!"
	 data-zeroes="yes">
	<div class="facebook" title="Поделиться ссылкой на Фейсбуке"></div>
	<div class="vkontakte" title="Поделиться ссылкой во Вконтакте"></div>
</div>