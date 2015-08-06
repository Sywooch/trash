<?xml version='1.0'  encoding="UTF-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

<xsl:output method="html" encoding="utf-8"/>




<xsl:template name="priceShow">

<xsl:param name="specialPrice" select="'0'" />
<xsl:param name="price" select="'0'" />

<xsl:variable name="actualPrice">
    <xsl:choose>
        <xsl:when test="$price !=''">
            <xsl:value-of select="$price" />
        </xsl:when>
        <xsl:otherwise>
            0
        </xsl:otherwise>
    </xsl:choose>
</xsl:variable>

<span class="js-tooltip-tr" title="Цена указана за первичный прием. В нее не входит стоимость дополнительных исследований и выезда на дом.">
Стоимость приема
<!--<sup class="helper js-tooltip-tr" title="Цена указана за первичный прием. В нее не входит стоимость дополнительных исследований и выезда на дом.">(?)</sup> -->
-
<xsl:choose>
    <xsl:when test="$specialPrice &gt; 0">
        <del class="oldprice">
            <xsl:value-of select="format-number($actualPrice, '#')"/>р.
        </del>
        <ins class="price_special">
            <xsl:value-of select="format-number($specialPrice, '#')"/>р. только на DocDoc!
        </ins>
		<!--
        <sup class="note">
            спеццена docdoc
        </sup>
        -->
    </xsl:when>
    <xsl:otherwise>
        <span class="price">
            <xsl:value-of select="format-number($actualPrice, '#')"/>р.
        </span>
    </xsl:otherwise>
</xsl:choose>
</span>

</xsl:template>

</xsl:transform>

