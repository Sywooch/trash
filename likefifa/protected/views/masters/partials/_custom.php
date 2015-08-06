<?php

use likefifa\models\RegionModel;
use likefifa\components\Seo;
use likefifa\models\CityModel;

/**
 * @var MastersController   $this
 * @var string              $speciality
 * @var CActiveDataProvider $dataProvider
 * @var CityModel           $city
 * @var LfService           $service
 * @var LfSpecialization    $specialization
 * @var CActiveDataProvider $top10
 */
use likefifa\components\helpers\ListHelper;

$cityUrl = $this->forDefault()->createSearchUrl(null, null, null, [], null, null, $city);
?>

<div class="content-wrap content-pad-bottom">
<div class="col-left">
<div class="breadcrumbs">
	<a href="<?php echo $this->forDefault()->createSearchUrl(); ?>">Все мастера</a>

	<?php if($speciality): ?>
		<span>&raquo;</span>
		<?php
		$group = LfGroup::model()->getModelByRewriteName($speciality);
		if($group) {
			$cityUrl = $group->getLinkForMain($city);
			echo CHtml::link(
				su::ucfirst($group->name),
				$group->getLinkForMain(),
				[
					'class' => $city != null ? '' : 'act',
				]
			);
		}
		?>
	<?php endif; ?>

	<?php if ($specialization): ?>
		<span>&raquo;</span>
		<?php
		$specialization->setMastersSearch();
		$cityUrl = $specialization->getSearchUrl($city);
		echo CHtml::link(
			su::ucfirst($specialization->name),
			$specialization->getSearchUrl(),
			[
				'class' => $city != null ? '' : 'act',
			]
		);
		?>
	<?php endif; ?>

	<?php if ($service): ?>
		<span>&raquo;</span>
		<?php
		$service->setMastersSearch();
		$cityUrl = $service->getSearchUrl($city);
		echo CHtml::link(
			su::ucfirst($service->name),
			$service->getSearchUrl(),
			[
				'class' => $city != null ? '' : 'act',
			]
		);
		?>
	<?php endif; ?>

	<?php if ($city): ?>
		<span>&raquo;</span>
		<?php echo CHtml::link(
			$city->name,
			$cityUrl,
			[
				'class' => 'act'
			]
		); ?>
	<?php endif; ?>
</div>

<?php if($specialization != null && $specialization->isAllowPhoto()): ?>
	<a href="<?php echo $this->forDefault()->createGalleryUrl($specialization, $service); ?>"
	   class="search-gal-all-link png">Фотогалерея работ</a>
<?php endif; ?>

<div class="seo-txt">
	<h1><?php echo su::ucfirst($this->pageHeader ?: $this->pageTitle); ?></h1>
	<?php if ($this->pageSubheader): ?>
		<p><?php echo $this->pageSubheader; ?></p>
	<?php endif; ?>
	<?php
	if ($seoText && !$speciality) {
		$this->widget('application.components.likefifa.widgets.LfSeoTextWidget', compact('seoText'));
	} else {
		if ($stations && $serviceName) {
			?>
			<p><strong><?php echo $serviceName; ?> возле метро <?php echo $stationsName; ?>.</strong> Вам необходима
				услуга "<?php echo $serviceName; ?>"?
				Найдите частного мастера красоты на нашем сайте!</p>
			<?php if(count($stations) == 1): ?>
				<p><strong>На сайте LikeFifa.ru</strong> представлены лучшие мастера красоты, работающие возле
					метро <?php echo $stationsName; ?>.
					В анкете мастеров Вы можете ознакомиться с прайс-листом, фотографиями работ, образованием мастера,
					а также посмотреть на карте его адрес. При выборе мастера обращайте внимание на отзывы от его
					клиентов и его рейтинг.</p>
			<?php endif; ?>
		<?php
		} else {
			if ($districts && $serviceName && Yii::app()->request->getQuery("districts")) {
				?>
				<p><strong><?php echo $serviceName; ?> в районе <?php echo $districtsName; ?>.</strong> Вам
					необходима
					услуга "<?php echo $serviceName; ?>"?
					Найдите частного мастера красоты на нашем сайте!</p>
				<p><strong>На сайте LikeFifa.ru</strong> представлены лучшие мастера красоты Москвы, работающие в
					районе <?php echo $districtsName; ?>.
					В анкете мастеров Вы можете ознакомиться с прайс-листом, фотографиями работ, образованием
					мастера,
					а также посмотреть на карте его адрес. При выборе мастера обращайте внимание на отзывы от его
					клиентов и его рейтинг.</p>
			<?php
			}
		}
	}
	?>
</div>

