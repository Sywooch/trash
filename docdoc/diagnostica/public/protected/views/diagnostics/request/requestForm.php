<?php

/**
 * @var dfs\docdoc\models\DiagnosticaModel $diagnostic
 * @var dfs\docdoc\models\DiagnosticaModel $parentDiagnostic
 * @var dfs\docdoc\models\PartnerModel     $partner
 * @var array                              $params
 */
?>
<div class="request-form-container">
<?php
$this->widget('\dfs\docdoc\diagnostica\widgets\RequestFormWidget', array(
		'diagnostic'        => $diagnostic,
		'parentDiagnostic'  => $parentDiagnostic,
		'withServiceInfo'   => true,
		'partner'           => $partner,
	));
?>
<div class="popup_close">х</div>
<script type="text/javascript">
	$(document).ready(function() {
		$(document).trigger("requestPopupOpen", <?=json_encode($params)?>);
		$(".popup_close").click(function() {
			$(document).trigger("requestPopupCloseStart");
		});
	});
</script>
</div>
