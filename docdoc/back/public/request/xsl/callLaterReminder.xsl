<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:output method="html" encoding="utf-8"/>


	<xsl:template match="/">
		<xsl:apply-templates select="root"/>
	</xsl:template>




	<xsl:template match="root">
		<xsl:if test="dbInfo/RequestList/Element">
			<xsl:call-template name="ceilInfo">
				<xsl:with-param name="id" select="'reminder'"/>
				<xsl:with-param name="style" select="'width: 200px; min-height: 210px; background-color: #ffda6a'"/>
				<xsl:with-param name="body">
					<h2 class="mb15">Напоминание о перезвоне</h2>
					
					<xsl:for-each select="dbInfo/RequestList/Element">
						<div class="mb15">Заявка № <a href="/request/request.htm?type={/root/srvInfo/TypeView}&amp;id={Id}"><xsl:value-of select="Id"/></a>
							<br/>
							Перезвонить
								<strong> 
								<xsl:value-of select="CallLaterDate"/>
								в
								<xsl:value-of select="CallLaterTime"/>
								</strong>
							<br/>
							<span class="txt10">Оператор: <xsl:value-of select="Owner"/></span>
						</div>
						
					</xsl:for-each>
					<div class="r txt10"><a href="/request/index.htm?type={/root/srvInfo/TypeView}&amp;shStatus=7">все остальные</a></div>
				</xsl:with-param>
				<xsl:with-param name="callback">$("#reminderBlock").hide();</xsl:with-param>
			</xsl:call-template>
		</xsl:if>
	</xsl:template>
	
	
	
	<xsl:template name="ceilInfo">	  
		<xsl:param name="id"/>
		<xsl:param name="body" select="''"/>
		<xsl:param name="style" select="'width: 720px; min-height: 200px'"/>
		<xsl:param name="callback" select="''"/>
		
		<div id="ceilWin_{$id}" class="m0 shd infoEltR" style=" padding: 10px; {$style} ">
			<xsl:copy-of select="$body"/>
			
			<img src="/img/common/clBt.gif" width="15" height="14"  alt="закрыть" style="position: absolute; cursor: pointer; right: 4px; top: 4px;" title="закрыть" onclick="$('#ceilWin_{$id}').hide();{$callback}" border="0"/>
		</div>
	</xsl:template>

</xsl:transform>

