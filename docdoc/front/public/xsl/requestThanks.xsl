<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">


    <xsl:output method="html" encoding="utf-8"/>


    <xsl:template match="/root">

        <xsl:call-template name="requestThanks"/>

    </xsl:template>

<xsl:template name="requestThanks">

<main class="l-main l-wrapper" role="main">

    <a href="/">Вернуться на главную</a><!-- home url -->

    <h1>Благодарим вас за обращение!</h1>
    <p>
        Ваша заявка о записи на прием к врачу отправлена. Наши консультанты свяжутся с вами в течение 15 минут ежедневно с 9:00 до 21:00 и запишут Вас на прием.
    </p>
    <xsl:if test="dbInfo/RequestId">
        <form class="req_form request_cl request_decline" action="/service/cancelRequest.php">
            <input type="hidden" name="requestId" value="{dbInfo/RequestId}" />
            <a class="req_form__submit-btn">
                Отклонить заявку
            </a>
        </form>
    </xsl:if>

</main>
</xsl:template>


</xsl:transform>
