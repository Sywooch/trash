<?php

class TagDependency implements ICacheDependency {
	
	protected $tags = array();
	protected $timestamp = null;
	
	public function __construct($tags) {
		$tags = is_array($tags) ? $tags : array($tags);
	}
	
	public function evaluateDependency() {
		$this->timestamp = time();
	}
	
	public function getHasChanged() {
		$tags = array_map(function($tag) {
			return TaggedCacheBehavior::PREFIX.$tag;
		}, $this->tags);
		
		$values = Yii::app()->cache->mget($tags);
		foreach ($values as $value) {
			if ((int) $value > $this->timestamp) return true;
		}
		
		return false;
	}
	
}