<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'registration-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>

<div id="step-panel">
	<div class="step-1-active <?php if (!empty($_GET["registration"])) { ?>hide<?php } ?>">
		<div class="step step-active">
			<div class="number">1</div>
			<div class="text">Ознакомьтесь <br />с нашей <br />программой</div>
		</div>
		<a href="#" class="select-slide-2">
			<div class="step">
				<div class="number">2</div>
				<div class="text">Станьте <br />участником <br />программы</div>
			</div>
		</a>
		<a href="#" class="next select-slide-2">Следующий шаг</a>
	</div>
	<div class="step-2-active <?php if (empty($_GET["registration"])) { ?>hide<?php } ?>">
		<a href="#" class="select-slide-1">
			<div class="step">
				<div class="number">1</div>
				<div class="text">Ознакомьтесь <br />с нашей <br />программой</div>
			</div>
		</a>
		<div class="step step-active">
			<div class="number">2</div>
			<div class="text">Станьте <br />участником <br />программы</div>
		</div>
		<?php echo CHtml::submitButton('Регистрация', array("class" => "btn")); ?>
	</div>
</div>

<div class="step-slides-container">
	<div id="step-slides" <?php if (!empty($_GET["registration"])) { ?>style="margin-left: -3800px"; <?php } ?>>
		<div class="step step1">
			<div class="cover-left"></div>
			<div class="cover-right"></div>
			<div class="card">
				<p>Заработайте деньги <strong>легко!</strong></p>
				<p>Получите <strong>скидку</strong> от 5% до 15% <br />на все товары! <br /><a href="/program/">Подробнее</a></p>
			</div>
		</div>
		<div class="step step2">
			<div class="cover-left"></div>
			<div class="cover-right"></div>
			<div class="card">
				<div>
					<?php echo $form->textField($model,'name', array("placeholder" => "Ваше имя")); ?>
					<?php echo $form->error($model,'name'); ?>
				</div>
				<div>
					<?php echo $form->textField($model,'email', array("placeholder" => "Ваш электронный адрес")); ?>
					<?php echo $form->error($model,'email'); ?>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="pagination">
	<div class="pagination-1 <?php if (!empty($_GET["registration"])) { ?>hide<?php } ?>">
		<div class="step1"></div>
		<a href="#" class="select-slide-2"><div class="step2"></div></a>
	</div>
	<div class="pagination-2 <?php if (empty($_GET["registration"])) { ?>hide<?php } ?>">
		<a href="#" class="select-slide-1"><div class="step1"></div></a>
		<div class="step2"></div>
	</div>
</div>

<?php $this->endWidget(); ?>

<div id="goods">
	<div class="images image1">Экономьте свое время</div>
	<div class="images image2">Зарабатывайте с нами не выходя из дома</div>
	<div class="images image3">Принимаем любые виды платежей</div>
	<div class="images image4">100% безопасность</div>
	<div class="container">
		<div class="popular">Популярные товары</div>
		<div class="hand-left"></div>
		<div class="hand-right"></div>
		<div class="item"></div>
		<div class="item item2"></div>
		<div class="item item3"></div>
		<div class="item item4"></div>
		<div class="clear"></div>
	</div>
</div>