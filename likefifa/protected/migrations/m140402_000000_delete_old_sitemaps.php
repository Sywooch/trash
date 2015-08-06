<?php

/**
 * m140402_000000_delete_old_sitemaps class file.
 *
 * Удаляет старые sitemap.xml
 *
 * @author   Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @see      https://docdoc.megaplan.ru/task/1003365/card/
 * @package  migrations
 */
class m140402_000000_delete_old_sitemaps extends CDbMigration
{

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$dir = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR;

		for ($i = 0; $i < 100; $i++) {
			if ($i) {
				$file = "{$dir}sitemap{$i}.xml";
			} else {
				$file = "{$dir}sitemap.xml";
			}

			if (file_exists($file)) {
				unlink($file);
			}
		}
	}
}