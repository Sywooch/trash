<?php

namespace models;

use components\Model;

/**
 * Class CommentModel
 *
 * Model for table "comments"
 *
 * @package models
 *
 * @method CommentModel byId
 * @method CommentModel with
 * @method CommentModel find
 */
class CommentModel extends Model
{

	/**
	 * News ID
	 *
	 * @var integer
	 */
	public $news_id;

	/**
	 * Date
	 *
	 * @var string
	 */
	public $date;

	/**
	 * Name
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Comment
	 *
	 * @var string
	 */
	public $text;

	/**
	 * Model News
	 *
	 * @var NewsModel|null
	 */
	public $newsModel;

	/**
	 * Gets table name
	 *
	 * @return string
	 */
	public function tableName()
	{
		return "comments";
	}

	/**
	 * Rules for validate
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			"news_id" => ["required"],
			"name"    => ["required", "max" => 255],
			"text"    => ["required"],
			"date"    => [],
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
			"name" => "Name",
			"text" => "Text",
		];
	}

	/**
	 * Relations
	 *
	 * @return array
	 */
	public function relations()
	{
		return [
			"newsModel" => ['models\NewsModel', "news_id"]
		];
	}

	/**
	 * Gets model
	 *
	 * @param string $className class name
	 *
	 * @return CommentModel
	 */
	public static function model($className = __CLASS__)
	{
		return new $className;
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
	 * Select by news ID
	 *
	 * @param integer $newsId news ID
	 *
	 * @return CommentModel
	 */
	public function byNewsId($newsId)
	{
		$this->db->addCondition("t.news_id = :news_id");
		$this->db->params["news_id"] = $newsId;

		return $this;
	}

	/**
	 * Order by date DESC
	 *
	 * @return CommentModel
	 */
	public function ordered()
	{
		$this->db->order = "t.date DESC";

		return $this;
	}
}