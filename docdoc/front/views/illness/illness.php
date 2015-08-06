<?php

use dfs\docdoc\components\seo\SeoInterface;
use dfs\docdoc\models\IllnessModel;

/**
 * @var IllnessModel $illness
 * @var IllnessModel[] $relatedIllnesses
 * @var IllnessModel[] $letterIllnesses
 * @var array $doctorsData
 * @var array $specialities
 */

$letter = $illness->getFirstLetter();
$seoTexts = Yii::app()->seo->getSeoTexts(true);
?>

<main class="l-main l-wrapper" role="main">

	<div class="has-aside illness">

		<?php echo $this->renderPartial('/illness/alphabetCatalog', ['letter' => $letter]); ?>

		<h1>
			<?php echo $illness->name; ?>
			<span class="top_doctors"><a href="#IllnessDoctors">Топ-5 врачей</a> по заболеванию</span>
		</h1>

		<p class="letter_more">
			<b>Ещё заболевания на букву &laquo;<?php echo $letter; ?>&raquo;:</b>
			<?php
				$ill = [];
				foreach ($letterIllnesses as $item) {
					$ill[] = '<a href="/illness/' . $item->rewrite_name . '">' . $item->name . '</a>';
				}
				echo implode(', ', array_slice($ill, 0, 5));
				if (count($ill) > 5) {
					echo ', <a href="#" onclick="$(this).hide(); $(\'span\', this.parentNode).show();">показать ещё</a>';
					echo '<span style="display: none;">', implode(', ', array_slice($ill, 5)), '</span>';
				}
			?>
		</p>

		<?php if ($illness->text_other): ?>

			<div class="static_content">
				<?php echo $illness->text_other; ?>
			</div>

		<?php else: ?>

			<h2>О заболевании</h2>
			<p><?php echo $illness->text_desc; ?></p>

			<h2>Симптомы <?php echo $illness->full_name; ?>.</h2>
			<p><?php echo $illness->text_symptom; ?></p>

			<h2>Лечение <?php echo $illness->full_name; ?>.</h2>
			<p><?php echo $illness->text_treatment; ?></p>

		<?php endif; ?>

		<?php echo $this->renderPartial('/illness/doctors', $doctorsData); ?>

	</div>

	<aside class="l-aside">
		<?php
			echo $this->renderPartial('/elements/listSpec', ['sectorList' => $specialities]);
			echo $this->renderPartial('/illness/listSpecIllness', ['illnesses' => $relatedIllnesses]);
			echo $this->renderPartial('/elements/asideBanners');
		?>
	</aside>

	<?php echo empty($seoTexts[SeoInterface::SEO_TEXT_BOTTOM]) ? '' : $seoTexts[SeoInterface::SEO_TEXT_BOTTOM]['Text']; ?>

</main>
