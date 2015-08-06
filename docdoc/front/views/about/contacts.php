<?php
/**
 * @var array $centralOffice
 * @var array $emails
 * @var array $press
 * @var array $callCenterPhones
 */
?>

<main class="l-main l-wrapper section_about_wrap" role="main">

	<?php echo $this->renderPartial('leftmenu', ['menu' => $this->_leftmenu, 'active' => 'contacts']); ?>

	<section class="page_about" style="float:right;">

		<article>

			<h3>Служба поддержки</h3>

			<p style="margin-bottom:0;">Будем рады ответить на ваши вопросы, предложения или жалобы. <br>
				<strong>Адрес в Москве:</strong> <?php echo $centralOffice["address"]; ?>
			</p>

			<div class="page_contact_map_wrap">
				<span class="page_contact_maplink l-ib js-ymap-tr">схема проезда</span>

				<div class="js-ymap js-ymap-data" id="map_contacts">
					<div class="js-map-data"
						data-address="<?php echo $centralOffice["address"]; ?>"
						data-latitude="<?php echo $centralOffice["latitude"]; ?>"
						data-longitude="<?php echo $centralOffice["longitude"]; ?>"
						data-zoom-control="1"></div>
				</div>
			</div>
			<div class="aboutus_item_dotted">
				<h3>Оставайтесь на связи</h3>

				<div style="margin-bottom:12px;">Телефоны нашего колл-центра:</div>
				<p style="margin-bottom:0;">
					в Москве — <?php echo $callCenterPhones["msk"]; ?>;
					<br>
					в Санкт-Петербурге — <?php echo $callCenterPhones["spb"]; ?>; <br>
					по России — <?php echo $callCenterPhones["common"]; ?>. <br>
					Поддержка пользователей 7 дней в неделю с 8:00 по 21:00.
				</p>
			</div>
			<div class="aboutus_item_dotted">
				<h3>Пишите нам</h3>

				<p>
					Технические проблемы, пожелания, предложения:
					<a href="mailto:<?php echo $emails["public"]; ?>"><?php echo $emails["public"]; ?></a><br>
					Вопросы по сотрудничеству:
					<a href="mailto:<?php echo $emails["account"]; ?>"><?php echo $emails["account"]; ?></a><br>
					Реклама и маркетинг:
					<a href="mailto:<?php echo $emails["seo"]; ?>"><?php echo $emails["seo"]; ?></a><br>
					Для СМИ:
					<a href="mailto:<?php echo $press["email"]; ?>"><?php echo $press["email"]; ?></a><br>
					Генеральному директору Дмитрию Петрухину:
					<a href="mailto:<?php echo $emails["generalManager"]; ?>">
						<?php echo $emails["generalManager"]; ?>
					</a>
				</p>
			</div>

		</article>

	</section>

</main>
