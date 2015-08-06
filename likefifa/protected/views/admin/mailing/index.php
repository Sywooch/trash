<?php
/**
 * @var LfMaster[] $masters
 *
 * @var string[]   $groupList
 */
?>

<h1>Рассылка</h1>

<?php if ($isSend) { ?>
	<p>
		<strong>Спасибо, Ваше сообщение отправлено!</strong>
	</p>
<?php } ?>

<form method="post" action="" enctype="multipart/form-data">

	<div id="mailing">

		<div class="filter">

			<div class="all">
				<a href="#" class="select-all">Выбрать всех</a>
				| <a href="#" class="take-off-all">Убрать всех</a>
			</div>

			<div>&nbsp;</div>

			<div class="groups">
				<?php foreach ($groupList as $groupId => $groupName) { ?>
					<div>
						<input type="checkbox" id="group-<?php echo $groupId; ?>"/>
						<label for="group-<?php echo $groupId; ?>">
							<?php echo $groupName; ?>
						</label>
					</div>
				<?php } ?>
			</div>

			<div>&nbsp;</div>

			<div>
				<strong>Поиск:</strong> <input type="text" class="search">
			</div>

		</div>

		<div class="masters-list">
			<?php foreach ($masters as $master) { ?>
				<div class="master-item" master-id="<?php echo $master->id; ?>">
					<input
						type="checkbox"
						id="checkbox-<?php echo $master->id; ?>"
						class="group-<?php echo $master->masterGroup ? $master->masterGroup->group_id : null; ?>"
						/>
					<label for="checkbox-<?php echo $master->id; ?>">
						<?php echo $master->getFullName(); ?>
					</label>
				</div>
			<?php } ?>
		</div>

		<div class="clear"></div>

		<div class="forms title">
			<strong>Тема письма</strong> <br/>
			<input type="text" name="Mailing[title]"/>
		</div>

		<div class="forms message">
			<strong>Сообщение</strong> <br/>
			<textarea rows="15" name="Mailing[text]"></textarea>
		</div>

		<div class="forms file">
			<input type="file" name="mailingFile"/>
		</div>

		<div>
			&nbsp;
			<input type="hidden" value="" class="id-string" name="Mailing[ids]"/>
		</div>

		<div class="forms send">
			<input type="submit" value="Отправить" class="send-button" name="Mailing[submit]"/>
		</div>

	</div>

</form>