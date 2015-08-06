<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output method="html" encoding="utf-8"/>
	
	<xsl:template name="metroLine">
		<xsl:param name="lineId" />
		
		<xsl:choose>
			<xsl:when test="$lineId = '1'"><i style="background-position: 0 -81px"/></xsl:when>
			<xsl:when test="$lineId = '2'"><i style="background-position: 0 -90px"/></xsl:when>
			<xsl:when test="$lineId = '3'"><i style="background-position: 0 0px"/></xsl:when>
			<xsl:when test="$lineId = '4'"><i style="background-position: 0 -36px"/></xsl:when>
			<xsl:when test="$lineId = '5'"><i style="background-position: 0 -54px"/></xsl:when>
			<xsl:when test="$lineId = '6'"><i style="background-position: 0 -27px"/></xsl:when>
			<xsl:when test="$lineId = '7'"><i style="background-position: 0 -18px"/></xsl:when>
			<xsl:when test="$lineId = '8'"><i style="background-position: 0 -72px"/></xsl:when>
			<xsl:when test="$lineId = '9'"><i style="background-position: 0 -45px"/></xsl:when>
			<xsl:when test="$lineId = '10'"><i style="background-position: 0 -9px"/></xsl:when>
			<xsl:when test="$lineId = '11'"><i style="background-position: 0 -63px"/></xsl:when>
			<xsl:when test="$lineId = '12'"><i style="background-position: 0 -99px"/></xsl:when>
		</xsl:choose>
	</xsl:template>
	
	
	
	<xsl:template name="digitVariant">
		<xsl:param name="one" />
		<xsl:param name="two" />
		<xsl:param name="five" />
		<xsl:param name="digit" />
		
		<xsl:choose>
			<xsl:when test="number($digit) = 1"><xsl:copy-of select="$one"/></xsl:when>
			<xsl:when test="number($digit) &gt; 1 and number($digit) &lt;= 5"><xsl:copy-of select="$two"/></xsl:when>
			<xsl:when test="number($digit) &gt; 5 and number($digit) &lt;= 9"><xsl:copy-of select="$five"/></xsl:when>
			<xsl:otherwise><xsl:copy-of select="$five"/></xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
</xsl:transform>

