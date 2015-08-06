<?php

namespace models;

use components\Model;

/**
 * Class NewsModel
 *
 * Model for table "news"
 *
 * @package models
 */
class NewsModel extends Model
{

	/**
	 * Topic ID
	 *
	 * @var integer
	 */
	public $topic_id;

	/**
	 * Date
	 *
	 * @var string
	 */
	public $date;

	/**
	 * Title
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Text
	 *
	 * @var string
	 */
	public $text;

	/**
	 * Gets table name
	 *
	 * @return string
	 */
	public function tableName()
	{
		return "news";
	}

	/**
	 * Rules for validate
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			"topic_id" => ["required"],
			"title"    => ["required", "max" => 255],
			"text"     => [],
			"date"     => [],
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
			"text"  => "Text",
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
	 * @return NewsModel
	 */
	public static function model($className = __CLASS__)
	{
		return new $className;
	}

	/**
	 * Select by topic ID
	 *
	 * @param integer $topicId topic ID
	 *
	 * @return NewsModel
	 */
	public function byTopicId($topicId)
	{
		$this->db->addCondition("t.topic_id = :topic_id");
		$this->db->params["topic_id"] = $topicId;

		return $this;
	}

	/**
	 * Gets date
	 *
	 * @return string
	 */
	public function getDate()
	{
		return date("d.m.Y H:i", strtotime($this->date));
	}

	/**
	 * Runs before delete model
	 * Deletes comments for this news
	 *
	 * @return bool
	 */
	protected function beforeDelete()
	{
		$comments = CommentModel::model()->byNewsId($this->id)->findAll();
		foreach ($comments as $comment) {
			if (!$comment->delete(false)) {
				return false;
			}
		}

		return parent::beforeDelete();
	}
}