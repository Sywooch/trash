<?php
/**
 * @var \dfs\docdoc\back\controllers\RatingController   $this
 */

?>
<form class="form" method="post" enctype="multipart/form-data" action="/2.0/<?php echo $this->route;?>">

	<div class="row">
		<?php echo CHtml::fileField('ratingFile'); ?>
		<b><?php echo $message;?></b>
	</div>

	<div class="row">
		<?php echo CHtml::submitButton('Загрузить'); ?>
	</div>

</form>
