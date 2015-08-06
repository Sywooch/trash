<div class="popup-abuse_success">
	<div class="popup-close"></div>
	<div class="popup-success_head">Ваша жалоба отправлена модератору.</div>
	<div class="popup-success_thx">Спасибо!</div>
</div>
<script type="text/javascript">
$(function(){
	var successAbuseTxt = $(".popup-abuse_success");
	$(".popup-abuse_success").parent().hide();
	$("#overlay").show();
	$("#popup").html(successAbuseTxt);
	$classNameForPopup = "popup-abuse-landing";
	showPopup($classNameForPopup);
});
</script>