<div class="search-res_head">
	<div class="sort">
		<span>упорядочить по:</span>

		<?php
		$d = $sorting === 'rating_composite' ? $reverseDirection : 'desc';
		$class = $sorting === 'rating_composite' ? ($d === 'desc' ? '' : 'desc') : 'desc';
		?>

		<?php if ($sorting === 'rating_composite'): ?><b><?php endif; ?>
			<a href="<?php if (isset($_GET['showAll'])) {
				$showAll = 1;
			} else {
				$showAll = 0;
			}
			echo $this->forDefault()->createSearchUrl(
				$specialization,
				$service,
				$hasDeparture,
				$stations,
				$area,
				$districts,
				$city,
				$showAll,
				'rating_composite',
				$d
			); ?>" class="<?php echo $class; ?>"><span>по рейтингу</span></a>
			<?php if ($sorting === 'rating_composite'): ?></b><?php endif; ?>

		<?php if ($service): ?>
			<?php
			$d = $sorting === 'price' ? $reverseDirection : 'asc';
			$class = $sorting === 'price' ? ($d === 'desc' ? '' : 'desc') : '';
			?>

			<?php if ($sorting === 'price'): ?><b><?php endif; ?>
			<a href="<?php echo $this->forDefault()->createSearchUrl(
				$specialization,
				$service,
				$hasDeparture,
				$stations,
				$area,
				$districts,
				$city,
				$showAll,
				'price',
				$d
			); ?>" class="<?php echo $class; ?>"><span>по цене</span></a>
			<?php if ($sorting === 'price'): ?></b><?php endif; ?>
		<?php endif; ?>

	</div>
			<span class="txt">
				<?php echo su::caseForNumber(
					$dataProvider->totalItemCount,
					array('Найден', 'Найдено', 'Найдено')
				); ?> <span><?php echo
						$dataProvider->totalItemCount .
						' ' .
						su::caseForNumber(
							$dataProvider->totalItemCount,
							array('мастер', 'мастера', 'мастеров')
						); ?></span>
				<?php if (!$speciality) { ?>
					<a
						href="<?php echo $this->forSalons()->createSearchUrl(
							$specialization,
							$service,
							$hasDeparture,
							$stations,
							$area,
							$districts,
							$city
						); ?>"
						data-url="<?php echo $this->forSalons()->createCountUrl(
							$specialization,
							$service,
							$hasDeparture,
							$stations,
							$area,
							$districts,
							$city
						); ?>"
						class="another-entity-count"
						></a>
				<?php } ?>
			</span>
</div>

<?php
$this->widget(
	'zii.widgets.CListView',
	array(
		'ajaxUpdate'         => false,
		'dataProvider'       => $dataProvider,
		'viewData'           => compact('specialization', 'service', 'hasDeparture'),
		'itemView'           => 'partials/_view',
		'sortableAttributes' => array(),
		'template'           => '{items} {pager}',
		'emptyText'          => '',
		'pager'              => array(
			'cssFile'        => false,
			'header'         => false,
			'prevPageLabel'  => '<',
			'nextPageLabel'  => '>',
			'firstPageLabel' => '',
			'lastPageLabel'  => '',
			'maxButtonCount' => 8,
		),
	)
);

if ($top10) {
	?>

	<?php if (!$dataProvider->totalItemCount) { ?>
		<p class="top-10-message">
			<strong>
				<em>По данному запросу мастеров не найдено.</em>
			</strong>
			<br/>Рекомендуем ознакомиться со списком наших лучших мастеров.
		</p>
	<?php } else { ?>
		<p class="top-10-message">
			<strong>
				<em>Это все мастера, найденные по заданным параметрам.</em>
			</strong>
			<br/>Предлагаем Вам также ознакомиться со списком лучших мастеров.
		</p>
	<?php } ?>

	<div class='search-res_head search-res-top10'>
		<span class='txt'><span>Лучшие мастера</span></span>
	</div>

	<?php
	$this->widget(
		'zii.widgets.CListView',
		array(
			'ajaxUpdate'         => false,
			'dataProvider'       => $top10,
			'viewData'           => compact('specialization', 'service', 'hasDeparture'),
			'itemView'           => 'partials/_view',
			'sortableAttributes' => array(),
			'template'           => '{items}',
			'emptyText'          => '',
		)
	);
} elseif (!$dataProvider->totalItemCount) {
	echo '<p>К сожалению, по данным параметрам мастеров не найдено.</p>';
}

?>

