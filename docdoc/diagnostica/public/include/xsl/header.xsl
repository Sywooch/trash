<?xml version="1.0" encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	
	<xsl:param name="headerType" select="'normal'"/>
	<xsl:param name="stat" select="'no'"/>
	<xsl:param name="GA" select="''" />

	<xsl:output method="html" encoding="utf-8"/>
	
	
	<xsl:template match="/">
		<xsl:choose>
			<xsl:when test="$headerType = 'simple'"><xsl:call-template name="headerSimple"/></xsl:when>
			<xsl:when test="$headerType = 'noFix'"><xsl:call-template name="headerNoFix"/></xsl:when>
			<xsl:when test="$headerType = 'noHead'"><xsl:call-template name="headerNoHead"/></xsl:when>
			<xsl:otherwise><xsl:call-template name="header"/></xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template name="headerNoFix">
		<xsl:text disable-output-escaping="yes"><![CDATA[<html> ]]></xsl:text>
		<head>
			<meta http-equiv="Pragma" content="no-cache"/>
			<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
			<link rel="stylesheet" type="text/css" href="/st/css/map/map.css"/>
			<link rel="shortcut icon" href="/st/i/common/favicon.ico" type="image/x-icon"/>
				<![CDATA[
				<!--[if IE]>
					 <meta http-equiv="X-UA-Compatible" content="IE=edge" /> 
				<![endif]-->
				]]>
				<script>
					(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
					(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
					m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
					})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

					ga('create', '<xsl:value-of select="$GA" />', 'auto');
					ga('send', 'pageview');
				</script>
			<title>Поиск диагностических центров по карте</title>
		</head>
		
		<xsl:text disable-output-escaping="yes"><![CDATA[ <body>  ]]></xsl:text>
	</xsl:template>
	
	<xsl:template name="headerNoHead">
	</xsl:template>
	
</xsl:transform>