<?php $i=0; foreach ($tabs as $tabName => $tabData): $i++;?><!--
	--><?php if ($tabName === $currentTab):?><!--
		--><a href="" class="act in-bl<?if ($i == 1){?> first<?}?><?if (count($tabs) == $i){?> last<?}?>"><span><i class="in-bl prof-appointment-tab_ico-<?php echo $i;?>"></i><?php echo $tabData['title'].' ('.$itemsCount[$tabData['title']].')'; ?></span></a><!--
	--><?php else: ?><!--
		--><a href="<?php echo $this->owner->createUrl($tabData['url']); ?>" class="in-bl<?if ($i == 1){?> first<?}?><?if (count($tabs) == $i){?> last<?}?>"><span><i class="in-bl prof-appointment-tab_ico-<?php echo $i;?>"></i><?php echo $tabData['title'].' ('.$itemsCount[$tabData['title']].')'; ?></span></a><?php endif; ?><!--
--><?php endforeach; ?>