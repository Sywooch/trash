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
		<script src="/reports/js/monthReport.js" type="text/javascript"></script>
		<script>
			function exportData () {
				document.forms['formFilter'].action = "/reports/service/exportMonthRequestReport.htm";
				document.forms['formFilter'].submit();
				document.forms['formFilter'].action = "";
			}	
		</script>
		
		<iframe name="export" id="export" height="0" width="0" frameborder="0" scrolling="No"/>
	</xsl:template>




	<xsl:template match="root">
		<div id="main">
			<h1>Анализ обращений по месяцам</h1>

<!--	Фильтр	-->
			<xsl:call-template name="filter">
				<xsl:with-param name="formId" select="'formFilter'"/>
				<xsl:with-param name="startPage" select="dbInfo/Pager/@currentPageId"/>
				<xsl:with-param name="generalLine">
					<div class="inBlockFilter">
						<div>
							<div>
							    Дата c:
							    <input name="crDateShFrom" id="crDateShFrom" style="width:70px" maxlength="12" value="{srvInfo/CrDateShFrom}"/>
							    &#160;по:
							    <input name="crDateShTill" id="crDateShTill" style="width:70px" maxlength="12" value="{srvInfo/CrDateShTill}"/>
							</div>

						</div>
					</div>
				</xsl:with-param>
			</xsl:call-template>
			
			

 
			<div class="actionList">  
				<a href="javascript:exportData()">Экспорт</a>
				
				<form method="get" name="data" id="data" target="export" action="#">
					<input type="hidden" name="dateFrom" value="{srvInfo/CrDateShFrom}"/>
					<input type="hidden" name="dateTill" value="{srvInfo/CrDateShTill}"/>
					<input type="hidden" name="shClinicId" value="{srvInfo/ShClinicId}"/>
					<xsl:if test="/root/srvInfo/Branch = '1'">
						<input type="hidden" name="shBranch" value="1"/>
					</xsl:if>
					<input type="hidden" name="shStatus" value="{srvInfo/ShStatus}"/>
					
					<input type="hidden" name="sortBy" value="{/root/srvInfo/SortBy}"/>
					<input type="hidden" name="sortType" value="{/root/srvInfo/SortType}"/>
				</form>
			</div>

			<div id="resultSet">
				
				
				
				<xsl:variable name="tdCount" select="10"/>
				<table cellpadding="0" cellspacing="1" width="100%" border="0" class="resultSet">
					<col width="250" />
					<col width="150"/>
					<col width="80"/>
					<col width="80"/>
					<col width="80"/>
					<col width="80"/>
					<col width="80"/>
					<col width="80"/>
					<col width="80"/>
					<col width="80"/>
		
					<tr>
						<th rowspan= "2">Месяц</th>
						<th rowspan= "2">Всего обращений</th>
						<th colspan="2">Переведенных</th>
						<th colspan="2">Записаных</th>
						<th colspan="2">Дошедшиих (в этот месяц всего)</th>
						<th colspan="2">Дошедшиих (из обратившихся)</th>
						<th rowspan= "2">--</th>
					</tr>
					<tr>
						<th class="sub">всего</th>
						<th class="sub">конверсия</th>
						<th class="sub">всего</th>
						<th class="sub">конверсия</th>
						<th class="sub">всего</th>
						<th class="sub">конверсия</th>
						<th class="sub">всего</th>
						<th class="sub">конверсия</th>
					</tr>
					<xsl:for-each select="dbInfo/Reports/Report">
						<xsl:variable name="class">
							<xsl:choose>
								<xsl:when test="(position() div 2) - floor(position() div 2) &gt; 0">odd</xsl:when>
								<xsl:otherwise>even</xsl:otherwise>
							</xsl:choose>
						</xsl:variable>

						<tr id="tr_{position()}" class="{$class}" backclass="{$class}" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','{$class}')">
							<th class="l txt13">
								<xsl:value-of select="Month"/>
							</th>
							<td class="r"><xsl:value-of select="Total"/></td>
							<td class="r"><xsl:value-of select="Transfer"/></td>
							<td class="r">
								<xsl:if test="round(Transfer div Total*100)">
									<xsl:value-of select="format-number(Transfer div Total,'#.0%')"/>
								</xsl:if>
							</td>
							<td class="r"><xsl:value-of select="Apointment"/></td>
							<td class="r">
								<xsl:if test="round(Apointment div Total*100)">
									<xsl:value-of select="format-number(Apointment div Total,'#.0%')"/>
								</xsl:if>
							</td>
							<td class="r"><xsl:value-of select="Complete"/></td>
							<td class="r">
								<xsl:if test="round(Complete div Total*100)">
									<xsl:value-of select="format-number(Complete div Total,'#.0%')"/>
								</xsl:if>
							</td>
							<td class="r"><xsl:value-of select="ThisPeriodComplete"/></td>
							<td class="r">
								<xsl:if test="round(ThisPeriodComplete div Total*100)">
									<xsl:value-of select="format-number(ThisPeriodComplete div Total,'#.0%')"/>
								</xsl:if>
							</td>
							<td class="r"></td>
						</tr>
						<xsl:for-each select = "Contracts/Contract">
							<tr id="tr_{position()}" class="{$class}" backclass="{$class}" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','{$class}')">
								<td class="r">
									<xsl:value-of select="Title"/>
								</td>
								<td class="r"><xsl:value-of select="Total"/></td>
								<td class="r"><xsl:value-of select="Transfer"/></td>
								<td class="r">
									<xsl:if test="round(Transfer div Total*100)">
										<xsl:value-of select="format-number(Transfer div Total,'#.0%')"/>
									</xsl:if>
								</td>
								<td class="r"><xsl:value-of select="Apointment"/></td>
								<td class="r">
									<xsl:if test="round(Apointment div Total*100)">
										<xsl:value-of select="format-number(Apointment div Total,'#.0%')"/>
									</xsl:if>
								</td>
								<td class="r"><xsl:value-of select="Complete"/></td>
								<td class="r">
									<xsl:if test="round(Complete div Total*100)">
										<xsl:value-of select="format-number(Complete div Total,'#.0%')"/>
									</xsl:if>
								</td>
								<td class="r"><xsl:value-of select="ThisPeriodComplete"/></td>
								<td class="r">
									<xsl:if test="round(ThisPeriodComplete div Total*100)">
										<xsl:value-of select="format-number(ThisPeriodComplete div Total,'#.0%')"/>
									</xsl:if>
								</td>
								<td class="r"></td>
							</tr>
						</xsl:for-each>
					</xsl:for-each>
					<!-- 
					<tr>
						<td class="r">ИТОГО:</td>
						<td class="r b"><xsl:value-of select="sum(dbInfo/Reports/Report/Total)"/></td>
						<td class="r b"><xsl:value-of select="sum(dbInfo/Reports/Report/Transfer)"/></td>
						<td></td>
						<td class="r b"><xsl:value-of select="sum(dbInfo/Reports/Report/Apointment)"/></td>
						<td></td>
						<td class="r b"><xsl:value-of select="sum(dbInfo/Reports/Report/Complete)"/></td>
						<td></td>
						<td class="r b"><xsl:value-of select="sum(dbInfo/Reports/Report/FullApointment)"/></td>
						<td></td>
						<td class="r b"></td>
					</tr>
					 -->
				</table>
			</div>
			

		</div>
	</xsl:template>

</xsl:transform>

