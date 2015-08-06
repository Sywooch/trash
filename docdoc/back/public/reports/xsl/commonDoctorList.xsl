<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:import href="../../lib/xsl/common.xsl"/>
	<xsl:import href="../../request/xsl/common.xsl"/>
	<xsl:import href="../../lib/xsl/pager.xsl"/>
	<xsl:import href="../../lib/xsl/filter.xsl"/>
	<xsl:import href="../../lib/xsl/sortBy.xsl"/>

	<xsl:output method="html" encoding="utf-8"/>

	<xsl:key name="status" match="/root/dbInfo/StatusDict/Element" use="@id"/>
	<xsl:key name="clinic" match="/root/dbInfo/ClinicList/Element" use="@id"/>

	<xsl:template match="/">
		<link type="text/css" href="/css/jquery.autocomplete.css" rel="stylesheet" media="screen"/>
		<link type="text/css" href="/css/jquery-ui-1.7.2.custom.css" rel="stylesheet" />
		
		<xsl:apply-templates select="root"/>

		<script src="/lib/js/jquery-ui-1.7.2.custom.min.js" type="text/javascript" ></script>
		<script src="/lib/js/ui.datepicker-ru.js" type="text/javascript" ></script>
		<script src='/lib/js/jquery.autocomplete.min.js' type='text/javascript' language="JavaScript"></script>
		<script src="/reports/js/report.js" type="text/javascript"></script>
		<script>
			$(document).ready(function(){ 
			});
			
			function exportData () {
				document.forms['data'].action = "/reports/service/exportCommonRequestReport.htm";
				document.forms['data'].submit();
			}	
		</script>
		
		<iframe name="export" id="export" height="0" width="0" frameborder="0" scrolling="No"/>
	</xsl:template>




	<xsl:template match="root">
		<div id="main">
			<h1>Врачи. Общий отчёт</h1>
			
 
			<div class="actionList">  
				<a href="javascript:exportData()">Экспорт</a>
				
				<form method="get" name="data" id="data" target="export" action="#">
				</form>
			</div>

			
 
			<div id="resultSet">
				<xsl:variable name="tdCount" select="8"/>
				<table cellpadding="0" cellspacing="1" width="100%" border="0" class="resultSet">
					<col width="30"/>
					<col/>
					<col width="80"/>
					<col width="80"/>
					<col width="80"/>
					<col width="80"/>
					

					<tr>
						<th>#</th>
						<th>Клиника</th>
						<th>Всего переведено</th>
						<th>Записано</th>
						<th>Приём состоялся</th>
						<th>Отказ</th>
					</tr>
					<xsl:choose>
						<xsl:when test="dbInfo/ClinicList/Element">
							<xsl:for-each select="dbInfo/ClinicList/Element">
								<xsl:variable name="class">
									<xsl:choose>
										<xsl:when test="(position() div 2) - floor(position() div 2) &gt; 0">odd</xsl:when>
										<xsl:otherwise>even</xsl:otherwise>
									</xsl:choose>
								</xsl:variable>
								<tr id="tr_{@id}" class="{$class}" backclass="{$class}" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','{$class}')">

									<td><xsl:value-of select="position()"/></td>
									<td>
										<a href="/reports/requestList.htm?crDateShFrom={/root/srvInfo/CrDateShFrom}&amp;crDateShTill={/root/srvInfo/CrDateShTill}&amp;shClinic={Name}&amp;shClinicId={@id}" target="_blank">
											<xsl:if test="ParentId != '0'"><xsl:attribute name="style">margin-left: 40px</xsl:attribute></xsl:if>
											<xsl:value-of select="Name"/>
										</a>
									</td>
									<td>
										<xsl:value-of select="Transfer"/>
									</td>
									<td>
										<xsl:value-of select="Apointment"/>
									</td>
									<td>
										<xsl:value-of select="Complete"/>
									</td>
									<td>
										<xsl:if test="Reject != '0'"><xsl:attribute name="class">red</xsl:attribute></xsl:if>
										<xsl:value-of select="Reject"/>
									</td>
								</tr>
							</xsl:for-each>
						</xsl:when>
						<xsl:otherwise>
							<tr>
								<td colspan="{$tdCount}" align="center">
									<div class="error" style="margin: 20px">Данных не найдено</div>
								</td>
							</tr>
						</xsl:otherwise>
					</xsl:choose>
				</table>
			</div>
			<xsl:call-template name="pager">
				<xsl:with-param name="context" select="dbInfo/Pager"/>
			</xsl:call-template>
				

		</div>
	</xsl:template>

</xsl:transform>

