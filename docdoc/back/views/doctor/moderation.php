<?php

use dfs\docdoc\models\DoctorModel;

/**
 * @var DoctorModel $doctor
 * @var array $changes
 */
?>

<div class="popup-form">
	<div class="form-row-field">Модерация изменений врача</div>

	<form id="DoctorModerationForm">
		<input type="hidden" name="id" value="<?php echo $doctor->id; ?>">
		<table>
		<tr>
			<th>Параметр</th>
			<th>Старое значение</th>
			<th>Новое значение</th>
			<th>Принять</th>
			<th>Отклонить</th>
		</tr>
		<?php foreach ($changes as $field => $params): ?>
			<tr>
				<td><?php echo $params['name']; ?></td>
				<td><?php echo $params['old']; ?></td>
				<td><?php echo $params['new']; ?></td>
				<td>
					<input type="checkbox" name="apply[<?php echo $field; ?>]" id="ModerationApply_<?php echo $field; ?>" class="apply" value="1"/>
					<label for="ModerationApply_<?php echo $field; ?>">Принять</label>
				</td>
				<td>
					<input type="checkbox" name="reset[<?php echo $field; ?>]" id="ModerationReset_<?php echo $field; ?>" class="reset" value="1"/>
					<label for="ModerationReset_<?php echo $field; ?>">Отклонить</label>
				</td>
			</tr>
		<?php endforeach; ?>
		<tr class="all">
			<td colspan="3"></td>
			<td><a class="apply_all" href="javascript:void(0);">Применить все</a></td>
			<td><a class="reset_all" href="javascript:void(0);">Отклонить все</a></td>
		</tr>
		</table>
	</form>

	<div class="form-row-field">
		<div class="form popup-button js-close" style="margin-left: 10px">ЗАКРЫТЬ</div>
		<div class="form popup-button js-save" style="margin-right: 10px">СОХРАНИТЬ</div>
	</div>
</div>
