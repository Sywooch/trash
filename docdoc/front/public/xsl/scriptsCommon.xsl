<?xml version="1.0" encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:output method="html" encoding="utf-8" />


	<xsl:template name="scriptsCommon">

		<![CDATA[
        <!--[if lte IE 9]>
            <script src="/js/ie9lte.js"></script>
            <link rel="stylesheet" href="/css/ielove/ie9lte.css" />
        <![endif]-->
        ]]>

		<!-- sripts for both mobile and desktop browsers -->
		<script src="/js/plugins.js"></script>
		<script src="/js/plugin/json2.js"></script>
		<script src="/js/plugin/jquery.raty.js"></script>

		<link rel="stylesheet" href="/css/metro.css" />

		<script src="/js/plugin/jquery.maskedinput.min.js"></script>
		<script src="/js/plugin/social-likes.min.js"></script>
		<script src="/js/plugin/validate.js"></script>
		<script src="/js/plugin/validate_additional_methods.js"></script>
		<script src="/js/jquery.bxslider/jquery.bxslider.min.js"/>

		<!-- sripts for both mobile and desktop browsers end -->

	</xsl:template>

	<xsl:template name="scriptsMobile">

		<script src="/js/stat/ga.js"/>
		<script src="/js/main.js"></script>
		<script src="/js/mobile.js"></script>

	</xsl:template>

	<xsl:template name="scriptsDesktop">
		<script src="/js/plugin/jquery-ui-1.10.3.min.js"></script>
		<link rel="stylesheet" href="/css/ui/ui-lightness/jquery-ui-1.10.3.custom.min.css" />

		<xsl:choose>
			<xsl:when test="/root/dbHeadInfo/City/@id = '1'">
				<script src="/js/metro.js"></script>
				<script src="/js/extended_search.js"></script>
			</xsl:when>
			<xsl:otherwise>
				<script src="/js/search_geo.js"></script>
			</xsl:otherwise>
		</xsl:choose>

		<script src="/js/ddpopup.js"></script>
		<script src="/js/stat/ga.js"/>
		<script src="/js/main.js"></script>
	</xsl:template>

	<xsl:template name="scriptsYmaps">

		<script src="/js/maps.js"></script>
		<!--
		<script src="http://api-maps.yandex.ru/2.0-stable/?load=package.standard&amp;lang=ru-RU" type="text/javascript"></script> -->

		<!--
		<script src="http://api-maps.yandex.ru/2.0-stable/?load=package.standard&amp;lang=ru-RU&amp;loadByRequire=1" type="text/javascript"></script>-->

		<!--
		<script type="text/javascript">
			(function() {
			var d=document,
			h=d.getElementsByTagName('head')[0],
			s=d.createElement('script');
			s.type='text/javascript';
			s.async=true;
			s.src='http://api-maps.yandex.ru/2.0-stable/?load=package.standard&amp;lang=ru-RU&amp;callback=createmap';
			h.appendChild(s);
			}());
		</script>
		-->

		<!--
		<script type="text/javascript">
			//<![CDATA[
(function(id) {
 document.write('<script type="text/javascript" src="' +
   'http://api-maps.yandex.ru/2.0-stable/?load=package.standard&amp;lang=ru-RU' + '"></' + 'script>');
})();
//]]>
		</script>
		-->

	</xsl:template>

</xsl:transform>