<?php
/**
 * @var array $groupDict
 */
?>

<label>Тарифный калькулятор</label>
<ul class="contract_costs">
<?php if (!empty($costs)) {?>
	<?php foreach ($costs as $key => $item) {?>
		<li class="row contract_cost">
			<?php echo CHtml::dropDownList("service_id", $item->group_uid, $groupDict, [
				'class' => 'service-id',
			]); ?>
			от <input type="text" class="line-input from_num" value="<?php echo $item->from_num;?>"> пациентов - <input type="text" class="line-input cost" value="<?php echo $item->cost;?>"> руб.
			<input type="checkbox" class="active_contract_cost" <?php echo $item->is_active ? 'checked="checked"' : '';?> />
			<span class="form delete_row">-</span>
			<?php if ($key == count($costs) - 1) {?>
				<span class="form add_row">+</span>
			<?php }?>
		</li>
	<?php }?>
<?php } else {?>
	<li class="row contract_cost">
		<?php echo CHtml::dropDownList("service_id", 0, $groupDict, [
			'class' => 'service-id',
		]); ?>
		от <input type="text" class="line-input from_num" value=""> пациентов - <input type="text" class="line-input cost"> руб.
		<input type="checkbox" class="active_contract_cost" checked="checked" />
		<span class="form delete_row">-</span>
		<span class="form add_row">+</span>
	</li>
<?php }?>
</ul>