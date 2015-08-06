<?php

use dfs\docdoc\listInterface\ClinicList;
use dfs\docdoc\models\SectorModel;
use dfs\docdoc\components\seo\SeoInterface;

/**
 * @var ClinicList $clinicList
 * @var ClinicList[] $bestClinics
 * @var SectorModel[] $sectors
 */

$isMain = $clinicList->getParam('isMain');
$seoHead = Yii::app()->seo->getHead();
$seoTexts = Yii::app()->seo->getSeoTexts(true);
?>

<main class="l-main l-wrapper clinics" role="main">

	<div class="has-aside">

		<div class="seo-header">
			<h1><?php echo $seoHead; ?></h1>
		</div>

		<?php echo empty($seoTexts[SeoInterface::SEO_TEXT_TOP]) ? '' : $seoTexts[SeoInterface::SEO_TEXT_TOP]['Text']; ?>

		<?php
			if ($isMain) {
				echo $this->renderPartial('/clinic/specialties', ['sectors' => $sectors]);
			}

			echo $this->renderPartial('/clinic/listFilters', ['clinicList' => $clinicList]);
		?>

		<section class="doctor_list clinic_list">
			<?php
				foreach ($clinicList->getItems() as $clinic) {
					echo $this->renderPartial('/clinic/teaser', [
						'clinic' => $clinic,
						'speciality' => $clinicList->getSpeciality(),
					]);
				}
			?>
		</section>

		<?php if ($bestClinics): ?>
			<div class="b-notification">
				<p class="i-notification">
					По вашему запросу клиник и центров остеопатии найдено мало клиник, поэтому мы рекомендуем обратиться в лучшие центры в других районах Москвы.
				</p>
			</div>
			<ul>
				<?php foreach ($bestClinics as $clinic): ?>
					<li>
						<?php echo $this->renderPartial('/clinic/teaser', [
							'clinic' => $clinic,
							'speciality' => $clinicList->getSpeciality(),
						]); ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php echo $this->renderPartial('/elements/pager', [
			'url' => $clinicList->createUrl([
				'order' => Yii::app()->request->getParam('order'),
				'direction' => Yii::app()->request->getParam('direction'),
			]) . '/page/',
			'page' => $clinicList->getPage(),
			'count' => $clinicList->getPageCount(),
		]); ?>
	</div>

	<aside class="l-aside">
		<?php
			if (!$isMain) {
				echo $this->renderPartial('/clinic/listSpecialties', ['sectors' => $sectors]);
			}

			echo $this->renderPartial('/elements/asideBanners');
		?>
	</aside>

	<?php echo empty($seoTexts[SeoInterface::SEO_TEXT_BOTTOM]) ? '' : $seoTexts[SeoInterface::SEO_TEXT_BOTTOM]['Text']; ?>

</main>
