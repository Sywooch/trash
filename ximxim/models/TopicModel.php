<?php

namespace models;

use components\Model;

/**
 * Class TopicModel
 *
 * Model for table "topics"
 *
 * @package models
 *
 * @method TopicModel byId
 * @method TopicModel find
 */
class TopicModel extends Model
{

	/**
	 * Title
	 *
	 * @var string
	 */
	public $title = "";

	/**
	 * Parent ID
	 *
	 * @var int
	 */
	public $parent_id = 0;

	/**
	 * Gets table name
	 *
	 * @return string
	 */
	public function tableName()
	{
		return "topics";
	}

	/**
	 * Rules for validate
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			"title"     => ["required", "max" => 255],
			"parent_id" => [],
		];
	}

	/**
	 * Fields labels
	 *
	 * @return array
	 */
	public function labels()
	{
		return [
			"title" => "Title",
		];
	}

	/**
	 * Relations
	 *
	 * @return array
	 */
	public function relations()
	{
		return [];
	}

	/**
	 * Gets model
	 *
	 * @param string $className class name
	 *
	 * @return TopicModel
	 */
	public static function model($className = __CLASS__)
	{
		return new $className;
	}

	/**
	 * Order by parent ID
	 *
	 * @return TopicModel
	 */
	public function orderByParentId()
	{
		$this->db->order = "t.parent_id";

		return $this;
	}

	/**
	 * Find by parent ID
	 *
	 * @param integer $parentId parent  ID
	 *
	 * @return TopicModel
	 */
	public function byParentId($parentId)
	{
		$this->db->addCondition("t.parent_id = :parent_id");
		$this->db->params["parent_id"] = $parentId;

		return $this;
	}

	/**
	 * Runs before delete model
	 * Deletes news for this topic
	 *
	 * @return bool
	 */
	protected function beforeDelete()
	{
		$childTopics = TopicModel::model()->byParentId($this->id)->findAll();
		foreach ($childTopics as $topic) {
			if (!$topic->delete(false)) {
				return false;
			}
		}

		$newsList = NewsModel::model()->byTopicId($this->id)->findAll();
		foreach ($newsList as $news) {
			if (!$news->delete(false)) {
				return false;
			}
		}

		return parent::beforeDelete();
	}
}