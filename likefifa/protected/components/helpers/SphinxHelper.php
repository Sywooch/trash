<?php


namespace likefifa\components\helpers;

use Yii;

class SphinxHelper
{
	/**
	 * Формирование запроса для поиска в сфинкс, учитывая ошибки в запросе пользователя
	 *
	 * @param string $query
	 * @param array  $words
	 * @param string $index
	 *
	 * @return string
	 */
	public static function suggest($query, $words, $index)
	{
		foreach ($words as $word => $data) {
			if ($data['docs'] < 100) {
				$trigrams = SphinxHelper::buildTrigrams($word);
				$len = mb_strlen($word, 'UTF-8');
				$wrongCount = 2;
				if (preg_match('|[А-Яа-я]|', $word)) {
					$len = $len * 2;
					$wrongCount = $wrongCount * 2;
				}

				$suggests = Yii::app()->search
					->setMatchMode(SPH_MATCH_EXTENDED2)
					->SetRankingMode(SPH_RANK_WORDCOUNT)
					->SetFilterRange("len", $len - $wrongCount, $len + $wrongCount)
					->SetSelect('*, @weight+2-abs(len-' . $len . ') AS myrank')
					->SetSortMode(SPH_SORT_EXTENDED, "myrank DESC, freq DESC")
					->SetArrayResult(true)
					->limit(0, 1)
					->from($index)
					->where("\"$trigrams\"/1")
					->searchRaw();

				if (count($suggests['matches']) == 0) {
					continue;
				}

				$suggest = $suggests['matches'][0]['attrs']['keyword'];
				$word = str_replace('*', '', $word);
				$query = preg_replace('|' . $word . '|iu', $suggest, $query);
			}
		}

		return $query;
	}

	/**
	 * Создает триграммы для слова
	 *
	 * @param string $keyword
	 *
	 * @return string
	 */
	public static function buildTrigrams($keyword)
	{
		$t = "__" . $keyword . "__";

		$trigrams = "";
		for ($i = 0; $i < mb_strlen($t, 'UTF-8') - 2; $i++) {
			$trigrams .= mb_substr($t, $i, 3, 'UTF-8') . " ";
		}

		return $trigrams;
	}

	/**
	 * Удаляет из строки спецсимволы
	 *
	 * @param        $query
	 * @param string $separator
	 *
	 * @return string
	 */
	public static function clearQuery($query, $separator = ' ')
	{
		$query = trim(mb_strtolower($query, 'UTF-8'));
		$query = preg_replace("[[^0-9a-zA-Zа-яА-Я-]+]ui", $separator, $query);
		$query = preg_replace('[\s+]', $separator, $query);
		return trim($query);
	}
} 