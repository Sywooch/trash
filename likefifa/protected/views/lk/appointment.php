<?php
/**
 * @var LkController $this
 * @var LfMaster     $model
 */

$this->renderPartial('_header', compact('model'));
$a = LfAppointment::model()->findByPk(10628);
?>

<div class='overlay'
	 style='position: fixed; width: 100%; height: 100%; left: 0; top: 0; background-color: #000; opacity: 0.4; z-index: 10; display: none;'></div>
<div id="popup" class="popup-success">
	<div class="popup-close"></div>
	<div class="popup-success_head">Завершение заявки № <strong class='app_number'></strong></div>
	<div class="popup-success_txt">Для того чтобы завершить заявку, Вам необходимо указать реальную стоимость заказа
	</div>
	<div style='height:35px; padding: 10px 0;'>
		<div class="form-inp" style="width:100px; float:left;"><input id="service_price2" type="text" maxlength="256">
		</div>
		<div class="popup-success_txt" style="float:left;">&nbsp;рублей</div>
	</div>
	<div class="hide" style='display:none;'>
		<div class="popup-success_txt percent">С вашего счета будет списана сумма <strong
				class='service_price2'></strong> руб.
		</div>
		<div style="margin-top:15px; text-align:center;" class='complete_button'><a href=''
																					class="button button-pink"><span>Завершить заявку</span></a>
		</div>
	</div>
</div>


