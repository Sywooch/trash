<?php
/**
 * @var string $refURL
 * @var \dfs\docdoc\models\ClinicModel $clinic
 * @var \dfs\docdoc\models\ClinicModel[] $nearestClinics
 * @var \dfs\docdoc\models\SectorModel[] $sectors
 * @var array $reviewsData
 * @var array $doctorsData
 */
?>
<main class="l-main l-wrapper" role="main">

	<div class="clinic_card_wrap">	
		<?php if ($refURL): ?>
			<a href="<?php echo $refURL; ?>" class="i-goback link-goback">
				<span class="link">Вернуться к результатам поиска</span>
			</a>
		<?php endif; ?>

		<article class="clinic_card js-clinic-full">
			<?php
				echo $this->renderPartial('/clinic/info', ['clinic' => $clinic, 'sectors' => $sectors]);

				echo $this->renderPartial('/clinic/doctors', $doctorsData);

				echo $this->renderPartial('/clinic/reviews', $reviewsData);
			?>
		</article>

		<?php $this->renderPartial('/clinic/nearest', ['clinics' => $nearestClinics]); ?>
	</div>

</main>
