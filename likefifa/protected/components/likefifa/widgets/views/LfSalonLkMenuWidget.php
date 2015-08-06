<div class="prof-menu">
	<?php foreach ($this->actions as $action => $name):?>
		<div>
			<?php if ($action === $currentAction):?>
				<?php echo CHtml::link($name, $this->controller->createUrl($action), array("class"=>"act")); ?>
			<?php else:?>
				<?php echo CHtml::link($name, $this->controller->createUrl($action)); ?>
			<?php endif?>
		</div>
	<?php endforeach;?>
	<?php if ($model): ?>
		<a href="<?php echo $model->getModelUrl(); ?>?cv" class="link-my-page">Как видят мою <br/>анкету клиенты</a>
	<?php endif; ?>
</div>