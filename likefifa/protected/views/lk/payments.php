<?php
/**
 * @var LkController        $this
 * @var LfMaster            $model
 * @var CActiveDataProvider $dataProvider
 */

$this->widget(
	'\likefifa\components\likefifa\widgets\lk\PaymentsWidget',
	array(
		'model' => $model,
	)
);
?>

<div class="content-wrap content-pad-bottom appointment-wrap">
	<div class="page-profile">
		<div class="prof-wrap">
			<?php $this->widget(
				'application.components.likefifa.widgets.LfLkTabsWidget',
				array(
					'currentTab' => 'payments',
				)
			); ?>

			<?php $this->widget(
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
					'emptyText'    => 'Нет платежей',
					'columns'      => [
						[
							'class'       => 'LfDataColumn',
							'name'        => '&nbsp;&nbsp;Платеж&nbsp;&nbsp;',
							'type'        => 'raw',
							'value'       => '$data->isIncoming() ? "Приход" : "Расход"',
							'htmlOptions' => [
								'style' => 'width:90px; text-align: center;'
							]
						],
						[
							'class'       => 'LfDataColumn',
							'name'        => '&nbsp;&nbsp;Дата&nbsp;платежа&nbsp;&nbsp;',
							'type'        => 'raw',
							'value'       => 'date("d.m.y", strtotime($data->create_date))',
							'htmlOptions' => [
								'style' => 'width:90px; text-align: center;'
							]
						],
						[
							'class'       => 'LfDataColumn',
							'name'        => '&nbsp;&nbsp;№ заявки&nbsp;&nbsp;',
							'type'        => 'raw',
							'value'       => '$data->getAppointment() ? CHtml::link($data->getAppointment()->id, "/lk/appointment/completed/") : "-"',
							'htmlOptions' => [
								'style' => 'width:90px; text-align: center;'
							]
						],
						[
							'class'       => 'LfDataColumn',
							'name'        => '&nbsp;&nbsp;Имя клиента&nbsp;&nbsp;',
							'type'        => 'raw',
							'value'       => '$data->getAppointment() ? $data->getAppointment()->name : "-"',
							'htmlOptions' => [
								'style' => 'width:90px; text-align: center;'
							]
						],
						[
							'class'       => 'LfDataColumn',
							'name'        => '&nbsp;&nbsp;Услуга&nbsp;&nbsp;',
							'type'        => 'raw',
							'value'       => '$data->getAppointment() && $data->getAppointment()->service ? $data->getAppointment()->service->name : "-"',
							'htmlOptions' => [
								'style' => 'width:90px; text-align: center;'
							]
						],
						[
							'class'       => 'LfDataColumn',
							'name'        => '&nbsp;&nbsp;Цена&nbsp;&nbsp;',
							'type'        => 'raw',
							'value'       => '$data->getAppointment() && $data->getAppointment()->getPriceFormatted() ? $data->getAppointment()->getPriceFormatted()." руб." : "-"',
							'htmlOptions' => [
								'style' => 'width:90px; text-align: center;'
							]
						],
						[
							'class'       => 'LfDataColumn',
							'name'        => '&nbsp;&nbsp;Сумма&nbsp;&nbsp;',
							'type'        => 'raw',
							'value'       => '$data->getPriceFormatted() . "руб."',
							'htmlOptions' => [
								'style' => 'width:90px; text-align: center;'
							]
						],
					]
				)
			);?>

		</div>
	</div>
</div>