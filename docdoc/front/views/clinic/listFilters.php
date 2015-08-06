<?php
/**
 * @var \dfs\docdoc\listInterface\ClinicList $clinicList
 */

$countClinics = $clinicList->getCount();
$sort = $clinicList->getSort();
?>

<div class="clinic_list_header list_header">

	<div class="h1 clinic_list_title l-ib">
		<?php if ($clinicList->getParam('isMain')): ?>

			Все клиники

		<?php else: ?>

			<?php echo RussianTextUtils::caseForNumber($countClinics, ['Найден', 'Найдено', 'Найдено']); ?>
			<span class="t-orange t-fs-xl"><?php echo $countClinics; ?></span>
			<?php echo RussianTextUtils::caseForNumber($countClinics, ['клиника', 'клиники', 'клиник']); ?>

		<?php endif; ?>
	</div><ul class="l-ib">

		<li class="filter_item">Сортировка по</li>

		<?php foreach ($clinicList->getSortingParams() as $key => $filter): ?>
		<?php if (!empty($filter['title'])): ?>
			<?php
				$direction = $clinicList->getSortDirection();
				$class = ($key == $sort ? ' s-active ' . ($direction == 'asc' ? 'i-asc' : 'i-dsc') : '');
				$urlParams = [
					'order' => $key,
					'direction' => $key == $sort ? ($direction == 'asc' ? 'desc' : 'asc') : $direction,
				];
			?>
			<li class="filter_item filter_sort">
				<a href="<?php echo $clinicList->createUrl($urlParams); ?>" class="filter_label<?php echo $class; ?>" rel="nofollow">
					<?php echo $filter['title']; ?>
				</a>
			</li>
		<?php endif; ?>
		<?php endforeach; ?>

	</ul>

</div>
