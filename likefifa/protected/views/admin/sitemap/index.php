<?php
$this->breadcrumbs=array(
	'Карта сайта',
);
?>

<h1>Карта сайта</h1>

<?php if ($fileExists) { ?>
	<p>Sitemap.xml последний раз был изменён <b><?php echo $mtime; ?></b>.</p>
<?php } else { ?>
	<p>Sitemap.xml <b>отсутствует</b>.</p>
<?php } ?>

<form method="post" action="<?php echo $this->createUrl('/admin/sitemap'); ?>">
	<input type="submit" value="Обновить" />
</form>