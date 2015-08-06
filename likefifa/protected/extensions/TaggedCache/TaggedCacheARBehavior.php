<?php

class TaggedCacheARBehavior extends CActiveRecordBehavior {

	public function afterSave() {
		$this->clearRelatedCache();
	}
	
	public function cacheTags() {
		$pk = $this->owner->getPrimaryKey();
		if (is_array($pk)) {
			$pk = implode('_', $pk);
		}
		$tags = array(get_class($this->owner).'_'.$pk);
		if (method_exists($this->owner, 'getCacheTags')) {
			$tags = array_merge($tags, $this->owner->getCacheTags());
		}
		
		foreach ($this->owner->metaData->relations as $name => $relation) {
			if (
				!($relation instanceof CBelongsToRelation) 
				|| !$this->owner->$name 
				|| !$this->owner->$name->asa(__CLASS__)
			) continue;
			
			$tags = array_merge($tags, $this->owner->$name->cacheTags());
		}
		
		return $tags;
	}
	
	public function clearRelatedCache() {
		Yii::app()->cache->clear($this->cacheTags());
	}
	
}