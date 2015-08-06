<?php

namespace dfs\tests\docdoc\api\feed;

use dfs\docdoc\api\feed\YmlFeed2;
use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\DoctorModel;
use Yii;

/**
 * Class YmlFeedTest
 *
 * @package dfs\tests\docdoc\api\rest
 */
class YmlFeed2Test extends YmlFeedTest
{

	/**
	 * Тест <categories>
	 *
	 * @dataProvider сategoriesData
	 *
	 * @param int    $city_id
	 * @param string $result
	 */
	public function testCategories($city_id, $result)
	{
		$feed = new YmlFeed2($city_id);
		$categories = $feed->categories();
		$document = $feed->getDocument();
		$document->appendChild($categories);
		$this->assertEquals(
			$result,
			$document->C14N(true, false)
		);
	}

	/**
	 * DataProvider для testCategories
	 *
	 * @return array
	 */
	public function сategoriesData()
	{
		return [
			[
				1,
				'<categories><category id="1000000">Запись в клинику</category><category id="2000000">Запись на прием к врачу</category><category id="1001" parentId="1000000">Акушер-гинеколог</category><category id="1003" parentId="1000000">Акушер</category></categories>',
			],
			[
				2,
				'<categories><category id="1000000">Запись в клинику</category><category id="2000000">Запись на прием к врачу</category></categories>',
			],
		];

	}

	/**
	 * Тест <currencies>
	 */
	public function testCurrencies()
	{
		$feed = new YmlFeed2(1);
		$categories = $feed->currencies();
		$document = $feed->getDocument();
		$document->appendChild($categories);
		$this->assertEquals(
			'<currencies><currency id="RUR" plus="0" rate="1"></currency></currencies>',
			$document->C14N(true, false)
		);

	}

	/**
	 * Тест <offer> для записи в клинику
	 */
	public function testClinicOffer()
	{
		$feed = new YmlFeed2(1);
		$document = $feed->getDocument();
		$clinic = ClinicModel::model()->findByPk(1);
		$clinicOffer = $feed->clinicOffer($clinic);
		$document->appendChild($clinicOffer);
		$this->assertEquals(
			'<offer available="true" id="1000001"><url>http://' . Yii::app()->params['hosts']['front'] . '/clinic/clinica_11</url><price>800</price><currencyId>RUR</currencyId><categoryId>1000000</categoryId><picture>http://' . Yii::app()->params['hosts']['front'] . '/upload/kliniki/logo/1.png</picture><name>Клиника №1</name><description>Многопрофильный медицинский центр, специализирующийся на проведении диагностического обследования взрослых и детей от 14 лет. Клиника расположена в шаговой близости от метро Люблино (5-10 мин.) Прием происходит по предварительной записи по многоканальному телефону +7 (495) 988-01-64.</description><param name="Тип заявки">Запись в клинику</param><param name="ID типа заявки">1000000</param></offer>',
			$document->C14N(true, false)
		);
	}

