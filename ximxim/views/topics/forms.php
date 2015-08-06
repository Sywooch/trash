<?php
/**
 * @var string            $title
 * @var models\TopicModel $model
 */
?>
<h1><?= $title; ?></h1>
<form action="" method="post">
	<div><?= $model->getLabel("title") ?></div>
	<div><input type="text" name="Data[t.title]" value="<?= $model->title ?>"/></div>

	<?php if (!empty($model->errors["t.title"])) { ?>
		<div><?= $model->errors["t.title"] ?></div>
	<?php } ?>

	<div>
		<button type="submit">Save</button>
	</div>
</form>