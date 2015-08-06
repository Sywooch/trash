<?php
class ImageResizeUtils {
	
	protected $path = null;

	/**
	 * 
	 * @param $path
	 * @return ImageResizeUtils
	 */
	public static function create($path) {
		return new self($path);
	}
	
	protected function __construct($path) {
		$this->path = $path;
	}

	protected function getFuncs($type) {
		$suffixes = array(
			IMAGETYPE_GIF => 'gif',
			IMAGETYPE_JPEG => 'jpeg',
			IMAGETYPE_JPEG2000 => 'jpeg',
			IMAGETYPE_PNG => 'png',
		);
		
		if (!isset($suffixes[$type]))
			return false;
			
		return array(
			'imagecreatefrom'.$suffixes[$type],
			'image'.$suffixes[$type],
		);
	}
	
	protected function prepare() {
		list($w, $h, $type) = getimagesize($this->path);
		if (
			!$type 
			|| !($funcs = $this->getFuncs($type))
		) {
			return false;
		}
		
		$size = array_values(compact('w', 'h'));
		
		$qualityList = array(
			IMAGETYPE_JPEG => 100,
			IMAGETYPE_JPEG2000 => 100,
			IMAGETYPE_PNG => 0,
		);
		
		$quality = 
			isset($qualityList[$type])
				? $qualityList[$type]
				: null;
				
		return
			compact('size', 'funcs', 'quality');
	}
	
	protected function createNewImage($w, $h) {
		$img = imagecreatetruecolor($w, $h);
		imagealphablending($img, false);
		imagesavealpha($img, true);
		return $img;
	}

	protected function checkSizeSmaller($newSize, $calculatedSize) {
		return ($calculatedSize['w'] <= $newSize['w'] && $calculatedSize['h'] <= $newSize['h']);
	}
	
	protected function checkSizeBigger($newSize, $calculatedSize) {
		return ($calculatedSize['w'] >= $newSize['w'] && $calculatedSize['h'] >= $newSize['h']);
	}
	
	protected function calcSize($newSize, $oldSize, $method) {
		$sizes = array(
			array('w' => $newSize['w'], 'h' => round($newSize['w'] * ($oldSize['h'] / $oldSize['w']))),
			array('h' => $newSize['h'], 'w' => round($newSize['h'] * ($oldSize['w'] / $oldSize['h'])))
		);
		
		foreach ($sizes as $size) {
			if ($this->{$method}($newSize, $size)) return $size;
		}
	}
	
	public function fitSmaller($fitWidth, $fitHeight) {
		if (!($data = $this->prepare())) {
			return false;
		}
				
		list($imageCreate, $imageSave) = $data['funcs'];
		list($w, $h) = $data['size'];
		
		if ($w < $fitWidth && $h < $fitHeight) {
			return true;
		}
		
		$size = $this->calcSize(
			array(
				'w' => $fitWidth, 
				'h' => $fitHeight
			),
			compact('w', 'h'),
			'checkSizeSmaller'
		);
		
		$imgOld = $imageCreate($this->path);
		$imgNew = $this->createNewImage($size['w'], $size['h']);
		
		$result = (
			imagecopyresampled($imgNew, $imgOld, 0, 0, 0, 0, $size['w'], $size['h'], $w, $h)
			&& $imageSave($imgNew, $this->path, $data['quality'])
		);
		
		imagedestroy($imgOld);
		imagedestroy($imgNew);
		
		return $result;
	}
	
	public function fitExact($fitWidth, $fitHeight) {
		if (!($data = $this->prepare())) {
			return false;
		}
				
		list($imageCreate, $imageSave) = $data['funcs'];
		list($w, $h) = $data['size'];
		
		$size = $this->calcSize(
			array(
				'w' => $fitWidth, 
				'h' => $fitHeight
			),
			compact('w', 'h'),
			'checkSizeBigger'
		);
		
		$x = ($fitWidth - $size['w']) / 2;
		$y = ($fitHeight - $size['h']) / 2;
		
		$imgOld = $imageCreate($this->path);
		$imgNew = $this->createNewImage($fitWidth, $fitHeight);
		
		$result = (
			imagecopyresampled($imgNew, $imgOld, $x, $y, 0, 0, $size['w'], $size['h'], $w, $h)
			&& $imageSave($imgNew, $this->path, $data['quality'])
		);
		
		imagedestroy($imgOld);
		imagedestroy($imgNew);
		
		return $result;
	}
	
	public function fitSoft($fitWidth, $fitHeight) {
		if ($fitWidth === null && $fitHeight === null) {
			return false;
		}
		
		if ($fitWidth !== null && $fitHeight !== null) {
			return false;
		}
		
		if (!($data = $this->prepare())) {
			return false;
		}
				
		list($imageCreate, $imageSave) = $data['funcs'];
		list($w, $h) = $data['size'];
		
		if ($fitWidth === null) {
			if ($h < $fitHeight) return true;
			$fitWidth = round($fitHeight * ($w / $h));
		}
		
		if ($fitHeight === null) {
			if ($w < $fitWidth) return true;
			$fitHeight = round($fitWidth * ($h / $w));
		}
		
		$imgOld = $imageCreate($this->path);
		$imgNew = $this->createNewImage($fitWidth, $fitHeight);
		
		$result = (
			imagecopyresampled($imgNew, $imgOld, 0, 0, 0, 0, $fitWidth, $fitHeight, $w, $h)
			&& $imageSave($imgNew, $this->path, $data['quality'])
		);
		
		imagedestroy($imgOld);
		imagedestroy($imgNew);
		
		return $result;
	}
	
	public function fit($params) {
		if (!isset($params['method'])) {
			$params['method'] = 'exact';
		}
		
		return
			$this->{'fit'.ucfirst($params['method'])}($params['w'], $params['h']);
	}

}