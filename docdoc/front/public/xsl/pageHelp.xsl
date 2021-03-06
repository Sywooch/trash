<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:import href="hotBanners.xsl" />
    <xsl:import href="specList.xsl" />

    <xsl:output method="html" encoding="utf-8"/>

    <xsl:template match="root">
        <xsl:call-template name="staticHowTo" />
    </xsl:template>

    <xsl:template name="staticHowTo">
        <div class="l-wrapper">
            <div id="content" class="page_static page_help">

                <div class="right" style="margin-top: -15px;">

                    <xsl:call-template name="specList" />

                    <xsl:call-template name="hotBanners">
                        <xsl:with-param name="bannersId" select="'help'"/>
                    </xsl:call-template>

                </div>
                <div class="box-left library">
                    <div class="doctor"></div>
                    <div class="breadcrumb"><a href="/">Главная</a> –&gt; Как найти врача на DocDoc.ru</div>
                    <h1>Как найти врача на DocDoc.ru</h1>
                    <p>Портал DocDoc – это современный on-line сервис по поиску врача. Вы можете оперативно 	подобрать необходимого специалиста и сразу же записаться на прием, для этого Вам необходим только доступ в Интернет. Поиск и подбор врачей осуществляется бесплатно!   </p>
                    <div class="page-help">
                        <div class="item" id="doctor-howitwork">
                            <div class="page-help-head">Как работает DocDoc?</div>
                            В нашей базе размещены анкеты врачей из 300 клиник и медицинских центров Москвы. В профиле каждого врача представлена информация о его специализации, опыте работы, образовании, рейтинг специалиста, а также отзывы пациентов. Анкеты врачей до размещения на портале проходят премодерацию: все профессиональные данные подтверждены документально.
                            Вы можете самостоятельно подобрать себе врача, воспользовавшись специальной формой на портале, или обратиться к нам и наши консультанты порекомендуют Вам нужного специалиста.
                            <div class="page-help-ico"><img src="/i/help/help-img-1.gif" /></div>
                        </div>
                        <div class="item" id="doctor-choice">
                            <div class="page-help-head">Как самостоятельно подобрать врача?</div>
                            На главной странице портала Вы задаете параметры поиска «специальность» врача и удобный для Вас «район Москвы» для проведения консультации, далее нажимаете «Найти врача». В результате Вам будет представлен список анкет, ранжированный с учетом стажа работы специалистов, внутреннего рейтинга портала и стоимости приема.
                            Вы выбираете подходящего врача и оформляете электронную заявку на прием или можете записаться к нему на консультацию по телефону, позвонив в наш контактный центр.
                            <div class="page-help-ico"><img src="/i/help/help-img-2.gif" /></div>
                        </div>
                        <div class="item">
                            <div class="page-help-head">Как поручить подбор врача консультантам DocDoc?</div>
                            Вы может поручить подбор врача специалистам нашего портала. Для этого Вы можете или позвонить по телефону 8 (495) 565-333-0 и сообщить требования, которым должен отвечать врач и вашу контактную информацию или заполнить электронную заявку на подбор врача на нашем сайте. Наш консультант быстро подберет несколько подходящих специалистов  и предложит Вам. После чего Вы сможете так же по телефону записаться на прием к выбранному врачу.
                            <div class="page-help-ico"><img src="/i/help/help-img-3.gif" /></div>
                        </div>
                        <div class="item" id="doctor-raiting">
                            <div class="page-help-head">Рейтинг врача</div>
                            При выборе врача кроме общей информации в анкете стоит обратить  внимание на его рейтинг. Рейтинг врача формируется по специально разработанной нами системе из таких показателей  как  образование, наличие ученых степеней, наличие дополнительных сертификатов, опыт работы, наличие профессиональных публикаций. Таким образом, Вы получаете независимую профессиональную оценку каждого специалиста.
                            <div class="page-help-ico"><img src="/i/help/help-img-4.gif" /></div>
                        </div>
                        <div class="item">
                            <div class="page-help-head">Оставить отзыв о враче</div>
                            Отзывы о врачах, публикуемые на нашем портале, могут оставить только те пациенты, которые посетили этого врача, записавшись через DocDoc. Мы собираем отзывы самостоятельно, звоня пациентам, которые прошли прием, и узнавая их мнение о враче и качестве консультации. Кроме этого на нашем портале есть возможность оставить отзыв о враче через электронную форму  на сайте, но перед публикацией он будет проверен модератором на достоверность.
                            <div class="page-help-ico"><img src="/i/help/help-img-5.gif" /></div>
                        </div>
                        <div class="item" id="doctor-home">
                            <div class="page-help-head">Вызов врача на дом</div>
                            Также на нашем портале есть возможность  вызвать  врача на дом. Для этого в параметрах поиска Вам нужно  выбрать опцию «вызов врача на дом». Вам будут показаны анкеты врачей, которые осуществляют выезд  на дом в указанном районе. Или Вы можете позвонить нам, и наши консультанты подберут Вам врача, который сможет осуществить прием на дому.
                            <div class="page-help-ico"><img src="/i/help/help-img-6.gif" /></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </xsl:template>

</xsl:stylesheet>