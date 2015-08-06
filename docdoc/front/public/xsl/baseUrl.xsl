<?xml version='1.0'  encoding="UTF-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

    <xsl:template name="baseUrl">

        <xsl:variable name="baseUrl">/doctor<xsl:if test="/root/srvInfo/SearchParams/SelectedSpeciality/Alias">/<xsl:value-of select="/root/srvInfo/SearchParams/SelectedSpeciality/Alias"/>
        	</xsl:if>

            <xsl:if test="/root/srvInfo/SearchParams/SelectedStations">/stations/<xsl:for-each select="/root/srvInfo/SearchParams/SelectedStations/Element">
                <xsl:value-of select="@id"/>
                <xsl:if test="position() != last()">,</xsl:if>
            </xsl:for-each>
            </xsl:if>

            <xsl:if test="/root/srvInfo/SearchParams/Area">/area/<xsl:value-of select="/root/srvInfo/SearchParams/Area/Alias"/></xsl:if>

			<xsl:if test="/root/srvInfo/SearchParams/District and /root/dbHeadInfo/City/@id != 1">/district/<xsl:value-of select="/root/srvInfo/SearchParams/District/Alias"/></xsl:if>

			<xsl:if test="/root/srvInfo/SearchParams/District and /root/dbHeadInfo/City/@id = 1">/<xsl:value-of select="/root/srvInfo/SearchParams/District/Alias"/></xsl:if>

        </xsl:variable>

    </xsl:template>





</xsl:transform>