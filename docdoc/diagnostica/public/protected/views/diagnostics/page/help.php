<div class="right lib-right">
	<div class="box round" style="margin-top: 8px;">
		<i></i>
		<a href="http://<?=Yii::app()->params['hosts']['front']?>/library<?php // echo $this->createUrl('diagnostics'.'/article/index'); ?>">Медицинская<br />библиотека</a>
		Полезные статьи о заболеваниях, современных методах лечения и диагностиках.
	</div>
<!--    
	<div class="box round">
		<i style="background-position: 0 -37px;"></i>
		<a href="http://diagnostica.docdoc.ru/">Диагностические<br />центры</a>
		Вам нужно провести диагностику или обследование? Специализированный портал поможет подобрать диагностический центр рядом с домом.
	</div>
-->
    <div class="box round">
		<i style="background-position: 0 -77px;"></i>
		<a href="http://<?=Yii::app()->params['hosts']['front']?>">Лучшие врачи города</a><br/>
		Мы объединили лучших врачей города на нашем портале. Найдите самого удобного для Вас. Будьте здоровы!
	</div>
</div>
<div class="box-left library">
	<div class="doctor"></div>
	<div class="breadcrumb"><a href="<?php echo Yii::app()->homeUrl; ?>">Главная</a> &ndash;&gt; Как подобрать диагностический центр</div>
	<h1>Информация о сервисе</h1>
	<p>Diagnostica.docdoc.ru – это on-line сервис по поиску диагностических центров. Здесь Вы можете бесплатно подобрать удобный для Вас по местоположению и ценовой политике центр, оказывающий диагностические услуги.</p>
	<div class="page-help">
		<div class="item" id="doctor-choice">
			<div class="page-help-head">Как работает Diagnostica.docdoc.ru?</div>
			В нашей базе размещены анкеты более 200 диагностических центров Москвы. В профиле каждого центра представлена информация о его месторасположении, времени работы, стоимости услуг, а также указан контактный телефонный номер. Вы можете самостоятельно подобрать себе удобный для Вас диагностический центр, воспользовавшись специальной формой поиска на портале.
			<div class="page-help-ico"><img src="<?php echo Yii::app()->homeUrl; ?>i/help/help-img-1.gif" /></div>
		</div>
		<div class="item">
			<div class="page-help-head">Как подобрать диагностический центр?</div>
			На Diagnostica.docdoc.ru Вы задаете параметры поиска «вид диагностики» и «район Москвы» (или «станция метро») для проведения процедуры. В результате Вам будет представлен список центров, ранжированный с учетом стоимости обследования. Вы выбираете подходящий диагностический центр и записываетесь на процедуры, позвонив по указанному номеру телефона. 
			<div class="page-help-ico"><img src="<?php echo Yii::app()->homeUrl; ?>i/help/help-img-2.gif" /></div>
		</div>
	</div>
</div>
<div class="clear"></div>
