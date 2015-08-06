<?php
/**
 * @var array  $menu
 * @var string $active
 */
?>

<nav class="l-nav">
	<ul class="menu_list">
		<?php foreach ($menu as $name => $item): ?>
			<li class="menu_item">
				<a class="menu_link<?php echo $name === $active ? ' s-current' : ''; ?>"
				   href="<?php echo $item['link']; ?>">
					<?php echo $item['title']; ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>
