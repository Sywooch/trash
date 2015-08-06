<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:import href="common.xsl"/>
	
	<xsl:output method="html" encoding="utf-8"/>
	
	<xsl:key name="diagnostica" match="/root/dbInfo/DiagnosticList/descendant-or-self::Element" use="@id"/>
	
	
	<xsl:template match="/">
		<xsl:apply-templates select="root"/>
	</xsl:template>
	
	
	
	
	<xsl:template match="root">
		<xsl:variable name="context" select="dbInfo/DCenter"/>
		<div class="map-popup round" style="margin: 0 0 0 0px; position: relative">
			<div class="close" onclick="closeBall();"></div>
			<div class="list-img">
				<a href="/kliniki/{$context/Rewrite}">
					<img width="100" src="http://docdoc.ru/upload/kliniki/logo/{$context/Logo}" alt=""/>
				</a>
				<div class="time">
					<xsl:if test="$context/WorkWeek != ''">
						<div>Будни:<br/> <xsl:value-of select="$context/WorkWeek"/></div>
					</xsl:if>
					<xsl:choose>
						<xsl:when test="$context/WeekEnd != ''">
							<div>Выходные:<br/> <xsl:value-of select="$context/WeekEnd"/></div>
						</xsl:when>
						<xsl:when test="$context/Saturday != '' or $context/Sunday != ''">
							<xsl:if test="$context/Saturday != ''">
								<div>Суббота:<br/> <xsl:value-of select="$context/Saturday"/></div>
							</xsl:if>
							<xsl:if test="$context/Sunday != ''">
								<div>Воскресенье:<br/> <xsl:value-of select="$context/Sunday"/></div>
							</xsl:if>
						</xsl:when>
					</xsl:choose>
				</div>
			</div>
			<div class="echo">
				<a href="/kliniki/{$context/Rewrite}" class="doc-name"><xsl:value-of select="$context/Title"/></a>
				<div class="profession">
					<xsl:for-each select="$context/Metro">
						<div style="space-word: nowrap; float: left; margin: 0; height: 14px">
							<xsl:call-template name="metroLine">
								<xsl:with-param name="lineId" select="@lineId"/>
							</xsl:call-template><a class="link-no-style">м.<xsl:value-of select="."/></a>
							<xsl:if test="position() != last()">,&#160;</xsl:if>
						</div> 
					</xsl:for-each>
					<div class="clear"/>
					<div>
						<xsl:value-of select="$context/Address"/>
					</div>	 
				</div>
				<div class="map-popup-txt"><xsl:value-of select="$context/Descr"/></div>
				
				<table class="tbl-price">
				<xsl:for-each select="$context/Diagnostics/Element[position() &lt;= 3]">
					<xsl:variable name="id" select="@id"/>
					<tr>
						<td>
							<span>
								<xsl:choose>
									<xsl:when test="/root/dbInfo/DiagnosticList/Element[@id = $id]">
										<xsl:choose>
											<xsl:when test="key('diagnostica',@id)/ReductionName != ''">
												<xsl:value-of select="key('diagnostica',@id)/ReductionName"/>
											</xsl:when>
											<xsl:otherwise>
												<xsl:value-of select="key('diagnostica',@id)/Name"/>
											</xsl:otherwise>
										</xsl:choose>
									</xsl:when>
									<xsl:otherwise>
										<!-- <xsl:value-of select="/root/dbInfo/DiagnosticList/descendant-or-self::Element[@id = $id]/../../Name"/> -->
										<xsl:choose>
											<xsl:when test="/root/dbInfo/DiagnosticList/descendant-or-self::Element[@id = $id]/../../ReductionName != ''">
												<xsl:value-of select="/root/dbInfo/DiagnosticList/descendant-or-self::Element[@id = $id]/../../ReductionName"/>
											</xsl:when>
											<xsl:otherwise>
												<xsl:value-of select="/root/dbInfo/DiagnosticList/descendant-or-self::Element[@id = $id]/../../Name"/>
											</xsl:otherwise>
										</xsl:choose>&#160;<xsl:choose>
											<xsl:when test="key('diagnostica',@id)/ReductionName != ''"><xsl:value-of select="key('diagnostica',@id)/ReductionName"/></xsl:when>
											<xsl:otherwise><xsl:value-of select="key('diagnostica',@id)/Name"/></xsl:otherwise>
										</xsl:choose>
										
									</xsl:otherwise>
								</xsl:choose>
							</span>
						</td>
						<td class="price">
							<xsl:choose>
								<xsl:when test="SpecialPrice != 0">
									<strike><xsl:value-of select="Price"/> р.</strike>
									<xsl:value-of select="SpecialPrice"/> р.
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="Price"/> р.
								</xsl:otherwise>
							</xsl:choose>
						</td>
					</tr>
					<xsl:if test="SpecialPrice != 0">
						<tr>
							<td colspan="2" class="spec-price">спец. цена на docdoc.ru</td>
						</tr>
					</xsl:if>
				</xsl:for-each>
				</table>
			
				<div class="null" style="height: 20px"/>
				<xsl:if test="$context/Phone and $context/Phone != ''">
					<div class="contact">
						Запись по телефону:
						<span><xsl:value-of select="$context/Phone"/></span>
					</div>
				</xsl:if>
			</div>
			<div class="clear"></div>
			<!-- <a href="" class="link-map">проложить маршрут</a> -->
		</div>
	</xsl:template>
</xsl:transform>

