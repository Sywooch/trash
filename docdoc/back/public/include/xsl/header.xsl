<?xml version="1.0" encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">


	
	<xsl:param name="headerType" select="'normal'"/>

	<xsl:output method="html" encoding="utf-8"/>
	
	
	<xsl:template match="/">
		<xsl:choose>
			<xsl:when test="$headerType = 'simple'"><xsl:call-template name="headerSimple"/></xsl:when>
			<xsl:when test="$headerType = 'noLogin'"><xsl:call-template name="headerSimple"/></xsl:when>
			<xsl:when test="$headerType = 'noHead'"><xsl:call-template name="headerNoHead"/></xsl:when>
			<xsl:otherwise><xsl:call-template name="header"/></xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	
	
	
	<xsl:template name="header">
		<xsl:text disable-output-escaping="yes"><![CDATA[<html> ]]></xsl:text>
			<head>
				<meta http-equiv="Pragma" content="no-cache"/>
				<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
				<link rel="stylesheet" type="text/css" href="/css/main.css" title="general"/>
				<link rel="stylesheet" type="text/css" href="/css/personal.css" title="general"/>
				<![CDATA[<![if IE]>
				    <link rel="stylesheet" type="text/css" href="/css/ieFix.css" title="generalIE"/>
				<![endif]>]]>
				<script language="JavaScript" type="text/javascript" src="{/root/srvInfo/jqueryPath}"></script>
				<script language="JavaScript" type="text/javascript" src="/js/main.js"></script>
				<title>docdoc.ru - система администрирования данных</title>
			</head>

			<xsl:text disable-output-escaping="yes"><![CDATA[ <body>  ]]></xsl:text>

			<div id="personalPanel">

				<div id="serverName" onclick="window.location.href='/'" style="cursor: pointer">
					<span id="docdoc">DocDoc</span> Back Office

					<div id="serverVersion">
						v<xsl:value-of select="/root/srvInfo/Version" />
					</div>
				</div>

				<div id="topMenu">

					<div class="personalNameOut">
						<span class="linkDotted" onclick="getPersonalData(this)"><xsl:value-of select="concat(/root/srvInfo/UserData/LastName,' ',/root/srvInfo/UserData/FirstName)"/></span>
					</div>

					<div id="supportReportPanel">
						<span class="linkDotted" id="supportReportScreen" onclick="getSupportReportScreen(this)">Сообщить о проблеме</span>
						<xsl:call-template name="ceilInfo">
							<xsl:with-param name="id" select="'SupportReportPanel'"/>
							<xsl:with-param name="style">text-align: left; width: 550px</xsl:with-param>
						</xsl:call-template>
					</div>

					<div id="cityPanel">
						Город:
						<strong onclick="changeCity(this)">
							<span class="linkDotted" style="cursor: pointer">
								<xsl:value-of select="/root/srvInfo/City"/>
							</span>
						</strong>
						<xsl:call-template name="ceilInfo">
							<xsl:with-param name="id" select="'City'"/>
							<xsl:with-param name="style">text-align: left; width: 150px</xsl:with-param>
							<xsl:with-param name="body">
								<div>
									<xsl:for-each select="/root/srvInfo/CityList/Element">
										<div>
											<xsl:choose>
												<xsl:when test="/root/srvInfo/City/@id != Id">
													<span>
														<xsl:attribute name="class">link</xsl:attribute>
														<xsl:attribute name="onclick">setCity('<xsl:value-of select="Id" />')</xsl:attribute>
														<xsl:value-of select="Name" /></span>
												</xsl:when>
												<xsl:otherwise>
													<span><xsl:value-of select="Name" /></span>
													<img src="/img/icon/ok.png" style="margin-left: 5px" align="absbottom"/>
												</xsl:otherwise>
											</xsl:choose>
										</div>
									</xsl:for-each>
								</div>
							</xsl:with-param>
						</xsl:call-template>
					</div>

					<div id="userSip">
						SIP:
						<xsl:choose>
							<xsl:when test="/root/srvInfo/Queue4User">
								<b><xsl:value-of select="/root/srvInfo/Queue4User/@sip"/></b>
								[<span class="green"><xsl:value-of select="/root/srvInfo/Queue4User/@name"/></span>]
							</xsl:when>
							<xsl:otherwise>
								<i>нет</i> [<span class="red">не в очереди</span>]
							</xsl:otherwise>
						</xsl:choose>
					</div>

					<div>
						<a href="http://info.docdoc.ru/" target="_blank">Информация</a>
					</div>

				</div>

				<xsl:call-template name="ceilInfo">
					<xsl:with-param name="id" select="'PersonalData'"/>
					<xsl:with-param name="style">text-align: left; width: 350px</xsl:with-param>
				</xsl:call-template>
			</div>

			<xsl:text disable-output-escaping="yes"><![CDATA[ <div id="all">  ]]></xsl:text>
			
			<xsl:call-template name="leftMenu"/>
					
			
			<div id="panel" onclick=""></div>
			<a href="#" id="toUpPage" title="Наверх"></a>
			<xsl:choose>
				<xsl:when test="/root/srvInfo/Panel = 'open'">
					<style type="text/css">
						#leftMenu{width:200px;overflow:visible;margin-left:-220px;padding-top:44px;}
						#all{margin-left:200px}
						#panel{background-position:-19px 0;left:200px;}
						#toUpPage{left:200px;}
					</style>
				</xsl:when>
				<xsl:when test="/root/srvInfo/Panel = 'close'">
					<style type="text/css">
						#leftMenu{width:0px;overflow:hidden;margin:0;padding:0;}
						#all{margin-left:0}
						#panel{background-position:-1px 0;left:0px;}
						#toUpPage{left:0px;}
					</style>
				</xsl:when>
			</xsl:choose>
				
			<![CDATA[ 	
				<script type="text/javascript">
					$("body").css('min-height',$("#leftMenu").height()+120+'px');
					$("#panel").toggle(
						function(){
							$("#leftMenu").css({'width':'0px','overflow':'hidden','margin':'0','padding':'0'});
							$("#all").css({'margin-left':'0'});
							$("#panel").css({'background-position':'-1px 0'});
							$("#panel").css({'left':'0px'});
							$("#toUpPage").css({'left':'0px'});
							setPanel('close');
						},
						function(){
							$("#leftMenu").css({'width':'200px','overflow':'visible','margin-left':'-220px','padding-top':'65px'});
							$("#all").css({'margin-left':'200px'});
							$("#panel").css({'background-position':'-19px 0'});
							$("#panel").css({'left':'200px'});
							$("#toUpPage").css({'left':'200px'});
							setPanel('open');
						}
					);
					
					function setPanel(state) {
						$.ajax({
							type: "get",
							url: "/include/setPanelState.htm",
							data: "state="+state
						});
					}
				</script>
				
				<div id="contentOut">
			
			]]>
			
	</xsl:template>	 
	
	
	
	<xsl:template name="leftMenu">	
		<div id="leftMenu">
			<ul>
				<xsl:for-each select="/root/LeftMenu/Group[Element[@display = 'yes' and (Rights/Right = /root/srvInfo/UserData/Rights/Right or Rights/Right = 'ALL')]]">
					<li>
					<xsl:value-of select="Title"/>
					<ul>
					<xsl:for-each select="Element[@display = 'yes' and (Rights/Right = /root/srvInfo/UserData/Rights/Right or Rights/Right = 'ALL')]">
						<li>
							<xsl:choose>
								<xsl:when test="contains(/root/srvInfo/URL, URL)">
									<span><xsl:value-of select="Title"/></span>
								</xsl:when>
								<xsl:otherwise>
									<a href="{URL}"><xsl:value-of select="Title"/></a>
								</xsl:otherwise>
							</xsl:choose>
						</li>
					</xsl:for-each>
					</ul>
					</li>
				</xsl:for-each>
			</ul>
		</div>
		
	</xsl:template>
	
	
	
	
	<xsl:template name="ceilInfo">	  
		<xsl:param name="id"/>
		<xsl:param name="style" select="''"/>
		<xsl:param name="body" select="''"/>

		<div id="ceilWin_{$id}" class="infoElt hd" style="{$style}">
			<div id="ceilWin_{$id}_container">
				<xsl:if test="$body != ''">
					<xsl:copy-of select="$body"/>
				</xsl:if>
			</div> 
			<img
				src="/img/common/clBt.gif"
				width="15"
				height="14"
				alt="закрыть"
				style="position: absolute; cursor: pointer; right: 4px; top: 4px;"
				title="закрыть"
				onclick="$('#ceilWin_{$id}').hide()"
				border="0"/>
		</div>
	</xsl:template> 
	
	
	
	
	<xsl:template name="headerSimple">
		<xsl:param name="cssFile" select="'/css/main.css'"/>  

		<xsl:text disable-output-escaping="yes"><![CDATA[<html> ]]></xsl:text>
			<head>
				<meta http-equiv="Pragma" content="no-cache"/>	 
				<meta http-equiv="content-type" content="text/html; charset=cp1251"/>
				<link rel="stylesheet" type="text/css" href="/css/main.css" title="general"/>
				<![CDATA[<![if IE]>
				    <link rel="stylesheet" type="text/css" href="/css/ieFix.css" title="generalIE"/>
				<![endif]>]]>
				<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
				<title></title>
			</head>
			<xsl:text disable-output-escaping="yes">
				<![CDATA[
					<body>
						
						<div id="all" style="margin:0;border:0;">
							<div id="contentOut">
				]]>
			</xsl:text>
	</xsl:template>
	
	
	
	
	
	
	
	<xsl:template name="headerNoHead">
	</xsl:template>
	
	
	
</xsl:transform>