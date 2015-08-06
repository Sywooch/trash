<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output method="html"  encoding="utf-8" />
	
	
	<xsl:template name="filter">
		<xsl:param name="id" select="'filter'"/>
		<xsl:param name="formId" select="'formFilter'"/>
		<xsl:param name="method" select="'get'"/>
		
		<xsl:param name="startPage" select="dbInfo/Pager/@currentPageId"/>
		<xsl:param name="generalLine" />
		<xsl:param name="addLine" select="''"/>
		<xsl:param name="addLine2" select="''"/>
		<xsl:param name="addLineState" select="'collapse'"/>	<!-- expand or collapse -->
		<xsl:param name="clearFunction" select="''" /> 
		
		<link rel="stylesheet" type="text/css" href="/css/searchFormFilter.css" media="screen"/>
		
		<div id="{$id}" class="searchFilterForm">
			<form id="{$formId}" name="{$formId}" method="{$method}" action="">
				<input type="hidden" name="addLineState" id="addLineState" value="{$addLineState}"/>
				
				<!-- Пейджер  -->
				<xsl:if test="$startPage != ''">
					<input type="hidden" name="startPage" id="startPage" value="{$startPage}"/>
					<input type="hidden" name="step" value="{/root/srvInfo/Step}"/>
				</xsl:if>
				
				<!-- основной контент -->
				<table align="center" style="max-width: 90%;" >
					<tr>
						<td>
							<!-- Основные поля фильтра -->
							<div>
								<xsl:copy-of select="$generalLine"/>
							</div>
							
							
							
							<!-- Дополнительные поля фильтра, если есть -->
							<xsl:if test="$addLine != ''">
								<div class="addFilterLine">
									<xsl:copy-of select="$addLine"/>
								</div>
							</xsl:if>
							<xsl:if test="$addLine2 != ''">
								<div class="addFilterLine">
									<xsl:copy-of select="$addLine2"/>
								</div>
							</xsl:if>
						
							<div class="cBlock"></div>
							
							<div style="float:right; width: 100%">
								<div style="float: right; margin-left: 30px">
									<input class="form button" type="submit" value="ПОИСК">
										<xsl:if test="$startPage != ''">
											<xsl:attribute name="onclick">$('#startPage').val('1')</xsl:attribute>
										</xsl:if>
									</input>
								</div>
								<xsl:if test="$addLine != ''">	  
									<div class="filterAddState" style="float: right; margin-right: 20px">
										<xsl:attribute name="onclick">
											$("#<xsl:value-of select="$id"/> .addFilterLine").toggle();
										</xsl:attribute>
										<span class="link">
										<xsl:choose>
											<xsl:when test="$addLineState = 'expand'">дополнительные фильтры</xsl:when>
											<xsl:when test="$addLineState = 'collapse'">дополнительные фильтры</xsl:when>
										</xsl:choose>
										</span>
									</div>
								</xsl:if>
								<xsl:if test="$clearFunction != ''">
									<div class="filterAddState" style="float: right; margin-right: 20px">
										<xsl:attribute name="onclick">
											<xsl:value-of select="$clearFunction"/>
										</xsl:attribute>
										<span class="link">сбросить фильтры</span>
									</div>
								</xsl:if>
							</div>
						</td>
					</tr>
					<!-- <xsl:if test="$addLine != ''">
						<tr>
							<td colspan="2">
								<div class="filterAddState">
									<xsl:attribute name="onclick">
										$("#<xsl:value-of select="$id"/> .addFilterLine").toggle();
									</xsl:attribute>
									<xsl:choose>
										<xsl:when test="$addLineState = 'expand'">Дополнительные фильтры</xsl:when>
										<xsl:when test="$addLineState = 'collapse'">Дополнительные фильтры</xsl:when>
									</xsl:choose>
								</div>
							</td>
						</tr>
					</xsl:if> -->
				</table>
			</form>
		</div>
		
	</xsl:template>

</xsl:transform>


