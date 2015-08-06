<form class="form-contracts" action="/2.0/clinic/saveContracts">
	<div>
		<label>Тарифы</label><br />
		<?php echo CHtml::dropDownList('contracts', 'contract_id', $contractsDict, array(
			'options'   => $selectedContracts,
			'multiple'  => 'multiple',
			'style'     => 'height: 150px;'
		)); ?>
		<input type="hidden" name="clinicId" value="<?php echo $model->id;?>" />
	</div>

	<div class="row-buttons">
		<div class="form" style="width:100px; float:right; margin-left: 10px" onclick="(modalWinKey === 'close') ? $('#modalWin').hide() : window.location.reload()">ЗАКРЫТЬ</div>
		<div class="form btn-save-clinic-data" style="width:100px; float:right;">СОХРАНИТЬ</div>
	</div>
</form>