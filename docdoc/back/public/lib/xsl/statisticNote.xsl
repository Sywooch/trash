<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output method="html" encoding="utf-8"/>
	
	
	<xsl:template name="statisticNote">
		<xsl:param name="body"/>
		<xsl:param name="width" select="'150px'"/>
		
		
		<div style="position: relative">
			<div style="position: absolute; left:5px; top: 5px;  z-index: 2;" >
				
				<div id="statisticNote" style="margin:0; padding: 4px 14px 1px 10px; background-color: #fff; line-height: 18px" class="wb shd">
					<div class="m0050" style="width: {$width}">
						<span class="link" onclick="$('#statisticNote .statisticBodyLine').toggle()">Краткая сводка</span>
					</div>
					<div class="m0 hd statisticBodyLine">
						<xsl:copy-of select="$body"/>
					</div>
					
					<div class="marker"/>
				</div>
				
			</div>
		</div>
	</xsl:template>

</xsl:transform>

