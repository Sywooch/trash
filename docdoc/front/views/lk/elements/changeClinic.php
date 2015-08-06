<?php
/**
 * @var \dfs\docdoc\models\ClinicModel $current
 * @var \dfs\docdoc\models\ClinicModel[] $clinics
 */
?>

<?php if (count($clinics) > 1): ?>

	<div class="b-dropdown">

		<div class="b-dropdown_item b-dropdown_item__current header_clinic__title">
			<span class="b-dropdown_item__text"><?php echo $current->name; ?></span><span class="b-dropdown_item__icon"/>
		</div>

		<ul class="b-dropdown_list">
			<?php foreach ($clinics as $clinic): ?>
				<li class="b-dropdown_item<?php echo $clinic->id == $current->id ? ' s-current' : ''; ?>" data-clinicid="<?php echo $clinic->id; ?>">
					<?php echo $clinic->name; ?>
				</li>
			<?php endforeach; ?>
		</ul>

		<form class="b-dropdown_form" method="POST" action="/lk/service/changeClinic">
			<input class="b-dropdown_input" name="clinicId" type="hidden" />
		</form>

	</div>

<?php else: ?>

	<span class="b-dropdown_list s-hidden">
		<span class="b-dropdown_item s-current" data-clinicid="<?php echo $current->id; ?>"></span>
	</span>
	<span class="clinic_current"><?php echo $current->name; ?></span>

<?php endif; ?>
