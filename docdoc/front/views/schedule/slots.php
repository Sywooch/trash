<?php
foreach ($data as $date => $slots) {
	echo '<div class="slide">
	';

	echo '<div class="slide_head">';
	echo $date;
	echo '</div>
';

	foreach ($slots as $slot) {
		echo '<div class="slot">
			<label>' . $slot['start_time'] . '</label>
			<input type="radio" name="slotId" value="' . $slot['id'] . '"/>
		</div>
		';
	}

	echo '</div>
';
}
