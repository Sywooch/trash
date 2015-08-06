<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:output method="html" encoding="utf-8"/>
	
	
	
	
	
	<xsl:template name="ceilInfo">	  
		<xsl:param name="id"/>
		<xsl:param name="contextImg"/>
		
		<div id="ceilWin_{$id}" class="helpElt hd">	  
			<div style="margin:0;">
				<xsl:if test="$contextImg != ''">
					<img src="{$contextImg}"/>
				</xsl:if>
			</div>
		</div>
	</xsl:template>
	
</xsl:transform>

