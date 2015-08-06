<?php

use dfs\docdoc\models\SectorModel;

/**
 * @var SectorModel[] $relatedSpecList
 * @var array[] $sectorList
 * @var bool $isLandingPage
 */
?>

<?php if (!empty($relatedSpecList)): ?>

	<h3>Связанные специальности</h3>

	<ul class="related_list">
		<?php foreach ($relatedSpecList as $spec): ?>
			<li class="related_item">
				<a href="/doctor/<?php echo $spec->rewrite_name; ?>" title="<?php echo $spec->name; ?>" class="related_link">
					<?php echo $spec->name; ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>

<?php endif; ?>

<?php if (!empty($sectorList)): ?>

	<h3>Все специальности</h3>

	<ul class="related_list">
		<?php foreach ($sectorList as $spec): ?>
			<li class="related_item">
				<a href="<?php echo (empty($isLandingPage) ? '/doctor/' : '/landing/') . $spec['rewriteName']; ?>" title="<?php echo $spec['name']; ?>" class="related_link">
					<?php echo $spec['name']; ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>

<?php endif; ?>
