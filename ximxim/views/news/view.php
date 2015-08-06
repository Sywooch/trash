<?php

use models\CommentModel;

/**
 * @var models\TopicModel     $topicModel
 * @var models\NewsModel      $model
 * @var models\CommentModel[] $comments
 */
?>

<a href="/topic/<?= $topicModel->id ?>/"><?= $topicModel->title ?></a> /
<h1>
	<?= $model->title ?>
	<a href="/topic/<?= $topicModel->id ?>/news/<?= $model->id ?>/edit/">[ edit ]</a>
	<a href="/topic/<?= $topicModel->id ?>/news/<?= $model->id ?>/delete">[ delete ]</a>
</h1>
<div><?= $model->getDate() ?></div>
<div class="news-text"><?= $model->text ?></div>

<h3>Comments</h3>
<div id="comments-container">
	<?php foreach ($comments as $comment) { ?>
		<div><?php $this->renderPartial("/comments/_comment", ["comment" => $comment]); ?></div>
	<?php } ?>
</div>


<form action="/comment/add/" id="comment-form" method="post" onsubmit="return sendComment(this);">
	<div>
		<div><?= CommentModel::model()->getLabel("name") ?></div>
		<div><input type="text" name="Data[t.name]"/></div>
	</div>
	<div>
		<div><?= CommentModel::model()->getLabel("text") ?></div>
		<div><textarea name="Data[t.text]"></textarea></div>
	</div>
	<input type="hidden" name="Data[t.news_id]" value="<?= $model->id ?>"/>

	<div>
		<button tyle="submit">Send</button>
	</div>
</form>
<div id="comment-success">Comment has been sent successfully!</div>