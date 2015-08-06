<?php
/**
 * @var \dfs\docdoc\models\SectorModel[] $sectors
 */
?>

<h3>Направления:</h3>

<ul class="related_list">
	<?php foreach ($sectors as $sector): ?>
		<li class="related_item">
			<a href="/clinic/spec/<?php echo $sector->rewrite_spec_name; ?>" title="<?php echo $sector->spec_name; ?>" class="related_link">
				<?php echo $sector->spec_name; ?>
			</a>
		</li>
	<?php endforeach; ?>
</ul>
