<?php
	$general =	array(
		'font'=>array(
			'name' => 'Arial',
			'size'=>'10',
			'color'=> array('rgb' => '353535')
		)
	);
	 
	$TH = array(
		'font'=>array(
			'name' => 'Arial',
			'size'=>'10',
			'bold'=>true,
			'color'=> array('rgb' => '000000')
		),
		'alignment'=>array(
			'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER
		),
		'fill'=>array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('rgb' => '67bbbc')
		) ,
		'borders' => array(
             'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
             )
       )
	);
	$Head = array(
		'font'=>array(
			'name' => 'Arial',
			'size'=>'16',
			'bold'=>true
		),
		'alignment'=>array(
			'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER
		) 
	);
	
	
	$strong = array(
		'font'=>array(
			'bold'=>true
		)
	);
	
	$moveRight3 = array(
		'alignment'=>array(
			'indent'=> '3'
		)
	);
	
	
	$even = array(
		'fill'=>array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('rgb' => 'ffffff')
		) 
	);
	
	$odd = array(
		'fill'=>array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('rgb' => 'eefbfc')
		) 
	);
	
	$red = array(
		'font'=>array(
			'color' => array('rgb' => 'a81010')
		)
	);
	
	
	$left = array(
		'alignment'=>array(
			'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
			'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER
		) 
	);
	$center = array(
		'alignment'=>array(
			'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER
		) 
	);
	
	$wb = array(
		'borders' => array(
        	'allborders' => array(
        		'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		)
	);
	
?>
