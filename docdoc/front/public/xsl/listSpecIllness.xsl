<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:output method="html" encoding="utf-8"/>

<xsl:template name="listSpecIllness">
<xsl:if test="/root/dbInfo/IllnessLikeList">
<h3>Заболевания</h3>
<ul class="related_list">
    <xsl:for-each select="/root/dbInfo/IllnessLikeList/Group/Element[not(/root/dbInfo/Illness/Element/@id) or @id != /root/dbInfo/Illness/Element/@id]">
        <!--<xsl:for-each select="/root/dbInfo/IllnessLikeList/Element">-->
            <li class="related_item">
                <a href="/illness/{RewriteName}" title="{Name}" class="related_link">
                    <xsl:value-of select="Name"/>
                </a>
            </li>
        </xsl:for-each>
    </ul>
</xsl:if>
    </xsl:template>

    </xsl:stylesheet>