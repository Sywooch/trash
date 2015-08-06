<?php

require_once dirname(__FILE__) . "/../../include/common.php";
require_once dirname(__FILE__) . "/../../lib/php/validate.php";

function createOpinion($params = array()) {
    $data = array();

    if (count($params) > 0) {

        /* 	Валидация	 */
        $doctorId = (isset($params['doctor'])) ? checkField($params['doctor'], "i", 0) : '0';
        $partnerId = (isset($params['partner'])) ? checkField($params['partner'], "i", 1) : '1';
        $opinionText = (isset($params['opinion'])) ? checkField($params['opinion'], "t", "") : '';
        $clientName = (isset($params['client'])) ? checkField($params['client'], "t", "") : '';
        $clientPhone = (isset($params['phone'])) ? checkField($params['phone'], "t", "") : '';
        $ratingQualification = (isset($params['ratingQualification'])) ? checkField($params['ratingQualification'], "i", 0) : 0;
        $ratingRoom = (isset($params['ratingRoom'])) ? checkField($params['ratingRoom'], "i", 0) : 0;
        $ratingAttention = (isset($params['ratingAttention'])) ? checkField($params['ratingAttention'], "i", 0) : 0;
        $clientPhone = formatPhone4DB($clientPhone);

        if ($partnerId <= 0) {
            setError("Не передан идентификатор партнера");
        }
        if ($doctorId <= 0) {
            setError("Не передан идентификатор врача");
        }
        if (empty($opinionText)) {
            setError("Не передан текст комментария");
        }
        if (empty($clientName)) {
            setError("Не передано имя клиента оставившего отзыв");
        }
        if (empty($clientPhone)) {
            setError("Не передан телефон клиента оставившего отзыв");
        }
        if (strlen($clientPhone) < 7) {
            setError("Не верный формат телефона");
        }

        if ($ratingQualification == 0 || $ratingAttention == 0 || $ratingRoom == 0) {
            setError("Не передан рейтинг");
        }

        $result = query("START TRANSACTION");

        if ($partnerId > 0 || $doctorId > 0) {
            // создание отзыва

            $sql = "INSERT INTO doctor_opinion SET
							doctor_id = " . $doctorId . ",
							author = 'gues',
							name = '" . $clientName . "',
							phone = '" . $clientPhone . "',
							status = 'hidden',
							allowed =  0,
							origin = 'original',
							is_fake = '1',
							text = '" . $opinionText . "',
							rating_qualification = '" . $ratingQualification . "',
							rating_attention = '" . $ratingAttention . "',
							rating_room = '" . $ratingRoom . "'";
            $result = query($sql);
            if (!$result) {
                setDBerror("Ошибка создания отзыва" . $sql);
            }
            $opinionId = legacy_insert_id();
        } else {
            setError("Не переданы обязательные параметры");
        }

        $result = query("commit");

        $data['status'] = 'success';
        $data['message'] = 'Отзыв принят';
    } else {
        setError("Не переданы параметры");
    }

    return array('Response' => $data);
}

?>