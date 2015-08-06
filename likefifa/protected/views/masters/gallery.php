<div class="content-wrap content-pad-bottom">
	<div class="det-back">
		<a href="<?php echo $this->createSearchUrl($specialization, $service); ?>">вернуться к результатам поиска</a>
	</div>
	<div class="seo-txt">
		<h1><?php echo $this->pageHeader; ?></h1>
		<?php $this->widget('application.components.likefifa.widgets.LfSeoTextWidget', compact('seoText')); ?>
	</div>
	<div class="gallery-list_adaptive__wrap gallery-list_adaptive__loading gallery-isotope">
		<?php
		$this->widget(
			'zii.widgets.CListView',
			array(
				'id'                 => 'gallery-list',
				'ajaxUpdate'         => false,
				'dataProvider'       => $dataProvider,
				'viewData'           => array(),
				'itemView'           => 'partials/_viewGallery',
				'sortableAttributes' => array(),
				'template'           => '{items} {pager}',
				'emptyText'          => '<p style="font-size:17px; font-style:italic; color:#CC00A3;">Работы, соответствующие указанным условиям, не найдены.</p>',
				'pager'              => array(
					'class'           => 'application.extensions.yiinfinite-scroll.YiinfiniteScroller',
					'contentSelector' => '#gallery-list .items',
					'itemSelector'    => '.gallery-list_adaptive__item',
					'pages'           => $dataProvider->pagination,
					'callback'        => 'js:galleryAdaptiveUpdate',
				),
			)
		); ?>
		<div class="clearfix"></div>
	</div>
</div>
<script type="text/javascript">
	$(function () {
		initCardLikes();
		initWorkCounter();
	});
</script>
