<?php
/**
 * @var FrontendController $this
 * @var LfSalon            $data
 * @var LfWork[]           $works
 * @var LfSpecialization   $specialization
 * @var LfService          $service
 * @var boolean            $all
 */

$i = 0;
$display = '';
foreach ($works as $work) {
	if ($i++ > 2) {
		$display = 'display:none;';
	}

	echo CHtml::link(
		CHtml::image(
			$work->preview('small'),
			$work->alt ? $work->alt : $work->service->name,
			['width' => 110]
		),
		$work->preview('full'),
		[
			'class'               => $i == 1 ? 'search-res_works_f' : false,
			'title'               => $work->service->name,
			'salon-id'            => $data->id,
			'data-filter-spec'    => $specialization != null ? $specialization->id : false,
			'data-filter-service' => $service != null ? $service->id : false,
			'rel'                 => $all ? 'prettyPhoto[gallery' . $data->id . ']' : false,
			'style'               => $display,

		]
	);
}