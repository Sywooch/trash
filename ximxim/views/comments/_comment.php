<?php
/**
 * @var models\CommentModel $comment
 */
?>
<div>[<?= $comment->getDate() ?>] <?= $comment->name ?> <a href="/comment/delete/<?= $comment->id ?>/">[ delete ]</a></div>
<div class="comment-text"><?= $comment->text ?></div>