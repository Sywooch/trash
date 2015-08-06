<?php

/**
 * @var array $diagnosticChanges
 */
?>

<div class="popup-form">

	<form id="DoctorModerationForm">
		<table>
		<tr>
			<th>Исследование</th>
			<th>Параметр</th>
			<th>Старое значение</th>
			<th>Новое значение</th>
			<th>Принять</th>
			<th>Отклонить</th>
		</tr>

		<?php foreach ($diagnosticChanges as $id => $item): ?>

			<?php foreach ($item['fields'] as $field => $params): ?>
				<?php
					$fieldId = "{$id}_{$field}";
				?>
				<tr class="moderation_item_<?php echo $id; ?>">
					<td><?php echo $item['title']; ?></td>
					<td><?php echo $params['name']; ?></td>
					<td><?php echo $params['old']; ?></td>
					<td><?php echo $params['new']; ?></td>
					<td>
						<input type="checkbox" name="apply<?php echo "[$id][$field]"; ?>" id="ModerationApply_<?php echo $fieldId; ?>" class="apply" value="1"/>
						<label for="ModerationApply_<?php echo $fieldId; ?>">Принять</label>
					</td>
					<td>
						<input type="checkbox" name="reset<?php echo "[$id][$field]"; ?>" id="ModerationReset_<?php echo $fieldId; ?>" class="reset" value="1"/>
						<label for="ModerationReset_<?php echo $fieldId; ?>">Отклонить</label>
					</td>
				</tr>
			<?php endforeach; ?>

			<?php if (!empty($item['delete'])): ?>
				<?php
					$fieldId = "{$id}_delete";
				?>
				<tr class="moderation_item_<?php echo $id; ?>">
					<td><?php echo $item['title']; ?></td>
					<td colspan="3" style="color:#f00;">Клиника удалила диагностику</td>
					<td>
						<input type="checkbox" name="apply<?php echo "[$id][delete]"; ?>" id="ModerationApply_<?php echo $fieldId; ?>" class="apply" value="1"/>
						<label for="ModerationApply_<?php echo $fieldId; ?>">Принять</label>
					</td>
					<td>
						<input type="checkbox" name="reset<?php echo "[$id][delete]"; ?>" id="ModerationReset_<?php echo $fieldId; ?>" class="reset" value="1"/>
						<label for="ModerationReset_<?php echo $fieldId; ?>">Отклонить</label>
					</td>
				</tr>
			<?php endif; ?>

		<?php endforeach; ?>

		<tr class="all">
			<td colspan="4"></td>
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
