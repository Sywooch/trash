<?php
use dfs\docdoc\models\DoctorClinicModel;

function rating_opinion($yes = 0, $no = 0, $total = 0)
{
	$delta = 0;

	if ($total > 0) {
		$percentYes = 100 * $yes / $total;
		$percentNo = 100 * $no / $total;

		if ($yes >= 5 && $percentYes >= 90) {
			$delta = 0.2;
		} elseif ($yes >= 3 && $percentYes >= 80) {
			$delta = 0.1;
		} elseif ($no >= 3 && $percentNo >= 50) {
			$delta = -1;
		} elseif ($no >= 2 && $percentNo >= 30) {
			$delta = -0.5;
		}
	}

	return $delta;
}

function opinionColor($rating = array())
{
	$color = 0;

	if (count($rating) == 3 && $rating[0] >= 4 && $rating[1] >= 4) {
		$color = 1;
	} elseif (count($rating) == 3 && ($rating[0] <= 2 || ($rating[0] <= 3 && $rating[1] <= 3 && $rating[2] <= 3))) {
			$color = -1;
	}

	return $color;
}
