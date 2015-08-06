<?php
/**
 * @var string           $title
 * @var models\NewsModel $model
 */
?>
<h1><?= $title; ?></h1>
<form action="" method="post">
	<div><?= $model->getLabel("title") ?></div>
	<div><input type="text" name="Data[t.title]" value="<?= $model->title ?>"/></div>
	<?php if (!empty($model->errors["t.title"])) { ?>
		<div><?= $model->errors["t.title"] ?></div>
	<?php } ?>

	<div><?= $model->getLabel("text") ?></div>
	<div><textarea name="Data[t.text]"><?= $model->text ?></textarea></div>
	<?php if (!empty($model->errors["t.text"])) { ?>
		<div><?= $model->errors["t.text"] ?></div>
	<?php } ?>

	<div>
		<button type="submit">Save</button>
	</div>
</form>