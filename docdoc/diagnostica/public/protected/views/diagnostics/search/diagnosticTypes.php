<?php if ($childDiagnostics): ?>
	<h3 class="related_list_head">Виды <?php echo $this->parentDiagnostic->reductionNameInGenitive(); ?>:</h3>
	<?php if (count($diagnosticSubtypes) > 0) { ?>
		<ul>
		<?php foreach ($diagnosticSubtypes as $subtype) {?>
			<li>
				<?php echo $subtype->name;?>
				<ul class="related_list related_list_right">
				<?php foreach ($subtype->diagnostics as $item) {?>
					<li class="related_item"><a
							href="<?php echo $this->isLandingPage ? '/online-zapis-so-skidkoy' : '';?>/<?php echo str_replace('/', '', $this->parentDiagnostic->rewrite_name); ?><?php echo $item->rewrite_name; ?>"
							class="related_link"><?php echo $item->name; ?></a></li>
				<?php }?>
				</ul>
			</li>
		<?php }?>
		</ul>
	<?php } else { ?>
		<ul class="related_list related_list_right">
			<?php foreach ($childDiagnostics as $item) { ?>
				<li class="related_item"><a
						href="<?php echo $this->isLandingPage ? '/online-zapis-so-skidkoy' : '';?>/<?php echo str_replace('/', '', $this->parentDiagnostic->rewrite_name); ?><?php echo $item->rewrite_name; ?>"
						class="related_link"><?php echo $item->name; ?></a></li>
			<?php } ?>
		</ul>
	<?php } ?>
<?php endif; ?>

<?php if (!$this->isLandingPage):?>

<?php
$diagnosticId = !is_null($this->parentDiagnostic) ? $this->parentDiagnostic->id : null;
$diagnosticId = !is_null($this->diagnostic) ? $this->diagnostic->id : $diagnosticId;
$this->widget('\dfs\docdoc\diagnostica\widgets\GeoLinksWidget', [
	'diagnosticId'   => $diagnosticId,
	'stations'       => $this->stations,
	'districts'      => $this->district
]);
?>

	<ul class="throughout_banners">
		<li class="throughout_item">
			<a href="<?=Yii::app()->city->getUrl()?>/library" class="throughout_link">Медицинская библиотека</a>

			<p class="throughout_text">Полезные статьи о заболеваниях, современных методах лечения и диагностиках. </p>
		</li>
		<li class="throughout_item">
			<a href="<?=Yii::app()->city->getUrl()?>" class="throughout_link">Сервис по поиску врачей</a>

			<p class="throughout_text">Нужен квалифицированный врач поближе к дому? Специализированный портал поможет</p>
		</li>
		<li class="throughout_item">
			<a href="<?=Yii::app()->city->getUrl()?>/illness" class="throughout_link">Справочник заболеваний</a>

			<p class="throughout_text">Медицинский справочник болезней от А до Я.</p>
		</li>
	</ul>

<?php endif;?>