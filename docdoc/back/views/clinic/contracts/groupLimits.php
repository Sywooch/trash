<?php
/**
 * @var array $groups
 */
?>

<ul class="contract_limits">
<?php foreach ($groups as $group) {?>
	<li class="row contract_limit" data-group-id="<?=$group['id']?>">
		<span><?=$group['name']?></span>
		<input type="text" class="line-input limit" value="<?=$group['limit']?>" />
	</li>
<?php }?>
</ul>