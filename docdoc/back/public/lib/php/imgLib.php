<?php
function img_resize($src, $dest, $width, $height, $quality = 100, $sharp = false) {
	$rgb=0xffffff;
	
	if (!file_exists($src)) return false;

	$size = getimagesize($src);
	if ($size === false) return false;

	$format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
	$icfunc = "imagecreatefrom" . $format;
	if (!function_exists($icfunc)) return false;

	$ratio1 = $width / $size[0];
	$ratio2 = $height / $size[1];
	($ratio1 > $ratio2) ? $ratio = $ratio2 : $ratio = $ratio1;
//	$height = $size[1]*$ratio;
	
	$new_width =  floor($size[0] * $ratio);
	$new_height =	floor($size[1] * $ratio);
	
//	$new_left    = $use_x_ratio  ? 0 : floor(($width - $new_width) / 2);
//	$new_top     = !$use_x_ratio ? 0 : floor(($height - $new_height) / 2);
	
	$isrc = $icfunc($src);
	$idest = imagecreatetruecolor($width, $height);
	imagefill($idest, 0, 0, $rgb);
	imagecopyresampled($idest, $isrc, 0, 0, 0, 0, $new_width, $new_height, $size[0], $size[1]);
	if ($sharp) sharp($idest);
	imagejpeg($idest, $dest, $quality);
	imagedestroy($isrc);
	imagedestroy($idest);
	
	return true;
}

function sharp ($image) {
	$matrix = array(array(-1,-1,-1), array(-1,16,-1), array(-1,-1,-1));
	imageconvolution($image, $matrix, 8, 0);

	return true;
}


/*	Выравнивание по большему и обрезание до ровного	*/
function img_resize_2($src, $dest, $width, $height, $widthMin = 0, $heightMin = 0, $quality=100 ) {
	$rgb=0xffffff;
	
	if ($widthMin == 0 || $heightMin  == 0 ) {
		$widthMin = $width;
		$heightMin = $height;
	}
	
	if (!file_exists($src)) return false;
	
	$size = getimagesize($src);
	if ($size === false) return false;

	$format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
	$icfunc = "imagecreatefrom" . $format;
	if (!function_exists($icfunc)) return false;

	$ratio1 = $width / $size[0];
	$ratio2 = $height / $size[1];
	($ratio1 > $ratio2) ? $ratio = $ratio2 : $ratio = $ratio1;
	
	$ratio1 = $widthMin / $size[0];
	$ratio2 = $heightMin / $size[1];
	($ratio1 < $ratio2) ? $ratioMin = $ratio2 : $ratioMin = $ratio1;

	if ($width < $size[0] || $height < $size[1] ) {
		$new_width = floor($size[0] * $ratio);
		$new_height = floor($size[1] * $ratio);
	} else if ($widthMin < $size[0] && $heightMin < $size[1] ) {
		$new_width = $size[0];
		$new_height = $size[1];
	} else {
		$new_width = floor($size[0] * $ratioMin);
		$new_height = floor($size[1] * $ratioMin);
	}
	$isrc = $icfunc($src);
	$idest = imagecreatetruecolor($new_width, $new_height);
	imagefill($idest, 0, 0, $rgb);
	imagecopyresampled($idest, $isrc, 0, 0, 0, 0, $new_width, $new_height, $size[0], $size[1]);

	imagejpeg($idest, $dest, $quality);
	imagedestroy($isrc);
	imagedestroy($idest);
	
	return true;
}
?>
