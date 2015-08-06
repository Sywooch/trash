<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:import href="hotBanners.xsl" />
    <xsl:import href="specList.xsl" />

    <xsl:output method="html" encoding="utf-8"/>

    <xsl:template match="root">
        <xsl:call-template name="thisIllness" />
    </xsl:template>




    <xsl:template name="thisIllness">
        <ul class="analog">
            <li class="title">
                Заболевания
            </li>
            <xsl:for-each select="dbInfo/IllnessLikeList/Element">
                <li>
                    <a href="/illness/{RewriteName}" title="{Name}">
                        <xsl:value-of select="Name"/>
                    </a>
                </li>
            </xsl:for-each>
        </ul>
    </xsl:template>

</xsl:stylesheet>