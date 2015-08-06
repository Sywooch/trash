<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 30.06.14
 * Time: 12:49
 */

namespace dfs\docdoc\models;

/**
 * Сдесь будет список предоставляемых услуг в будущем
 *
 * Class ServiceModel
 */
class ServiceModel
{
	/**
	 * @var array
	 */
	static $service_types = [
		self::TYPE_SUCCESSFUL_DOCTOR_REQUEST        => 'Подтвержденная по телефону запись к врачу',
		self::TYPE_SUCCESSFUL_DIAGNOSTICS_MRT_OR_KT => 'Подтвержденная по телефону запись на диагностику (МРТ, КТ)',
		self::TYPE_SUCCESSFUL_DIAGNOSTICS_OTHER     => 'Подтвержденная по телефону запись на диагностику (кроме МРТ, КТ)',
	];

	/**
	 * Запись к врачу
	 */
	const TYPE_SUCCESSFUL_DOCTOR_REQUEST = 1;

	/**
	 * Диагностика, только МРТ и КТ
	 */
	const TYPE_SUCCESSFUL_DIAGNOSTICS_MRT_OR_KT = 2;

	/**
	 * Диагностика, все кроме МРТ и КТ
	 */
	const TYPE_SUCCESSFUL_DIAGNOSTICS_OTHER = 3;
} 
