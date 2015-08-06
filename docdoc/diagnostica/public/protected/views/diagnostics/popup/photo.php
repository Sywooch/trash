<div class="photos">
	<div class="arrow-left"></div>
	<div id="photo">
		<?php $i=0;foreach($images as $image){?>
		<?php if($i==0){?>
		<div class="img"><img src="//<?=Yii::app()->params['hosts']['front']?>/upload/kliniki/photo/<?php echo $image;?>" style="width:500px;" /></div>
		<?php } else {?>
		<div class="img" style="display:none;"><img src="//<?=Yii::app()->params['hosts']['front']?>/upload/kliniki/photo/<?php echo $image;?>" style="width:500px;" /></div>
		<?php } ?>
		<?php $i++;}?>
	</div>
	<div class="arrow-right"></div>
</div>

<script>
//	var img = new Array("a_delta1","a_delta2");
	var i = 0;
	var length = <?php echo $i; ?>;
	$(".arrow-right").click(function(){
		if(i<length-1){
			$(".img:eq("+(i)+")").css("display", "none");
			$(".img:eq("+(i+1)+")").fadeIn(500);
			i++;
		}
	});
	$(".arrow-left").click(function(){
		if(i>0){
			$(".img:eq("+(i)+")").css("display", "none");
			$(".img:eq("+(i-1)+")").fadeIn(500);
			i--;
		}
	});
</script>