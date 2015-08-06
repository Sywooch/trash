<?php


namespace likefifa\extensions\image;

use CActiveRecordBehavior;
use CException;
use Exception;
use Yii;

class ImageBehavior extends CActiveRecordBehavior
{
	/**
	 * Название сущности
	 *
	 * @var string
	 */
	public $entity;

	/**
	 * Имя атрибута, хранящего название изображения
	 *
	 * @var string
	 */
	public $imageAttribute = 'image';

	/**
	 * Алиас дирректории, хранащая в себе изображения
	 *
	 * @var string
	 */
	public $imagePath = 'upload/imagess';

	/**
	 * Дирректория с оригинальными изображениями
	 *
	 * @var string
	 */
	public $originalFolder = 'originals';

	/**
	 * Дирректория с тамбнейлами
	 *
	 * @var string
	 */
	public $thumbFolder = 'thumbs';

	/**
	 * Путь до темповой директории с превьюшками
	 *
	 * @var mixed
	 */
	private $tempFolder = 'temp';

	/**
	 * Поле для хранения координат кропинга
	 *
	 * @var string
	 */
	public $cropAttribute = null;

	/**
	 * Хранит в себе имя ключа в запросе
	 *
	 * @var string
	 */
	public $requestName;

	/**
	 * Сохраняет параметры модели
	 *
	 * @param string $name
	 * @param array  $crops
	 *
	 * @return bool
	 */
	public function saveEntity($name, array $crops = [])
	{
		$saveAttributes = [$this->imageAttribute];

		$this->owner->{$this->imageAttribute} = $name;

		if ($this->cropAttribute != false) {
			$saveAttributes[] = $this->cropAttribute;
			$this->owner->{$this->cropAttribute} = empty($crops) ? null : serialize($crops);
		}

		if (!$this->owner->isNewRecord) {
			return $this->owner->saveAttributes($saveAttributes);
		}

		$this->normalizeSize();

		return false;
	}

	/**
	 * Уменьшает размер изображения
	 *
	 * @throws CException
	 */
	public function normalizeSize()
	{
		$image = new Image($this->getOriginalPath());
		if ($image->width > 2000 || $image->height > 2000) {
			$image->resize(2000, 2000, $image->width > $image->height ? Image::WIDTH : Image::HEIGHT);
			$image->save();
		}
	}

	/**
	 * Загружает файл из $_FILES
	 */
	public function uploadFile()
	{
		if (isset($_FILES[$this->getEntityName()])) {
			foreach ($_FILES[$this->getEntityName()]["error"] as $key => $error) {
				if ($error == UPLOAD_ERR_OK) {
					$tmp_name = $_FILES[$this->getEntityName()]['tmp_name'][$key];

					$extension = explode('.', $_FILES[$this->getEntityName()]['name'][$key]);
					$extension = end($extension);

					$imageName = md5($this->owner->primaryKey . time()) . '.' . $extension;

					move_uploaded_file($tmp_name, $this->getOriginalFolder() . $imageName);
					$this->saveEntity($imageName);
				}
			}
		}
	}

	/**
	 * Сохраняет изображение из временной папки
	 *
	 * @param string $name
	 * @param array  $crops
	 *
	 * @return boolean
	 */
	public function saveImage($name, array $crops = [])
	{
		$result = rename(
			$this->getTempPath() . $name,
			$this->getOriginalFolder() . $name
		);

		if ($result) {
			$this->saveEntity($name, $crops);
		}

		return $result;
	}

	/**
	 * Сохраняет изображение по ссылке
	 *
	 * @param string $url
	 * @param array  $crops
	 *
	 * @return bool
	 */
	public function saveImageByUrl($url, array $crops = [])
	{
		$imageName = md5($this->owner->primaryKey . time());
		$fileName =
			$this->getOriginalFolder() .
			$imageName;
		$content = file_get_contents($url);
		file_put_contents($fileName, $content);
		$imageType = exif_imagetype($fileName);

		switch ($imageType) {
			case 1 :
				$ext = 'gif';
				break;
			case 2 :
				$ext = 'jpg';
				break;
			case 3 :
				$ext = 'png';
				break;
			default:
				$ext = false;
		}

		if ($ext != false) {
			rename($fileName, $fileName . '.' . $ext);
			$imageName = $imageName . '.' . $ext;

			$this->saveEntity($imageName, $crops);

			return true;
		} else {
			unlink($fileName);
			return false;
		}
	}

