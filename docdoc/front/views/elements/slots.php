<?php
/**
 * @var \dfs\docdoc\models\RequestModel $request
 */

$df = Yii::app()->dateFormatter;
$time = time();
?>

<link type="text/css" href="/js/datetimepicker/jquery.datetimepicker.css" rel="stylesheet">
<link type="text/css" href="/css/lk/slots.css" rel="stylesheet">

<script src="/js/datetimepicker/jquery.datetimepicker.js"></script>
<script src="/js/slots.js"></script>


<div class="select_slots"
	 data-request-id="<?php echo $request->req_id; ?>"
	 data-clinic-id="<?php echo $request->clinic_id; ?>"
	 data-doctor-id="<?php echo $request->req_doctor_id; ?>"
	>
	<div class="request_popup_head_big">Дата записи</div>
	<div class="request_popup_select_date">
		<div class="b-select_wrap">
			<div class="b-select_arr"></div>
			<div class="b-select_current ui-grad-grey">
				<?php
					echo $request->date_admission ? $df->format('cccc, d MMMM yyyy', $request->date_admission) : 'Выберите дату';
				?>
			</div>
			<div class="b-select_list">
				<?php
				for ($i = 0; $i < 7; $i++) {
					$format = $i > 1 ? 'cccc' : ($i === 1 ? 'Завтра' : 'Сегодня');

					echo '<div class="b-select_list__item" data-date="', date('d-m-Y', $time) , '">';
					echo $df->format($format . ', d MMMM yyyy', $time);
					echo '</div>';

					$time += 86400;
				}
				?>
			</div>
		</div>
		<input type="text" class="request_popup_date_hidden" value="<?php echo date('d-m-Y', $request->date_admission); ?>" name="select_date">
		<input type="hidden" class="request_date_admission" value="<?php echo date('d-m-Y H:i', $request->date_admission); ?>" name="date_admission">
		<div class="request_popup_select_date_ico"></div>
	</div>
	<div class="request_popup_time_wrap">
		<div class="request_popup_time_prev" style="display:none;">...</div>
		<div class="request_popup_time_list"></div>
		<div class="request_popup_time_next" style="display:none;">...</div>
	</div>
</div>
