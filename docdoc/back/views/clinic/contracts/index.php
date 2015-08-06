<div id="tabs" class="clinic_contracts">
	
	<ul>
		<li><a href="#tabs-1">Реквизиты</a></li>
		<li><a href="#tabs-2">Тарифы</a></li>
		<li><a href="#tabs-3">Тарифный калькулятор</a></li>
		<li><a href="#tabs-4">Лимиты</a></li>
		<li><a href="#tabs-5">Настройки</a></li>
	</ul>

	<div id="tabs-1">
		<?php $this->renderPartial('contracts/details', ['model' => $model]);?>
	</div>
	<div id="tabs-2">
		<?php $this->renderPartial('contracts/contracts', [
			'model'             => $model,
			'contractsDict'     => $contractsDict,
			'selectedContracts' => $selectedContracts,
		]);?>
	</div>
	<div id="tabs-3">
		<?php $this->renderPartial('contracts/calc', [
			'model'             => $model,
			'clinicContracts'   => $clinicContracts,
		]);?>
	</div>
	<div id="tabs-4">
		<?php $this->renderPartial('contracts/limits', [
			'model'             => $model,
			'contractsDict'     => $contractsDict,
			'clinicContracts'   => $clinicContracts,
		]);?>
	</div>
	<div id="tabs-5">
		<?php $this->renderPartial('contracts/settings', ['model' => $model]);?>
	</div>

</div>