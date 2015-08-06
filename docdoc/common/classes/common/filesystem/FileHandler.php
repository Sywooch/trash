<?php
namespace dfs\common\filesystem;

/**
 * Class FileHandler
 *
 * работа с файлами
 *
 * @package dfs\common\filesystem
 */
class FileHandler
{

	const READ_FILE_BLOCK_SIZE = 32768;

	/**
	 * 	Запись в файл
	 *
	 * @param string $filePath  путь к файлу
	 * @param string $content контент для записи
	 * @param string $mode режим записи
	 *
	 * @return boolean
	 */
	public static function write($filePath, $content, $mode = "a")
	{
		$file_exists = is_file($filePath);

		$handle = fopen($filePath, $mode);
		if ($handle) {

			@flock ($handle, LOCK_EX);
			$status = fwrite ($handle, $content);
			@flock ($handle, LOCK_UN);
			fclose($handle);

			//для новых файлов проставляем права
			if (!$file_exists) {
				chmod($filePath, FILE_MODE);
			}

			//fwrite может вернуть 0
			return $status!==false;
		}

		return false;
	}

}
