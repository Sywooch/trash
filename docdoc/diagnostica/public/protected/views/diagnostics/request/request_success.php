<?php
/**
 * @var dfs\docdoc\models\RequestModel $model
 * @var bool                           $withNewline
 */
?>

Вы записаны на <?php
echo $model->diagnostics->parent
	? "{$model->diagnostics->parent->accusative_name} {$model->diagnostics->genitive_name}"
	: $model->diagnostics->accusative_name;
if (!empty($model->date_admission)) {
	$date = \CTimestamp::getDate($model->date_admission);
	echo " на " . date('d.m.Y', $model->date_admission);
	if ($date['hours'] != 0 || $date['minutes'] != 0) {
		echo " в " . date('H:i', $model->date_admission);
	}
}
?> в <?php echo $model->clinic->name; ?>. <?php if ($withNewline) { ?><br/><?php } ?>Адрес клиники: г. <?php
echo $model->clinic->clinicCity->title;
?>, <?php echo $model->clinic->getAddress(); ?>. <?php if ($withNewline) { ?><br/><?php
} ?>Оператор свяжется с Вами в течение 15 минут для подтверждения даты и времени посещения. <?php
if ($withNewline) { ?><br/><br/><?php } ?>Спасибо за использование нашего сервиса!