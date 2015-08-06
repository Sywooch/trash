
<p class="lk_notice">поиск по статусу:</p>

<ul class="filter_state__list">
	<?php foreach ($data as $key => $v): ?>
		<li class="filter_state__item">
			<input
				type="checkbox"
				name="<?php echo $field; ?>"
				value="<?php echo $key; ?>"
				id="<?php echo $field . '_' . $v['name']; ?>"
				class="filter_doctor__input s-hidden"
			/>
			<label class="filter_state__label" for="<?php echo $field . '_' . $v['name']; ?>">
				<span class="i-states i-<?php echo $v['class']; ?>"></span>
				<?php echo $v['title']; ?>
			</label>
		</li>
	<?php endforeach; ?>
</ul>
