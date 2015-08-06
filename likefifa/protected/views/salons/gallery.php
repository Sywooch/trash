		<div class="content-wrap content-pad-bottom">
			<div class="seo-txt">
				<h1><?php echo $this->pageHeader; ?></h1>
				<?php $this->widget('application.components.likefifa.widgets.LfSeoTextWidget', compact('seoText')); ?>
			</div>
			<div class="gal-list" style="padding-top:5px;">
				<?php 
                $this->widget('zii.widgets.CListView', array(
                    'ajaxUpdate' => false,
                    'dataProvider'=>$dataProvider,
                    'viewData' => array(),
                    'itemView'=>'_viewGallery',
                    'sortableAttributes'=>array(),
                    'template' => '{items} {pager}',
                    'emptyText' => '<p style="font-size:17px; font-style:italic; color:#CC00A3;">Работы, соответствующие указанным условиям, не найдены.</p>',
                   	'pager' => array(
                        'cssFile' => false,
                        'header' => '<div class="clearfix"></div><br/><br/><a href="'.$this->forDefault()->createGalleryUrl($specialization, $service, $hasDeparture, true).'" class="pager-all">показать всех на одной странице</a>', 
                        'prevPageLabel' => '<',
                        'nextPageLabel' => '>',
                        'firstPageLabel'=> '',
                        'lastPageLabel'=> '',
                   		'maxButtonCount' => 8,
                    ),
                )); ?>
                <div class="clearfix"></div>
			</div>
		</div>
	</div>	