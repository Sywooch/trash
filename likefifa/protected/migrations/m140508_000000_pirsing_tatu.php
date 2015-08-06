<?php

/**
 * m140508_000000_pirsing_tatu class file.
 *
 * Разделение специальностей "мастер по пирсингу и татуировкам"
 *
 * @author   Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @see      https://docdoc.megaplan.ru/task/1003600/card/
 * @package  migrations
 */
class m140508_000000_pirsing_tatu extends CDbMigration
{

	/**
	 * Идентификатор пирсинга
	 *
	 * @var int
	 */
	const PIERCING_ID = 6;

	/**
	 * Идентификатор специальности пирсинга
	 *
	 * @var int
	 */
	const PIERCING_SPEC_ID = 20;

	/**
	 * Идентификатор специальности татуировок
	 *
	 * @var int
	 */
	const TATTOO_SPEC_ID = 21;

	/**
	 * Идентификатор тату
	 *
	 * @var int
	 */
	private $_tattoosId = 7;

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->update(
			"lf_group",
			array(
				"name"         => "мастер по пирсингу",
				"rewrite_name" => "piercing",
				"genitive"     => "мастеров по пирсингу"
			),
			"id = :id",
			array(":id" => self::PIERCING_ID)
		);
		$this->insert(
			"lf_group",
			array(
				"name"         => "мастер по татуировкам",
				"rewrite_name" => "tattoos",
				"genitive"     => "мастеров по татуировкам"
			)
		);

		$this->_setTattooId();

		$this->update(
			"lf_specialization",
			array(
				"group_id" => $this->_tattoosId,
			),
			"id = :id",
			array(":id" => self::TATTOO_SPEC_ID)
		);

		$this->update(
			"lf_group_specialization",
			array(
				"group_id" => $this->_tattoosId,
			),
			"specialization_id = :specialization_id",
			array(":specialization_id" => self::TATTOO_SPEC_ID)
		);

		foreach ($this->_getMasterGroups() as $masterGroup) {
			$master = LfMaster::model()->findByPk($masterGroup->master_id);

			$isPiercing = false;
			$isTattoo = false;

			if ($master->prices) {
				foreach ($master->prices as $price) {
					if ($price->specialization->id == self::TATTOO_SPEC_ID) {
						$isTattoo = true;
					}
					if ($price->specialization->id == self::PIERCING_SPEC_ID) {
						$isPiercing = true;
					}
				}
			}

			if ($isTattoo) {
				$model = new LfMasterGroup;
				$model->master_id = $masterGroup->master_id;
				$model->group_id = $this->_tattoosId;
				$model->save();

				if (!$isPiercing) {
					$masterGroup->delete();
				}
			}
		}
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->_setTattooId();

		$this->update(
			"lf_group",
			array(
				"name"         => "мастер по пирсингу и татуировкам",
				"rewrite_name" => "piercing-tattoos",
				"genitive"     => "мастеров по пирсингу и татуировкам"
			),
			"id = :id",
			array(":id" => self::PIERCING_ID)
		);

		$this->update(
			"lf_specialization",
			array(
				"group_id" => self::PIERCING_ID,
			),
			"id = :id",
			array(":id" => self::TATTOO_SPEC_ID)
		);

		$this->update(
			"lf_group_specialization",
			array(
				"group_id" => self::PIERCING_ID,
			),
			"specialization_id = :specialization_id",
			array(":specialization_id" => self::TATTOO_SPEC_ID)
		);

		$this->delete(
			"lf_group",
			"id = :id",
			array(":id" => $this->_tattoosId)
		);

		$this->delete(
			"lf_master_group",
			"group_id = :group_id",
			array(":group_id" => $this->_tattoosId)
		);
	}

	/**
	 * Устанавливает идентификатор тату
	 *
	 * @return void
	 */
	private function _setTattooId()
	{
		$row = Yii::app()->db->createCommand()
			->select('id')
			->from('lf_group')
			->order('id DESC')
			->queryRow();
		if ($row) {
			$this->_tattoosId = $row["id"];
		}
	}

	/**
	 * Получает модели связей группа-мастер
	 *
	 * @return LfMasterGroup[]
	 */
	private function _getMasterGroups()
	{
		$criteria = new CDbCriteria;
		$criteria->condition = "t.group_id = :group_id";
		$criteria->params[":group_id"] = self::PIERCING_ID;

		return LfMasterGroup::model()->findAll($criteria);
	}
}