<div class="popup-close"></div>
<div class="popup-success_head">Ваша заявка принята.</div>
<div class="popup-success_txt"><span class="who">Мастер</span> свяжется с Вами в течении 2-х часов с 9:00 до 21:00 (ежедневно) для уточнения информации.</div>
<div class="popup-success_thx">Спасибо!</div>

<script>
	$(function(){
		$('#popup').removeClass($classNameForPopup);
		$classNameForPopup = "popup-success";
		$("#popup").addClass("popup-success");
		if ($(window).height() > $("#popup").height())
			$top = $(document).scrollTop()+($(window).height()-$("#popup").height())/2;
		else
			$top = $(document).scrollTop();
		$("#popup").css("top", $top);
		var salon = <?php echo isset($salon) ? 'true' : 'false';?>;
		var type = '<?php echo $type;?>';
		if (salon) {
			$(".popup-success_txt .who").text("Салон");
		}
		setPopupPosition();
	});
</script>