<div class='appointment_master_id' master='<?php echo $master_id; ?>'></div>
<div class='appointment_status' status='<?php echo $status; ?>'></div>
<div class="content-wrap content-pad-bottom appointment-wrap">
<div class="page-profile">
<div class="prof-wrap">
<?php $this->widget(
	'application.components.likefifa.widgets.LfLkTabsWidget',
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
			Поиск по ФИО:<?php echo LfHtml::textField(
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
			<div class="button button-blue" onclick="getElementById('app-filter-form').yt0.click()"><span>Искать</span>
			</div>
			<?php if ($status != 'apply'): ?>
				<div class="button button-blue" onclick="getElementById('app-filter-form').yt1.click()">
					<span>Все заявки</span></div>
			<?php endif; ?>
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
			'application.components.likefifa.widgets.LfLkAppTabsWidget',
			array(
				'currentTab' => $status,
				'itemsCount' => $itemsCount,
			)
		); ?>
		<div class="clearfix"></div>
	</div>
	<?php if ($status == 'apply'): ?>
		<div class="prof-tab-appointment__btn__time">
			<a class='appointment_refresh' onclick="getElementById('app-filter-form').yt1.click()"
			   style="cursor:pointer;">все заявки</a>
			<a href="" class="app_today <?php echo $button == 'today' ? 'act' : null; ?>">на сегодня</a>
			<a href="" class="app_yesterday <?php echo $button == 'yesterday' ? 'act' : null; ?>">на завтра</a>
			<a href="" class="app_week <?php echo $button == 'week' ? 'act' : null; ?>">на неделю</a>
			<a href="" class="app_month <?php echo $button == 'month' ? 'act' : null; ?>">на месяц</a>
		</div>
	<?php endif; ?>
	<div class="clearfix"></div>
</div>

<?php



$columns = array();
$columns[] = array(
	'class'           => 'LfDataColumn',
	'name'            => '№',
	'type'            => 'raw',
	'value'           => '$data->id',
	'evaluateOptions' => true,
	'htmlOptions'     => array(
		'class' => '"first"',
		'style' => '$data->oneHourLeft ? "width:40px; text-align: center; background-color: #fae4f7; border-color: #fae4f7; border-bottom-color: #dad8db;" : "width:40px; text-align: center;"'
	)
);
$columns[] = array(
	'class'           => 'LfDataColumn',
	'name'            => '&nbsp;&nbsp;Дата&nbsp;заявки&nbsp;&nbsp;',
	'type'            => 'raw',
	'value'           => 'date("d.m.y", $data->getNumericCreated())',
	'evaluateOptions' => true,
	'htmlOptions'     => array(
		'style' => '$data->oneHourLeft ? "width:90px; text-align: center; background-color: #fae4f7; border-color: #fae4f7; border-bottom-color: #dad8db;" : "width:90px; text-align: center;"'
	)
);
$columns[] = array(
	'class'           => 'LfDataColumn',
	'name'            => 'ФИО клиента',
	'type'            => 'raw',
	'value'           => '"<div style=\"word-wrap: break-word; width: 105px;\">".$data->name."</div>"',
	'evaluateOptions' => true,
	'htmlOptions'     => array(
		'style' => '$data->oneHourLeft ? "width:130px; background-color: #fae4f7; border-color: #fae4f7; border-bottom-color: #dad8db; white-space: pre-wrap; white-space: -moz-pre-wrap; white-space: -pre-wrap; white-space: -o-pre-wrap; word-wrap: break-word;" : "width:130px; white-space: pre-wrap; white-space: -moz-pre-wrap; white-space: -pre-wrap; white-space: -o-pre-wrap; word-wrap: break-word;"'
	)
);
if ($status != 'cancel') {
	$columns[] = array(
		'class'           => 'LfDataColumn',
		'name'            => 'Телефон',
		'type'            => 'raw',
		'value'           => '$data->phone',
		'evaluateOptions' => true,
		'htmlOptions'     => array(
			'style' => '$data->oneHourLeft ? "width:120px; text-align: center; white-space: nowrap; background-color: #fae4f7; border-color: #fae4f7; border-bottom-color: #dad8db;" : "width:120px; text-align: center; white-space:nowrap;"'
		)
	);
}

$columns[] = array(
	'class'           => 'LfDataColumn',
	'name'            => 'Цена',
	'type'            => 'raw',
	'value'           => '$data->getPriceFormatted() ? $data->getPriceFormatted()." руб." : ""',
	'evaluateOptions' => true,
	'htmlOptions'     => array(
		'style' => '$data->oneHourLeft ? "width:130px; text-align: center; font-weight:bold; white-space:nowrap; background-color: #fae4f7; border-color: #fae4f7; border-bottom-color: #dad8db;" : "width:130px; text-align: center; font-weight:bold; white-space:nowrap;"'
	)
);
$columns[] = array(
	'class'           => 'LfDataColumn',
	'name'            => 'Услуга',
	'type'            => 'raw',
	'value'           => '$data->service ? $data->service->name : ($data->specialization ? $data->specialization->name : "")',
	'evaluateOptions' => true,
	'htmlOptions'     => array(
		'style' => '$data->oneHourLeft ? "width:130px; background-color: #fae4f7; border-color: #fae4f7; border-bottom-color: #dad8db;" : "width:130px;"'
	)
);


if ($status == 'apply') {
	$columns[] = array(
		'class'           => 'LfDataColumn',
		'name'            => 'Дата приема',
		'type'            => 'raw',
		'value'           =>
			'date("H:i d.m.Y", $data->date)."<br><div style=\"position:relative\"><div class=\"popup-apply popup-note\"></div><a href=\"#\" class=\"apply-button\" data-id=".$data->id." data-status=\"' .
			$status .
			'\">редактировать</a></div>"',
		'evaluateOptions' => true,
		'htmlOptions'     => array(
			'style' => '$data->oneHourLeft ? "width:110px; background-color: #fae4f7; border-color: #fae4f7; border-bottom-color: #dad8db;" : "width:110px;"'
		)
	);
} elseif ($status == 'cancel' || $status == 'completed') {
	$columns[] = array(
		'class'           => 'LfDataColumn',
		'name'            => 'Время',
		'type'            => 'raw',
		'value'           => 'date("H:i <br>d.m.Y", $data->NumericChanged)',
		'evaluateOptions' => true,
		'htmlOptions'     => array(
			'style' => '$data->oneHourLeft ? "width:110px; background-color: #fae4f7; border-color: #fae4f7; border-bottom-color: #dad8db;" : "width:110px;"'
		)
	);
} else {
	$columns[] = array(
		'class'           => 'LfDataColumn',
		'name'            => 'Время',
		'type'            => 'raw',
		'value'           => '"Актуально до<br>".date("H:i d.m.y", $data->getRejectedTime())',
		'evaluateOptions' => true,
		'htmlOptions'     => array(
			'style' => '$data->oneHourLeft ? "width:130px; background-color: #fae4f7; border-color: #fae4f7; border-bottom-color: #dad8db;" : "width:110px;"'
		)
	);
}

$columns[] = array(
	'class'           => 'LfDataColumn',
	'name'            => 'Выезд на дом',
	'type'            => 'raw',
	'value'           => '$data->departure ? "<div style=\"word-wrap: break-word; width: 120px;\"><div class=\"prof-appointment-check\">Да</div>$data->address</div>" : "<div style=\"width: 120px;\">Нет</div>"',
	'evaluateOptions' => true,
	'htmlOptions'     => array(
		'style' => '$data->oneHourLeft ? "background-color: #fae4f7; border-color: #fae4f7; border-bottom-color: #dad8db;" : ""'
	)
);

if ($status == 'new' || $status == 'apply') {
	$columns[] = array(
		'class'       => 'LfButtonColumn',
		'header'      => 'Статус',
		'optionsData' => true,
		'buttons'     => array(
			'apply'    => array(
				'label'   => 'принять',
				'url'     => '"#"',
				'options' => array('class' => 'apply-button', 'data-id' => '$data->id', 'data-status' => $status)
			),
			'complete' => array(
				'label'   => 'завершить',
				'url'     =>
					'Yii::app()->createUrl("lk/updateStatus", array("act"=>"complete", "id"=>$data->id, "status"=>"' .
					$status .
					'"))',
				'options' => array(
					'class'       => 'complete-button',
					'data-id'     => '$data->id',
					"data-service_price"  => '$data->service_price',
					'data-status' => $status
				)
			),
			'cancel'   => array(
				'label'   => 'отклонить',
				'url'     => '"#"',
				'options' => array('class' => 'cancel-button', 'data-id' => '$data->id', 'data-status' => $status)
			),
		),
		'template'    => $status == 'new'
				? '<div style="position:relative"><div class="popup-abuse popup-note popup-apply"></div> {apply}</div><div style="position:relative"><div class="popup-abuse popup-note popup-cancel"></div> {cancel}</div>'
				: ($status == 'apply'
					? '{complete} <div style="position:relative"><div class="popup-abuse popup-note popup-cancel"></div> {cancel}</div>'
					: ''),
		'htmlOptions' => array(
			'style' => '$data->oneHourLeft ? "background-color: #fae4f7; border-color: #fae4f7; border-bottom-color: #dad8db;" : ""',
			'class' => '"button-column"'
		)
	);
} elseif ($status == 'cancel') {
	$columns[] = array(
		'class'       => 'CDataColumn',
		'name'        => 'Статус',
		'type'        => 'raw',
		'value'       => '$data->statusList[$data->status]."<br>".$data->reason',
		'htmlOptions' => array(
			'style' => 'max-width: 160px; min-width: 160px !important; width:160px !important; white-space: pre-wrap; white-space: -moz-pre-wrap; white-space: -pre-wrap; white-space: -o-pre-wrap; word-wrap: break-word;'
		)
	);
} else {
	$columns[] = array(
		'class'       => 'CDataColumn',
		'name'        => 'Статус',
		'type'        => 'raw',
		'value'       => '$data->statusList[$data->status]',
		'htmlOptions' => array(
			'style' => 'width:55px; padding-right:5px;'
		)
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


<script type="text/javascript">
	$(function () {
		var isPaymentsEnabled = <?php echo json_encode(Yii::app()->getModule('payments')->isActive()); ?>;

		$('.complete-button').on('click', function () {
			var link = $(this).attr('href');
			var app_id = $(this).attr('data-id');
			var price = $(this).attr('data-service_price');

			if (!isPaymentsEnabled) {
				// Монитизация отключени, просто завершаем заявку
				window.location.href = link;
				return true;
			}

			$('.app_number').text(app_id);
			$('.overlay').show();
			var popup = $('#popup');
			popup.show();

			var padding = parseInt($('#popup').css('paddingRight')) + parseInt($('#popup').css('paddingLeft'));
			var popupWidth = popup.width() + padding;
			var windowSizes = getViewPort();
			if(popupWidth > windowSizes.width)
				popup.width(windowSizes.width - padding);
			popup.css('marginLeft', popup.outerWidth() / 2 * -1);

			$("#service_price2").on('keyup', function () {
				if (parseInt($("#service_price2").val()) > 0) {
					$('.hide').slideDown(300);

					var service_price2 = parseInt($("#service_price2").val());
					var commission = parseInt(service_price2 * <?php echo ( number_format( Yii::app()->params['appointmentCommission'], 3, '.', '' ) ); ?>);
					$('.service_price2').text(commission);
					$('.button-pink').attr('href', link + "&service_price2=" + service_price2);

				}
				else {
					$('.hide').slideUp(300);
				}
			});

			$('.popup-close').on('click', function () {
				$('.overlay').hide();
				$('#popup').hide();
				return false;
			});

			$('.overlay').on('click', function () {
				$('.overlay').hide();
				$('#popup').hide();
				return false;
			});

			return false;
		});


	});
</script>