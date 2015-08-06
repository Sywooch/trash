<?php

class CachingActiveDataProvider extends CActiveDataProvider {
	
	public $expire = 600;
	
	/**
	 * @var CActiveDataProvider
	 */
	protected $dataProvider = null;
	
	public function __construct(CActiveDataProvider $dataProvider) {
		$this->dataProvider = $dataProvider;
	}
	
	protected function getCacheId() {
		$id = $this->modelClass.'_'.md5(json_encode($this->getCriteria()->toArray()));
		if ($pagination = $this->getPagination()) {
			$id .= '_'.$pagination->getLimit().'_'.$pagination->getOffset();
		}
		return $id;
	}
	
	protected function fetchData() {
		$cacheId = $this->getCacheId();
		if (!($data = Yii::app()->cache->get($cacheId))) {
			$data = parent::fetchData();
			if ($this->model->asa('TaggingBehavior')) {
				$tags = array();
				foreach ($data as $item) {
					$tags[] = $item->getTags();
				}
				$tags = array_unique($tags);
				$dependency = new TagDependency($tags);
			}
			else {
				$dependency = null;
			}
			
			Yii::app()->cache->set($cacheId, $data, $this->expire, $dependency);
		}
		
		return $data;
	}
	
}