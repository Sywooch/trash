<?php if (!empty($stationsTagList) && !empty($areasTagList)) { ?>
	<?php $diagLink = $this->diagnostic
		? $this->parentDiagnostic->rewrite_name . str_replace('/', '', $this->diagnostic->rewrite_name) . '/'
		: '/';?>
	<div class="tags-list">
		<span>Возможно вы также ищете:</span><br/>
		<table>
			<?php if ($this->diagnostic) {?>
				<tr>
					<td colspan="2">
						<?php echo $this->parentDiagnostic->reduction_name
							? $this->parentDiagnostic->reduction_name
							: $this->parentDiagnostic->name;?>
						<?php echo $this->diagnostic->name; ?>
					</td>
				</tr>
			<?php }?>
			<tr>
				<td>на станции метро:</td>
				<td>
					<?php $stationLinks = array();?>
					<?php foreach ($stationsTagList as $item) {?>
						<?php $stationLinks[] = "<a href='{$diagLink}station/" . $item['rewrite_name'] . "/'>{$item['name']}</a>";?>
					<?php }?>
					<?php echo implode(' | ', $stationLinks);?>
				</td>
			</tr>
			<tr>
				<td>в районе:</td>
				<td>
					<?php $areaLinks = array();?>
					<?php foreach ($areasTagList as $item) {?>
						<?php $link = "<a href='{$diagLink}";?>
						<?php $link .= $this->diagnostic
							? "area/{$item['area_rewrite_name']}/{$item['rewrite_name']}/"
							: "district/{$item['rewrite_name']}";?>
						<?php $link .= "'>{$item['name']}</a>";?>
						<?php $areaLinks[] = $link;?>
					<?php }?>
					<?php echo implode(' | ', $areaLinks);?>
				</td>
			</tr>
		</table>
	</div>
<?php } ?>
