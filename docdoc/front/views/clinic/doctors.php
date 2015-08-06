<?php
/**
 * @var \dfs\docdoc\models\ClinicModel $clinic
 * @var \dfs\docdoc\models\DoctorModel[] $doctors
 * @var \dfs\docdoc\models\SectorModel[] $sectors
 * @var int $countDoctors
 */

$countMore = $countDoctors - count($doctors);
$countMoreLimit = $countMore > 10 ? 10 : $countMore;
?>


<div class="clinic_card_cont clinic_card_doctors">

	<div class="clinic_card_doctors_head">
		<div class="clinic_card_doctors_select_wrap">
			Специализация врача:
			<div class="clinic_card_doctors_select l-ib">
				<div class="clinic_card_doctors_select_ico"></div>
				<select name="speciality">
					<option value="">-</option>
					<?php foreach ($sectors as $sector): ?>
						<option value="<?php echo $sector->id; ?>"><?php echo $sector->name; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<h2 class="t-fs-l mbs">
			В выбранной клинике <span class="clinic_card_doctors_count">
				<?php echo $countDoctors . ' '. RussianTextUtils::caseForNumber($countDoctors, ['врач', 'врача', 'врачей']); ?>
			</span>
		</h2>
	</div>

	<section class="doctor_list">
		<?php foreach ($doctors as $doctor): ?>
			<?php echo $this->renderPartial('/doctors/teaser', [
				'doctor' => $doctor,
				'teaserType' => 'clinicList',
			]); ?>
		<?php endforeach; ?>
	</section>

	<?php if ($countMore > 0): ?>
	<div class="clinic_card_doctors_more">
		<a href="#" class="showMore ps js-show-more"
		   data-clinic-id="<?php echo $clinic->id; ?>"
		   data-count-more="<?php echo $countMore; ?>">
			показать еще <?php echo $countMoreLimit . ' ' . RussianTextUtils::caseForNumber($countMoreLimit, ['врача', 'врачей', 'врачей']); ?>
		</a>
	</div>
	<?php endif; ?>

</div>
