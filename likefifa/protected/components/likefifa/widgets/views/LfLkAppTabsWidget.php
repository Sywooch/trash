<?php $i=0; foreach ($tabs as $tabName => $tabData): $i++;?><!--
	--><?php if ($tabName === $currentTab):?><!--
		--><a href="" class="act in-bl<?if ($i == 1){?> first<?}?><?if (count($tabs) == $i){?> last<?}?>"><span><i class="in-bl prof-appointment-tab_ico-<?php echo $i;?>"></i><?php echo $tabData['title'].' (<em '; if ($tabData["title"] == "Новые") { echo 'class="appointment_new_count"'; } echo 'style="font-style:normal;">'.$itemsCount[$tabData['title']].'</em>)'; ?></span></a><!--
	--><?php else: ?><!--
		--><a href="<?php echo $this->owner->createUrl($tabData['url']); ?>" class="in-bl<?if ($i == 1){?> first<?}?><?if (count($tabs) == $i){?> last<?}?>"><span><i class="in-bl prof-appointment-tab_ico-<?php echo $i;?>"></i><?php echo $tabData['title'].' (<em '; if ($tabData["title"] == "Новые") { echo 'class="appointment_new_count"'; } echo 'style="font-style:normal;">'.$itemsCount[$tabData['title']].'</em>)'; ?></span></a><?php endif; ?><!--
--><?php endforeach; ?>