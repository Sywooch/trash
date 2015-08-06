<?php
/**
 * @var FrontendController $this
 * @var LfMaster           $data
 * @var LfWork[]           $works
 * @var LfSpecialization   $specialization
 * @var LfService          $service
 * @var boolean            $all
 * @var integer            $count
 */

$i = 0;
$display = '';
foreach ($works as $work) {
	if ($i++ > $count - 1) {
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
			'class'               => 'det-works_img ',
			'data-work-id'        => $work->id,
			'master-id'           => $data->id,
			'data-service-id'     => $work->service->id,
			'data-spec-id'        => $work->specialization->id,
			'data-master-pic'     => $data->avatar(),
			'data-master-link'    => $data->getProfileUrl(),
			'data-master-name'    => $data->getFullName(),
			'data-service-price'  => $work->price ? $work->price->getPriceFormatted() : false,
			'data-filter-spec'    => $specialization != null ? $specialization->id : false,
			'data-filter-service' => $service != null ? $service->id : false,
			'data-count'          => $count,
			'rel'                 => $all ? 'prettyPhoto[gallery' . $data->id . ']' : false,
			'style'               => $display,
			'title'               => $work->alt ?: $work->service->name,
		]
	);
}