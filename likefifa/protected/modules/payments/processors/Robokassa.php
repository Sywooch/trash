<?php
namespace dfs\modules\payments\processors;

use dfs\modules\payments\base\Processor;
use dfs\modules\payments\models\PaymentsInvoice;
use RuntimeException;

/**
 * Class Robokassa
 *
 * @author Aleksey Parshukov <parshukovag@gmail.c0om>
 * @date 26.09.2013
 *
 * @see https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=1310733
 * @see http://robokassa.ru/ru/Doc/Ru/Interface.aspx#222
 *
 * @package dfs\modules\payments
 *
 *
sMerchantLogin
- login магазина в обменном пункте(обязательный параметр)
nOutSum
- требуемая к получению сумма (обязательный параметр). Формат представления числа - разделитель точка.
Сумма должна быть указана в той валюте, которая была указана при регистрации магазина, как валюта текущего баланса Продавца или как электронная валюта, в которой будет получать средства Продавец.

Например, если стоимость товаров у вас на сайте указана в долларах, а валюта Продавца рубли, то при выставлении счёта к оплате вам необходимо указывать уже пересчитанную сумму из долларов в рубли.
nInvId
- номер счета в магазине (должен быть уникальным для магазина). Может принимать значения от 1 до 2147483647 (2^31-1). Если содержит пустое значение, вовсе не указан, либо равен "0", то при создании операции ей будет автоматически присвоен уникальный номер счета. Рекомендуется использовать данную возможность только в очень простых магазинах, где не требуется какого-либо контроля.
sInvDesc
- описание покупки, можно использовать только символы английского или русского алфавита, цифры и знаки препинания. Максимальная длина 100 символов.
sSignatureValue
- контрольная сумма MD5(обязательный параметр) - строка представляющая собой 32-разрядное число в 16-ричной форме и любом регистре (всего 32 символа 0-9, A-F). Формируется по строке, содержащей следующие параметры, разделенные ':', с добавлением sMerchantPass1 - (устанавливается через интерфейс администрирования):
sMerchantLogin:nOutSum:nInvId:sMerchantPass1[:пользовательские параметры, в отсортированном алфавитном порядке]

При инициализации оплаты, вы можете передать дополнительные параметры, которые необходимы для работы вашего магазина. Переданные дополнительные параметры будут возвращены скриптам магазина по Result Url, Success Url и Fail Url.
Наименование дополнительных параметров должно ОБЯЗАТЕЛЬНО начинаться с "SHP" в любом регистре.
Например: Shp_item, SHP_1, ShpEmail, shp_oplata, ShpClientId и т.д.

При инициализации оплаты, каждый из передаваемых дополнительных параметров, ОБЯЗАТЕЛЬНО должен быть включён в подсчёт контрольной суммы (MD5).
Например, если переданы пользовательские параметры shpb=xxx и shpa=yyy, то подпись формируется из строки:
sMerchantLogin:nOutSum:nInvId:sMerchantPass1:shpa=yyy:shpb=xxx

При проверке контрольной суммы (MD5) в скриптах магазина по Result Url, Success Url и Fail Url также необходимо учитывать полученные дополнительные параметры при подсчёте контрольной суммы (MD5). См. соответствующие разделы документации.
sIncCurrLabel
- предлагаемая валюта платежа. Пользователь может изменить ее в процессе оплаты.

Доступные значения для параметра IncCurrLabel - метки валют.
Cпособ получения этой информации описан в разделе: XML интерфейсы. Интерфейс получения списка валют.
Однако он доступен только активным мерчантам (продавцам).
sEmail
- e-mail пользователя. Пользователь может изменить его в процессе оплаты.
sCulture
- опционально, язык общения с клиентом. Значения: en, ru. Если не установлен - берется язык региональных установок браузера.
 *
 */
class Robokassa extends Processor
{
	/**
	 * Ссылка по умолчанию для отправки запросов
	 *
	 * @var string
	 */
	const DEFAULT_PREFIX = 'https://auth.robokassa.ru/Merchant/Index.aspx';

