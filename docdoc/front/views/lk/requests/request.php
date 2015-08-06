<?php
/**
 * @var CActiveForm $form
 * @var \dfs\docdoc\models\RequestModel $request
 * @var string $action
 */

$diagnostic = $request->diagnostics;
$parentDiagnostic = $diagnostic ? $diagnostic->parent : null;
$date = date('d.m.Y H:i', $request->date_admission);

$title = $action === 'refused' ?
	'Укажите причину отклонения заявки' :
	'Проверьте дату приёма и нажмите кнопку "Сохранить"';
?>

<div class="result_title__ct">
	<h1 class="result_main__title"><?php echo $title; ?></h1>
</div>

<br/>

<div class="info_content">

	<div class="select_block">
		<?php if ($this->errorMsg): ?>

			<p class="errorMsg"><?php echo $this->errorMsg; ?></p>
			<br />
			<p><a href="/lk/drequest?online=yes">Перейти к онлайн-заявкам</a></p>

		<?php else: ?>

			<?php $form = $this->beginWidget('CActiveForm'); ?>

			<?php echo $form->errorSummary($request); ?>

			<?php if ($action === 'refused'): ?>

				<ul>
				<?php foreach ($this->_rejectReasonsOnline as $reason): ?>
					<li>
						<input type="radio" name="reject_reason" value="<?php echo $reason['value']; ?>" id="RejectReason<?php echo $reason['value']; ?>"/>
						<label for="RejectReason<?php echo $reason['value']; ?>"><?php echo $reason['label']; ?></label>
					</li>
				<?php endforeach; ?>
				</ul>

			<?php else: ?>

				<?php $this->renderPartial('/../elements/slots', [ 'request' => $request ]); ?>

			<?php endif; ?>

			<p class="select_submit"><?php echo CHtml::submitButton('Сохранить'); ?></p>

			<?php $this->endWidget(); ?>

		<?php endif; ?>
	</div>

	<div class="request_info">
		<h5>Заявка <?php echo $request->req_id; ?></h5>
		<table>
			<tr>
				<td>Время создания заявки:</td>
				<td><?php echo date('d.m.Y H:i:s', $request->req_created); ?></td>
			</tr>
			<tr>
				<td>Имя пациента:</td>
				<td><?php echo $request->client->name; ?></td>
			</tr>
			<tr>
				<td>Телефон:</td>
				<td><?php echo $request->client->phone; ?></td>
			</tr>
			<tr>
				<td>Клиника:</td>
				<td><?php echo $request->clinic->name; ?></td>
			</tr>
			<?php if ($diagnostic): ?>
				<tr>
					<td>Тип диагностики:</td>
					<td><?php echo $parentDiagnostic ? $parentDiagnostic->getFullName() : $diagnostic->getFullName(); ?></td>
				</tr>
			<?php endif; ?>
			<?php if ($parentDiagnostic): ?>
				<tr>
					<td>Вид услуги:</td>
					<td><?php echo $diagnostic->getFullName(); ?></td>
				</tr>
			<?php endif; ?>
			<tr>
				<td>Желаемая дата приёма:</td>
				<td><?php echo $date; ?></td>
			</tr>
		</table>
	</div>

</div>
