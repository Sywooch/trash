<?php
/**
 * @var \dfs\docdoc\models\SectorModel[] $sectors
 */
?>

<div class="block specialties">

	<ul class="library_list columns_3">
		<?php foreach (array_chunk($sectors, ceil(count($sectors) / 3)) as $chunk): ?>
			<li class="column">
				<ul>
					<?php foreach ($chunk as $sector): ?>
						<li class="library_list_item">
							<a class="library_list_link" href="/clinic/spec/<?php echo $sector->rewrite_spec_name; ?>">
								<?php echo $sector->spec_name; ?>
								<span class="library_list_count"><?php echo $sector->countClinics; ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</li>
		<?php endforeach; ?>
	</ul>

</div>
