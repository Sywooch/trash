<div class="page-container">
	<h1>Вопрос-ответ</h1>
	<div id="accordion" class="faqq">
		<?php foreach ($models as $model) { ?>
			<h3><?php echo $model->title; ?></h3>
			<div><?php echo $model->text; ?></div>
		<?php } ?>
	</div>
</div>