<?php

class CArIdsBehavior extends CActiveRecordBehavior {

	public function getRelationIds($relation) {
		$ids = array();
		foreach ($this->owner->$relation as $item) {
			if (is_object($item) || is_numeric($item)) {
				// if model attributes assigned through $model->attributes relation array will contains ids only
				$ids[] = is_object($item) ? $item->id : intval($item);
			}
		}
		
		return $ids;
	}
	
}