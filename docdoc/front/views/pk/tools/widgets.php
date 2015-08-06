<?php
/**
 * @var bool $divId
 * @var array $widgets
 * @var \dfs\docdoc\models\PartnerModel $partner
 */
?>

<?php if (!empty($divId)): ?>
<!-- Вставить этот div туда, где должен выводиться список-->
<div id="<?php echo $divId; ?>"></div>
<?php endif; ?>

<!-- Вставить этот код перед закрывающимся тегом </body> -->
<script>
	<?php
		foreach ($widgets as $name => $w) {
			$w['params']['pid'] = $partner->id;
			$strParams = json_encode($w['params'], JSON_PRETTY_PRINT);
			$varName = 'wgt' . $name;
			$code = "DdWidget($strParams);";
			echo str_replace("\n", "\n\t", $code), "\n";
		}
	?>
</script>