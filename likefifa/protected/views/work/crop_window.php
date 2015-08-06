<?php
/**
 * @var LfWork  $model
 * @var string  $imageUrl
 * @var integer $id
 * @var string  $name
 */
?>
<form action="" method="POST">
	<div id="crop-window">
		<img class="image" src="<?php echo $imageUrl; ?>?<?php echo rand(); ?>>" id="target"
			 alt="[Jcrop Example]"/>

		<div id="preview-pane">
			<div class="preview-container">
				<img src="<?php echo $imageUrl; ?>?<?php echo rand(); ?>" class="jcrop-preview"
					 alt="Preview"/>
			</div>
		</div>

		<input type="hidden" value="0" name="x1" id="x1"/>
		<input type="hidden" value="0" name="y1" id="y1"/>
		<input type="hidden" value="<?php echo Yii::app()->params['images']['workThumbBigWidth']; ?>" name="x2"
			   id="x2"/>
		<input type="hidden" value="<?php echo Yii::app()->params['images']['workThumbBigHeight']; ?>" name="y2"
			   id="y2"/>

		<input type="hidden" value="640" name="jcropWidth" id="jcropWidth"/>
		<input type="hidden" value="480" name="jcropHeight" id="jcropHeight"/>

		<input type="hidden" value="<?php echo $id; ?>" name="workId" id="workId"/>
		<input type="hidden" value="<?php echo $name; ?>" name="workName" id="workName"/>

		<button class="button button-blue save-crop-image" role="submit" name="saveCrop">Сохранить</button>
	</div>
</form>
<script type="text/javascript">
	initJcrop(<?=$model->previewSizes['big'][0]?>, <?=$model->previewSizes['big'][1]?>, <?=$model->previewSizes['small'][0]?>, <?=$model->previewSizes['small'][1]?>);
</script>
