<?xml version="1.0" encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	
	<xsl:param name="headerType" select="'normal'"/>
	<xsl:param name="footer" select="'normal'"/>
	
	<xsl:output method="html" encoding="utf-8" />
	
	<xsl:template match="/"> 
		<xsl:choose>
			<xsl:when test="$headerType = 'simple'"><xsl:call-template name="footerSimple"/></xsl:when>
			<xsl:when test="$headerType = 'noHead'"><xsl:call-template name="footerNoHead"/></xsl:when>
			<xsl:otherwise><xsl:call-template name="footer"/></xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	
	<xsl:template name="footer">	
		<xsl:text disable-output-escaping="yes">
			<![CDATA[
							</div>
							<div id="hfooter"></div>
						</div>
						<!-- <div id="footer"><h1>Система администрирования контента</h1></div> -->
					</body>
				</html>
			]]>
		</xsl:text>
	</xsl:template>	  
	
	
	
	
	<xsl:template name="footerSimple">	
		<xsl:text disable-output-escaping="yes">
			<![CDATA[
							</div>
						</div>
					</div>
				</body>
			</html>
			]]>
		</xsl:text>
	</xsl:template>
	
	
	
	
	
	<xsl:template name="footerNoHead">	
	</xsl:template>
</xsl:transform>