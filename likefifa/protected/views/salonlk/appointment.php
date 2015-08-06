<div class="content-wrap content-pad-bottom">
<div class="page-profile">
<div class="prof-wrap">
<?php $this->widget(
	'application.components.likefifa.widgets.LfSalonLkTabsWidget',
	array(
		'currentTab' => 'appointment',
	)
); ?>
<div class="prof-tab-appointment_filter__head">
	<?php $form = $this->beginWidget(
		'CActiveForm',
		array(
			'id'                   => 'app-filter-form',
			'enableAjaxValidation' => true,
		)
	); ?>
	<div class="prof-tab-appointment__search__wrap">
		<div class="prof-tab-appointment__search">
			поиск по ФИО:<?php echo LfHtml::textField(
				'app_name',
				isset(Yii::app()->request->cookies['app_name']) ? Yii::app()->request->cookies['app_name'] : ''
			) ?>
		</div>
		<div class="prof-tab-appointment__date">
			Период с:
			<?php
			$this->widget(
				'zii.widgets.jui.CJuiDatePicker',
				array(
					'name'        => 'from_date',
					'language'    => 'ru',
					'value'       => isset(Yii::app()->request->cookies['from_date']) ? date(
							'd.m.Y',
							Yii::app()->request->cookies['from_date']->value
						) : '', // value comes from cookie after submittion
					'options'     => array(
						'dateFormat' => 'dd.mm.yy',
					),
					'htmlOptions' => array(
						'autoComplete' => 'off'
					),
				)
			);
			?>&nbsp;&nbsp;
			по:
			<?php
			$this->widget(
				'zii.widgets.jui.CJuiDatePicker',
				array(
					'name'        => 'to_date',
					'language'    => 'ru',
					'value'       => isset(Yii::app()->request->cookies['to_date']) ? date(
							'd.m.Y',
							Yii::app()->request->cookies['to_date']->value
						) : '',
					'options'     => array(
						'dateFormat' => 'dd.mm.yy',

					),
					'htmlOptions' => array(
						'autoComplete' => 'off'
					),
				)
			);
			?>
		</div>
		<div class="prof-tab-appointment__submit">
			<?php echo CHtml::hiddenField('date_button', 'all', array('id' => 'date_button')); ?>
			<div class="button button-blue" onclick="getElementById('app-filter-form').yt0.click()">
				<span>Искать</span></div>
			<div class="button button-blue" onclick="getElementById('app-filter-form').yt1.click()">
				<span>Сбросить</span>
			</div>
			<div class="btn-submit_hidden">
				<?php echo CHtml::submitButton('Искать'); ?>
				<?php echo CHtml::submitButton('Сбросить'); ?>
			</div>
		</div>
	</div>
	<?php $this->endWidget(); ?>

	<div class="prof-tab-appointment_filter__tab">
		<p>поиск по статусу:</p>
		<?php $this->widget(
			'application.components.likefifa.widgets.LfSalonLkAppTabsWidget',
			array(
				'currentTab' => $status,
				'itemsCount' => $itemsCount,
			)
		); ?>
		<div class="clearfix"></div>
	</div>
	<div class="prof-tab-appointment__btn__time">
		<a href="" class="app_today <?php echo $button == 'today' ? 'act' : null; ?>">сегодня</a>
		<a href="" class="app_yesterday <?php echo $button == 'yesterday' ? 'act' : null; ?>">вчера</a>
		<a href="" class="app_week <?php echo $button == 'week' ? 'act' : null; ?>">за неделю</a>
		<a href="" class="app_month <?php echo $button == 'month' ? 'act' : null; ?>">за месяц</a>
		<a href="" class="app_3month <?php echo $button == '3month' ? 'act' : null; ?>">за квартал</a>
	</div>
	<div class="clearfix"></div>
</div>

