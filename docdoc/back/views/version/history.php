<?php
/**
 * @var \dfs\docdoc\components\Version $version
 */
?>
<h1>История версий в картинках</h1>

<?php foreach(array_reverse($version->getImageUrlList()) as $image) { ?>
	<h3><?php echo $version->clearVersion($image); ?> </h3>
	<img src="/img/release_logo/<?=$image?>" />
<?php } ?>
