<?php


namespace likefifa\models\forms;

use CActiveDataProvider;
use CDbCriteria;
use LfSalon;

/**
 * Class LfSalonAdminFilter
 * Вспомогательная модель для фильтрации салонов в админке
 *
 * @package likefifa\models\forms
 */
class LfSalonAdminFilter extends LfSalon
{
	public static $statusList = [
		-1 => 'Не указан',
		1  => 'активен',
		0  => 'не активен',
	];

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('is_published', $this->is_published);
		$criteria->compare('phone_numeric', $this->phone_numeric, true);
		$criteria->compare('email', $this->email, true);

		return new CActiveDataProvider(new LfSalonAdminFilter, [
			'criteria'   => $criteria,
			"pagination" => [
				"pageSize" => 50,
			],
			'sort'       => [
				'attributes'   => [
					'created',
					'name',
					'rating',
					'rating_inner' => [
						'asc'   => '(rating_inner IS NULL), rating_inner ASC',
						'desc'  => '(rating_inner IS NULL), rating_inner DESC',
						'label' => 'Рейтинг 2'
					],
				],
				'defaultOrder' => 't.id DESC',
			],
		]);
	}

	/**
	 * Возвращает текущий статус салона
	 *
	 * @return string
	 */
	public function getStatus()
	{
		if ($this->is_published === null) {
			return '<span class="label label-default">не определен</div>';
		} else {
			if (!$this->is_published) {
				return '<span class="label label-default">не активен</div>';
			} else {
				return '<span class="label label-success">активен</span>';
			}
		}
	}

	/**
	 * @param string $className
	 *
	 * @return LfSalonAdminFilter
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
} 