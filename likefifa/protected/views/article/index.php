<div class="content-wrap content-pad-bottom">
	<div class="det-line_sep" style="margin-top: 11px;"><h1><?php if ($section): ?>Статьи в категории «<?php echo
			$section !=
			'other' ? $section->name : 'другое'; ?>»<?php else: ?>Все статьи<?php endif; ?></h1></div>
	<div class="articles-menu">
		<ul>
			<?php foreach ($sections as $s): ?>
				<?php if ($section && $section != 'other' && $section->id === $s->id): ?>
					<li><?php echo $s->name; ?></li>
				<?php else: ?>
					<li><a href="<?php echo Yii::app()->createUrl(
							'article/index',
							array('sectionRewriteName' => $s->rewrite_name)
						); ?>"><?php echo $s->name; ?> (<?php echo count($s->articles); ?>)</a></li>
				<?php endif; ?>
			<?php endforeach; ?>
			<?php if ($section != 'other'): ?>
				<li><a href="<?php echo Yii::app()->createUrl(
						'article/index',
						array('sectionRewriteName' => 'other')
					); ?>"><?php echo 'другое'; ?> (<?php echo count(
							Article::model()->findAll('article_section_id IS NULL')
						); ?>)</a></li>
			<?php else: ?>
				<li><?php echo 'другое'; ?></li>
			<?php endif; ?>
		</ul>
		<?php if ($section): ?>
			<a href="<?php echo Yii::app()->createUrl('article/index'); ?>">все статьи (<?php echo $allArticlesCount; ?>
				)</a>
		<?php else: ?>
			<span>все статьи (<?php echo $allArticlesCount; ?>)</span>
		<?php endif; ?>
		<br/>
		<br/>
	</div>
	<div class="articles-wrap">
		<?php
		$this->widget(
			'zii.widgets.CListView',
			array(
				'ajaxUpdate'         => false,
				'dataProvider'       => $dataProvider,
				'viewData'           => false,
				'itemView'           => 'data',
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
		?>
	</div>
	<div class="clearfix"></div>
</div>


