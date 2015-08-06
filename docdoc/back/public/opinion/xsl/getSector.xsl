<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	   
	<xsl:import href="../../lib/xsl/common.xsl"/> 
	
	<xsl:param name="debug" select="'no'"/>
	
	<xsl:output method="html" version="4.0" indent="yes" encoding="utf-8" omit-xml-declaration="yes"/>
	
	
	<xsl:template match="/">	
		
		<xsl:apply-templates select="root"/>
		  
	</xsl:template>
	
	
	
	
	<xsl:template match="root">	
		<xsl:for-each select="dbInfo/SectorList/Sector">
			<xsl:value-of select="."/>
			<xsl:if test="position() != last()">, </xsl:if>
		</xsl:for-each>
	</xsl:template>
 
</xsl:transform>

