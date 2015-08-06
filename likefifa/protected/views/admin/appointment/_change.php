<?php
/**
 * @var string   $controller
 * @var integer  $id
 * @var string   $rewrite_name
 * @var string   $fullName
 * @var boolean  $is_free
 * @var LfMaster $model
 */
?>
<span class="app-id-<?php echo $id; ?>">
	<?php if ($is_free !== null): ?>
		<span
			class="fa fa-lightbulb-o master-free-status status-<?php echo $is_free ?>"
			data-toggle="tooltip"
			title="<?php echo $is_free ? 'готов' : 'не готов' ?> принимать заказы"
			></span>
	<?php endif; ?>

	<span>
		<a href="<?php echo Yii::app()->createUrl("{$controller}s/index", array("rewriteName" => $rewrite_name)); ?>"
		   target="_blank">
			<?php echo $fullName; ?>
		</a>
	</span>

	<span class="change-master-salon">
		<a
			href="#"
			title="Перевести на другого мастера/салон"
			data-toggle="tooltip"
			appointment="<?php echo $id; ?>"
			class="on_change fa fa-pencil"
			></a>
	</span>

	<?php if ($model != null && $model instanceof LfMaster): ?>
		<p class="master-description">
			<?php
			$this->widget(
				'booster.widgets.TbEditableField',
				array(
					'type'      => 'text',
					'model'     => $model,
					'attribute' => 'comment',
					'url'       => Yii::app()->createUrl('/admin/master/editField'),
					'emptytext' => '&nbsp;&nbsp;&nbsp;'
				)
			);
			?>
		</p>
	<?php endif; ?>
</span>