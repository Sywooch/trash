<?php
/**
 * @var dfs\docdoc\back\controllers\SectorSeoTextController $this
 * @var dfs\docdoc\models\SectorSeoTextModel                $model
 * @var CActiveForm                                         $form
 *
 */

use dfs\docdoc\models\SectorSeoTextModel;
use dfs\docdoc\models\SectorModel;

?>
<div class="form">

	<?php $form = $this->beginWidget(
		'CActiveForm',
		array(
			'id'                   => 'sector-seo-text-form',
			'enableAjaxValidation' => false,
		)
	); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model, 'position'); ?>
		<?php echo $form->dropDownList($model, 'position', SectorSeoTextModel::model()->getPositionNames()); ?>
		<?php echo $form->error($model, 'position'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'name'); ?>
		<?php echo $form->textField($model, 'name', array('size' => 60, 'maxlength' => 512)); ?>
		<?php echo $form->error($model, 'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'text'); ?>
		<?php echo $form->textArea($model, 'text', array('rows' => 6, 'cols' => 50, 'class' => 'rich')); ?>
		<?php echo $form->error($model, 'text'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'sectors'); ?>
		<div class="checkbox-list">
			<table>
				<tr>
					<td>
						<?php
						$sectors         = SectorModel::model()->getListItems();
						$selectedSectors = $model->getRelationIds('sectors');
						$countPerColumn  = (count($sectors) / 4);
						$i               = 0;

						foreach ($sectors as $sectorId => $sectorName) {
							$i++;

							$id = "sector_{$sectorId}";
							echo CHtml::checkBox(
								CHtml::modelName($model) . '[sectors][]',
								in_array($sectorId, $selectedSectors),
								array(
									'id'    => $id,
									'value' => $sectorId,
								)
							);
							echo CHtml::label($sectorName, $id);
							echo '<div class="clear"></div>';

							if ($i > $countPerColumn) {
								$i = 0;
								echo '</td><td>';
							}
						} ?>
					</td>
				</tr>
			</table>
		</div>
		<?php echo $form->error($model, 'sectors'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'disabled'); ?>
		<?php echo $form->checkBox($model, 'disabled'); ?>
		<?php echo $form->error($model, 'disabled'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить'); ?>
	</div>

	<?php $this->endWidget(); ?>

</div><!-- form -->