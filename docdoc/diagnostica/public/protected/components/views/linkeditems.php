<?php
/**
 * @var array  $items
 * @var string $noLinkedItems
 */

$this->beginWidget(
	'zii.widgets.CPortlet',
	array(
		'title' => $title . ($items ? ' (' . count($items) . ')' : ''),
	)
);

?>

<?php if ($items) { ?>
	<?php $i = 0;
	foreach ($items as $item) {
		$i++;
		echo ($i > 1 ? ', ' : '');
		echo isset($item['url'])
			? CHtml::link($item['label'], $item['url'])
			: $item['label'];
	} ?>
<?php } else { ?>
	<?php echo $noLinkedItems; ?>
<?php } ?>

<?php $this->endWidget(); ?>
