<?php

class CArModTimeBehavior extends CActiveRecordBehavior
{

	/**
	 * Does given class has mod time behavior?
	 *
	 * @param string $class
	 *
	 * @return bool
	 */
	protected function hasModTimeBehavior($class)
	{
		$model = new $class;
		return $model->asa(get_class($this)) !== null;
	}

	/**
	 * Should we update items for given relation?
	 * Parent items only for now.
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	protected function checkRelation($name)
	{
		$relation = $this->owner->getActiveRelation($name);

		return
			in_array(get_class($relation), array('CBelongsToRelation'))
			&& $this->hasModTimeBehavior($relation->className)
			&& $this->owner->{$relation->name};
	}

	/**
	 * Update all given items.
	 *
	 * @param mixed $items
	 */
	protected function updateItems($items)
	{
		$items = is_array($items) ? $items : array($items);
		foreach ($items as $item) {
			$item->save();
		}
	}

	/**
	 * Check and update (if possible) related items.
	 */
	protected function updateRelations()
	{
		foreach ($this->owner->relations() as $name => $relation) {
			if ($this->checkRelation($name)) {
				$this->updateItems($this->owner->$name);
			}
		}
	}

	/**
	 *
	 */
	public function updateModTime()
	{
		if ($this->owner->hasAttribute('mod_time')) {
			$this->owner->mod_time = time();
		}

		return $this->owner;
	}

	public function beforeSave($event)
	{
		$this->updateModTime();
	}

	public function beforeDelete($event)
	{
		$this->updateRelations();
	}

	public function afterSave($event)
	{
		$this->updateRelations();
	}

}