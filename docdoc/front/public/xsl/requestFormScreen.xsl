<?xml version='1.0'  encoding="UTF-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

    <xsl:import href="requestForm.xsl"/>

    <xsl:decimal-format decimal-separator = '.' grouping-separator = ' ' NaN = ' '/>

    <xsl:output method="html" encoding="utf-8"/>


   <xsl:template match="/">
		<xsl:apply-templates select="root"/>
   </xsl:template>


   <xsl:template match="root">
       <xsl:call-template name="requestForm">
           <xsl:with-param name="doctor" select="dbInfo/Doctor"/>
       </xsl:call-template>
   </xsl:template>
</xsl:transform>
