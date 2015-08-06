<div class="dd-widget">
	<div class="dd-online-popup dd-online-popup-<?=$this->type?>">
		<iframe src="<?php echo $this->getFrameUrl(); ?>" frameborder="0" scrolling="no"
				width="<?=$this->getWidth()?>" height="<?=$this->getHeight()?>" class="dd-modal-frame"></iframe>
	</div>
	<div class="dd-online-overlay"></div>
</div>