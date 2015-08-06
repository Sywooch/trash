<?xml version="1.0" encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	
	<xsl:param name="headerType" select="'normal'"/>
	<xsl:param name="stat" select="'no'"/>
	
	<xsl:output method="html" encoding="utf-8" />
	
	<xsl:template match="/"> 
		
		<xsl:choose>
			<xsl:when test="$headerType = 'simple'"><xsl:call-template name="footerSimple"/></xsl:when>
			<xsl:when test="$headerType = 'noFix'"><xsl:call-template name="footerNoFix"/></xsl:when>
			<xsl:when test="$headerType = 'noHead'"><xsl:call-template name="footerNoHead"/></xsl:when>
			<xsl:otherwise><xsl:call-template name="footer"/></xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	
	<xsl:template name="footer">	
		<xsl:text disable-output-escaping="yes">
			<![CDATA[
					</body>
				</html>
			]]>
		</xsl:text>
	</xsl:template>	  
	
	
	
	
	<xsl:template name="footerSimple">	
		<xsl:text disable-output-escaping="yes">
			<![CDATA[
				</body>
			</html>
			]]>
		</xsl:text>
	</xsl:template>
	
	
	
	
	
	<xsl:template name="footerNoFix">	
			<xsl:if test="$stat = 'yes'">
			<![CDATA[
				<!-- Yandex.Metrika counter -->
				<script type="text/javascript">
				(function (d, w, c) {
				    (w[c] = w[c] || []).push(function() {
				        try {
				            w.yaCounter15482359 = new Ya.Metrika({id:15482359, enableAll: true, webvisor:true});
				        } catch(e) {}
				    });
				    
				    var n = d.getElementsByTagName("script")[0],
				        s = d.createElement("script"),
				        f = function () { n.parentNode.insertBefore(s, n); };
				    s.type = "text/javascript";
				    s.async = true;
				    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";
				
				    if (w.opera == "[object Opera]") {
				        d.addEventListener("DOMContentLoaded", f);
				    } else { f(); }
				})(document, window, "yandex_metrika_callbacks");
				</script>
				<noscript><div><img src="//mc.yandex.ru/watch/15482359" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
				<!-- /Yandex.Metrika counter -->
			]]>
			</xsl:if>
			<xsl:text disable-output-escaping="yes">
			<![CDATA[
				</body>
			</html>
			]]>
		</xsl:text>
	</xsl:template>
	
	
	
	
	
	<xsl:template name="footerNoHead">	
	</xsl:template>
</xsl:transform>