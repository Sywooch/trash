<?php
/**
 * Создает поле "Дата создания" для мастеров
 *
 * @see https://docdoc.megaplan.ru/task/1002401/card/
 */

class m131114_105722_lf_master_cteated extends CDbMigration
{

	/**
	 * Удаляет старое поле и добавляет новое
	 *
	 * @return void
	 */
	public function up()
	{
		$this->dropColumn('lf_master', 'created');
		$this->addColumn('lf_master', 'created', 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP');
	}

	/**
	 * Возвращает поле к старому типу
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropColumn('lf_master', 'created');
		$this->addColumn('lf_master', 'created', 'int');
	}
}