<div class="has-aside">
	<div class="page-all-diagnostics">
		<div class="i-doctor-diagnostics"></div>
		<h1>Все виды диагностики</h1>
		<ul class="sitemap">
		<?php 
			foreach($diagnostics as $diagnostic){
				if($diagnostic->parent_id == 0){ ?>
					<li class="met-dia">
						<a href="<?php echo $diagnostic->rewrite_name; ?>"><strong><?php echo $diagnostic->name; ?></strong></a>
						<ul>
						<?php foreach($diagnostics as $diagnosticChild){
							$diagParent = str_replace("/", "", $diagnostic->rewrite_name);
							if($diagnosticChild->parent_id == $diagnostic->id){ ?>
								<li><a href="/<?php echo $diagParent.$diagnosticChild->rewrite_name; ?>"><?php echo $diagnosticChild->name; ?></a></li>
							<?php } ?>
						<?php } ?>
						</ul>
					</li>
				<?php } ?>
		<?php } ?>
		</ul>
	</div>
</div>
<aside class="l-aside">   
    <ul class="throughout_banners">
        <li class="throughout_item">
	        <a class="throughout_link" href="<?=Yii::app()->city->getUrl()?>/library">Медицинская библиотека</a>
            <p class="throughout_text">Полезные статьи о заболеваниях, современных методах лечения и диагностиках. </p>
        </li>
        <li class="throughout_item">
            <a class="throughout_link" href="<?=Yii::app()->city->getUrl()?>">Сервис по поиску врачей</a>
            <p class="throughout_text">Нужен квалифицированный врач поближе к дому? Специализированный портал поможет</p>
        </li>
        <li class="throughout_item">
            <a class="throughout_link" href="<?=Yii::app()->city->getUrl()?>/illness">Справочник заболеваний</a>
            <p class="throughout_text">Медицинский справочник болезней от А до Я.</p>
        </li>
    </ul>   
</aside>