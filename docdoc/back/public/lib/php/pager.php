<?php

/*  Постраничная навигация */
/* $page - номер страницы, $num - колличество записей на странице	*/
function pager($sql, $page = 1, $step = 10, $namespace = null, $sqlForCountEstimate = false)
{
	global $sqlLimit;

	$xml = "";

	if ($sqlForCountEstimate) {
		$sqlCnt = $sqlForCountEstimate;
	} else {
		$sqlCnt = "select count(*) from (" . $sql . ") as pagerTable";
	}

	$result = query($sqlCnt);
	if ($result) {
		$posts = legacy_result_first($result, 0);
		if ($posts == 0) {
			return array($sql, $xml);
		}
	} else {
		return array($sql, "");
	}

	@$total = intval(($posts - 1) / $step) + 1;
	$page = intval($page);
	// Если значение $page меньше единицы или отрицательно
	// переходим на первую страницу
	// А если слишком большое, то переходим на последнюю
	if (empty($page) || $page <= 0) $page = 1;
	if ($page > $total) $page = $total;

	// Вычисляем начиная к какого номера
	// следует выводить сообщения
	$start = $page * $step - $step;
	if (strpos(strtoupper($sql), "LIMIT") === false) {
		$sql .= " LIMIT $start, $step";
	} else {
		$sql = "SELECT * FROM (" . $sql . ") AS sourceSQL LIMIT $start, $step";
	}
	//echo $sql; exit;//

	$xml = "<Pager total=\"" . $posts . "\" step=\"" . $step . "\" currentPageId=\"" . $page . "\" " . ($namespace ? ' namespace="' . $namespace . '" ' : ' ') . ">";
	for ($i = 0; $i < $total; $i++) {
		if ((($i + 1) * $step + 1) > $posts) {
			$xml .= "<Page id=\"" . ($i + 1) . "\" start=\"" . ($i * $step + 1) . "\" end=\"" . $posts . "\"/>";
		} else {
			$xml .= "<Page id=\"" . ($i + 1) . "\" start=\"" . ($i * $step + 1) . "\" end=\"" . ($i * $step + $step) . "\"/>";
		}
	}
	$xml .= "</Pager>";

	return array($sql, $xml);
}

/**
 * Функция, возвращающая запрос и данные для пэйджера
 *
 * @param $sql
 * @param int $page
 * @param int $step
 *
 * @return array
 */
function pagerArr($sql, $page = 1, $step = 10)
{
	$data = array();

	$sqlCnt = "select count(*) from (" . $sql . ") as pagerTable";

	$result = query($sqlCnt);
	if ($result) {
		$posts = legacy_result_first($result, 0);
		if ($posts == 0) {
			return array($sql, $data, $posts);
		}
	} else {
		return array($sql, "", 0);
	}

	@$total = intval(($posts - 1) / $step) + 1;
	$page = intval($page);
	// Если значение $page меньше единицы или отрицательно
	// переходим на первую страницу
	// А если слишком большое, то переходим на последнюю
	if (empty($page) || $page <= 0) $page = 1;
	if ($page > $total) $page = $total;

	// Вычисляем начиная к какого номера
	// следует выводить сообщения
	$start = $page * $step - $step;
	$sql = $sql . " LIMIT $start, $step";
	//echo $sql; exit;//

	$data = getPages($posts, $step, $page);

	return array($sql, $data, $posts);

}

/**
 * Массив для отображения пэйджера
 *
 * @param $count
 * @param $step
 * @param $page
 *
 * @return array
 */
function getPages($count, $step, $page)
{
	$data = array();

	$total = intval(($count - 1) / $step) + 1;
	$page = intval($page);
	// Если значение $page меньше единицы или отрицательно
	// переходим на первую страницу
	// А если слишком большое, то переходим на последнюю
	if ( empty($page) || $page <= 0 ) $page = 1;
	if ( $page > $total) $page = $total;

	$data['Params']['Total'] = $count;
	$data['Params']['Step'] = $step;
	$data['Params']['CurrentPage'] = $page;
	for ($i = 0; $i < $total; $i++) {
		$data['Pages'][$i]['Id'] = $i + 1;
		$data['Pages'][$i]['Start'] = $i * $step + 1;
		if ((($i + 1) * $step + 1) > $count) {
			$data['Pages'][$i]['End'] = $count;
		} else {
			$data['Pages'][$i]['End'] = $i * $step + $step;
		}
	}

	return $data;
}
