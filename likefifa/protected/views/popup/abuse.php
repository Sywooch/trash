<div class="popup-close"></div>
<div class="popup-note_cont">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'master-abuse',
		'enableAjaxValidation'=>false,
		'htmlOptions' => array(	),
	)); ?>
		<?php echo LfHtml::activeRadioButtonList($model,'type', $model->getTypeListItems()); ?>
		<div class="popup-abuse_textarea"><?php echo LfHtml::activeTextArea($model, 'comment', array('rows' => 1)); ?></div>
		<div class="popup-abuse_btn">пожаловаться</div>
		<div class="clearfix"></div>
	<?php $this->endWidget(); ?>
</div>
<div class="popup-arr"></div>
<script type="text/javascript">
$(function(){

	$(".popup-abuse_textarea").click(function(){
		var parentPop = $(this).closest(".popup-note");
		parentPop.find("input:checked").attr("checked", false);
		parentPop.find(".checked").removeClass("checked");
		parentPop.find(".form-inp_radio:last input").attr("checked", true).prev().addClass("checked");
		$(this).addClass("focus").find("textarea").focus();
	});
	
	$(".popup-abuse .form-inp_radio").click(function(){
		var parentPop = $(this).closest(".popup-note");
		if(($(this).index()+1) == parentPop.find(".form-inp_radio").length)
			$(this).closest(".popup-note").find(".popup-abuse_textarea").addClass("focus").find("textarea").focus();
		else
			$(".popup-abuse_textarea").removeClass("focus");
	});
	
	var $formAbuse = $('#master-abuse');
	
	$formAbuse.submit(function(e) {
		e.preventDefault();

		$.ajax({
			url: $formAbuse.attr('action'),
			type: $formAbuse.attr('method'),
			dataType: 'html',
			data: $formAbuse.serializeArray()
		}).done(function(response) {
			$formAbuse.closest('.popup-abuse').html(response);
		});
	})
	.find('.popup-abuse_btn').click(function() {
		$formAbuse.submit();
	});
});
</script>