	/**
	 * Возвращает путь до папки с изображениями
	 *
	 * @return string
	 */
	public function getImagesFolder()
	{
		return
			Yii::getPathOfAlias('application') .
			DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
			$this->imagePath;
	}

	public function getOriginalFolder()
	{
		return
			$this->getImagesFolder() .
			DIRECTORY_SEPARATOR .
			$this->originalFolder .
			DIRECTORY_SEPARATOR .
			$this->getEntityName() .
			DIRECTORY_SEPARATOR;
	}

	/**
	 * Возвращает путь до оригинального изображения
	 *
	 * @return string
	 */
	public function getOriginalPath()
	{
		return $this->getOriginalFolder() . $this->owner->{$this->imageAttribute};
	}

	/**
	 * Возвращает урл до оригинального изображения
	 *
	 * @return string
	 */
	public function getOriginalUrl()
	{
		return implode(
			'/',
			array(
				Yii::app()->getBaseUrl(true),
				$this->imagePath,
				$this->originalFolder,
				$this->getEntityName(),
				$this->owner{$this->imageAttribute},
			)
		);
	}

	/**
	 * Возвращает путь до дирректории с темпами
	 *
	 * @return string
	 */
	public function getTempPath()
	{
		return $this->getImagesFolder() . DIRECTORY_SEPARATOR . $this->tempFolder . DIRECTORY_SEPARATOR;
	}

	/**
	 * Возвращает урл до дирректории с темпами
	 *
	 * @return string
	 */
	public function getTempUrl()
	{
		return implode(
			'/',
			array(
				Yii::app()->getBaseUrl(true),
				$this->imagePath,
				$this->tempFolder,
			)
		);
	}

	/**
	 * Возвращает путь до превью
	 *
	 * @param integer      $width     ширина превью
	 * @param integer      $height    высота превью
	 * @param bool         $crop      нужно ли сделать кропинг
	 * @param integer|null $master    параметр ориентации ресайза
	 * @param bool         $force     принудительное создание
	 * @param string       $watermark водная метка
	 *
	 * @return bool|string
	 */
	public function getPreviewPath($width, $height, $crop = false, $master = Image::WIDTH, $force = false, $watermark)
	{
		$thumb_dir =
			$this->getImagesFolder() .
			DIRECTORY_SEPARATOR .
			$this->thumbFolder .
			DIRECTORY_SEPARATOR .
			$this->getEntityName();
		$thumb_path = $thumb_dir . DIRECTORY_SEPARATOR . $width . 'x' . $height;
		$thumb = $thumb_path . DIRECTORY_SEPARATOR . $this->owner->{$this->imageAttribute};

		// Если нет превью или нужно заменить старые - перегенерируем
		if (!file_exists($thumb) || $force) {
			if (!file_exists($this->getOriginalPath())) {
				return false;
			}

			if (!file_exists($thumb_dir)) {
				mkdir($thumb_dir, 755);
			}

			if (!file_exists($thumb_path)) {
				mkdir($thumb_path, 755);
				$handle = fopen($thumb_path . DIRECTORY_SEPARATOR . 'index.html', 'x+');
				fclose($handle);
			}

			$image = null;
			try {
				$image = new Image($this->getOriginalPath());
			} catch (Exception $e) {
				return false;
			}

			// Если есть поле с координатами кропинга и оно не пустое - применяем кропинг
			if ($this->cropAttribute && $this->owner->{$this->cropAttribute} != null) {
				$crop = false;
				$crops = $this->getCropCoordinates(unserialize($this->owner->{$this->cropAttribute}));
				$image->crop($crops['x2'] - $crops['x1'], $crops['y2'] - $crops['y1'], $crops['y1'], $crops['x1']);
			}

			// Если нужно автоматом уменьшить изображение
			if ($crop && $master == Image::WIDTH) {
				$sourceRatio = $image->width / $image->height;
				$targetRatio = $width / $height;

				if ($sourceRatio < $targetRatio) {
					$scale = $image->width / $width;
				} else {
					$scale = $image->height / $height;
				}

				$new_width = $image->width;
				$new_height = $image->height;
				if ($width <= $height) {
					if ($image->width < $image->height) {
						$resizeHeight = (int)($image->height / $scale);

						$heightDiff = ($resizeHeight - $height) * $scale;
						if ($heightDiff >= 0) {
							$new_height = $image->height - $heightDiff;
						}
					} else {
						$resizeWidth = (int)($image->width / $scale);

						$widthDiff = ($resizeWidth - $width) * $scale;
						if ($widthDiff >= 0) {
							$new_width = $image->width - $widthDiff;
						}
					}

				} else {
					if ($image->width <= $image->height) {
						$resizeHeight = (int)($image->height / $scale);

						$heightDiff = ($resizeHeight - $height) * $scale;
						if ($heightDiff >= 0) {
							$new_height = $image->height - $heightDiff;
						}
					} else {
						$resizeWidth = (int)($image->width / $scale);

						$widthDiff = ($resizeWidth - $width) * $scale;
						if ($widthDiff >= 0) {
							$new_width = $image->width - $widthDiff;
						}
					}
				}

				$image->crop($new_width, $new_height);
			}

			$image->quality(100);

			if (
				($master == Image::WIDTH && $image->width > $width) ||
				($master == Image::HEIGHT && $image->height > $height) ||
				($master == Image::AUTO && ($image->width > $width || $image->height > $height))
			) {
				$image->resize($width, $height, Image::WIDTH);
			}

			if ($watermark != null) {
				$image->watermark($watermark, 100, 'SouthEast', 10, 5);
			}

			$image->save($thumb);
			unset($image);
		}
		return $thumb;
	}

