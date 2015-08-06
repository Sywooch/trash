<?php
/**
 * Created by PhpStorm.
 * User: atyutyunnikov
 * Date: 30.03.15
 * Time: 20:13
 */

/**
 * Class PhoneProvidersCommand
 * todo удалить после релиза
 */
class PhoneProvidersCommand extends \dfs\common\components\console\Command
{
	public function actionIndex($file = 'docdoc_phones.csv')
	{
		$fileName = ROOT_PATH . '/' . $file;
		$handle = fopen($fileName, 'r');

		if (!$handle) {
			throw new \CException('Ошибка открытия файла ' . $fileName);
		}

		$k = 0;
		$title = fgetcsv($handle);
		$data = [];

		while (($row = fgetcsv($handle)) !== false) {
			foreach ($row as $key => $item) {
				$data[$k][$key] = $item;
			}

			$k++;
		}

		fclose($handle);

		$providers = [
			'UIS011227' => 2,
			'3856652' => 3,
			'Caravan' => 4,
			'ЦетроСеть' => 5,
		];

		foreach ($data as $d) {

			$providerName = $d[6];
			$phoneNumber = $d[3];

			if (!$phone = \dfs\docdoc\models\PhoneModel::model()->byNumber($phoneNumber)->find()) {
				$this->log('trying to create phone: Phone not found ' . $phoneNumber);

				$phone = new \dfs\docdoc\models\PhoneModel();
				$phone->number = $phoneNumber;
			}

			if (isset($providers[$providerName])) {
				$phone->provider_id = $providers[$providerName];

				if ($phone->save()) {
					$this->log($phone->number . ' => ' . $providerName);
				} else {
					$this->log($phone->number . var_export($phone->getErrors(), true));
				}
			} else {
				$this->log('provider not found ' . $providerName . ' setting to default');
				$phone->provider_id = 1;
				if ($phone->save()) {
					$this->log($phone->number . ' => default, in file => ' . $providerName);
				} else {
					$this->log($phone->number . var_export($phone->getErrors(), true));
				}
			}
		}
	}
}