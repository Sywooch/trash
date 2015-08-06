<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:output method="html" encoding="utf-8"/>

    <xsl:template name="listSpecClinics">

        <xsl:if test="count(/root/dbInfo/SpecializationList/Group/Element) &gt; 0">
            <h3>Направления:</h3>
            <ul class="related_list">
                <!--<li class="related_item">
                    <a href="/clinic">
                        Все клиники
                    </a>
                </li>-->
                <xsl:for-each select="/root/dbInfo/SpecializationList/Group/Element">
                    <li class="related_item">
                        <a href="/clinic/spec/{./RewriteName}" title="{Name}" class="related_link">
                            <xsl:choose>
                                <xsl:when test="number(/root/srvInfo/SearchParams/SelectedSpeciality/Id) = number(./@id)">
                                    <xsl:value-of select="./Name"/>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:value-of select="./Name"/>
                                </xsl:otherwise>
                            </xsl:choose>
                        </a>
                    </li>
                </xsl:for-each>
            </ul>
        </xsl:if>

    </xsl:template>



</xsl:stylesheet>