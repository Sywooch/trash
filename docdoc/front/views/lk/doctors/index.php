<?php
/**
 * @var int $selectedClinic
 * @var array $branches
 * @var array $statuses
 * @var array $tableConfig
*/

$hostStatic = 'https://' . Yii::app()->params['hosts']['static'];
?>

<div class="result_title__ct">
	<h1 class="result_main__title">Врачи</h1>
</div>

<br/>
<div class="result_filters">
	<form id="DoctorsFiltersForm">
		<?php if (count($branches) > 1): ?>
			<div>
				<span class="filter_doctor__label-txt">Клиника</span>
				<?php
					echo CHtml::dropDownList('clinic_id', $selectedClinic, $branches, [
						'empty' => 'Все филиалы',
						'class' => 'dt_filter',
					]);
				?>
			</div>
			<br />
		<?php endif; ?>

		<div>
			<span class="filter_doctor__label-txt">Статус врача</span>
			<?php
				echo CHtml::dropDownList('status', 0, $statuses, [
					'empty' => 'Все статусы',
					'class' => 'dt_filter',
				]);
			?>
		</div>
	</form>
</div>

<br/>
<button id="newDoctor">Добавить врача</button>&nbsp;&nbsp;&nbsp;
<button id="exportInExcel">Экспорт в Excel</button>
<br/><br/>



<?php
	$this->renderPartial('/../elements/datatable', [
		'tableId' => 'DoctorsTable',
		'tableConfig' => $tableConfig,
		'isInit' => false,
	]);
?>

<script>
	$(document).ready(function() {
		var tableConfig = <?php echo json_encode($tableConfig); ?>;

		tableConfig.fields['schedule'].render = function(data, type, row) {
			return '<a class="button_lk state_change__approve" href="/lk/schedule?doctorId=' + data['id'] + '&clinicId=' + data['clinicId'] + '">Расписание</a>';
		};

		var dt = new DataTableWrapper('#DoctorsTable', '#DoctorsFiltersForm', tableConfig);
		dt.init();
	});
</script>
<link rel="stylesheet" type="text/css" href="<?php echo $hostStatic; ?>/js/chosen/chosen.min.css"/ >
<script src="<?php echo $hostStatic; ?>/js/chosen/chosen.jquery.min.js"></script>
<script src="/js/lk/doctors.js"></script>
