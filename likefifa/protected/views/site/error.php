<div class="content-wrap content-pad-bottom">
<?php
$this->pageTitle=Yii::app()->name . ' - Ошибка';
?>

<h1>Ошибка <?php echo $code; ?></h1>

<div class="error">
<?php echo CHtml::encode($message); ?>
</div>

</div>