<?php

use dfs\docdoc\components\seo\SeoInterface;
use dfs\docdoc\models\IllnessModel;

/**
 * @var IllnessModel[] $illnesses
 * @var string $letter
 * @var array $specialities
 */

$seoTexts = Yii::app()->seo->getSeoTexts(true);
?>

<main class="l-main l-wrapper" role="main">

	<div class="has-aside illness">

		<?php echo $this->renderPartial('/illness/alphabetCatalog', ['letter' => $letter]); ?>

		<h1>Все заболевания на &laquo;<?php echo $letter; ?>&raquo;</h1>

		<?php if ($illnesses): ?>
			<ul class="illness_list columns_2">
				<?php foreach (array_chunk($illnesses, ceil(count($illnesses) / 2)) as $column): ?>
					<li class="column">
						<ul>
						<?php foreach ($column as $illness): ?>
							<li>
								<a class="illness_list_link" href="/illness/<?php echo $illness->rewrite_name; ?>" title="<?php echo $illness->name; ?>">
									<?php echo $illness->name; ?>
								</a>
							</li>
						<?php endforeach; ?>
						</ul>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

	</div>

	<aside class="l-aside">
		<?php
			echo $this->renderPartial('/elements/listSpec', ['sectorList' => $specialities]);
			echo $this->renderPartial('/elements/asideBanners');
		?>
	</aside>

	<?php echo empty($seoTexts[SeoInterface::SEO_TEXT_BOTTOM]) ? '' : $seoTexts[SeoInterface::SEO_TEXT_BOTTOM]['Text']; ?>

</main>
