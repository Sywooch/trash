<h1>Карта сайта</h1>

<ul class="sitemap">
	<li>&ndash; <a href="<?php echo Yii::app()->homeUrl; ?>"><strong>Главная</strong>
	</a>
		<ul>
			<li>&ndash; <a href="<?php echo $this->createUrl('/kliniki/'); ?>"><strong>Все
						виды диагностики</strong> </a>
						<ul><li>&ndash; <a href="/sitemap/all">в районах <?php echo Yii::app()->city->getCity()->title_genitive;?></a></li></ul> <?php
						foreach($diagnostics as $diagnostic){
							if($diagnostic->parent_id == 0){ ?>
				<div class="met-dia">
					&ndash; <a href="/sitemap/<?php echo $diagnostic->id; ?>"><strong><?php echo $diagnostic->name; ?>
					</strong> </a>
					<ul>
					<?php foreach($diagnostics as $diagnosticChild){
						$diagParent = str_replace("/", "", $diagnostic->rewrite_name);
						if($diagnosticChild->parent_id == $diagnostic->id){ ?>
						<li>&ndash; <a href="/sitemap/<?php echo $diagnosticChild->id; ?>"><?php echo $diagnosticChild->name; ?>
						</a></li>
						<?php } ?>
						<?php } ?>
					</ul>
				</div> <?php } ?> <?php } ?>
			</li>
		</ul>
	</li>

</ul>
