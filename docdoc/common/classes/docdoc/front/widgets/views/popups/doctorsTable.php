<?php

use dfs\docdoc\extensions\TextUtils;

/**
 * @var bool $isMainPage
 * @var \dfs\docdoc\models\SectorModel[] $specialityList
 */

$addAttr = $isMainPage ? '' : ' data-related-form="search_form"';
?>

<h2 class="popup_title ui-border_b">Выберите специальность врача</h2>

<ul class="spec_list columns_3">
	<?php foreach (TextUtils::formatItemsByAlphabet($specialityList, 3) as $column): ?>
		<li class="column">
			<?php foreach ($column as $letter => $group): ?>
				<ul class="column_group">
					<?php
						foreach ($group as $speciality) {
							$pos = strpos($speciality->name, '(');
							$name = $pos === false ? $speciality->name : trim(substr($speciality->name, 0, $pos));

							echo '<li class="spec_list_item js-specselect" data-spec-id="', $speciality->id, '"', $addAttr, '>',
								'<a class="spec_list_link" href="/doctor/', $speciality->rewrite_name, '">', $name, '</a>',
								'</li >';
						}
					?>
				</ul>
			<?php endforeach; ?>
		</li>
	<?php endforeach; ?>
</ul>
