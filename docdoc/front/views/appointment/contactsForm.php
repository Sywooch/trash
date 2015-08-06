<form action="/appointment/createRequest" method="post" class="request-form">
	<div class="row">
		<div class="label">Ваше имя: <span class="red">*</span></div>
		<!-- end .label-->
		<input name="requestName" value="" type="text">
	</div>
	<!-- end .row-->
	<div class="row">
		<div class="label">Ваш телефон: <span class="red">*</span></div>
		<!-- end .label-->
		<input name="requestPhone" class="placeholder js-mask-phone" type="text">
	</div>
	<!-- end .row-->
	<div class="row">
		<div class="label">Комментарий:</div>
		<!-- end .label-->
		<textarea name="requestComments"></textarea>
	</div>
	<!-- end .row-->
	<input type="hidden" name="clinicId" id="clinicId" value="<?=$clinic->id?>" />
	<input type="hidden" name="doctorId" id="doctorId" value="" />
	<input type="hidden" name="specId" id="specId" value="" />
	<input type="hidden" name="diagnosticId" id="diagnosticId" value="" />
</form>
