<?php

use dfs\docdoc\models\IllnessModel;

/**
 * @var IllnessModel[] $illnesses
 */
?>

<?php if (!empty($illnesses)): ?>

	<h3>Заболевания</h3>

	<ul class="related_list">
		<?php foreach ($illnesses as $illness): ?>
			<li class="related_item">
				<a href="/illness/<?php echo $illness->rewrite_name; ?>" title="<?php echo $illness->name; ?>" class="related_link">
					<?php echo $illness->name; ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>

<?php endif; ?>