</div>
<div class="col-right">

	<?php if (Yii::app()->activeRegion->isMoscow()) { ?>
		<div class="pre-map-rht">
			<a href="<?php echo $this->forDefault()->createMapUrl($specialization, $service, $hasDeparture); ?>"
			   class="pre-map-img"><img src="<?php echo Yii::app()->homeUrl; ?>i/search-map.jpg"
										style="display:block;"/></a>
			<a href="<?php echo $this->forDefault()->createMapUrl($specialization, $service, $hasDeparture); ?>"
			   class="pre-map-lbl">посмотреть на карте</a>
		</div>
	<?php } ?>

	<form action="<?php echo $this->forDefault()->createRedirectUrl('custom'); ?>" method="GET"
		  class="form-wrap filter-rht" id="filter-form">
		<input type="hidden" name="speciality" id="speciality" value="<?php echo $speciality; ?>"/>
		<input type="hidden" name="area" id="areaMoscow" value="<?php echo $area ? $area->id : null; ?>"/>
		<input type="hidden" name="districts" id="districtMoscow"
			   value="<?php echo ListHelper::buildIdList($districts); ?>"/>

		<div class="head">Вы ищете:</div>
		<div class="filter-head">вид услуг:</div>
		<div class="form-inp spec-selector">
			<input type="hidden" id="inp-select-popup-service-type" name="specialization"
				   value="<?php echo $specialization ? $specialization->id : null; ?>"/>

			<div class="form-select-over" data-select-popup-id="select-popup-service-type"></div>
			<div class="form-select" id="cur-select-popup-service-type"><?php echo $specialization
					? $specialization->name : 'Выберите из списка'; ?></div>
			<div class="form-select-arr png"></div>
			<div class="form-select-popup" id="select-popup-service-type">
				<div class="form-select-popup-long">
					<span class="item<?php echo $specialization ? '' : ' act'; ?>"
						  data-value="">Выберите из списка</span>
					<?php foreach (LfSpecialization::model()->ordered()->findAll() as $spec): ?>
						<span class="item<?php echo
						$specialization && ($spec->id == $specialization->id) ? ' act' : ''; ?>"
							  data-value="<?php echo $spec->id; ?>"><?php echo $spec->name; ?></span>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<?php $this->renderPartial("/partials/_service_list", compact("service")); ?>
		<?php $this->renderPartial("/partials/_geo", compact("stations", "districts")); ?>
		<?php $this->renderPartial("/partials/_city_list", compact("city")); ?>

		<div class="filter-check">
			<span class="form-inp_check" data-check-id="f_home"><i id="i-check_f_home" class="png"></i><input
					type="checkbox" id="inp-check_f_home" name="hasDeparture" <?php echo $hasDeparture
					? 'checked="checked"' : ''; ?> />Возможен выезд</span>
		</div>
		<div style="text-align:center"><input type="submit" class="style-submit" value="Найти"/>

			<div class="button button-pink" id="filter-right-sbmt"><span>Найти</span><img
					src="<?php echo Yii::app()->homeUrl; ?>i/icon-search-filter.png" class="png"/></div>
		</div>
	</form>
	<?php
	if ($specialization && $specialization->services) {
		?>
		<div class="rht-links">
			<div class="head">
				<a href="<?php echo $specialization->getSearchUrl($city); ?>"><?php echo $specialization->name; ?>:</a>
			</div>
			<ul>
				<?php foreach ($specialization->services as $service) { ?>
					<?php if ($specialization->bindedService &&
						$service->id === $specialization->bindedService->id
					) {
						continue;
					} ?>
					<li><a href="<?php echo $service->getSearchUrl($city); ?>"><?php echo $service->name; ?></a></li>
				<?php } ?>
				<li>
					<a href="<?php echo $specialization->groupOne->getLinkForMain($city); ?>">
						Лучшие <?php echo $specialization->groupOne->many . " " . Seo::$location->name_genitive; ?>
					</a>
				</li>
			</ul>
		</div>
	<?php
	} else {
		if ($speciality) {
			$specializations = LfGroup::model()->getSpecializationsByRewriteName($speciality);
			if ($specializations) {
				?>
				<div class="rht-links">
					<div class="head">
						Услуги <?php echo LfGroup::model()->getModelByRewriteName($speciality)->genitive; ?>
					</div>
					<ul>
						<?php
						foreach ($specializations as $spec) {
							if (count($specializations) <= 2) {
								foreach ($spec->services as $service) {
									?>
									<li>
										<a href="<?php echo $service->getSearchUrl($city); ?>"><?php echo $service->name; ?></a>
									</li>
								<?php }
							} else { ?>
								<li><a href="<?php echo $spec->getSearchUrl($city); ?>"><?php echo $spec->name; ?></a></li>
							<?php }
						} ?>
					</ul>
				</div>
			<?php
			}
		}
	}
	?>
	<div class="rht-articles">
		<?php if (count($articles) < 4): ?>
			<?php foreach ($articles as $article): ?>
				<div class="item">
					<a href="<?php echo $article->getDetailUrl(); ?>" class="name"><?php echo $article->name; ?></a>

					<div class="txt">
						<?php echo $article->description; ?>
					</div>
				</div>
			<?php endforeach; ?>
		<?php else: ?>
			<?php for ($i = 0; $i < 3; $i++): ?>
				<div class="item">
					<a href="<?php echo $articles[$i]->getDetailUrl(); ?>"
					   class="name"><?php echo $articles[$i]->name; ?></a>

					<div class="txt">
						<?php echo $articles[$i]->description; ?>
					</div>
				</div>
			<?php endfor; ?>
			<a href="<?php echo $articles[0]->getSectionUrl(); ?>">Читать все статьи раздела</a>
		<?php endif; ?>
	</div>
</div>
<div class="clearfix"></div>
</div>
</div>