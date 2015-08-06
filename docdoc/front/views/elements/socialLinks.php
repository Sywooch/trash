
<?php if (socialKey == 'yes'): ?>

	<div class="social-likes" data-url="http://<?php echo Yii::app()->params['hosts']['front'] . Yii::app()->request->getUrl(); ?>" data-zeroes="yes">
		<div class="facebook">Нравится</div>
		<div class="vkontakte">Одобряю</div>
		<div class="twitter">Твитнуть</div>
	</div>

<?php endif; ?>
