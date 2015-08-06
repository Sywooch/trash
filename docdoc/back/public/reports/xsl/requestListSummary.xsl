<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:import href="../../lib/xsl/common.xsl"/>
	<xsl:import href="../../request/xsl/common.xsl"/>
	<xsl:import href="../../lib/xsl/pager.xsl"/>
	<xsl:import href="../../lib/xsl/filter.xsl"/>
	<xsl:import href="../../lib/xsl/sortBy.xsl"/>

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
		<script src="/reports/js/report.js" type="text/javascript"></script>
		<script>
			$(document).ready(function(){ 
				$("#shClinic").autocomplete("/service/getClinicList.htm",{
					delay:10,
					minChars:2,
					max:20,
					autoFill:true,
					selectOnly:true,
                    matchContains: true,
					extraParams: { cityId: <xsl:value-of select="/root/srvInfo/City/@id"/>  },
					formatResult: function(row) {
						return row[0];
					},
					formatItem: function(row, i, max) {		
						return row[0];
					}
				}).result(function(event, item) {
					$("#shClinicId").val(item[1]);
					$('#startPage').val("1");
				});
			});
			
			function exportData () {
				document.forms['data'].action = "/reports/service/exportRequestSummaryReport.htm";
				document.forms['data'].submit();
			}	
		</script>
		
		<iframe name="export" id="export" height="0" width="0" frameborder="0" scrolling="No"/>
	</xsl:template>




	<xsl:template match="root">
		<div id="main">
			<h1>Суммарный отчет по переведенным/записанным/дошедшим пациентам</h1>

<!--	Фильтр	-->
			<xsl:call-template name="filter">
				<xsl:with-param name="formId" select="'filter'"/>
				<xsl:with-param name="startPage" select="dbInfo/Pager/@currentPageId"/>
				<xsl:with-param name="generalLine">
					<xsl:call-template name="sortInit">
						<xsl:with-param name="form" select="'filter'"/>
						<xsl:with-param name="sortBy" select="/root/srvInfo/SortBy"/>
						<xsl:with-param name="sortType" select="/root/srvInfo/SortType"/>
					</xsl:call-template>
					
					<div class="inBlockFilter">
						<div>
							<label>Дата приёма:</label>
							<div>
							    c:
							    <input name="crDateShFrom" id="crDateShFrom" style="width:70px" maxlength="12" value="{srvInfo/CrDateShFrom}"/>
							    &#160;по:
							    <input name="crDateShTill" id="crDateShTill" style="width:70px" maxlength="12" value="{srvInfo/CrDateShTill}"/>
							</div>

						</div>
					</div>
					<div class="inBlockFilter ml10">
					    <div>
						    <label>Клиника и фил.:</label>
						    <div>
								<input name="shClinic" id="shClinic" style="width: 300px" maxlength="25"  value="{srvInfo/ShClinic}"/>
								<input type="hidden" name="shClinicId" id="shClinicId" value="{srvInfo/ShClinicId}"/>
						    </div>
					    </div>
					</div>
					<div class="inBlockFilter ml5">
					    <div>
						    <label>&#160;</label>
						    <div>
								<input type="checkbox" name="shBranch" id="shBranch" value="1">
									<xsl:if test="/root/srvInfo/Branch = '1'">
										<xsl:attribute name="checked"/>
									</xsl:if>
								</input>
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
					<col width="100" />
					<col width="60"/>
					<col width="60"/>
					<col width="60"/>
					<col width="60"/>
					<col width="60"/>
					<col width="60"/>
					<col width="60"/>
					<col width="60"/>
					<col width="60"/>
		
					<tr>
						<th>Месяц</th>
						<th>Всего обращений</th>
						<th colspan="2">Переведено</th>
						<th colspan="2">Записано</th>
						<th colspan="2">Приём состоялся</th>
						<th colspan="2">Отказ</th>
						<th colspan="2">Оплачено</th>
					</tr>
					<xsl:for-each select="dbInfo/Reports/Report">
						<xsl:variable name="class">
							<xsl:choose>
								<xsl:when test="(position() div 2) - floor(position() div 2) &gt; 0">odd</xsl:when>
								<xsl:otherwise>even</xsl:otherwise>
							</xsl:choose>
						</xsl:variable>

						<tr id="tr_{position()}" class="{$class}" backclass="{$class}" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','{$class}')">
							<th>
								
								<xsl:value-of select="Month"/>
								<a  href="/reports/requestList.htm?crDateShFrom={StartDate}&amp;crDateShTill={EndDate}&amp;shClinic={/root/srvInfo/ShClinic}&amp;shClinicId={/root/srvInfo/ShClinicId}" target="_blank">
									<sup class="link">детали</sup>
								</a>
							</th>
							<td class="r"><xsl:value-of select="Total"/></td>
							<td class="r"><xsl:value-of select="Transfer"/></td>
							<td class="r">
								<xsl:if test="round(Transfer div Total*100)">
									<xsl:value-of select="round(Transfer div Total*100)"/>%
								</xsl:if>
							</td>
							<td class="r"><xsl:value-of select="Apointment"/></td>
							<td class="r">
								<xsl:if test="round(Apointment div Total*100)">
									<xsl:value-of select="round(Apointment div Total*100)"/>%
								</xsl:if>
							</td>
							<td class="r"><xsl:value-of select="Complete"/></td>
							<td class="r">
								<xsl:if test="round(Complete div Total*100)">
									<xsl:value-of select="round(Complete div Total*100)"/>%
								</xsl:if>
							</td>
							<td class="r"><xsl:value-of select="Reject"/></td>
							<td class="r">
								<xsl:if test="round(Complete div Total*100)">
									<xsl:value-of select="round(Reject div Total*100)"/>%
								</xsl:if>
							</td>
							<td class="r"></td>
						</tr>
					</xsl:for-each>
					<tr>
						<td class="r">ИТОГО:</td>
						<td class="r b"><xsl:value-of select="sum(dbInfo/Reports/Report/Total)"/></td>
						<td class="r b"><xsl:value-of select="sum(dbInfo/Reports/Report/Transfer)"/></td>
						<td></td>
						<td class="r b"><xsl:value-of select="sum(dbInfo/Reports/Report/Apointment)"/></td>
						<td></td>
						<td class="r b"><xsl:value-of select="sum(dbInfo/Reports/Report/Complete)"/></td>
						<td></td>
						<td class="r b"><xsl:value-of select="sum(dbInfo/Reports/Report/Reject)"/></td>
						<td></td>
						<td class="r b"></td>
					</tr>

				</table>
			</div>
			

		</div>
	</xsl:template>

</xsl:transform>

