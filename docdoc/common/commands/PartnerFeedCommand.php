<?php
use dfs\common\components\console\Command;
use dfs\docdoc\api\feed\YmlFeed;
use dfs\common\filesystem\FileHandler;
use dfs\docdoc\models\CityModel;
use dfs\docdoc\api\feed\Gis2;
/**
 * Генерирует файл с Xml Feed'om для партнеров
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 *
 */
class PartnerFeedCommand extends Command
{

	/**
	 * Публикует YML feed для партнеров
	 *
	 * @return int|void
	 *
	 * @param string $cityName
	 */
	public function actionYml($cityName)
	{
		$this->log("Начинаем публикацию");

		$city = CityModel::model()->byRewriteName($cityName)->find();

		// Сохранение фида 1.0
		$this->saveYmlFeed($city);

		// Сохранение фида 2.0
		$this->saveYmlFeed($city, 2);
	}

	/**
	 * Сохраняет список клиник в xml для 2gis
	 *
	 * @throws Exception
	 */
	public function action2gis()
	{
		$this->log("Начинаем публикацию");

		$file = ROOT_PATH . "/back/runtime/feed/gis2.xml";

		$config = \Yii::app()->params['2gis'];

		$xml = new Gis2();

		try{
			if(FileHandler::write($file, $xml->generateXml($config), "w")){
				$this->log("XML-feed сохранен в файл {$file}");
			} else {
				$msg = "XML-feed Не сохранен в файл {$file}";
				trigger_error($msg, E_USER_WARNING);
			}
		} catch (Exception $e){
			$this->log($e->getMessage());
			trigger_error($e->getMessage());
		}
	}

	/**
	 * Сохранение yml фида
	 *
	 * @param $city
	 * @param string $version
	 */
	private function saveYmlFeed($city, $version = '')
	{
		/**
		 * @var YmlFeed $feed
		 */
		$class = 'dfs\docdoc\api\feed\YmlFeed' . $version;
		$feed = new $class($city->id_city);
		$file = ROOT_PATH . "/back/runtime/feed/ymlfeed{$version}_city_{$city->rewrite_name}.xml";
		FileHandler::write($file, $feed->getFeed(), "w");
		$this->log("XML-feed сохранен в файл {$file}");
	}
} 
