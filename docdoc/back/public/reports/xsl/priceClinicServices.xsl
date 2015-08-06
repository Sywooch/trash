<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:import href="../../lib/xsl/common.xsl"/>
	<xsl:import href="../../request/xsl/common.xsl"/>
	<xsl:import href="../../lib/xsl/pager.xsl"/>
	<xsl:import href="../../lib/xsl/filter.xsl"/>
	<xsl:import href="../../lib/xsl/sortBy.xsl"/>

	<xsl:output method="html" encoding="utf-8"/>

	<xsl:template match="/">
		<link type="text/css" href="/css/jquery.autocomplete.css" rel="stylesheet" media="screen"/>
		<link type="text/css" href="/css/jquery-ui-1.7.2.custom.css" rel="stylesheet" />

		<xsl:apply-templates select="root"/>

		<script src="/lib/js/jquery-ui-1.7.2.custom.min.js" type="text/javascript" ></script>
		<script src="/lib/js/ui.datepicker-ru.js" type="text/javascript" ></script>
		<script src='/lib/js/jquery.autocomplete.min.js' type='text/javascript' language="JavaScript"></script>
		<script src="/reports/js/report.js" type="text/javascript"></script>
		<script src="/reports/js/priceClinic.js" type="text/javascript"></script>

		<iframe name="export" id="export" height="0" width="0" frameborder="0" scrolling="No"/>
	</xsl:template>


	<xsl:template match="root">
		<div id="main">
			<h1>Прайс-лист на услуги диагностических центров</h1>


			<div class="actionList">
				<a href="javascript:exportData()">Экспорт</a>

				<form method="get" name="data" id="data" target="export" action="#">
					<input type="hidden" id="clinicIds" name="clinicIds" />
 				</form>
			</div>



			<div id="resultSet">
				<xsl:variable name="tdCount" select="8"/>
				<table cellpadding="0" cellspacing="1" width="100%" border="0" class="resultSet">
					<col width="30"/>
					<col width="30"/>
					<col/>


					<tr>
						<th><input type="checkbox" class="allSelect" /></th>
						<th>#</th>
						<th>Клиника</th>
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
									<td><input type="checkbox" name="Selector[clinic]" class="clinicSelector" data-id="{Id}" /></td>
									<td><xsl:value-of select="Id"/></td>
									<td><xsl:value-of select="Name"/></td>
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

