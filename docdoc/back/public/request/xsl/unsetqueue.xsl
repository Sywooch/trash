<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:output method="html" encoding="utf-8"/>


	<xsl:template match="/">
		<xsl:apply-templates select="root"/>
		<script>
			
		</script>
	</xsl:template>




	<xsl:template match="root">
		<div style="width: 200px; padding: 20px;">
			<div class='loader32'/>
		</div>
	</xsl:template>

</xsl:transform>