	/**
	 * Тест <offer> для записи к врачу
	 */
	public function testDoctorOffer()
	{
		$feed = new YmlFeed2(1);
		$document = $feed->getDocument();
		$doctor = DoctorModel::model()->findByPk(2);
		foreach ($feed->doctorOffer($doctor) as $offer) {
			$document->appendChild($offer);
		}

		$this->assertEquals(
			'<offer available="true" id="2000002"><url>http://' . Yii::app()->params['hosts']['front'] . '/doctor/Nikolaev_Nikolai</url><price>2300</price><currencyId>RUR</currencyId><picture>https://' . Yii::app()->params['hosts']['front'] . '/img/doctorsNew/2_small.jpg</picture><name>Николаев Николай Николаевич</name><description>Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.</description><categoryId>1001</categoryId><typePrefix>Акушер-гинеколог</typePrefix><vendor>Клиника №1</vendor><param name="Рейтинг">9.5</param><param name="Отзывы">http://' . Yii::app()->params['hosts']['front'] . '/doctor/Nikolaev_Nikolai#reviews</param><param name="Отзыв_19">Хочу выразить благодарность врачу. Уже после 2-го сеанса ноги стали приходить в норму. Роман настоящий профессионал своего дела! Спасибо!</param><param name="Отзыв_20">Обратился  с варикозной болезнью вен, которой страдаю уже 15 лет. Все эти годы не решался на операцию, так как к нашей медицине отношусь скептически. На приеме доктор мне грамотно и понятно объяснил необходимость операции и возможные риски, сделал дуплексное сканирование вен и сразу же подобрал программу лечения. Операция прошла безболезненно, уже на второй день был дома, шрамов не осталось. Могу с уверенность сказать, Роман - хирург от Бога.</param><param name="Тип заявки">Запись на прием к врачу</param><param name="ID типа заявки">2000000</param></offer><offer available="true" id="2000002"><url>http://' . Yii::app()->params['hosts']['front'] . '/doctor/Nikolaev_Nikolai</url><price>2300</price><currencyId>RUR</currencyId><picture>https://' . Yii::app()->params['hosts']['front'] . '/img/doctorsNew/2_small.jpg</picture><name>Николаев Николай Николаевич</name><description>Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.</description><categoryId>1001</categoryId><typePrefix>Акушер-гинеколог</typePrefix><vendor>Клиника №2</vendor><param name="Рейтинг">9.5</param><param name="Отзывы">http://' . Yii::app()->params['hosts']['front'] . '/doctor/Nikolaev_Nikolai#reviews</param><param name="Отзыв_19">Хочу выразить благодарность врачу. Уже после 2-го сеанса ноги стали приходить в норму. Роман настоящий профессионал своего дела! Спасибо!</param><param name="Отзыв_20">Обратился  с варикозной болезнью вен, которой страдаю уже 15 лет. Все эти годы не решался на операцию, так как к нашей медицине отношусь скептически. На приеме доктор мне грамотно и понятно объяснил необходимость операции и возможные риски, сделал дуплексное сканирование вен и сразу же подобрал программу лечения. Операция прошла безболезненно, уже на второй день был дома, шрамов не осталось. Могу с уверенность сказать, Роман - хирург от Бога.</param><param name="Тип заявки">Запись на прием к врачу</param><param name="ID типа заявки">2000000</param></offer><offer available="true" id="2000002"><url>http://' . Yii::app()->params['hosts']['front'] . '/doctor/Nikolaev_Nikolai</url><price>2300</price><currencyId>RUR</currencyId><picture>https://' . Yii::app()->params['hosts']['front'] . '/img/doctorsNew/2_small.jpg</picture><name>Николаев Николай Николаевич</name><description>Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.</description><categoryId>1001</categoryId><typePrefix>Акушер-гинеколог</typePrefix><vendor>Диагностический центр №3</vendor><param name="Рейтинг">9.5</param><param name="Отзывы">http://' . Yii::app()->params['hosts']['front'] . '/doctor/Nikolaev_Nikolai#reviews</param><param name="Отзыв_19">Хочу выразить благодарность врачу. Уже после 2-го сеанса ноги стали приходить в норму. Роман настоящий профессионал своего дела! Спасибо!</param><param name="Отзыв_20">Обратился  с варикозной болезнью вен, которой страдаю уже 15 лет. Все эти годы не решался на операцию, так как к нашей медицине отношусь скептически. На приеме доктор мне грамотно и понятно объяснил необходимость операции и возможные риски, сделал дуплексное сканирование вен и сразу же подобрал программу лечения. Операция прошла безболезненно, уже на второй день был дома, шрамов не осталось. Могу с уверенность сказать, Роман - хирург от Бога.</param><param name="Тип заявки">Запись на прием к врачу</param><param name="ID типа заявки">2000000</param></offer>',
			$document->C14N(true, false)
		);
	}

}
