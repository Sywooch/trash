
<div class="help">

    <label class="help_label" for="help_comment">Нужна помощь или консультация?</label>
    <span>Телефон: <?php echo GeneralPhone; ?></span>
    <br />

    <span>
        email:
        <a class="link_email" href="mailto:<?php echo Yii::app()->params['email']['partner']; ?>">
            <?php echo Yii::app()->params['email']['partner']; ?>
        </a>
    </span>
    <br />

    <label class="help_label" for="help_comment">Задайте вопрос:</label>
    <form class="help_form" method="POST" type="POST" action="/pk/service/sendQuestion">
        <input type="hidden" name="clinicId" value="" />
        <textarea id="help_comment" name="message" class="help_comment" placeholder=""></textarea>
        <span class="button_lk help_form__submit">Отправить</span>
    </form>

</div>
