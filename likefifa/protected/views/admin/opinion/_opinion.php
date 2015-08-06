<?php
/**
 * @var OpinionController $this
 * @var LfOpinion         $data
 */

/**
 * @var LfMaster | LfSalon $owner
 */
$owner = $data->master ? $data->master : $data->salon;
$editUrl = $this->createUrl('editField');
?>
<li class="clearfix">
	<?php if($data->warning_level == LfOpinion::WARNING_POSSIBLE): ?>
		<p><i class="fa fa-warning red" data-toggle="tooltip" title="Возможно фейк"></i></p>
	<?php endif; ?>
	<?php if($data->warning_level == LfOpinion::WARNING_TOP): ?>
		<p>
			<i class="fa fa-warning red" data-toggle="tooltip" title="Определенно фейк"></i>
			<i class="fa fa-warning red" data-toggle="tooltip" title="Определенно фейк"></i>
		</p>
	<?php endif; ?>
	<h5>
		<?php echo CHtml::link($owner->getFullName(), $owner->getProfileUrl(true), ['target' => '_blank']) ?>
	</h5>

	<div class="aside">
		<a href="<?php echo $owner->getProfileUrl(true) ?>" target="_blank">
			<?php if ($owner->isUploaded('photo')): ?>
				<?php echo CHtml::image(
					$owner->avatar(),
					$owner->getFullName(),
					['class' => 'avatar', 'style' => 'height: auto;']
				); ?>
			<?php endif; ?>
		</a>
		<span class="current-status label label-<?php echo $this->getStatusLabel($data) ?>">
			<?php echo $data->getAllowed() ?>
		</span>

		<?php echo CHtml::ajaxButton(
			'Принять',
			['editField'],
			[
				'type' => 'post',
				'data' => [
					'pk'    => $data->id,
					'name'  => 'allowed',
					'value' => 1,
				],
				'success' => 'js:function() {document.location.reload();}'
			],
			[
				'class' => 'btn btn-success btn-xs',
				'disabled' => $data->allowed == 1,
			]
		) ?>
		<br/>
		<?php echo CHtml::ajaxButton(
			'Отклонить',
			['editField'],
			[
				'type' => 'post',
				'data' => [
					'pk'    => $data->id,
					'name'  => 'allowed',
					'value' => -1,
				],
				'success' => 'js:function() {document.location.reload();}'
			],
			[
				'class' => 'btn btn-danger btn-xs',
				'disabled' => $data->allowed == -1,
			]
		) ?>
	</div>

	<div class="message">
		<?php if ($data->advantages): ?>
			<p>
				<i class="fa fa-plus" style="color: green;"></i>
				<?php     $this->widget(
					'booster.widgets.TbEditableField',
					[
						'type'      => 'text',
						'model'     => $data,
						'attribute' => 'advantages',
						'url'       => $editUrl,
					]
				); ?>
			</p>
		<?php endif; ?>

		<?php if ($data->disadvantages): ?>
			<p>
				<i class="fa fa-minus" style="color: red;"></i>
				<?php     $this->widget(
					'booster.widgets.TbEditableField',
					[
						'type'      => 'text',
						'model'     => $data,
						'attribute' => 'disadvantages',
						'url'       => $editUrl,
					]
				); ?>
			</p>
		<?php endif; ?>

		<?php if ($data->text): ?>
			<p>
				<i class="fa fa-comments"></i>
				<?php     $this->widget(
					'booster.widgets.TbEditableField',
					[
						'type'      => 'textarea',
						'mode'      => 'inline',
						'model'     => $data,
						'attribute' => 'text',
						'encode'    => false,
						'text'      => nl2br(CHtml::encode($data->text)),
						'url'       => $editUrl,
					]
				); ?>
			</p>
		<?php endif; ?>

		<p>
			<strong>Автор:</strong>
			<?php echo $data->name ?>, <?php echo $data->tel ?>
		</p>

		<?php if ($data->appointment_id && $data->appointment): ?>
			<strong>Заявка:</strong>
			<?php echo CHtml::link(
				'№' . $data->appointment_id,
				['admin/appointment/update', 'id' => $data->appointment_id],
				['target' => '_blank']
			) ?>
			<i class="fa fa-<?php echo $data->appointment->getStatusIcon() ?>" data-toggle="tooltip"
			   title="<?php echo $data->appointment->getStatus() ?>"></i>
		<?php endif; ?>
	</div>
	<div class="date"><?php echo Yii::app()->dateFormatter->format(
			"dd MMMM yyyy, HH:mm",
			$data->created
		) ?></div>
</li>