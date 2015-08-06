<?php


namespace likefifa\models\forms;

use CActiveDataProvider;
use CDbCriteria;
use CDbExpression;
use dfs\modules\payments\models\PaymentsAccount;
use dfs\modules\payments\models\PaymentsOperations;
use Yii;

class PaymentsOperationsAdminFilter extends PaymentsOperations
{
	/**
	 * Начальная дата для фильтрации
	 *
	 * @var string
	 */
	public $createdFrom;

	/**
	 * Конечная дата для фильтрации
	 *
	 * @var string
	 */
	public $createdTo;

	/**
	 * Сумма выбранных транзакций
	 *
	 * @var integer
	 */
	public $sum;

	public function rules()
	{
		return [
			['createdTo, createdFrom, account_to, account_from, create_date', 'safe'],
		];
	}

	/**
	 * Поиск в списке операций
	 *
	 * @return CActiveDataProvider
	 */
	public function search()
	{
		$criteria = new CDbCriteria;
		if (empty($this->account_from)) {
			$criteria->condition = "t.account_to > :account_to";
			$criteria->params[":account_to"] = PaymentsAccount::MIN_USER_ID;
		} else {
			if ($this->account_from == 1000) {
				$criteria->compare('t.account_from', $this->account_from);
				$criteria->addCondition('t.amount_real > 0 and t.type = :type');
				$criteria->params[':type'] = PaymentsOperations::TYPE_TOP_UP;
			} else {
				if ($this->account_from == PaymentsAccount::SYSTEM_ID) {
					$criteria->compare('t.account_to', $this->account_from);
					$criteria->addCondition('t.type = :type');
					$criteria->params[':type'] = PaymentsOperations::TYPE_COMMISSION;
				}
			}
		}

		if (!empty($this->createdFrom) || !empty($this->createdTo)) {
			$createdFrom = $createdTo = null;
			if (!empty($this->createdFrom)) {
				$createdFrom = date('Y-m-d 00:00:00', strtotime($this->createdFrom));
			}
			if (!empty($this->createdTo)) {
				$createdTo = date('Y-m-d 23:59:59', strtotime($this->createdTo));
			}

			$criteria->addBetweenCondition('t.create_date', $createdFrom, $createdTo);
		}

		$criteria->order = 't.create_date DESC';

		return new CActiveDataProvider(
			$this, array(
				'criteria'   => $criteria,
				'pagination' => array(
					'pageSize' => 50,
				),
				'sort'       => false,
			)
		);
	}

	/**
	 * Возвращает текущее состояние счета LF
	 *
	 * @return integer
	 */
	public static function getRealAmount()
	{
		return Yii::app()->db->createCommand()
			->select(new CDbExpression('sum(amount_real)'))
			->from('payments_operation')
			->where('amount_real > 0 and type = 1 and account_from = 1000')
			->queryScalar();
	}

	/**
	 * Возвращает, сколько было списано с мастеров (с учетом фильтров)
	 *
	 * @return integer
	 */
	public function getFakeAmount()
	{
		$model = new self;
		$model->attributes = $this->attributes;
		$model->createdFrom = $this->createdFrom;
		$model->createdTo = $this->createdTo;
		$model->account_from = PaymentsAccount::SYSTEM_ID;

		$sumCriteria = $model->search()->getCriteria();
		$sumCriteria->select = [new CDbExpression('SUM(t.amount_real) as sum')];
		return PaymentsOperationsAdminFilter::model()->find($sumCriteria)->sum;
	}

	/**
	 * Возвращает, сколько было списано с мастеров за всю историю
	 *
	 * @return integer
	 */
	public static function getTotalFakeAmount()
	{
		return Yii::app()->db->createCommand()
			->select(new CDbExpression('sum(amount_real)'))
			->from('payments_operation')
			->where('type = 2 and account_to = 1')
			->queryScalar();
	}

	/**
	 * @param string $className
	 *
	 * @return PaymentsOperationsAdminFilter
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
} 