<?php

/**
 * Добавляет колонки phone_numeric для салонов и заявок
 * Class m140725_085226_create_numeric_phones
 */
class m140725_085226_create_numeric_phones extends CDbMigration
{
	public function safeUp()
	{
		$this->addColumn('lf_salons', 'phone_numeric', 'VARCHAR(16) NULL DEFAULT NULL');
		$this->addColumn('lf_appointment', 'phone_numeric', 'VARCHAR(16) NULL DEFAULT NULL');

		// Обновляем телефоны у салонов
		$salons = Yii::app()->db->createCommand()
			->from('lf_salons')
			->queryAll();
		foreach ($salons as $salon) {
			if (empty($salon['phone'])) {
				continue;
			}
			Yii::app()->db->createCommand()
				->update(
					'lf_salons',
					[
						'phone_numeric' => preg_replace('/\D+/', '', $salon['phone']),
					],
					'id = :id',
					[':id' => $salon['id']]
				);
		}

		// Обновляем телефоны у заявок
		$appointments = Yii::app()->db->createCommand()
			->from('lf_appointment')
			->queryAll();
		foreach ($appointments as $a) {
			if (empty($a['phone'])) {
				continue;
			}
			Yii::app()->db->createCommand()
				->update(
					'lf_appointment',
					[
						'phone_numeric' => preg_replace('/\D+/', '', $a['phone']),
					],
					'id = :id',
					[':id' => $a['id']]
				);
		}
	}

	public function safeDown()
	{
		$this->dropColumn('lf_salons', 'phone_numeric');
		$this->dropColumn('lf_appointment', 'phone_numeric');
	}
}