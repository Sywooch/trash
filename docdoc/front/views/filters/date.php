<?php
	$crDateFrom = '';
	$crDateTill = '';

	if (isset($periods[$defaultPeriod])) {
		$p = $periods[$defaultPeriod];
		$crDateFrom = date('d.m.Y', (empty($p['date']) ? time() : strtotime($p['date'])));
		$crDateTill = date('d.m.Y', (empty($p['dateEnd']) ? time() : strtotime($p['dateEnd'])));
	}
?>

<ul class="filter_date__hotlinks">
	<?php foreach ($periods as $k => $v): ?>
		<li class="filter_date__hotlinks-item">
			<span class="filter_date__hotlinks-link<?php echo ($k === $defaultPeriod ? ' s-active' : ''); ?> dt_filter_link"
				  data-monthbegin="<?php echo date('d.m.Y', empty($v['date']) ? time() : strtotime($v['date'])); ?>"
				  data-monthend="<?php echo date('d.m.Y', empty($v['dateEnd']) ? time() : strtotime($v['dateEnd'])); ?>">
				<?php echo $v['title']; ?>
			</span>
		</li>
	<?php endforeach; ?>
</ul>

<span><?php echo isset($dateLabel) ? $dateLabel : 'Дата'; ?> &nbsp; с</span>
<input name="crDateFrom" class="DatePicker date-filter" type="text" value="<?php echo $crDateFrom; ?>" size="10">
<span>по</span>
<input name="crDateTill" class="DatePicker date-filter" type="text" value="<?php echo $crDateTill; ?>" size="10">
