<?php
/**
 * @var array $items
 * @var array $list
 */
?>
<ul>
	<?php foreach ($items as $item) { ?>
		<li>
			<a href="/topic/<?= $item["id"] ?>/"><?= $item["title"] ?></a>
			<?= $this->createTopicTree($list, $item["id"]); ?>
		</li>
	<?php } ?>
</ul>