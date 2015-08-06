<?php echo $text; ?>
<p>
<hr/>
По всем вопросам пишите нам на
<a href="mailto:<?php echo Yii::app()->params['mailing']['email']; ?>">
	<?php echo Yii::app()->params["mailing"]["email"]; ?>
</a>
<br/>
Или звоните нашим координаторам
<?php echo Yii::app()->params["mailing"]["phone"]; ?>

<br/><br/>
С уважением, <br/>Ваша LikeFifa
</p>