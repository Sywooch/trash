<?php

class TaggedCacheBehavior extends CBehavior {
	
	const PREFIX = '__tag__';
	
	public function clear($tags) {
		$time = time();
		foreach ($tags as $tag) {
			$this->owner->set(self::PREFIX.$tag, $time);
		}
	}
	
}