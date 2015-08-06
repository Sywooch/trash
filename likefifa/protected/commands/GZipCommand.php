<?php

/**
 * Class GZipCommand
 *
 * Сжимает js и css файлы в проекте
 */
class GZipCommand extends CConsoleCommand
{
	/**
	 * @var string name or path of/to the JAVA executable. Default is 'java'.
	 */
	public $javaBin = 'java';

	public $exclude = [
		'tiny_mce',
		'highstock',
	];

	/**
	 * Сжимает файлы в директории
	 */
	public function actionIndex()
	{
		$files =
			CFileHelper::findFiles(
				Yii::getPathOfAlias('application') . '/../js',
				['fileTypes' => ['css', 'js'], 'exclude' => $this->exclude]
			);
		$files2 =
			CFileHelper::findFiles(
				Yii::getPathOfAlias('application') . '/../css',
				['fileTypes' => ['css', 'js'], 'exclude' => $this->exclude]
			);

		$files = CMap::mergeArray($files, $files2);

		foreach ($files as $file) {
			$oldName = $file;

			if (!strstr($file, '.min') && !strstr($file, '-min')) {
				echo $file . PHP_EOL;
				$ext = explode('.', $file);
				$type = end($ext);
				$outFile = str_replace('.' . $type, '.min.' . $type, $file);

				$jar =
					Yii::getPathOfAlias('application.vendors.nervo.yuicompressor') .
					DIRECTORY_SEPARATOR .
					'yuicompressor.jar';
				$command =
					sprintf(
						"%s -jar %s --type %s -o %s %s",
						escapeshellarg($this->javaBin),
						escapeshellarg($jar),
						$type,
						escapeshellarg($outFile),
						escapeshellarg($file)
					);
				exec($command, $output, $result);
				$file = $outFile;
			}

			$command = 'gzip -9 -c ' . $file . '  > ' . $oldName . '.gz';
			exec($command);
		}

		$this->actionAssets();
	}

	/**
	 * Сжимает временные файлы
	 */
	public function actionAssets()
	{
		$files =
			CFileHelper::findFiles(Yii::getPathOfAlias('application') . '/../assets/', ['fileTypes' => ['css', 'js']]);
		foreach ($files as $file) {
			echo "Compressing {$file}" . PHP_EOL;
			$command = 'gzip -9 -c ' . $file . '  > ' . $file . '.gz';
			exec($command);
		}
	}
}