	/**
	 * Создать ссылку на мёрчента
	 *
	 * @param PaymentsInvoice $invoice
	 *
	 * @return string
	 */
	public function buildMerchantUrl(PaymentsInvoice $invoice)
	{
		$prefix = $this->getConfigParam('url', self::DEFAULT_PREFIX);

		if (strlen($invoice->message)>100) {
			trigger_error(E_USER_WARNING, "Message text to long");
		}

		$params=array(
			'MrchLogin'=>$this->getConfigParam('login'),
			'OutSum'=>$invoice->getAmount(),
			//'InvId'=>'nInvId', // Мы не используем идентификатор счёта, потому что у нас нету такого
			'Desc'=>$invoice->message,
			'Email'=>$invoice->email,
			'shp_invoice'=>$invoice->id,
			//'shpb'=>'xxx',...-пользовательские_параметры_начинающиеся_с_SHP_в_сумме_до_2048_знаков]
		);

		$params['SignatureValue']=$this->buildSignature($params);
		return $prefix.'?'.http_build_query($params);
	}

	/**
	 *
	 * Подпись типа
	 * sMerchantLogin:nOutSum:nInvId:sMerchantPass1:shpa=yyy:shpb=xxx
	 *
	 * @param array $params
	 *
	 * @return string
	 */
	public function buildSignature(array $params)
	{
		$signature=array();
		$signature[]=$this->getConfigParam('login');
		$signature[]=$params['OutSum'];
		$signature[]=isset($params['nInvId']) ? $params['nInvId'] : '';
		$signature[]=$this->getConfigParam('password1');

		return md5(join(':', array_merge($signature, $this->filterUserParams($params))));
	}

	/**
	 * Отбирает пользовательские параметры
	 *
	 * @param array $params
	 * @return string
	 */
	private function filterUserParams(array $params)
	{
		$signature=array();
		foreach($params as $key=>$value)
		{
			$lowKey=strtolower($key);
			if (strpos($lowKey, 'shp')===0)
			{
				$add[$key]=$value;
			}
		}
		ksort($add);
		foreach($add as $key=>$value)
		{
			$signature[]="{$key}={$value}";
		}

		return $signature;
	}

	/**
	 * nOutSum:nInvId:sMerchantPass1[:пользовательские параметры, в отсортированном порядке]
	 */
	public function buildResultSignature(array $params)
	{
		$signature=array();
		$signature[]=$params['OutSum'];
		$signature[]=$params['InvId'];
		$signature[]=$this->getConfigParam('password2');

		return md5(join(':', array_merge($signature, $this->filterUserParams($params))));
	}

	/**
	 * Валидируем запрос
	 *
	 * @param array $result
	 *
	 * @return bool
	 */
	public function validateSignature(array $result)
	{
		return $this->buildResultSignature($result)===strtolower($result['SignatureValue']);
	}

	/**
	 * Уведомление об успешной оплате
	 *
	 * @param array $result
	 * @throws \RuntimeException
	 * @return string
	 */
	public function result(array $result)
	{
		// Наличие полей
		if (
			!isset($result['OutSum'])
			|| !isset($result['InvId'])
			|| !isset($result['shp_invoice'])
			|| !isset($result['SignatureValue'])
		)
		{
			throw new RuntimeException("Invalid request");
		}

		// Проверка подписи
		if (!$this->validateSignature($result))
		{
			throw new RuntimeException("Invalid signature");
		}

		// Проверка инвойса
		$invoice=PaymentsInvoice::model()->findByPk($result['shp_invoice']);
		if (!$invoice)
		{
			throw new RuntimeException("Invoice not found");
		}

		// проверка правильного процессора
		$invoice=PaymentsInvoice::model()->findByPk($result['shp_invoice']);
		if ($invoice->processor->key!=='robokassa')
		{
			throw new RuntimeException("Invalid processor");
		}

		if (!$invoice->canClose()) {
			throw new RuntimeException("Already paid");
		}

		$invoice->close();
		return "OK{$result['InvId']}";
	}

}