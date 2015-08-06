<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	
	<xsl:output method="html" encoding="utf-8"/>
	
	<xsl:template name="statusState">

		<xsl:param name="style" select="''"/>
		<xsl:param name="class" select="''"/>
		<xsl:param name="withName" select="'yes'"/>
		<xsl:param name="name" select ="''"/>
		
		
		<span class="" title="{$name}">
			<xsl:attribute name="class">i-status <xsl:value-of select="$class"/></xsl:attribute>
			<xsl:if test="$style != ''">
				<xsl:attribute name="style"><xsl:value-of select="$style"/></xsl:attribute>
			</xsl:if>
			
			<xsl:if test="$withName = 'yes'">
				<xsl:value-of select="$name"/>
			</xsl:if>	
		</span>
	</xsl:template>
	
	
	
	
	 <xsl:template name="digitVariant">
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

