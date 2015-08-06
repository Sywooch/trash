<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:import href="../../lib/xsl/common.xsl"/>
	<xsl:import href="../../request/xsl/common.xsl"/>
	<xsl:import href="../../lib/xsl/pager.xsl"/>
	<xsl:import href="../../lib/xsl/filter.xsl"/>

	<xsl:decimal-format decimal-separator = '.' grouping-separator = ' ' NaN = '0'/>

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
		<script src="/reports/js/monthDiagReport.js" type="text/javascript"></script>
		<script>
			function exportData () {
				document.forms['formFilter'].action = '/reports/requestCount.htm';
				document.forms['formFilter']['Excel'].value = '1';
				document.forms['formFilter'].submit();
				document.forms['formFilter'].action = '';
				document.forms['formFilter']['Excel'].value = '';
			}
		</script>
		
		<iframe name="export" id="export" height="0" width="0" frameborder="0" scrolling="No"/>
	</xsl:template>




	<xsl:template match="root">
		<div id="main">
			<h1>Количество заявок</h1>

			<xsl:call-template name="filter">
				<xsl:with-param name="formId" select="'formFilter'"/>
				<xsl:with-param name="generalLine">

					<input type="hidden" name="Excel" value=""/>

					<div class="inBlockFilter">
						<div>
							Заявка создана c:
							<input name="crDateShFrom" id="crDateShFrom" style="width:70px" maxlength="12" value="{srvInfo/CrDateShFrom}"/>
							&#160;по:
							<input name="crDateShTill" id="crDateShTill" style="width:70px" maxlength="12" value="{srvInfo/CrDateShTill}"/>
							<span class="eraser" title="очистить поле" onclick="$('#crDateShFrom').val('');$('#crDateShTill').val('');"/>
						</div>
					</div>

					<div class="inBlockFilter ml20">
						<div>
							Пациент дошел c:
							<input name="crDateShFrom2" id="crDateShFrom2" style="width:70px" maxlength="12" value="{srvInfo/CrDateShFrom2}"/>
							&#160;по:
							<input name="crDateShTill2" id="crDateShTill2" style="width:70px" maxlength="12" value="{srvInfo/CrDateShTill2}"/>
							<span class="eraser" title="очистить поле" onclick="$('#crDateShFrom2').val('');$('#crDateShTill2').val('');"/>
						</div>
					</div>

					<div class="clear"/>

					<div class="inBlockFilter ml20">
						<label>Отчет: </label>
						<div>
							<select name="ReportType" id="ReportType" style="width: 150px" >
								<xsl:for-each select="/root/dbInfo/ReportTypes/Type">
									<option value="{@type}">
										<xsl:if test="@type = /root/srvInfo/ReportType">
											<xsl:attribute name="selected"/>
										</xsl:if>
										<xsl:value-of select="."/>
									</option>
								</xsl:for-each>
							</select>
						</div>
					</div>

					<div class="inBlockFilter ml20">
						<label>Тип: </label>
						<div>
							<select name="RequestType" id="RequestType" style="width: 150px" >
								<option value="">Любой</option>
								<xsl:for-each select="/root/dbInfo/RequestTypes/Type">
									<option value="{@type}">
										<xsl:if test="@type = /root/srvInfo/RequestType">
											<xsl:attribute name="selected"/>
										</xsl:if>
										<xsl:value-of select="."/>
									</option>
								</xsl:for-each>
							</select>
						</div>
					</div>

				</xsl:with-param>
			</xsl:call-template>

			<div class="actionList">
				<a href="javascript:exportData()">Экспорт</a>
			</div>

			<div id="resultSet">
				<table cellpadding="0" cellspacing="1" border="0" class="resultSet">
					<col width="100"/>
					<col width="600" />
					<xsl:choose>
						<xsl:when test="/root/srvInfo/ReportType = 'clinics'">
							<col width="100"/>
						</xsl:when>
						<xsl:otherwise>
							<col width="100"/>
							<col width="100"/>
							<col width="100"/>
						</xsl:otherwise>
					</xsl:choose>
					<col width="150"/>
					<tr>
						<th>ID клиники</th>
						<th>Клиника</th>

						<xsl:choose>
							<xsl:when test="/root/srvInfo/ReportType = 'clinics'">
								<th>Количество записанных</th>
							</xsl:when>
							<xsl:otherwise>
								<th>КТ</th>
								<th>МРТ</th>
								<th>УЗИ и прочие</th>
							</xsl:otherwise>
						</xsl:choose>

						<th>Тип</th>
					</tr>
					<xsl:for-each select="dbInfo/Reports/Report">
						<xsl:variable name="type" select="type"/>
						<tr>
							<td><xsl:value-of select="clinic_id"/></td>
							<td><xsl:value-of select="clinic_name"/></td>

							<xsl:choose>
								<xsl:when test="/root/srvInfo/ReportType = 'clinics'">
									<td><xsl:value-of select="count"/></td>
								</xsl:when>
								<xsl:otherwise>
									<td><xsl:value-of select="count_kt"/></td>
									<td><xsl:value-of select="count_mrt"/></td>
									<td><xsl:value-of select="count_other"/></td>
								</xsl:otherwise>
							</xsl:choose>

							<td><xsl:value-of select="/root/dbInfo/RequestTypes/Type[@type = $type]"/></td>
						</tr>
					</xsl:for-each>
				</table>
			</div>
			

		</div>
	</xsl:template>

</xsl:transform>

