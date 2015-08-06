<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

    <xsl:output method="html" encoding="utf-8"/>

    <xsl:template name="declension">
        <xsl:param name="one" />
        <xsl:param name="two" />
        <xsl:param name="five" />
        <xsl:param name="digit" />

        <xsl:variable name="lastDigit" select="number($digit) - floor(number($digit) div 10)*10"/>
        <xsl:variable name="lastTwoDigit" select="number($digit) - floor(number($digit) div 100)*100"/>

        <!--  <xsl:value-of select="$lastTwoDigit"/>-->
        <xsl:choose>
            <xsl:when test="$lastTwoDigit &gt; 11 and $lastTwoDigit &lt;= 20 ">
                <xsl:copy-of select="$five" />
            </xsl:when>
            <xsl:when test="$lastDigit = 1">
                <xsl:copy-of select="$one" />
            </xsl:when>
            <xsl:when test="$lastDigit &gt; 1 and $lastDigit &lt;= 5">
                <xsl:copy-of select="$two" />
            </xsl:when>
            <xsl:when test="$lastDigit &gt; 5 and $lastDigit &lt;= 9">
                <xsl:copy-of select="$five" />
            </xsl:when>
            <xsl:otherwise>
                <xsl:copy-of select="$five" />
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
</xsl:transform>