<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:import href="specList.xsl" />
    <xsl:import href="doctorsList.xsl" />
    <xsl:import href="../lib/xsl/common.xsl" />
    <xsl:import href="listSpecIllness.xsl" />
    <xsl:import href="listSpecUnderspec.xsl" />
    <xsl:import href="listSpecDiag.xsl" />
    <xsl:import href="listSpec.xsl" />
    <xsl:import href="doctorsBest.xsl" />
    <xsl:import href="asideBanners.xsl" />

    <xsl:decimal-format decimal-separator = '.' grouping-separator = ' ' NaN = ' '/>

    <xsl:key name="sector" match="/root/dbInfo/SectorList/Element" use="@id"/>

    <xsl:output method="html" encoding="utf-8"/>

    <xsl:template match="root">
        <xsl:call-template name="illnessSection" />
    </xsl:template>




    <xsl:template name="illnessSection">


        <main class="l-main l-wrapper" role="main">

            <div class="has-aside library">

                <ul class="breadcrumbs">
                    <li class="breadcrumbs_item">
                    <a href="/library">Справочник пациента</a>
                    </li>
                    <li class="breadcrumbs_item">
                    <span class="breadcrumbs_arrow"></span>
                    <xsl:value-of select="dbInfo/ArticleSection/Name"/>
                    </li>
                </ul>

                <h1>
                    <xsl:value-of select="dbInfo/ArticleSection/Name"/>
                </h1>
                <xsl:copy-of select="dbInfo/ArticleSection/Text"/>


                <h2 class="i-information">
                    Памятка пациента
                </h2>


                <ul>
                <xsl:for-each select="dbInfo/ArticleList/Element">
                    <li>
                        <h2>
                            <a href="/library/{/root/dbInfo/ArticleSection/Alias}/{Alias}"><xsl:value-of select="Name"/></a>
                        </h2>
                        <p>
                            <xsl:copy-of select="Description"/>
                        </p>
                    </li>
                </xsl:for-each>
                </ul>

                <h2 class="i-library">
                    <xsl:value-of select="dbInfo/ArticleSection/Name"/> – заболевания
                </h2>

                <ul class="t-fs-n columns_2">

                    <xsl:choose>
                    <xsl:when test="dbInfo/IllnessLikeList/Group/Element">

                        <xsl:for-each select="dbInfo/IllnessLikeList/Group">
							<li class="column">
								<ul>
									<xsl:for-each select="Element">
										<li class="mvs">
											<a href="/illness/{RewriteName}"><xsl:value-of select="Name"/></a>
										</li>
									</xsl:for-each>
								</ul>
							</li>
                        </xsl:for-each>

                    </xsl:when>
                    <xsl:otherwise>
                        <li class="">
                            <p>Заболеваний нет</p>
                        </li>
                    </xsl:otherwise>
                    </xsl:choose>

                </ul>

                <xsl:if test="number(/root/dbInfo/DoctorCount) &gt; 0"><!-- we got %count% doctors and doctors list here -->
                    <h3 class="h1 i-doctor_5">
                        В нашей базе
                        <span class="t-orange t-fs-xl">
                            <xsl:value-of select="dbInfo/DoctorCount"/>&#160;</span>
                            <xsl:call-template name="digitVariant">
                            <xsl:with-param name="digit" select="dbInfo/DoctorCount"/>
                            <xsl:with-param name="one" select="'специалист'"/>
                            <xsl:with-param name="two" select="'специалиста'"/>
                            <xsl:with-param name="five" select="'специалистов'"/>
                        </xsl:call-template>
                        по лечению <xsl:value-of select="dbInfo/Illness/Element/FullName"/>
                    </h3>

                    <section class="doctor_list">
                        <xsl:for-each select="dbInfo/DoctorList/Element">
                            <xsl:call-template name="doctorShortCard">
                                <xsl:with-param name="context" select="."/>
                            </xsl:call-template>
                        </xsl:for-each>
                    </section>

                </xsl:if><!-- we got %count% doctors and doctors list here END -->

            </div><!-- has-aside -->

            <aside class="l-aside">
                <xsl:call-template name="doctorsBest" />
                <xsl:call-template name="listSpecUnderspec" />
                <xsl:call-template name="listSpecDiag" />
                <xsl:call-template name="listSpec" />
                <xsl:call-template name="asideBanners" />
                <!--
                <xsl:call-template name="listSpecIllness" />
                -->
            </aside>


            <xsl:if test="number(/root/dbInfo/DoctorCount) &gt; 0"><!-- we got %count% doctors and doctors list here -->
                <h3 class="h1 i-doctor_5">
                    В нашей базе
                    <span class="t-orange t-fs-xl">
                        <xsl:value-of select="dbInfo/DoctorCount"/>
                    </span>&#160;
                    <xsl:call-template name="digitVariant">
                    <xsl:with-param name="digit" select="dbInfo/DoctorCount"/>
                    <xsl:with-param name="one" select="'специалист'"/>
                    <xsl:with-param name="two" select="'специалиста'"/>
                    <xsl:with-param name="five" select="'специалистов'"/>
                </xsl:call-template>
                    по лечению <xsl:value-of select="dbInfo/Illness/Element/FullName"/>
                </h3>

                <section class="doctor_list">
                    <xsl:for-each select="dbInfo/DoctorList/Element">
                        <xsl:call-template name="doctorShortCard">
                            <xsl:with-param name="context" select="."/>
                        </xsl:call-template>
                    </xsl:for-each>
                </section>

            </xsl:if><!-- we got %count% doctors and doctors list here END -->
        </main>

    </xsl:template>

</xsl:stylesheet>