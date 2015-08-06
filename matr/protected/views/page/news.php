<div class="page-container">
	<h1>Новости</h1>
	<?php foreach ($models as $model) { ?>
		<div class="news-item">
			<div class="date"><?php echo $model->getDate(); ?></div>
			<div class="title">
				<a href="/news/<?php echo $model->id; ?>"><?php echo $model->title; ?></a>
			</div>
			<div class="description"><?php echo $model->description; ?></div>
		</div>
	<?php } ?>
</div>