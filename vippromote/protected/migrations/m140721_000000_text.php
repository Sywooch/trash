<?php

class m140721_000000_text extends CDbMigration
{

	/**
	 * Применяет миграцию в трансакции
	 *
	 * @return bool
	 */
	public function safeUp()
	{
		$this->createTable(
			"text",
			array(
				"id"    => "pk",
				"title" => "VARCHAR(255) NOT NULL",
				"text"  => "TEXT NOT NULL",
			),
			"ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci"
		);

		$this->insert(
			"text",
			array(
				"id"    => 1,
				"title" => "Главная",
				"text"  => $this->_getRulesText(),
			)
		);

		$this->insert(
			"text",
			array(
				"id"    => 2,
				"title" => "Правила",
				"text"  => $this->_getRulesText(),
			)
		);

		$this->insert(
			"text",
			array(
				"id"    => 3,
				"title" => "Реклама",
				"text"  => $this->_getAdvertisementText(),
			)
		);

		$this->insert(
			"text",
			array(
				"id"    => 4,
				"title" => "Контактная информация",
				"text"  => $this->_getContactsText(),
			)
		);
	}

	/**
	 * Откатывает миграцию в трансакции
	 *
	 * @return bool
	 */
	public function safeDown()
	{
		$this->dropTable("text");
	}

	private function _getContactsText()
	{
		return "
			<p><strong>Тел. +7(920)891-96-75 Александр</strong></p>
			<p><strong>Техподдержка скайп: alexhodackov</strong></p>
		";
	}

	private function _getAdvertisementText()
	{
		return '
			<p>Раздел находится в стадии оформления</p>
			<p>Пока все вопросы можно уточнить :</p>
			<p><strong>Тел. +7(920)891-96-75 Александр</strong></p>
			<p><strong>Техподдержка скайп: alexhodackov</strong></p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
		';
	}

	private function _getRulesText()
	{
		return '

<p>Наша компания предлагает вам стать участником нашей целевой аудитории- промоутером. Участие в компании добровольное нет никаких обязательных норм, заданий, штрафов и прогулов.Вы сами решаете насколько принимать участие, но вы должны понимать что величина вознаграждений зависит от дохода с рекламы (просмотры, переходы и прочее)и роста целевой аудитории новых промоутеров!</p>

<p><strong>У нас 2 вида вознаграждений</strong></p>

<p><strong>№1</strong> часть (доля) от доходов с рекламы - доход полученный с рекламы делится между промоутерами</p>

<p><strong>№2</strong>  за привлечениеновых промоутеров (партнерский бонус)</p>

<p style="text-align: center;"><strong>Подробнее о вознаграждениях</strong></p>

<p>Вознаграждения получают только участники-промоутеры, которые после регистрации оплатили ежегодный членский взнос в размере 100 долларов.</p>

<p>1. Выплачивается раз в месяц  15-20 числа  размер определяется по итогам отчетного месяца</p>

<p>2. Начисляется в личном кабинете автоматически по системе тринар</p>

<p>
Далее как работает тринар это система автоматического заполнения уровней участниками ограниченная количеством участников на каждом уровне. Приглашения в системе не обязательны действует система переливов, но в вашем личном кабинете есть ваша личная ссылка для приглашения которая поможет вам быстрее и больше зарабатывать.Ниже на картинке показано как работает система перелива если у вас уже заполнен первый уровеньто приглашая новых участников система ставит их вашим промоутерам тем самым вы не только сами зарабатываете но и помогаете зарабатывать своим партнерам
</p>

<p style="text-align: center;">
	<img src="/images/image3.jpg" />
</p>

<p style="text-align: center;"><strong>Таблица расчета по 2 виду вознаграждения за год на одного промоутера.</strong></p>

<table border="1" cellpadding="10" cellspacing="0" width="100%">
	<tbody>
	<tr>
		<td>
	Уровень
		</td>
		<td>
	Процент %
		</td>
		<td>
	Количество человек
	</td>
		<td>
	Сумма на вывод $
		</td>
	</tr>
	<tr>
		<td>
	1
		</td>
		<td>
	10
		</td>
		<td>
	3
		</td>
		<td>
	30
	</td>
	</tr>
	<tr>
		<td>
	2
		</td>
		<td>
	5
		</td>
		<td>
	9
		</td>
		<td>
	45
	</td>
	</tr>
	<tr>
		<td>
	3
		</td>
		<td>
	5
		</td>
		<td>
	27
		</td>
		<td>
	135
	</td>
	</tr>
	<tr>
		<td>
	4
		</td>
		<td>
	5
		</td>
		<td>
	81
		</td>
		<td>
	405
	</td>
	</tr>
	<tr>
		<td>
	5
		</td>
		<td>
	5
		</td>
		<td>
	243
		</td>
		<td>
	1215
	</td>
	</tr>
	<tr>
		<td>
	6
		</td>
		<td>
	5
		</td>
		<td>
	729
		</td>
		<td>
	3645
	</td>
	</tr>
	<tr>
		<td>
	7
		</td>
		<td>
	5
		</td>
		<td>
	2187
		</td>
		<td>
	10935
	</td>
	</tr>
	<tr>
		<td>
	8
		</td>
		<td>
	5
		</td>
		<td>
	6561
		</td>
		<td>
	32805
	</td>
	</tr>
	<tr>
		<td>
	9
		</td>
		<td>
	5
		</td>
		<td>
	19683
		</td>
		<td>
	98415
	</td>
	</tr>
	<tr>
		<td>
			<strong>Итог</strong>
		</td>
		<td>
			<strong>50</strong>
		</td>
		<td>
			<strong>29523</strong>
		</td>
		<td>
			<strong>147630</strong>
		</td>
	</tr>
	</tbody>
</table>

<p>Спасибо за внимание! По всем вопросам обращайтесь в скайп администрации: alexhodackov .</p>
	';
	}
}