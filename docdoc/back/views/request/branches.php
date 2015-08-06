<div id="companyInput">
	<?php if (!is_null($clinic)) {?>
		<?php echo $clinic->name;?>
		<input id="chManualClinic" name="chManualClinic" value="0" type="hidden" autocomplete="off"/>
		<input name="clinicId" id="clinicId" type="hidden" value="<?php echo $clinic->id;?>" autocomplete="off"/>
		<span class="link" style="margin-left: 10px" onclick="chCompanySelector()">изменить</span>
		<?php if ($clinic->address) {?>
			<br/>
			<span class="em grey txt11" style="line-height: 16px"><?php echo $clinic->address;?></span>
		<?php }?>
	<?php }?>
</div>

<div id="companySelector" class="hd">
	<select name="clinicName" id="clinicName" class="inputForm" style="width: 100%" onchange="$('#clinicId').val(this.value); $('#chManualClinic').val(1); $('#clinicName').next().text($('#clinicName option:selected').attr('address'));">
		<?php foreach($clinics as $item) {?>
			<option value="<?php echo $item->id;?>" address="<?php echo $item->address;?>" <?php echo $clinic->id === $item->id ? 'selected' : '';?>>
				<?php echo $item->name;?>
			</option>
		<?php }?>
	</select>
	<em>
		<?php if ($clinic->address) {?>
			<br/>
			<span class="em grey txt11" style="line-height: 16px"><?php echo $clinic->address;?></span>
		<?php }?>
	</em>
</div>

