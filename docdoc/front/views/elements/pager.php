<?php
/**
 * @var string $url
 * @var int $page
 * @var int $count
 */

$from = $page - 4 > 0 ? $page - 4 : 1;
$to = $page + 4 > $count ? $count : $page + 4;
?>

<?php if ($count > 1): ?>

	<ul class="pager">

		<?php if ($page > 1): ?>
			<li class="pager_item">
				<a href="<?php echo $url . ($page - 1); ?>" class="pager_item_link pager_item_nav">←</a>
			</li>
		<?php endif; ?>

		<?php for ($i = $from; $i <= $to; $i++): ?>
			<li class="pager_item">
				<a class="pager_item_link<?php echo $i == $page ? ' s-current' : ''; ?>"
					href="<?php echo $url . $i; ?>"
					><?php echo $i; ?></a>
			</li>
		<?php endfor; ?>

		<?php if ($page < $count): ?>
			<li class="pager_item">
				<a href="<?php echo $url . ($page + 1); ?>" class="pager_item_link pager_item_nav">→</a>
			</li>
		<?php endif; ?>

	</ul>

<?php endif; ?>
