<?php

use dfs\docdoc\models\ServiceModel;

/**
 * @var dfs\docdoc\models\PartnerModel       $partner
 * @var dfs\docdoc\models\PartnerCostModel[] $partnerCosts
 */
?>

<div class="result_title__ct">
    <h1 class="result_main__title">О партнере</h1>
</div>

<div class="info_content l-bubble">

	<span class="clinic_about__row">
		<span class="strong">Партнер: </span><?php echo $partner->name; ?>
	</span>

	<span class="clinic_about__row">
		<span class="strong"><a href="/static/docs/docdoc_partner_offer.pdf" target="_blank">Оферта</a> принята: </span>
        <?php echo ($partner->offer_accepted ? 'Да' : 'Нет') . ' (' . $partner->offer_accepted_timestamp . ' c ' .$partner->offer_accepted_from_addresses . ')'; ?>
	</span>

	<span class="clinic_about__row">
		<span class="strong">Контактное лицо: </span><?php echo $partner->contact_name; ?>
	</span>

	<span class="clinic_about__row">
		<span class="strong">Телефон: </span><?php echo $partner->contact_phone; ?>
	</span>

	<span class="clinic_about__row">
		<span class="strong">Электронная почта: </span><?php echo $partner->contact_email; ?>
	</span>

	<span class="clinic_about__row">
		<span class="strong">Юридическое лицо: </span> —
	</span>

	<span class="clinic_about__row">
		<span class="strong">ОГРН: </span> —
	</span>

	<span class="clinic_about__row">
		<span class="strong">ИНН: </span> —
	</span>

	<span class="clinic_about__row">
		<span class="strong">Адрес фактический: </span> —
	</span>

	<span class="clinic_about__row">
		<span class="strong">Адрес юридический: </span> —
	</span>

	<span class="clinic_about__row">
		<span class="strong">Применяемая система налогооблажения: </span> —
	</span>

	<span class="clinic_about__row">
		<span class="strong">Банковские реквизиты для осуществления платежей: </span> —
	</span>

	<?php foreach ($partnerCosts as $partnerCost) { ?>
		<?php if (array_key_exists($partnerCost->service_id, ServiceModel::$service_types)) { ?>
			<span class="clinic_about__row">
				<span class="strong">
					<?php echo ServiceModel::$service_types[$partnerCost->service_id]; ?>
					в <?php echo $partnerCost->city ? $partnerCost->city->title_prepositional : "Москве"; ?>
				</span>
				—
					<?php echo number_format($partnerCost->cost, 2, '.', ' '); ?> р.
			</span>
		<?php } ?>
	<?php } ?>

    <span class="clinic_about__row">
		Для партнеров существует возможность использования партнерской ссылки для переливания
		трафика с баннеров/других рекламных материалов с последующей оплатой за лиды.
		Чтобы мы могли отслеживать партнерские заявки, добавляйте в адрес ссылки
		параметр вида ?pid=<?php echo $partner->id; ?>
	</span>

</div>
