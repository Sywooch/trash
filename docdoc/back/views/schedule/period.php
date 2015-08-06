<h1>Расписание</h1>
<?php

$data = array();

foreach ($intervals as $interval) {

	$time = strtotime($interval['start']);
	$data[date('Y-m-01', $time)][date('Y-m-d', $time)][] = 'c '.date('H:i', $time).' по '.date('H:i', strtotime($interval['end']));
}


foreach ($data as $month => $days) {

	echo '<a class="toogle-month" href="#month' . date('Ym', strtotime($month)) . '">
			' . Yii::app()->dateFormatter->format('MM.yyyy', $month). '
		</a>';

	if (empty($date)) {
		$class = ($month === date('Y-m-01')) ? 'open' : '';
	} else {
		$class = (date('Y-m-01', $date) === $month) ? 'open' : '';
	}

	echo '<ul id="month' . date('Ym', strtotime($month)) . '" class="schedule schedule-period ' . $class . '">';

	foreach ($days as $day => $schedule) {
		echo '<li>
			<div class="sc-day">' . Yii::app()->dateFormatter->format('E, dd.MM', $day) . '</div>
			<div class="sc-time">';

		foreach ($schedule as $work) {
			echo '<p>' . $work . '</p>';
		}

		echo '</div>
		</li>';

	}

	echo '</ul>';
}

?>
<script type="text/javascript">

	$(".toogle-month").click(function() {
		$(".schedule").removeClass('open');
		$($(this).attr('href')).addClass('open');
		updateScrollPane();
		return false;
	});
</script>