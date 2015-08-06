<?php
use dfs\docdoc\objects\Phone;
use dfs\docdoc\models\ClinicModel;

/**
 * @var ClinicModel $clinic
 */

$phones = [];
if ($clinic->asterisk_phone) {
	$phones[] = 'Основной: ' . (new Phone($clinic->asterisk_phone))->prettyFormat('+7 ');
} else {
	foreach ($clinic->phones as $phone) {
		$phones[] = $phone->label . ': ' . (new Phone($phone->number_p))->prettyFormat('+7 ');
	}
}
?>

<div class="result_title__ct">
    <h1 class="result_main__title">О клинике</h1>
</div>

<div class="info_content l-bubble">

	<span class="clinic_about__row">
		<span class="strong">Название клиники: </span><?php echo $clinic->name; ?>
	</span>

	<span class="clinic_about__row">
		<span class="strong">Контактное лицо: </span><?php echo $clinic->contact_name; ?>
	</span>

	<span class="clinic_about__row">
		<span class="strong">Сайт: </span><?php echo $clinic->url; ?>
	</span>

	<span class="clinic_about__row">
		<span class="strong">Электронная почта: </span><?php echo $clinic->email; ?>
	</span>

	<span class="clinic_about__row">
		<span class="strong">Город: </span><?php echo $clinic->city; ?>
	</span>

	<span class="clinic_about__row">
		<span class="strong">Улица: </span><?php echo $clinic->street; ?>
	</span>

	<span class="clinic_about__row">
		<span class="strong">Дом: </span><?php echo $clinic->house; ?>
	</span>

	<span class="clinic_about__row">
		<span class="strong">Телефон: </span><?php echo implode(', ', $phones); ?>
	</span>

</div>
