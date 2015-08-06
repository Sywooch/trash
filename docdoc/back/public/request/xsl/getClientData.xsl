<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:import href="request.xsl"/>
	<xsl:output method="html" encoding="utf-8"/>

	<xsl:key name="type" match="/root/dbInfo/TypeDict/Element" use="@id"/>
	<xsl:key name="status" match="/root/dbInfo/StatusDict/Element" use="@id"/>

	<xsl:template match="/">
		<xsl:apply-templates select="root"/>
	</xsl:template>


	<xsl:template match="root">
		<xsl:call-template name="clientData">
			<xsl:with-param name="context" select="dbInfo/Request"/>
		</xsl:call-template>
	</xsl:template>

</xsl:transform>

