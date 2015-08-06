<?php
if (!empty($centers)) $centerOne = $centers[0];
?>

<div class="has-aside">
	<?php if (!is_null($this->head)) {?>
		<div class="seo-header">
			<h1><?php echo $this->head; ?></h1>
		</div>
		<?php if ($this->parentDiagnostic) { ?>

			<?php
				if (
					$firstPage && empty($this->stations) && empty($this->district) && is_null($this->area)
					&& !($order == 'price' && $direction == 'asc')
				) { ?>
				<div class="short-description"><?php echo $diagnostic->getShortDescription(Yii::app()->city->getCity()); ?> <a id="showDesc" href="#">дальше</a>
				</div>
				<div class="full-description" style="display:none;">
					<?php echo $diagnostic->getSeoText(Yii::app()->city->getCity()); ?> <a
						id="hideDesc" href="#">скрыть</a>
				</div>
			<?php } else { ?>
				<div class="full-description"><?php echo Yii::app()->seo->getSeoTextTop(); ?></div>
			<?php } ?>

		<?php } ?>

		<?php if (!empty($this->regCity)) { ?>
			Если Вы проживаете в городе <?php echo $this->regCity->name; ?>, DocDoc.ru рекомендует Вам
			пройти
			<?php
			if (!empty($this->parentDiagnostic)) {
				echo mb_strtolower($this->parentDiagnostic->getParentName(true));
			}
			echo !empty($this->diagnostic) ? ' ' . mb_strtolower($this->diagnostic->name) : '';
			?> в тех клиниках и диагностических центрах Москвы, которые расположены
			максимально близко к Вашему городу.
		<?php } ?>

	<?php }?>

	<div class="list_header">
		<div class="h1 mvm list_title">Найдено&nbsp;<span
				class="t-orange t-fs-xl"><?php echo $count; ?></span>&nbsp;<?php echo RussianTextUtils::caseForNumber($count, array('центр', 'центра', 'центров')); ?>
			диагностики
		</div>
		<?php if (!empty($orders)) { ?>
			<noindex>
				<ul class="filter_list">
					<li class="filter_item">Сортировка по</li>
					<?php
					foreach ($orders as $orderName => $orderData) {
						list($orderTitle, $orderDirection) = $orderData;

						$isCurrentOrder = ($orderName === $order);

						$urlParams = array();
						if ($stationIds) {
							$urlParams['stations'] = $stationIds;
						}
						$urlParams['order'] = $orderName;
						$urlParams['direction'] = $isCurrentOrder ? $oppDirection : $orderDirection;

						if ($near) {
							$urlParams['near'] = $near;
						}
						?>

						<li class="filter_item">
							<a class="filter_label sort-price <?php echo isset($_GET['direction']) ?
								' s-active i-' . $urlParams['direction'] : ''; ?>"
							   href="<?php echo $this->createUrl($baseUrl, $urlParams); ?>" rel="nofollow">Стоимости</a>
						</li>

					<?php } ?>
				</ul>
			</noindex>
		<?php } ?>
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

	<?php if ($isBest): ?>
		<br/><br/>
		<div class="b-notification">
			<p class="i-notification">
				<b>
					По вашему запросу диагностических
					центров <?php echo $dataProvider->totalItemCount > 0 ? 'найдено мало' : 'не найдено'; ?>.
					Мы рекомендуем вам обратиться в один из следующих диагностических центров
				</b>
			</p>
		</div>
		<section class="clinic_list">
			<?php
			$this->widget('zii.widgets.CListView', array(
				'ajaxUpdate' => false,
				'dataProvider' => $bestClinicsDataProvider,
				'viewData' => compact('diagnostic', 'baseUrl'),
				'itemView' => '_view',
				'template' => '{items}',
			)); ?>
		</section>
	<?php endif; ?>

	<?php if (!is_null(Yii::app()->seo->getSeoTextBottom())) { ?>
		<div class="seo-text"><p>Вопросы и ответы:</p><?php echo Yii::app()->seo->getSeoTextBottom(); ?></div>
	<?php } ?>

	<?php $this->renderPartial('geoLinks', compact('stationsTagList', 'areasTagList')); ?>

</div>

<?php if (!$this->isMobile): ?>
	<aside class="l-aside">
		<?php echo $this->renderPartial('search', compact('parentDiagnostics', 'childDiagnostics', 'districtIds', 'diagnostic')); ?>
		<?php echo $this->renderPartial('diagnosticTypes', [
			'childDiagnostics' => $childDiagnostics,
			'diagnosticSubtypes' => $diagnosticSubtypes,
		]); ?>
	</aside>
<?php endif; ?>

<div class="clear"></div>