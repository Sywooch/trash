<?php

/**
 * @var dfs\docdoc\front\controllers\FrontController $this
 */

$isSimple = $this->mode === 'headerSimple';

$cityList = $this->getCityList();

end($cityList);
$lastKeyCityList = key($cityList);
?>

<?php if ($this->mode !== 'noHead'): ?>

	<footer class="l-footer l-wrapper<?php echo $isSimple ? ' m-simple' : ''; ?>">

		<div class="<?php echo $isSimple ? 'footer_group_simple' : 'footer_group'; ?>">

			<?php if (!$isSimple): ?>
				<h4 class="footer_group_title">О проекте</h4>
				<ul class="footer_about_list track_links">
					<li class="footer_about_item">
						<a href="/about" class="footer_about_link">О сервисе</a>
					</li>
					<li class="footer_about_item">
						<a href="/doctor" class="footer_about_link">Все врачи</a>
					</li>
					<li class="footer_about_item">
						<a href="/clinic" class="footer_about_link">Все клиники</a>
					</li>
				</ul>

				<div class="footer_forbes">
					<a target="_blank" href="http://www.forbes.ru/svoi-biznes-photogallery/startapy/240008-final-konkursa-startapov-forbes-20122013/photo/5" class="footer_about_link_img">
						<img class="" src="/img/common/forbes.png" alt="Forbes" title="Forbes - Призер 'Стартап 2012'" />
						Призер "Стартап 2012"
					</a>
				</div>
			<?php endif; ?>

			<p class="footer_copyright">
				DocDoc.ru - поиск врачей и клиник
				<?php foreach ($cityList as $k => $c): ?>
					<a href="http://<?php echo $c->prefix; ?>docdoc.ru" class="footer_copyright_link t-nd">
						<?php echo $c->title_genitive; ?>
					</a>
					<?php if ($lastKeyCityList != $k): ?> | <?php endif; ?>
				<?php endforeach; ?>
			</p>

			<p class="footer_copyright">
				Copyright <?php echo date('Y'); ?> &copy;
				<a href="/sitemap" class="footer_copyright_link">Карта сайта</a>
				<?php if (!$isSimple): ?>
					| <a href="/offer" class="footer_copyright_link">Оферта</a>
					| <a href="/affiliate" class="footer_copyright_link">Партнерская программа</a>
				<?php endif; ?>
			</p>
		</div>

		<?php if (!$isSimple): ?>
			<div class="footer_group">
				<h4 class="footer_group_title">Врачам и клиникам</h4>
				<ul class="footer_lk">
					<li class="footer_lk_item i-lk_enter">
						<a href="https://docdoc.ru/lk/auth" class="footer_lk_link">Личный кабинет</a>
					</li>
					<li class="footer_lk_item i-lk_reg">
						<a href="https://docdoc.ru/register" class="footer_lk_link">Регистрация</a>
					</li>
				</ul>
				<p class="footer_lk_info">
					Регистрация врачей и клиник на портале <span class="t-uc">бесплатна</span>.
				</p>
			</div>
		<?php endif; ?>

	</footer>

<?php else: ?>

	<script type="text/javascript">
		$(document).ready(function () {
			window.isFrame = true;
			window.parent.postMessage(document.body.clientHeight, "*");
		});
	</script>

<?php endif; ?>
