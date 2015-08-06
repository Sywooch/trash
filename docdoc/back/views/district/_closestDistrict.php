<?php
/**
 * @var array $list
 */
?>
<table>
	<tr>
		<th>Район</th>
		<th>Приоритет</th>
	</tr>
	<?php foreach ($list as $item) { ?>
		<tr>
			<td>
				<label>
					<?php echo CHtml::checkBox("closestDistrict[{$item["id"]}]", $item["priority"] !== null); ?>
					<?php echo $item["name"]; ?>
				</label>
			</td>
			<td>
				<?php echo CHtml::textField(
					"closestDistrictPriority[{$item["id"]}]",
					$item["priority"],
					["size" => 8]
				); ?>
			</td>
		</tr>
	<?php } ?>
</table>