<?xml version="1.0" encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	
	<xsl:output method="html" encoding="utf-8"/>
	
	
	<xsl:template name="modalWindow">
		<xsl:param name="id" select="'modalWin'"/>
		<xsl:param name="title" select="'Заголовок окна'"/>
		<xsl:param name="width" select="'400'"/>
		<xsl:param name="deltaX" select="'-200'"/>
		<xsl:param name="deltaY" select="'100'"/>
		
		
		<link rel="stylesheet" type="text/css" href="/css/modalWindow.css" media="screen"/>
		<script type="text/javascript" src="/lib/js/jquery.easydrag.js"></script>
		<script type="text/javascript" src="/lib/js/jquery.bgiframe.js"></script>
		<script language="JavaScript" type="text/javascript">
			var modalWinKey = 'close'; /* close or reload	*/ 
			var windowCenterX;
			var windowCenterY;
			var deltaX = -200;
			var deltaY = 100;
			
			$(document).ready(function(){ 
				$("#<xsl:value-of select="$id"/>").appendTo('body');
				$("#<xsl:value-of select="$id"/>").css({'position':'absolute','top':'100px', 'z-index':'100'});
				$("#<xsl:value-of select="$id"/>").easydrag();
				$("#<xsl:value-of select="$id"/>").setHandler("easydragSq_<xsl:value-of select="$id"/>");
				$("#<xsl:value-of select="$id"/>").bgiframe(); 	
				
				var H;
				var W;
				
				if (window.innerHeight) { H = window.innerHeight }
				else if (document.documentElement &amp;&amp;  document.documentElement.clientHeight) {H = document.documentElement.clientHeight }
				else if (document.body) { H = document.body.clientHeight }
				
				windowCenterY = Math.floor(H/2) + deltaX ;
//				windowCenterY = getClientCenterY();
				
				if (window.innerWidth) { W = window.innerWidth }
				else if (document.documentElement &amp;&amp;  document.documentElement.clientWidth) {W = document.documentElement.clientWidth }
				else if (document.body) { W = document.body.clientWidth }
				windowCenterX = Math.floor(W/2) + deltaY ;
			});
			
			<![CDATA[
			function getClientCenterY() {
			    return parseInt(getClientHeight()/2)+getBodyScrollTop();
			}
			
			function getBodyScrollTop() {
			    return self.pageYOffset || (document.documentElement && document.documentElement.scrollTop) || (document.body && document.body.scrollTop);
			}

			
			function getDocumentHeight() {
			    return (document.body.scrollHeight > document.body.offsetHeight)?document.body.scrollHeight:document.body.offsetHeight;
			}
			
			
			function getClientHeight() {
				  return document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientHeight:document.body.clientHeight;
			}
			]]>
		</script>
		<div id="{$id}" class="modWin wb" style="width: {$width}px;">
			<div class="modWinTitle" id="easydragSq_{$id}"><h1><xsl:value-of select="$title"/></h1></div>
			<div id="popUp" class="modWinContent"></div>
			
			<img src="/img/common/clBtBig.gif" width="20" height="20"  alt="закрыть" title="закрыть1" class="modWinClose" sonclick="(modalWinKey ==='close')?$('#{$id}').hide():window.location.reload();" onclick="$('#{$id}').hide()" border="0"/>
		</div>
	</xsl:template>

</xsl:transform>

