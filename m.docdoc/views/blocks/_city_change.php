<div class="city-dropdown-wrapper">
	<select>
		<?php foreach (Yii::app()->city->getListForDropDown() as $cityForDropDown) { ?>
			<option
				value="<?php echo $cityForDropDown["url"]; ?>"
				<?php echo($cityForDropDown["isSelected"] ? "selected" : ""); ?>
			>
				<?php echo $cityForDropDown["name"]; ?>
			</option>
		<?php } ?>
	</select><i></i>
</div>