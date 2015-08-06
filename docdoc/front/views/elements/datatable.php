<?php
/**
 * @var string $className
 * @var string $tableId
 * @var array  $tableConfig
 * @var string $className
 */

$className = isset($className) ? $className : "";

if (empty($noLoad)) {
?>
<link rel="stylesheet" type="text/css" href="/js/datatables/css/jquery.dataTables.min.css" />
<link rel="stylesheet" type="text/css" href="/js/datatables-tabletools/css/dataTables.tableTools.css" />
<link rel="stylesheet" type="text/css" href="/js/datatables-colvis/css/dataTables.colVis.css" />
<link rel="stylesheet" type="text/css" href="/js/datatables-editor/css/dataTables.editor.min.css" />
<link rel="stylesheet" type="text/css" href="/js/datetimepicker/jquery.datetimepicker.css"/ >

<script src="/js/datatables/js/jquery.dataTables.min.js"></script>
<script src="/js/datatables-tabletools/js/dataTables.tableTools.js"></script>
<script src="/js/datatables-colvis/js/dataTables.colVis.js"></script>
<script src="/js/datatables-editor/js/dataTables.editor.min.js"></script>
<script src="/js/datetimepicker/jquery.datetimepicker.js"></script>
<script src="/static/js/notify.min.js"></script>
<script src="/static/js/datatables.js"></script>
<?php
}

if (isset($fixHeader)) {
?>
<link rel="stylesheet" type="text/css" href="/js/datatables-fixedHeader/css/dataTables.fixedHeader.css" />
<script src="/js/datatables-fixedHeader/js/dataTables.fixedHeader.js"></script>
<?php
}

if (isset($keyTable)) {
?>
<link rel="stylesheet" type="text/css" href="/js/datatables-keyTable/css/dataTables.keyTable.css" />
<script src="/js/datatables-keyTable/js/dataTables.keyTable.js"></script>
<?php
}
?>

<table id="<?php echo $tableId; ?>" class="result_table <?=$className?>" cellpadding="0" cellspacing="0">
	<thead>
	<tr>
		<?php
			foreach ($tableConfig['columns'] as $name) {
				$field = $tableConfig['fields'][$name];
				echo '<th' . (isset($field['width']) ? ' width="' . $field['width'] . '"' : '') . '>' . $field['label'] . '</th>' . PHP_EOL;
			}
		?>
	</tr>
	</thead>
</table>

<?php if (!empty($isInit)): ?>
<script>
	$(document).ready(function() {
		var dt = new DataTableWrapper(<?php echo "'#" . $tableId . "', " . (isset($filtersId) ? "'#" . $filtersId . "'" : 'null') . ", " . json_encode($tableConfig); ?>);
		dt.init();
	});
</script>
<?php endif; ?>
