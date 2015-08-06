<?php
/**
 * @var dfs\docdoc\back\controllers\SectorController $this
 * @var dfs\docdoc\models\SectorModel                $model
 * @var dfs\docdoc\models\DoctorModel[]              $linkedDoctors
 */

$this->breadcrumbs = array(
	'Направления' => array('index'),
	'Изменение направления',
);
?>

<h1>Изменение направления <?php echo $model->name; ?></h1>

<?php $this->widget(
	'LinkedItemsWidget',
	array(
		'title' => 'Связанные врачи',
		'items' => $linkedDoctors,
	)
); ?>
<?php echo $this->renderPartial('_form', array('model' => $model)); ?>