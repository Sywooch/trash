<?php
namespace dfs\modules\likes\commands;

use dfs\common\components\console\Command;
use Yii;

/**
 * Class AppointmentsCommand
 * Ведет подсчет лайков по ссылке
 *
 * @author Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @date   21.11.2013
 *
 * @see    https://docdoc.megaplan.ru/task/1002382/card/
 */
abstract class LikesCommand extends Command
{

	/**
	 * Считывать ли лайки из Вконтакте
	 *
	 * @var bool
	 */
	protected $isVk = true;

	/**
	 * Считывать ли лайки из Facebook
	 *
	 * @var bool
	 */
	protected $isFb = true;

	/**
	 * Считывать ли лайки из Twitter
	 *
	 * @var bool
	 */
	protected $isTwitt = true;

	/**
	 * Считывать ли лайки из Google +1
	 *
	 * @var bool
	 */
	protected $isPlusOnes = true;

	/**
	 * Получает количество всех лайков
	 *
	 * @param string $url адрес страницы
	 *
	 * @return int
	 */
	protected function getAllLikes($url)
	{
		$this->log("Get likes for {$url}");

		$allLikes = 0;

		$messageLikes = "";

		if ($this->isVk) {
			$vkLikes = $this->getVkLikes(Yii::app()->params["vk"]["apiId"], $url);
			$allLikes += $vkLikes;
			$messageLikes .= "VK: {$vkLikes}; ";
		}

		if ($this->isFb) {
			$fbLikes = $this->getFbLikes($url);
			$allLikes += $fbLikes;
			$messageLikes .= "FaceBook: {$fbLikes}; ";
		}

		if ($this->isTwitt) {
			$twitterLikes = $this->getTwittLikes($url);
			$allLikes += $twitterLikes;
			$messageLikes .= "Twitter: {$twitterLikes}; ";
		}

		if ($this->isPlusOnes) {
			$googleLikes = $this->getPlusOnes($url);
			$allLikes += $googleLikes;
			$messageLikes .= "Google: {$googleLikes}; ";
		}

		$messageLikes .= "All: {$allLikes}; ";
		$this->log("{$messageLikes} \n");

		return $allLikes;
	}

	/**
	 * Получает количество лайков Вконтакте
	 *
	 * @param int    $appId    идентификатор пользователя
	 * @param string $url      адрес страницы
	 *
	 * @return int
	 */
	private function getVkLikes($appId, $url)
	{
		$curl = curl_init();
		$url =
			"https://api.vkontakte.ru/method/likes.getList?type=sitepage&owner_id={$appId}&format=json&page_url={$url}";
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$curlResults = curl_exec($curl);
		curl_close($curl);
		$res = json_decode($curlResults);
		if ($res) {
			if (!empty($res->response)) {
				return intval($res->response->count);
			}
		}
		return 0;
	}

	/**
	 * Получает количество лайков Facebook
	 *
	 * @param string $url адрес страницы
	 *
	 * @return int
	 */
	private function getFbLikes($url)
	{
		$curl = curl_init();
		$url = "http://api.facebook.com/restserver.php?method=links.getStats&format=json&urls={$url}";
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$curlResults = curl_exec($curl);
		curl_close($curl);
		$res = json_decode($curlResults);
		if ($res && !is_object($res)) {
			if (is_object($res[0])) {
				return $res[0]->like_count;
			}
		}
		return 0;
	}

	/**
	 * Получает количество лайков Twitter
	 *
	 * @param string $url адрес страницы
	 *
	 * @return int
	 */
	function getTwittLikes($url)
	{
		$curl = curl_init();
		$url = "http://urls.api.twitter.com/1/urls/count.json?url={$url}";
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$curlResults = curl_exec($curl);
		curl_close($curl);
		$res = json_decode($curlResults);
		if ($res) {
			if (!empty($res->count)) {
				return intval($res->count);
			}
		}
		return 0;
	}

	/**
	 * Получает количество лайков Google +
	 *
	 * @param string $url адрес страницы
	 *
	 * @return int
	 */
	function getPlusOnes($url)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, "https://clients6.google.com/rpc");
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt(
			$curl,
			CURLOPT_POSTFIELDS,
			'[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' .
			$url .
			'","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]'
		);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
		$curlResults = curl_exec($curl);
		curl_close($curl);
		$json = json_decode($curlResults, true);
		if ($json) {
			if (!empty($json[0])){
				if (!empty($json[0]['result'])){
					return $json[0]['result']['metadata']['globalCounts']['count'];
				}
			}
		}
		return 0;
	}
}