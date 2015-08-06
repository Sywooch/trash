<?php

use dfs\docdoc\components\seo\SeoInterface;
use dfs\docdoc\models\IllnessModel;
use dfs\docdoc\extensions\TextUtils;

/**
 * @var IllnessModel[] $illnesses
 * @var array $specialities
 */

$seoTexts = Yii::app()->seo->getSeoTexts(true);
?>

<main class="l-main l-wrapper" role="main">

	<div class="has-aside">
		<h1>Справочник заболеваний</h1>
		<ul class="illness_list columns_3">
			<?php foreach (TextUtils::formatItemsByAlphabet($illnesses, 3) as $column): ?>
				<li class="column">
					<?php foreach ($column as $letter => $group): ?>
						<span class="illness_list_letter">
							<?php echo $letter; ?>
						</span>
						<ul>
							<?php foreach ($group as $illness): ?>
								<li>
									<a class="illness_list_link" href="/illness/<?php echo $illness->rewrite_name; ?>" title="<?php echo $illness->name; ?>">
										<?php echo $illness->name; ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endforeach; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>

	<aside class="l-aside">
		<?php
			echo $this->renderPartial('/elements/listSpec', ['sectorList' => $specialities]);
			echo $this->renderPartial('/elements/asideBanners');
		?>
	</aside>

	<?php echo empty($seoTexts[SeoInterface::SEO_TEXT_BOTTOM]) ? '' : $seoTexts[SeoInterface::SEO_TEXT_BOTTOM]['Text']; ?>

</main>
