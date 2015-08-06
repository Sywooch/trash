<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:param name="partnerJsFile" select="''" />
	<xsl:output method="html" encoding="utf-8"/>

	<xsl:template name="partner_js">
		<xsl:if test="$partnerJsFile != ''">
			<script type="text/javascript" src="{$partnerJsFile}"/>
		</xsl:if>

	</xsl:template>

</xsl:transform>
