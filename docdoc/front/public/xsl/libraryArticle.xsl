<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">


    <xsl:import href="listSpecIllness.xsl" />
    <xsl:import href="listSpecUnderspec.xsl" />
    <xsl:import href="listSpecDiag.xsl" />
    <xsl:import href="listSpec.xsl" />
    <xsl:import href="doctorsBest.xsl" />
    <xsl:import href="doctorsList.xsl" />
    <xsl:import href="../lib/xsl/common.xsl" />
    <xsl:import href="asideBanners.xsl" />

 	<xsl:decimal-format decimal-separator = '.' grouping-separator = ' ' NaN = ' '/>

 	<xsl:key name="sector" match="/root/dbInfo/SectorList/Element" use="@id"/>

    <xsl:output method="html" encoding="utf-8"/>



    <xsl:template match="root">
        <xsl:call-template name="staticLibrary" />
    </xsl:template>




<xsl:template name="staticLibrary">
<main class="l-main l-wrapper" role="main">

    <div class="has-aside illness">

        <xsl:if test="dbInfo/ArticleSection">
            <ul class="breadcrumbs">
                <li class="breadcrumbs_item">
                    <a href="/library/{dbInfo/ArticleSection/Alias}">
                        <xsl:value-of select="dbInfo/ArticleSection/Name"/>
                    </a>
                </li>
                <li class="breadcrumbs_item">
                    <span class="breadcrumbs_arrow"></span>
                    <xsl:value-of select="dbInfo/Article/Name"/>
                </li>
            </ul>

            <!--
            <a href="/library/{dbInfo/ArticleSection/Alias}">
                <xsl:value-of select="dbInfo/ArticleSection/Name"/>
            </a>
            -->
        </xsl:if>

        <h1>
            <xsl:value-of select="dbInfo/Article/Name"/>
        </h1>


        <div class="static_content">
        <xsl:copy-of select="dbInfo/Article/Text"/>
        </div>

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
            </h3>

            <section class="doctor_list">
                <xsl:for-each select="dbInfo/DoctorList/Element">
                    <xsl:call-template name="doctorShortCard">
                        <xsl:with-param name="context" select="."/>
                    </xsl:call-template>
                </xsl:for-each>

                <a class="mtm l-ib" href="/doctor/{key('sector',dbInfo/SectorId)/RewriteName}">
                    Показать всех специалистов
                </a>
            </section>

        </xsl:if><!-- we got %count% doctors and doctors list here END -->


    </div><!-- has-aside -->

    <aside class="l-aside">
        <xsl:call-template name="doctorsBest" />
        <xsl:call-template name="listSpecUnderspec" />
        <xsl:call-template name="listSpecDiag" />
        <xsl:call-template name="listSpec" />
        <xsl:call-template name="listSpecIllness" />
        <xsl:call-template name="asideBanners" />
    </aside>

    <xsl:value-of select="/root/dbHeadInfo/SEO/Texts/Element[Position='2']/Text" />


</main>
</xsl:template>

</xsl:stylesheet>