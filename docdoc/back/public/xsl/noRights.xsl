<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:output method="html" version="4.0" indent="yes" encoding="utf-8" omit-xml-declaration="yes"/>
	
	
	<xsl:template match="/">
		<xsl:apply-templates select="root"/>
	</xsl:template>
	
	
	
	
	<xsl:template match="root">
		<div align="center" class="error" style="margin: 100px 0 100px 0">Вы не имеете необходимых прав. Обратитесь к администратору системы.</div>
	</xsl:template>
</xsl:transform>

