<?php
/**
 * @var models\TopicModel  $model
 * @var models\NewsModel[] $newsList
 */
?>
	<h1>
		<?= $model->title ?>
		<a href="/topic/<?= $model->id ?>/edit/">[ edit ]</a>
		<a href="/topic/<?= $model->id ?>/delete/">[ delete ]</a>
		<a href="/topic/<?= $model->id ?>/add/">[ add subtopic ]</a>
		<a href="/topic/<?= $model->id ?>/news/add/">[ add news ]</a>
	</h1>
<?php foreach ($newsList as $news) { ?>
	<div class="news-item">
		[<?= $news->getDate() ?>] <a href="/topic/<?= $model->id ?>/news/<?= $news->id ?>/"><?= $news->title ?></a>
	</div>
<?php } ?>