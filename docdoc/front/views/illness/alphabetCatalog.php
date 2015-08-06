<?php

use dfs\docdoc\models\IllnessModel;

/**
 * @var string $letter
 */
?>

<ul class="alphabet">
	<?php foreach (IllnessModel::$alphabet as $alias => $letterValue): ?>
		<li>
			<?php if ($letter !== $letterValue): ?>
				<a href="/illness/alphabet/<?php echo $alias; ?>"><?php echo $letterValue; ?></a>
			<?php else: ?>
				<?php echo $letterValue; ?>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
</ul>
