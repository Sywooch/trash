<?php

use dfs\docdoc\extensions\TextUtils;

/**
 * @var dfs\docdoc\front\controllers\FrontController $this
 */

$statistics = $this->getStatistics();

$city = Yii::app()->city->getCity();
$cityList = $this->getCityList();
?>

<header class="l-header l-wrapper <?php echo $this->mode === 'headerSimple' ? 'l-header-simple m-simple' : ''; ?>">

	<?php if ($this->mode === 'headerSimple'): ?>

		<div class="logo">
			<span class="logo_link">
				<img class="m-fit" src="/img/common/logo.png" alt="DocDoc.ru" />
			</span>
		</div>

		<div class="header_contact">
			<p>
				<span class="header_contact_title">Поможем найти врача</span>
				<?php if ($this->phoneForPage): ?>
					<span class="header_contact_phone comagic_phone call_phone_1">
						<?php echo $this->phoneForPage->prettyFormat(); ?>
					</span>
				<?php endif; ?>
			</p>
		</div>

	<?php elseif ($this->mode !== 'noHead'): ?>

		<div class="logo">
			<a class="logo_link" <?php echo $this->isLandingPage ? '' : 'href="/"'; ?>>
				<img class="m-fit" src="/img/common/logo.png" alt="DocDoc.ru" title="На главную страницу" />
			</a>

			<?php if ($this->isLandingPage): ?>

				<div class="freelabel">
					<div class="freelabel_item">бесплатно</div>
				</div>

			<?php else: ?>

				<div id="ChangeCityBlock" class="b-dropdown tooltip">

					<div class="b-dropdown_item b-dropdown_item__current">
						<span id="CurrentCityName" class="b-dropdown_item__text">
							<?php echo $city->title; ?>
						</span>
						<span class="b-dropdown_item__icon">
						</span>
					</div>

					<ul class="b-dropdown_list">
						<?php foreach ($cityList as $c): ?>
							<li class="b-dropdown_item <?php echo $c->id_city == $city->id_city ? 's-current' : ''; ?>" data-cityid="<?php echo $c->id_city; ?>">
								<?php echo $c->title; ?>
							</li>
						<?php endforeach; ?>
					</ul>

					<form class="b-dropdown_form" name="cityselector" method="get" action="/service/changeCity.php" >
						<input class="b-dropdown_input" name="cityid" type="hidden" />
					</form>

				</div>

			<?php endif; ?>
		</div>

		<ul class="counters">
			<li class="counters_item">
				<span class="counters_num"><?php echo $statistics['RequestCount']; ?></span>
				<?php echo TextUtils::caseForNumber($statistics['RequestCount'], ['запись', 'записи', 'записей']); ?> сегодня
			</li>
			<li class="counters_item">
				<span class="counters_num"><?php echo $statistics['DoctorsCount']; ?></span>
				<?php echo TextUtils::caseForNumber($statistics['DoctorsCount'], ['врач', 'врача', 'врачей']); ?> в базе
			</li>
			<li class="counters_item">
				<span class="counters_num"><?php echo $statistics['ReviewsCount']; ?></span>
				<?php echo TextUtils::caseForNumber($statistics['ReviewsCount'], ['отзыв', 'отзыва', 'отзывов']); ?>
			</li>
		</ul>

		<div class="header_contact i-doctor_r">
			<span class="header_contact_title">Поможем найти врача</span>
			<div class="header_contact_text">

				<?php if ($this->phoneForPage): ?>
					<span class="t-nw">
						звоните
						<?php if ($this->isMobile): ?>
							<a href="tel:+<?php echo $this->phoneForPage->getNumber(); ?>" class="header_contact_phone">
								<?php echo $this->phoneForPage->prettyFormat(''); ?>
							</a>
						<?php else: ?>
							<span class="header_contact_phone comagic_phone call_phone_1">
								<?php echo $this->phoneForPage->prettyFormat(); ?>
							</span>
						<?php endif; ?>
					</span>
				<?php endif; ?>

				<span class="callback js-callmeback">
					<?php echo $this->phoneForPage ? 'или' : ''; ?>
					<span class="js-callmeback-tr ps">мы перезвоним вам</span>
					<form class="req_form callback_form s-hidden" action="/routing.php?r=request/save" method="post">
						<input type="text" class="callback_input ui-input m-small js-mask-phone" name="requestPhone" />
						<input type="submit" class="ui-btn ui-btn_green callback_submit" value="ок" />
					</form>
				</span>

				<?php echo $this->phoneForPage ? '' : '<br />'; ?>

			</div>
		</div>

		<?php echo $this->renderXsl('searchPanel'); ?>

	<?php else: ?>

		<?php // echo $this->renderPartial('searchPanel'); ?>
		<?php echo $this->renderXsl('searchPanel'); ?>

	<?php endif; ?>

</header>
