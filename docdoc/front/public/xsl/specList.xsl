<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">


    <xsl:output method="html" encoding="utf-8"/>


    <xsl:template name="specList">

        <xsl:if test="count(/root/dbInfo/SectorList/Element) &gt; 0">
            <ul>
                <li>
                    <a href="/doctor">
                        Все врачи
                    </a>
                </li>
                <xsl:for-each select="/root/dbInfo/SectorList/Element">
                    <li>
                        <a href="/doctor/{./RewriteName}" title="{Name}">
                            <xsl:choose>
                                <xsl:when test="number(/root/srvInfo/SearchParams/SelectedSpeciality/Id) = number(./@id)">
                                    <strong>
                                        <xsl:value-of select="./Name"/>
                                    </strong>
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


</xsl:transform>