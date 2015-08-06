<?php
use likefifa\models\CityModel;
use likefifa\components\helpers\ListHelper;

/**
 * @var SalonsController    $this
 * @var string              $speciality
 * @var CActiveDataProvider $dataProvider
 * @var CityModel           $city
 * @var LfService           $service
 * @var LfSpecialization    $specialization
 */

$this->forSalons();

$cityUrl = $this->forDefault()->createSearchUrl(null, null, null, [], null, null, $city);
?>
<div class="content-wrap content-pad-bottom">
<div class="col-left">
	<div class="breadcrumbs">

		<a href="<?php echo $this->forDefault()->createSearchUrl(); ?>">Все салоны</a>
		<?php if ($specialization): ?>
			<span>&raquo;</span>
			<?php
			$specialization->setSalonsSearch();
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
			$service->setSalonsSearch();
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
	<a href="<?php echo $this->forMasters()->createGalleryUrl($specialization, $service); ?>"
	   class="search-gal-all-link png">Фотогалерея работ</a>

	<div class="seo-txt">
		<h1><?php echo su::ucfirst($this->pageHeader ?: $this->pageTitle); ?></h1>
		<?php if ($this->pageSubheader): ?>
			<p><?php echo $this->pageSubheader; ?></p>
		<?php endif; ?>
		<?php $this->widget('application.components.likefifa.widgets.LfSeoTextWidget', compact('seoText')); ?>
	</div>
	<div class="search-res_head">
		<div class="sort">
			<span>упорядочить по:</span>

			<?php
			$d = $sorting === 'rating_composite' ? $reverseDirection : 'desc';
			$class = $sorting === 'rating_composite' ? ($d === 'desc' ? '' : 'desc') : 'desc';
			?>

			<?php if ($sorting === 'rating_composite'): ?><b><?php endif; ?>
				<noindex><a href="<?php echo $this->forDefault()->createSearchUrl(
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
					); ?>" class="<?php echo $class; ?>" rel="nofollow"><span>по рейтингу</span></a></noindex>
				<?php if ($sorting === 'rating_composite'): ?></b><?php endif; ?>

			<?php if ($service): ?>
				<?php
				$d = $sorting === 'price' ? $reverseDirection : 'asc';
				$class = $sorting === 'price' ? ($d === 'desc' ? '' : 'desc') : '';
				?>

				<?php if ($sorting === 'price'): ?><b><?php endif; ?>
				<noindex>
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
					); ?>" class="<?php echo $class; ?>" rel="nofollow"><span>по цене</span></a></noindex>
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
									array('салон', 'салона', 'салонов')
								); ?></span>
						<a
							href="<?php echo $this->forMasters()->createSearchUrl(
								$specialization,
								$service,
								$hasDeparture,
								$stations,
								$area,
								$districts,
								$city
							); ?>"
							data-url="<?php echo $this->forMasters()->createCountUrl(
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
					</span>
	</div>
	<?php
	$this->widget(
		'zii.widgets.CListView',
		array(
			'ajaxUpdate'         => false,
			'dataProvider'       => $dataProvider,
			'viewData'           => compact('specialization', 'service', 'hasDeparture'),
			'itemView'           => '_view',
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
					<em>По данному запросу салонов красоты не найдено.</em>
				</strong>
				<br/>Рекомендуем ознакомиться со списком лучших салонов.
			</p>
		<?php } else { ?>
			<p class="top-10-message">
				<strong>
					<em>Это все салоны красоты, найденные по заданным параметрам.</em>
				</strong>
				<br/>Предлагаем Вам также ознакомиться со списком лучших салонов.
			</p>
		<?php } ?>

		<div class='search-res_head search-res-top10'>
			<span class='txt'><span>Лучшие салоны</span></span>
		</div>

		<?php
		$this->widget(
			'zii.widgets.CListView',
			array(
				'ajaxUpdate'         => false,
				'dataProvider'       => $top10,
				'viewData'           => compact('specialization', 'service', 'hasDeparture'),
				'itemView'           => '_view',
				'sortableAttributes' => array(),
				'template'           => '{items}',
				'emptyText'          => '',
			)
		);
	} elseif (!$dataProvider->totalItemCount) {
		echo '<p>Салоны, соответствующие указанным условиям, не найдены.</p>';
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
						<span class="item<?php echo $specialization && ($spec->id == $specialization->id) ? ' act'
							: ''; ?>" data-value="<?php echo $spec->id; ?>"><?php echo $spec->name; ?></span>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<?php $this->renderPartial("/partials/_service_list", compact("service")); ?>
		<?php $this->renderPartial("/partials/_geo", compact("stations", "districts")); ?>
		<?php $this->renderPartial("/partials/_city_list", compact("city")); ?>

		<div style="text-align:center"><input type="submit" class="style-submit" value="Найти"/>

			<div class="button button-pink" id="filter-right-sbmt"><span>Найти</span><img
					src="<?php echo Yii::app()->homeUrl; ?>i/icon-search-filter.png" class="png"/></div>
		</div>
	</form>
	<?php if ($specialization && $specialization->services): ?>
		<div class="rht-links">
			<div class="head"><a href="<?php echo $specialization->setSalonsSearch()->getSearchUrl(
					$city
				); ?>"><?php echo $specialization->name; ?>:</a></div>
			<ul>
				<?php foreach ($specialization->services as $service): ?>
					<?php if ($specialization->bindedService && $service->id === $specialization->bindedService->id) {
						continue;
					} ?>
					<li><a href="<?php echo $service->setSalonsSearch()->getSearchUrl(
							$city
						); ?>"><?php echo $service->name; ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
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