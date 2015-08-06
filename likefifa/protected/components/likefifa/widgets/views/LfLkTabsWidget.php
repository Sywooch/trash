<div class="prof-tabs"><?php foreach ($tabs as $tabName => $tabData): ?>
	<?php if ($tabName === $currentTab):?>
		<a href="" class="act"><?php echo $tabData['title']; ?></a>
	<?php else: ?>
		<a href="<?php echo $this->owner->createUrl($tabData['url']); ?>"><?php echo $tabData['title']; ?></a>
	<?php endif; ?>
<?php endforeach; ?><div class="clearfix"></div></div>