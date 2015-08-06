<?php
use dfs\docdoc\models\CityModel;

/**
 * @var CityModel $city
 */
?>

<div class="footer l-wrapper">
	<div class="copy">
		<p>
			DocDoc.ru &ndash; поиск врачей и клиник

			<?php
				if (!empty($this->_cities)) {
					$isFirst = true;
					foreach ($this->_cities as $city) {
						if ($isFirst) {
							$isFirst = false;
						} else {
							echo ' | ';
						}
						$host = $city->prefix . \Yii::app()->params['hosts']['front'];
						echo '<a class="footer_copyright_link t-nd" href="//', $host, '">', $city->title_genitive, '</a>';
					}
				}
			?>

			<br />Copyright &copy; <a href="/sitemap">Карта сайта</a>
		</p>
	</div>

	<div class="foot-menu">
		<i></i>
		<p><a href="/register">Регистрация врачей и клиник</a></p>
		Бесплатная регистрация и размещение анкет на портале.
	</div>

	<div class="clear"></div>
</div>