<?php $columns = array(
	array(
		'class' => 'CDataColumn',
		'name'  => '№',
		'type'  => 'raw',
		'value' => '$row+1',
	),
	array(
		'class'       => 'CDataColumn',
		'name'        => 'Дата заявки',
		'type'        => 'raw',
		'value'       => 'date("d.m.Y", $data->getNumericCreated())',
		'htmlOptions' => array(
			'class' => 'first'
		)
	),
	array(
		'class' => 'CDataColumn',
		'name'  => 'ФИО клиента',
		'type'  => 'raw',
		'value' => '$data->name'
	),
	array(
		'class'       => 'CDataColumn',
		'name'        => 'Телефон',
		'type'        => 'raw',
		'value'       => '$data->phone',
		'htmlOptions' => array(
			'nowrap' => 'nowrap'
		)
	),
	array(
		'class' => 'CDataColumn',
		'name'  => 'Цена',
		'type'  => 'raw',
		'value' => '$data->service ? ( ($data->salon->getPriceForService($data->service->id)) ? $data->salon->getPriceForService($data->service->id)->price." руб." : "") : ""',
	),
	array(
		'class' => 'CDataColumn',
		'name'  => 'Услуга',
		'type'  => 'raw',
		'value' => '$data->service ? $data->service->name : $data->specialization->name'
	),
);

if ($status == 'apply') {
	$columns[] = array(
		'class' => 'CDataColumn',
		'name'  => 'Время',
		'type'  => 'raw',
		'value' =>
			'"Акутально до<br>".date("H:i d.m.Y", $data->date)."<br><div class=\"popup-apply popup-note\"></div><a href=\"#\" class=\"apply-button\" data-id=".$data->id." data-status=\"' .
			$status .
			'\">редактировать</a>"',
	);
} else {
	$columns[] = array(
		'class' => 'CDataColumn',
		'name'  => 'Время',
		'type'  => 'raw',
		'value' => '"Акутально до<br>".date("H:i d.m.Y", $data->NumericCreated + 2 * 3600)',
	);
}

$columns[] = array(
	'class' => 'CDataColumn',
	'name'  => 'Выезд на дом',
	'type'  => 'raw',
	'value' => '$data->departure ? "Да<br>$data->address" : "Нет"'
);

$columns[] = array(
	'class' => 'CDataColumn',
	'name'  => 'Мастер',
	'type'  => 'raw',
	'value' => '$data->master ? $data->master->getFullName() : "-"'
);

if (0 && ($status == 'new' || $status == 'apply')) {
	$columns[] = array(
		'class'       => 'LfButtonColumn',
		'header'      => 'Статус',
		'optionsData' => true,
		'htmlOptions' => array(
			'class' => '"button-column"',
		),
		'buttons'     => array(
			'apply'    => array(
				'label'   => 'принять',
				'url'     => '"#"',
				'options' => array('class' => 'apply-button', 'data-id' => '$data->id', 'data-status' => $status)
			),
			'complete' => array(
				'label' => 'завершить',
				'url'   =>
					'Yii::app()->createUrl("salonlk/updateStatus", array("act"=>"complete", "id"=>$data->id, "status"=>"' .
					$status .
					'"))',
			),
			'cancel'   => array(
				'label'   => 'отклонить',
				'url'     => '"#"',
				'options' => array('class' => 'cancel-button', 'data-id' => '$data->id', 'data-status' => $status)
			),
		),
		'template'    => $status == 'new'
				? '<div class="popup-apply popup-note"></div> {apply} <div class="popup-cancel popup-note"></div> {cancel}'
				: ($status == 'apply' ? '{complete} <div class="popup-cancel popup-note"></div> {cancel}' : '')
	);
} else {
	$columns[] = array(
		'class' => 'CDataColumn',
		'name'  => 'Статус',
		'type'  => 'raw',
		'value' => '$data->statusList[$data->status]'
	);
}

$this->widget(
	'zii.widgets.grid.CGridView',
	array(
		'id'           => 'prof-appointment-grid',
		'cssFile'      => false,
		'pager'        => array(
			'cssFile'        => false,
			'header'         => '',
			'prevPageLabel'  => '&larr;',
			'nextPageLabel'  => '&rarr;',
			'firstPageLabel' => '',
			'lastPageLabel'  => '',
			'maxButtonCount' => 8
		),
		'dataProvider' => $dataProvider,
		'emptyText'    => 'Нет заявок',
		'columns'      => $columns
	)
);?>
</div>
</div>
</div>
