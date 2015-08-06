<?xml version='1.0'  encoding="UTF-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

    <xsl:import href="docdocMenu.xsl" />

	<xsl:output method="html" encoding="utf-8"/>

	<xsl:template match="root">

        <xsl:call-template name="docdocAbout" />
	</xsl:template>


<xsl:template name="docdocAbout">

<main class="l-main l-wrapper" role="main">

    <nav class="l-nav">
        <xsl:call-template name="docdocMenu"/>
    </nav>

    <section class="page_about" id="about">

        <article class="aboutus_item" id="aboutus">
            <h1 class="aboutus_title">
                DocDoc (ООО «МедСервис») – это быстрый, удобный и бесплатный для пациентов online-сервис по поиску врача.
            </h1>
            <p>
                Портал оказывает услуги по подбору врачей из клиник Москвы, Московской области и Санкт-Петербурга.
            </p>
            <p>
                DocDoc помогает Вам среди всего множества специалистов найти «своего» врача и оперативно записаться к нему на прием в удобное для Вас время.
            </p>
            <p>
                Основная цель сервиса – упростить процесс записи пациента на прием к выбранному специалисту и сделать работу врачей и клиник более прозрачной для потребителей.
            </p>
        </article>

        <article class="aboutus_item">
            <h2 class="aboutus_title">
                DocDoc сегодня
            </h2>
            <ul class="today_list">
                <li class="today_item i-today-doctor">
                    <span class="today_count">
                        <xsl:value-of select="dbHeadInfo/Statistics/DoctorsCount"/>
                    </span>
                    врачей в базе
                </li>
                <li class="today_item i-today-medicine">
                    <span class="today_count">
                        200
                    </span>
                    клиник-партнеров
                </li>
                <li class="today_item i-today-partners">
                    <span class="today_count">
                        25 000
                    </span>
                    обратившихся пациентов
                </li>
                <li class="today_item i-today-requests">
                    <span class="today_count">
                        4 600
                    </span>
                    обращений в августе
                </li>
                <li class="today_item i-today-reviews">
                    <span class="today_count">
                        <xsl:value-of select="dbHeadInfo/Statistics/ReviewsCount"/>
                    </span>
                    опубликованных отзывов
                    проверенных пациентов
                </li>
                <li class="today_item i-today-comeback">
                    <span class="today_count">
                        25%
                    </span>
                    пациентов обращаются
                    на портал повторно
                </li>
            </ul>
        </article>

        <article class="aboutus_item i-aboutus_cashfree">
            <h2 class="aboutus_title_xl">
                Сервис DocDoc бесплатен для пациентов!
            </h2>
            <p>
                Наши услуги оплачиваются только клиниками или частными кабинетами, которые с нами сотрудничают и чьи врачи размещены на нашем портале. Пациенты, записавшиеся на прием через DocDoc, не покрывают стоимость услуг сервиса для клиник.
            </p>
        </article>

        <article class="aboutus_item i-aboutus_discount">
            <h2 class="aboutus_title">
                Скидки нашим пациентам от клиник-партнеров!
            </h2>
            <p>
                Клиники-партнеры предлагают пациентам, записавшимся к ним на прием через портал DocDoc, скидки на стоимость первичного приема врача (от 5 до 35%).
            </p>
            <p class="aboutus_disclaimer">
                Скидочные цены можно увидеть в анкете врача рядом с перечеркнутой красной ценой.
            </p>
        </article>

        <article class="aboutus_item">
            <h2 class="aboutus_title">
                Как мы работаем
            </h2>
            <ul class="aboutus_how">
                <li class="aboutus_how_item i-how_reqcall">
                    <span class="aboutus_how_step">1.</span>
                    Вы звоните нам
                </li>
                <li class="aboutus_how_item i-how_reqonline">
                    <span class="aboutus_how_step">1.</span>
                    Вы оставляете заявку на сайте
                </li>
                <li class="aboutus_how_item i-how_doctorsearch">
                    <span class="aboutus_how_step">2.</span>
                    Мы подбираем врача
                </li>
                <li class="aboutus_how_item i-how_reqclinic">
                    <span class="aboutus_how_step">3.</span>
                    Соединяем с клиникой, записываем на прием
                </li>
                <li class="aboutus_how_item i-how_review">
                    <span class="aboutus_how_step">4.</span>
                    Собираем отзывы о работе врача
                </li>
            </ul>
        </article>

        <article class="aboutus_item i-aboutus_mission" id="ourmission">
            <h2 class="aboutus_title">
                Наша миссия
            </h2>
            <ul class="list">
                <li class="list_item">
                    <h3 class="aboutus_subtitle">
                        Мы представляем интересы пациентов.
                    </h3>
                    <p class="aboutus_txt">
                        DocDoc – это в первую очередь социальный проект. Поэтому в отношениях «пациент – врач (клиника)» мы являемся представителями пациентов и защищаем их интересы.
                    </p>
                </li>
                <li class="list_item">
                    <h3 class="aboutus_subtitle">
                        Мы хотим показать людям хороших врачей.
                    </h3>
                    <p class="aboutus_txt">
                        Реклама клиник – распространенные явление, но никто не говорит о том, какие врачи в них
                        работают. Мы переносим акцент с поиска клиник на поиск врачей.
                    </p>
                </li>
                <li class="list_item">
                    <h3 class="aboutus_subtitle">
                        Мы помогаем врачам и клиникам получать обратную связь.
                    </h3>
                    <p class="aboutus_txt">
                        Врачи и клиники заинтересованы в получении независимого мнения о своей работе.
                        В частности, это мнение составляется с помощью отзывов, которые мы собираем у пациентов.
                    </p>
                </li>
                <li class="list_item">
                    <h3 class="aboutus_subtitle">
                        Мы формируем культуру оказания медицинских услуг.
                    </h3>
                    <p class="aboutus_txt">
                        Человек должен быть уверен, что он отдает свое здоровье в надежные руки.
                        Проект призван показать, что работа врачей и клиник не бесконтрольна, а пациент – не беспомощен. Его мнение не только учитывается, но и действует – формирует
                        общественное мнение и, соответственно, влияет на качество оказания медицинской помощи.
                    </p>
                </li>
            </ul>
        </article>

        <article class="aboutus_item i-aboutus_history aboutus_item_history" id="ourstory">
            <h2 class="aboutus_title">
                Наша история
            </h2>
            <p>
                Сервис DocDoc появился в декабре 2011 г. в Москве. Причиной и одновременно поводом создания сервиса стали несколько неудачных визитов будущих создателей DocDoc  к врачу.
            </p>
            <p>
                Оказалось, что врачей существует много, а как найти среди всего этого множества хороших – неизвестно. Основной способ поиска врача – советы друзей. Но иногда у друзей нет в «списке» нужного врача, иногда вкусы на врачей не совпадают, а иногда просто нет этих самых друзей.
            </p>
            <p>
                Так и появился DocDoc – как место, где человек сможет найти себе врача исходя из множества значимых для него критериев, а также отзывов о работе доктора.
            </p>

            <ul class="list aboutus_item_history_footer">
                <li class="list_item">
                    <h3 class="aboutus_title_history">
                        В январе 2012 г. был запущен портал <span class="strong_xl">Diagnostica.DocDoc.ru</span>,
                    </h3>
                    <p class="mvn">
                        который помогает пациентам Москвы и области подбирать диагностические центры по удобным районам и станциям метро.
                    </p>
                </li>
                <li class="list_item">
                    <h3 class="aboutus_title_history">
                        В декабре 2012 г. DocDoc открыл филиал в Санкт-Петербурге.
                    </h3>
                </li>
            </ul>
        </article>

    </section><!-- about -->


</main>




</xsl:template>

</xsl:transform>

