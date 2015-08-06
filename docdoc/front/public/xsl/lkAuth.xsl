<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

    <xsl:output method="html" encoding="utf-8"/>

	<xsl:param name="headerType" select="headerType"/>


    <xsl:template match="/root">
        <xsl:call-template name="auth" />
    </xsl:template>

    <xsl:template name="auth">
        <div id="login" class="registration round shadow">
            <div class="reg placeholder">
                <form id="lk-login-form" method="post" action="/lk/service/login.php">
                    <div class="doctor"></div>
                    <h1>Вход в личный кабинет</h1>

                    <label class="required" for="LKLoginForm_email"></label>
                    <input id="LKLoginForm_email" class="input required" type="text" autofocus="autofocus" name="LKLoginForm[email]" value="" autocomplete="off" placeholder="введите email" />

                    <label class="required" for="LKLoginForm_password"></label>
                    <input id="LKLoginForm_password" class="input required" type="password" name="LKLoginForm[password]" autocomplete="off" placeholder="введите пароль" />

                    <div><a href="/lk/recoveryPassword">Забыли пароль?</a></div>

                    <div class="depart"><input name="LKLoginForm[rememberMe]" id="LKLoginForm_rememberMe" type="checkbox" /> <label for="LKLoginForm_rememberMe">Запомнить меня</label></div>
                    <div class="button-place"><input class="blue-but round" type="submit" value="Войти" /></div>
                </form>
            </div>

            <!--
            <div class="reg-inf">

                <div class="error error-txt">Неправильный email или пароль!</div>

            </div>
            -->
            <div class="reg-inf-bottom">
                <p>Если у вас возникли вопросы, связанные с авторизацией, свяжитесь с нами:</p>
                <p><strong>email: <a href="mailto:admin@docdoc.ru">admin@docdoc.ru</a><br/>тел.: +7 (495) 565-333-0<?php }?><br /></strong></p>
            </div>

            <div class="clear"></div>
        </div>

    </xsl:template>


</xsl:transform>
