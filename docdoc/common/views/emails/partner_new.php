<?php
/**
 * @var \dfs\docdoc\models\MailQueryModel $mail
 * @var \dfs\docdoc\models\PartnerModel $partner
 */

$mail->subj = '[docdoc.ru] Регистрация в партнёрском кабинете';
$mail->reply = \Yii::app()->params['email']['affiliate'];
?>
<p>Здравствуйте!</p>

<p>
	Меня зовут Владимир Никишков, я отвечаю за развитие партнерской программы в компании DocDoc.ru.
	Я буду вашим личным менеджером.
	<br />
	Для завершения регистрации напишите, пожалуйста, в ответном письме, какие у вас источники трафика.
	Я, в свою очередь, проведу анализ вашей площадки и дам рекомендации по использованию наиболее подходящих для вас инструментов.
	После модерации на ваш почтовый ящик придет письмо с доступами в ваш личный кабинет.
</p>

<p>
	Если у вас возникли какие-либо вопросы, свяжитесь любым удобным для вас способом:<br />
	Skype: vladimir.nikishkov<br />
	Почта: <?php echo \Yii::app()->params['email']['affiliate']; ?><br />
	Linkedin: <a href="https://www.linkedin.com/pub/vladimir-nikishkov">https://www.linkedin.com/pub/vladimir-nikishkov</a>
</p>