	/**
	 * Возвращает ссылку до превью
	 *
	 * @param integer $width
	 * @param integer $height
	 * @param bool    $crop
	 * @param integer $master
	 * @param boolean $force
	 * @param string  $watermark
	 *
	 * @return string
	 */
	public function preview($width, $height, $crop = false, $master = Image::WIDTH, $force = false, $watermark = null)
	{
		try {
			$this->getPreviewPath($width, $height, $crop, $master, $force, $watermark);
		} catch (Exception $e) {
			return false;
		}

		return implode(
			'/',
			array(
				Yii::app()->getBaseUrl(true),
				$this->imagePath,
				$this->thumbFolder,
				$this->getEntityName(),
				$width . 'x' . $height,
				$this->owner{$this->imageAttribute},
			)
		);
	}

	/**
	 * Переворачивает изображение
	 *
	 * @param string $direction направление вращения
	 * @param string $temp      имя временной фото
	 */
	public function rotate($direction, $temp = null)
	{
		if ($temp == null) {
			$imageName = $this->getOriginalPath();
		} else {
			$imageName = $this->getTempPath() . $temp;
		}
		(new Image($imageName))->rotate($direction)->save();
	}

	/**
	 * Рассчитывает правильные координаты для кропинга
	 *
	 * @param array  $crops
	 * @param string $temp
	 *
	 * @return array
	 */
	public function getCropCoordinates($crops, $temp = null)
	{
		$x1 = $crops['x1'];
		$x2 = $crops['x2'];
		$y1 = $crops['y1'];
		$y2 = $crops['y2'];
		$width = $crops['width'];
		$height = $crops['height'];

		$imagePath = $temp == null ? $this->getOriginalPath() : $this->getTempPath() . $temp;

		$sizes = getimagesize($imagePath);
		$originalWidth = $sizes[0];
		$originalHeight = $sizes[1];

		$x1 = (int)($x1 * $originalWidth / $width);
		$x2 = (int)($x2 * $originalWidth / $width);
		$y1 = (int)($y1 * $originalHeight / $height);
		$y2 = (int)($y2 * $originalHeight / $height);

		return compact('x1', 'x2', 'y1', 'y2');
	}

	/**
	 * Возвращает размер изображения
	 *
	 * @return int|null
	 */
	public function getFileSize()
	{
		if (!$this->getOriginalPath()) {
			return null;
		}

		return filesize($this->getOriginalPath());
	}

	/**
	 * Возаращает название сущности
	 *
	 * @return string
	 */
	private function getEntityName()
	{
		if($this->entity != null) {
			return $this->entity;
		}

		$class = get_class($this->owner);
		if (strpos($class, '\\') !== false) {
			$namespace = explode('\\', $class);
			return $namespace[count($namespace) - 1]; //get last element - model name
		}
		return $class;
	}
} 