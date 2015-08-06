<?php
/**
 * @var string $maxDiscount
 * @var string $discountExpires
 * @var \dfs\docdoc\models\DiagnosticaModel[]$childDiagnostics
 * @var \dfs\docdoc\models\DiagnosticaSubtypeModel[] $diagnosticSubtypes
 */

if (!empty($centers)) $centerOne = $centers[0];
?>

<div class="has-aside">

	<div class="seo-header">
		<h1>Получите дополнительную скидку до <?=$maxDiscount?>% при онлайн-записи на
		<?php echo $this->parentDiagnostic->getParentName();?>
		<?php echo !is_null($this->diagnostic) ? $this->diagnostic->name : '';?></h1>
		<p>Теперь при онлайн-записи на сервисе DocDoc вы получаете <b>дополнительную скидку до <?=$maxDiscount?>%</b> на любой вид
			<?=$this->parentDiagnostic->getParentName()?>-исследования.</p>
		<p>Предложение действительно <b>до <?=$discountExpires?> года</b>, только при онлайн-записи.</p>
	</div>

	<section class="clinic_list">
		<?php
		$isBest = !empty($bestClinicsDataProvider) && $bestClinicsDataProvider->totalItemCount > 0;

		$this->widget('zii.widgets.CListView', array(
			'cssFile' => false,
			'ajaxUpdate' => false,
			'dataProvider' => $dataProvider,
			'viewData' => compact('diagnostic', 'baseUrl'),
			'itemView' => '_view',
			'sortableAttributes' => array(),
			'template' => '{items} {pager}',
			'emptyText' => $isBest ? '' : '<p>Диагностические центры, соответствующие указанным условиям, не найдены.</p>',
			'pager' => array(
				'class' => 'CLinkPagerCustom',
				'cssFile' => false,
				'header' => '',
				'previousPageCssClass' => 'pager_item',
				'nextPageCssClass' => 'pager_item',
				'internalPageCssClass' => 'pager_item',
				'prevPageLabel' => '&larr;',
				'nextPageLabel' => '&rarr;',
				'htmlOptions' => array('class' => 'pager'),
				'linkHtmlOptions' => array('class' => 'pager_item_link ', 'selected' => 's-current'),
			),
		)); ?>

	</section>

</div>

<?php if (!$this->isMobile): ?>
	<aside class="l-aside">
		<?php echo $this->renderPartial('diagnosticTypes', [
			'childDiagnostics' => $childDiagnostics,
			'diagnosticSubtypes' => $diagnosticSubtypes,
		]); ?>
	</aside>
<?php endif; ?>

<div class="clear"></div>