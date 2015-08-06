<?xml version='1.0'  encoding="UTF-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

    <xsl:decimal-format decimal-separator = '.' grouping-separator = ' ' NaN = ' '/>

    <xsl:output method="html" encoding="utf-8"/>

    <xsl:template name="requestForm">
        <xsl:param name="doctor" />

        <div class="echo">
            <div class="">
                <xsl:choose>
                <xsl:when test="$doctor/Status = 4">
                    <p class="req_form request_cl req_short">Уважаемые посетители, в настоящий момент запись к данному врачу ограничена.<br/>
                        Вы можете <a href="/doctor/{$doctor/SpecList/Element[position() = 1]/Alias}">выбрать из доступных </a> <xsl:value-of select="$doctor/SpecList/Element[position() = 1]/NameInGenitive" /> и оставить заявку на <a href='/request'>запись онлайн</a> или по телефону <!-- phone number -->
                    </p>
                </xsl:when>
                <xsl:otherwise>
                <div class="wr-arr png"></div>

                <form class="req_form request_cl req_short" method="post" action="/routing.php?r=request/save" novalidate="novalidate">
                    <input type="hidden" id="requestCityId" name="requestCityId" value="1" />
                    <input type="hidden" id="doctor" name="doctor" value="{$doctor/Id}" />
                    <input type="hidden" id="sector" name="sector">
                    	<xsl:attribute name="value">
                    		<xsl:choose>
                    			<xsl:when test="/root/srvInfo/SearchParams/SelectedSpeciality/Id">
                    				<xsl:value-of select="/root/srvInfo/SearchParams/SelectedSpeciality/Id"/>
                    			</xsl:when>
                    			<xsl:when test="$doctor/Specialities/Element and count($doctor/Specialities/Element) = 1">
                    				<xsl:value-of select="$doctor/Specialities/Element/Id"/>
                    			</xsl:when>
                    		</xsl:choose>
                    	</xsl:attribute>
                    </input>
                    <span class="req_form__title">
                        Запишитесь на прием к этому врачу
                    </span>
                    <p class="req_form__phone">
                        <strong class="i-phone comagic_phone call_phone_1">
                            <xsl:value-of select="dbHeadInfo/Phone/Short" />
                        </strong> или отправив заявку:</p>
                    <p class="req_form__doctor">
                        <span class="req_form__doctor-txt">
                            врач:
                        </span>
                        <xsl:value-of select="$doctor/Name" />
                    </p>
                    <p class="req_form__input-ct">
                        <label class="req_form__label" for="requestName">
                        </label>
                        <input id="requestName" class="req_form__input required" type="text" autofocus="autofocus" name="requestName" maxlength="100" placeholder="Ваше имя (обязательно)" />
                    </p>
                    <p class="req_form__input-ct">
                        <label class="req_form__label" for="requestPhone"></label>
                        <input id="requestPhone" class="req_form__input required js-mask-phone" type="text" name="requestPhone" placeholder="Ваш телефон (обязательно)" />
                    </p>
                    <div class="req_form__radio">
                        <span class="req_form__radio-title">врач для</span>
                        <p class="req_form__radio-ct">
                            <input id="ageSelectorAdult" type="radio" class="radio_imit__input" name="requestAgeSelector" value="adult" checked="" />
                            <label for="ageSelectorAdult" class="radio_imit__label">
                                <span class="radio_imit__icon i-adult"></span>
                                Взрослого
                            </label>
                        </p>
                        <p class="req_form__radio-ct">
                            <input id="ageSelectorChild" type="radio" class="radio_imit__input" name="requestAgeSelector" value="child" /><label for="ageSelectorChild" class="radio_imit__label"><span class="radio_imit__icon i-child"></span>
                            Ребенка
                        </label>
                        </p>
                    </div>
                    <p class="req_form__input-ct">
                        <label for="requestComments" class="req_form__label-comment">
                            Оставить комментарий
                        </label>
                        <textarea id="requestComments" class="req_form__comment" name="requestComments" placeholder="Напишите здесь комментарий (необязательно)" style="display: none;"></textarea>
                    </p>
                    <a class="req_form__submit-btn">
                        Записаться на прием <span class="req_form__submit-icon"></span>
                    </a>
                </form>
                </xsl:otherwise>
                </xsl:choose>
            </div>
        </div>
    </xsl:template>
</xsl:transform>