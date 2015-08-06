<?php
class CArFileUploadBehavior extends CActiveRecordBehavior {
	
	protected $paths 		= null;
	protected $urls			= null;
	
	protected $uploaded		= array();
	
	protected function getOwnerValue($attribute, $ownerMethod, $prop, $defaultValue) {
		if (
			($this->$prop === null)
			&& method_exists($this->owner, $ownerMethod)
		) {
			$this->$prop = $this->owner->$ownerMethod();
		}
		
		return 
			is_array($this->$prop) 
			&& isset($this->{$prop}[$attribute]) 
				? $this->{$prop}[$attribute] 
				: $defaultValue;
	}
	
	public function getUploadPath($attribute) {
		return $this->getOwnerValue($attribute, 'uploadPaths', 'paths', Yii::app()->basePath.'/../upload/');
	}
	
	public function getUploadUrl($attribute) {
		return 
			$this->getOwnerValue($attribute, 'uploadUrls', 'urls', Yii::app()->baseUrl.'/upload/');
	}
	
	public function getFilename($attribute, CUploadedFile $file) {
		return
			method_exists($this->owner, 'uploadFilename') 
			&& ($filename = $this->owner->uploadFileName($attribute, $file))
				? $filename
				: $file->getName();
	}
	
	public function isFileTouched($attribute) {
		return in_array($attribute, $this->uploaded);
	}
	
	public function isUploaded($attribute) {
		return (is_string($this->owner->$attribute) && strlen($this->owner->$attribute) > 0);
	}
	
	public function getPath($attribute) {
		return
			$this->isUploaded($attribute)
				? $this->getUploadPath($attribute).$this->owner->$attribute
				: null;
	}
	
	public function getUrl($attribute) {
		return 
			$this->isUploaded($attribute)
				? $this->getUploadUrl($attribute).$this->owner->$attribute
				: null;
	}
	
	protected function getFileAttributes() {
		$attributes = array();
		foreach ($this->owner->rules() as $rule) {
			if (!isset($rule[1]) || $rule[1] !== 'file') continue;
			
			$attributes =
				array_merge(
					$attributes,
					array_map('trim', explode(',', $rule[0]))
				);
		}
		
		return $attributes;
	}

	public function uploadFile($attribute) {
		if (!($file = CUploadedFile::getInstance($this->owner, $attribute))) {
			return $this->owner;
		}
		
		$this->uploaded[] = $attribute;
		
		$this->deleteFile($attribute);
		
		$path = $this->getUploadPath($attribute);
		$filename = $this->getFilename($attribute, $file);

		$file->saveAs($path.$filename);
		$this->owner->$attribute = $filename;
		
		return $this->owner;
	}
	
	public function uploadAllFiles() {
		foreach ($this->getFileAttributes() as $attribute) {
			$this->uploadFile($attribute);
		}
		
		return $this->owner;
	}
	
	public function deleteFile($attribute) {
		if (
			$this->isUploaded($attribute)
			&& file_exists($path = $this->getPath($attribute))
		) {
			unlink($path);
			$this->owner->$attribute = null;
		}
	
		return $this->owner;
	}
	
	public function deleteAllFiles() {
		foreach ($this->getFileAttributes() as $attribute) {
			$this->deleteFile($attribute);
		}
		
		return $this->owner;
	}
	
	public function renameFile($attribute, $newFilename) {
		if (!$this->isUploaded($attribute)) return $this->owner;
		
		$oldPath = $this->getPath($attribute);
		$newPath = $this->getUploadPath($attribute).$newFilename;
		
		rename($oldPath, $newPath);
		$this->owner->$attribute = $newFilename;
		
		return $this->owner;
	}
	
	public function beforeDelete($event) {
		$this->deleteAllFiles();
		parent::beforeDelete($event);
		return true;
	}
	
	public function getFilesize($attribute) {
		if (
			!$this->isUploaded($attribute)
			|| !file_exists($path = $this->getPath($attribute))
		) {
			return null;	
		}
		
		return filesize($path);
	}
	
}