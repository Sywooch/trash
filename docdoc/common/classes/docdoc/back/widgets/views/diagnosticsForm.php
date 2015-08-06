<div id="ceilWin_multy" class="m0 shd infoEltR hd diagnosticsForm" style="width: 450px; display:none;">
	<div class="mb5 checkBox4Text">
		<label><input class="checkBox4Text alldiagnostics" name="diagnostica[0]" id="diagnostica_0" type="checkbox" value="" autocomplete="off" data-title="Все диагностики" checked="">
			Все типы
		</label>
	</div>
	
	<?php foreach ($diagnostics as $item) {?>
		<div class="mb10 checkBox4Text">
			<?php if (count($item->childs) > 0) {?>
				<input class="checkBox4Text with-subdiagnostics" name="diagnostica[<?php echo $item->id;?>]" type="checkbox" value="<?php echo $item->id;?>" autocomplete="off" data-title="<?php echo $item->getFullName();?>" />
				<span class="pnt link" onclick="$('#subList_<?php echo $item->id;?>').toggle()">
					<?php echo $item->getFullName();?>
					(<?php echo count($item->childs);?>)
				</span>
			<?php } else {?>
				<label><input class="checkBox4Text without-subdiagnostics" name="diagnostica[<?php echo $item->id;?>]" type="checkbox" value="<?php echo $item->id;?>" autocomplete="off" data-title="<?php echo $item->getFullName();?>" />
					<?php echo $item->getFullName();?>
				</label>
			<?php }?>
		</div>
		<?php if (count($item->childs) > 0) {?>
			<div id="subList_<?php echo $item->id;?>" class="hd ml20">
				<?php foreach ($item->childs as $child) {?>
					<div class="mb5 checkBox4Text">
						<label>
							<input class="checkBox4Text subdiagnostic" name="diagnostica[<?php echo $child->id;?>]" type="checkbox" value="<?php echo $child->id;?>" autocomplete="off" data-title="<?php echo $child->getFullName();?>" />
							<?php echo $child->getFullName();?>
						</label>
					</div>
				<?php }?>
			</div>
		<?php }?>
	<?php }?>

	<div class="closeButton4Window closeDiagnosticForm" title="закрыть" onclick="$('.diagnosticsForm').hide();"/>

	<input id="selectedDiagnosticId" type="text" value="0" />
	<input id="selectedDiagnosticTitle" type="text" value="Все диагностики" />
	
</div>

<script>
	$(".without-subdiagnostics, .with-subdiagnostics, .subdiagnostic, .alldiagnostics").click(function() {
		var val = '';
		var title = '';
		$('#ceilWin_multy input:checked').each(function() {
			title += $(this).data('title') +", ";
			val += $(this).val() +", ";
		});

		$("#selectedDiagnosticId").val(val);
		$("#selectedDiagnosticTitle").val(title);

		/*$(this).attr('checked',true);
		$(this).parent('.checkBox4Text').next().find('input').attr('checked',true);
		$("#selectedDiagnosticId").val($(this).val());
		$("#selectedDiagnosticTitle").val($(this).data('title'));*/
	});
</script>