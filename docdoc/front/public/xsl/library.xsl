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

    <xsl:output method="html" encoding="utf-8"/>



    <xsl:template match="root">
        <xsl:call-template name="staticLibrary" />
    </xsl:template>





<xsl:template name="staticLibrary">

<main class="l-main l-wrapper" role="main">

<div class="has-aside">

    <h1>
        Справочник пациента
    </h1>

	<ul class="library_list columns_3">
		<xsl:for-each select="dbInfo/ArticleGroupList/Group">
			<li class="column">
				<ul>
					<xsl:for-each select="Element">
						<li class="library_list_item">
							<a class="library_list_link" href="library/{./RewriteName}">
								<xsl:value-of select="./Name" />
								<span class="library_list_count">
									<xsl:value-of select="./Count" />
								</span>
							</a>
						</li>
					</xsl:for-each>
				</ul>
			</li>
		</xsl:for-each>
	</ul><!-- library_list end -->

    <h2>
        Новые статьи
    </h2>
    <section>
    <xsl:for-each select="dbInfo/ArticleNoGroupList/Element">
        <xsl:choose>
            <xsl:when test="position() &lt; 6">
                <article>
                    <h2>
                        <a href="/library/{./RewriteName}">
                            <xsl:value-of select="./Name"/>
                        </a>
                    </h2>
                    <p>
                        <xsl:value-of select="./Description" />
                    </p>
                </article>
            </xsl:when>
            <xsl:otherwise>
                <article>
                    <h2>
                        <a href="/library/{./RewriteName}">
                            <xsl:value-of select="./Name"/>
                        </a>
                    </h2>
                    <p>
                        <xsl:value-of select="./Description" />
                    </p>
                </article>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:for-each>
    </section>

    <!--
    <div class="">
        ??? Тут дисклеймер типа на нашем портале вы можете подобрать не только врача но и медцентр и список этих медцентров!
    </div>
    -->

</div><!-- has-aside -->

<aside class="l-aside">
    <xsl:call-template name="doctorsBest" />
    <xsl:call-template name="listSpecUnderspec" />
    <xsl:call-template name="listSpecDiag" />
    <xsl:call-template name="listSpec" />
    <xsl:call-template name="asideBanners" />
    <!--<xsl:call-template name="listSpecIllness" />-->
</aside>

<xsl:value-of  select="/root/dbHeadInfo/SEO/Texts/Element[Position='2']/Text" />


</main>

</xsl:template>

</xsl:stylesheet>