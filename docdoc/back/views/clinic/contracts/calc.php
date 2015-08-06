<?php
/**
 * @var array $clinicContracts
 */
?>
<form class="calc-form" action="/2.0/clinic/saveContractCosts">
	<div>
		<label>Выберите тариф</label><br />
		<?php echo CHtml::dropDownList('contract', 'contract_id', $clinicContracts, ['empty' => 'Не выбран']); ?>
	</div>

	<div class="contract_calc" style="display:none;"></div>

	<div class="row-buttons">
		<input type="hidden" name="costs" id="costs" />
		<div class="form" style="width:100px; float:right; margin-left: 10px" onclick="(modalWinKey === 'close') ? $('#modalWin').hide() : window.location.reload()">ЗАКРЫТЬ</div>
		<div class="form btn-save-clinic-data" style="width:100px; float:right;">СОХРАНИТЬ</div>
	</div>
</form>
