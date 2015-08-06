<ul>
<?php foreach ($filters as $name => $params): ?>

	<li>
		<div class="filter_doctor">
			<label class="filter_doctor__label">
				<?php echo $params['label']; ?>:
				<?php echo CHtml::dropDownList(
					$name,
					(isset($params['value']) ? $params['value'] : null),
					$params['data'],
					[
						'empty' => 'Все',
						'class' => 'dt_filter',
						'style' => 'width: 160px;',
					]);
				?>
			</label>
		</div>
	</li>
<?php endforeach; ?>
</ul>
