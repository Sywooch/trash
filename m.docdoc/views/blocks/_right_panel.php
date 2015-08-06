<?php
/**
 * @var string $id
 */
?>
<div
	id="<?php echo !empty($id) ? $id : "rightPanel"; ?>"
	data-role="panel"
	data-position="right"
	data-display="reveal"
	class="rightPanel"
	>
	<a class="close-btn ui-link right-panel-close">Скрыть меню</a>
	<div class="aside-header">
		<a href="<?php echo Yii::app()->createUrl("site/index"); ?>" title="Перейти на главную">
			<span class=" logo-small"></span>
		</a>

	</div>

    <?php $this->renderPartial("/blocks/_city_change"); ?>

	<div class="aside-description">
		<p>На нашем портале вы можете выбрать врача и записаться к нему на прием.</p>

		<p>Мы поможем вам найти хорошего специалиста!</p>
	</div>
	<div class="aside-block mb-15"><strong>О проекте</strong>
		<ul>
			<li><a href="<?php echo Yii::app()->createUrl("search/search"); ?>">Все врачи</a><i></i>
			</li>
		</ul>
	</div>
	<div class="aside-foo"><a href="<?php echo Yii::app()->createUrl("site/index"); ?>">Перейти на главную</a></div>

	<div class="aside-foo">
		<a href="<?=Yii::app()->city->getDesktopUrl() ?>">
			Перейти на основную версию сайта
		</a>
	</div>

</div>