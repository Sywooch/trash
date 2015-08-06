<?php
/**
 * @var \dfs\docdoc\models\MailQueryModel $mail
 * @var \dfs\docdoc\models\PartnerModel $partner
 */

$mail->subj = '[docdoc.ru] Заявка на партнёрство';
?>
<p>
	Новый партнер.<br />
	pid: <?php echo $partner->id; ?><br />
	 <?php
		if ($partner->contact_phone) {
			echo 'телефон: ' . $partner->contact_phone;
		} else {
			echo 'email: ' . $partner->contact_email;
		}
	?>
</p>
