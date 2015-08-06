<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:import href="../xsl/doctorsTable.xsl" />

    <xsl:output method="html" encoding="utf-8"/>

    <xsl:template match="root">
        <xsl:call-template name="context" />
    </xsl:template>




    <xsl:template name="context">

        <main class="l-main l-wrapper" role="main">

	        <xsl:call-template name="doctorsTableIndex" />

            <!--
            <div class="search_onmap">
                <a href="#" class="search_onmap_link">
                    <img src="img/common/onmap_msk.png" alt="Все врачи на карте" title="Все врачи на карте" />
                </a>
            </div>
            -->

            <section class="about track_links">
                <ul class="about_list_short">
                    <li class="about_item i-closetohome">
                        <h3>
                            <!--<a href="#" class="about_link"></a>-->
                            Нужен врач поближе к дому?
                        </h3>
                        <p class="mvn">
                            Ищите врачей, указав ближайшую станцию метро. Система подберет врачей, работающих в вашем районе.
                        </p>
                    </li>
                    <li class="about_item i-ratingsystem">
                        <h3>
                            Система рейтингов
                        </h3>
                        <p class="mvn">
                            Все наши врачи рейтингуются по множеству параметров. Вы сами можете оставить отзыв и оценку врачу.
                        </p>
                    </li>
                    <li class="about_item i-sortbyprice">
                        <h3>
                            Отсортируйте врачей по цене
                        </h3>
                        <p class="mvn">
                            Все наши врачи занесены в базу с указанием стоимости приема. Вы можете найти специалиста, ориентируясь на устраивающую вас цену.
                        </p>
                    </li>
                </ul>
                <ul class="about_list i-doctor_l">
                    <li class="about_item">
                        <h3>
                            <a href="{/root/dbHeadInfo/City/Diagnostica}" class="about_link i-diagcenters" target="_blank">
                                Диагностические центры
                            </a>
                        </h3>
                        <p>
                            Вам нужно сдать анализы или провести обследование? Специализированный портал поможет Вам найти нужный центр.
                        </p>
                    </li>
                    <li class="about_item">
                        <h3>
                            <a href="/library" class="about_link i-pacientlib">
                                Медицинская библиотека
                            </a>
                        </h3>
                        <p>
                            Мы собрали для вас массу полезных статей о врачах, медицинских направлениях, современных методах лечения и диагностики.
                        </p>
                    </li>
                    <li class="about_item">
                        <h3>
                            <a href="/illness" class="about_link i-sicklist">
                                Справочник заболеваний
                            </a>
                        </h3>
                        <p>
                            Здесь Вы можете подобрать врача, который специализируется на лечении конкретного заболевания.
                        </p>
                    </li>
                </ul>
            </section>

        </main>

    </xsl:template>

</xsl:stylesheet>
