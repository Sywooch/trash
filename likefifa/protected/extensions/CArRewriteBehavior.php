<?php

class CArRewriteBehavior extends CActiveRecordBehavior
{
	public function getRewriteName()
	{
		if (
			!$this->owner->rewrite_name
			&& !$this->owner->isNewRecord
			&& method_exists($this->owner, 'generateRewriteName')
			&& ($this->owner->rewrite_name = $this->owner->generateRewriteName())
		) {
			$this->owner->save();
		}

		return $this->owner->rewrite_name ? : $this->owner->id;
	}

	public function beforeSave($event)
	{
		if (
			$this->owner->isNewRecord
			&& !$this->owner->rewrite_name
			&& method_exists($this->owner, 'generateRewriteName')
		) {
			$this->owner->rewrite_name = $this->owner->generateRewriteName();
		}
	}

	/**
	 * @param $rewriteName
	 *
	 * @return CActiveRecord
	 */
	public function findByRewrite($rewriteName)
	{
		return $this->owner->find(
			is_numeric($rewriteName) ? 't.id = :rewrite' : 't.rewrite_name = :rewrite',
			array('rewrite' => $rewriteName)
		);
	}

	/**
	 * @param $rewriteNameArray
	 *
	 * @return CActiveRecord[]
	 */
	public function findAllByRewrite($rewriteNameArray)
	{
		if (!$rewriteNameArray) {
			return array();
		}

		$ids = array();
		$names = array();

		foreach ($rewriteNameArray as $id) {
			if (is_numeric($id)) {
				$ids[] = $id;
			} else {
				$names[] = $id;
			}
		}

		return array_merge(
			$ids ? $this->owner->findAllByAttributes(array('id' => $ids,)) : array(),
			$names ? $this->owner->findAllByAttributes(array('rewrite_name' => $names,)) : array()
		);
	}

}