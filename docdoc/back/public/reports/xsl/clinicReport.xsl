<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:import href="../../lib/xsl/common.xsl"/>
	<xsl:import href="../../request/xsl/common.xsl"/>
	<xsl:import href="../../lib/xsl/filter.xsl"/>
	<xsl:import href="../../lib/xsl/sortBy.xsl"/>

	<xsl:output method="html" encoding="utf-8"/>

	<xsl:key name="status" match="/root/dbInfo/StatusDict/Element" use="@id"/>
	<xsl:key name="clinic" match="/root/dbInfo/ClinicList/Element" use="@id"/>
	<xsl:key name="contract" match="/root/dbInfo/ContractDict/Element" use="@id"/>

	<xsl:template match="/">
		<link type="text/css" href="/css/jquery.autocomplete.css" rel="stylesheet" media="screen"/>
		<link type="text/css" href="/css/jquery-ui-1.7.2.custom.css" rel="stylesheet" />
		
		<xsl:apply-templates select="root"/>

		<script src="/lib/js/jquery-ui-1.7.2.custom.min.js" type="text/javascript" ></script>
		<script src="/lib/js/ui.datepicker-ru.js" type="text/javascript" ></script>
		<script src='/lib/js/jquery.autocomplete.min.js' type='text/javascript' language="JavaScript"></script>
		<script src="/reports/js/report.js" type="text/javascript"></script>
		<script type="text/javascript" src="/lib/js/checkUncheck.js"></script>
		<script>
			$(document).ready(function(){ 
			});
			
			function exportData (path) {
				var n = $("#data input:checked").length;
				if ( n >= 1 ) {
					document.forms['data'].action = path;
					document.forms['data'].submit();
				} else {
					alert("Вы не отметили позиции для экспорта.");
				}
			}	
		</script>
		
		<iframe name="export" id="export" height="0" width="0" frameborder="0" scrolling="No"/>
	</xsl:template>




	<xsl:template match="root">
		<div id="main">
			<h1>Отчеты для клиник</h1>

<!--	Фильтр	-->
			<xsl:call-template name="filter">
				<xsl:with-param name="formId" select="'filter'"/>
				<xsl:with-param name="startPage" select="dbInfo/Pager/@currentPageId"/>
				<xsl:with-param name="generalLine">
					<div class="inBlockFilter">
						<div>
							<label>Дата:</label>
							<div>
							    &#160;&#160;с:
							    <input name="crDateShFrom" id="crDateShFrom" style="width:70px" maxlength="12" value="{srvInfo/CrDateShFrom}"/>
							    &#160;по:
							    <input name="crDateShTill" id="crDateShTill" style="width:70px" maxlength="12" value="{srvInfo/CrDateShTill}"/>
							</div>
							<div style="margin-left: 20px">
								<xsl:for-each select="srvInfo/MonthList/Element">
									<span class="link" onclick="$('#crDateShFrom').val('{@start}'); $('#crDateShTill').val('{@end}')"><xsl:value-of select="."/></span>
									<xsl:if test="position() != last()">, </xsl:if>
								</xsl:for-each>
							</div>
						</div>
					</div>
					
					<div class="inBlockFilter" style="margin-left: 30px">
						<label>Тип: </label>
						<div>
							<select name="type" id="type" style="width: 150px">
								<option value="">--- Любой ---</option>
								<option value="clinic" style="background:url('/img/icon/hospital16.png') no-repeat; padding-left: 20px">
								    <xsl:if test="/root/srvInfo/Type = 'clinic'">
										<xsl:attribute name="selected"/>
								    </xsl:if>
								    Клиника
								</option>
								<option value="center" style="background:url('/img/icon/dc16.png') no-repeat; padding-left: 20px">
								    <xsl:if test="/root/srvInfo/Type = 'center'">
										<xsl:attribute name="selected"/>
								    </xsl:if>
								    Диагн.центр
								</option>
								<option value="privatDoctor" style="background:url('/img/icon/privatDoctor.png') no-repeat; padding-left: 20px">
								    <xsl:if test="/root/srvInfo/Type = 'privatDoctor'">
										<xsl:attribute name="selected"/>
								    </xsl:if>
								    Частный врач
								</option>
								
							</select>
						</div>
					</div>
				</xsl:with-param>

			</xsl:call-template>
<!-- **************  -->
			
			
 
			<div class="actionList">  
				<a href="javascript:exportData('/reports/service/groupExportRequest4Clinic.php')">Групповой экспорт детализированных отчетов (zip)</a>
				<span class="delimiter">|</span>
				<a href="javascript:exportData('/reports/service/groupExportSummary4Clinic.php')">Групповой экспорт суммарных отчетов</a>
			</div>

			
 
			<div id="resultSet">
				<form method="post" name="data" id="data" action="#">
					<input type="hidden" name="dateFrom" value="{srvInfo/CrDateShFrom}"/>
					<input type="hidden" name="dateTill" value="{srvInfo/CrDateShTill}"/>
					
				<xsl:variable name="tdCount" select="10"/>
				<table cellpadding="0" cellspacing="1" width="100%" border="0" class="resultSet">
					<col width="30"/>
					<col/>
					<col width="270"/>
					<col width="250"/>
					<col width="80"/>
					<col width="80"/>
					<col width="80"/>
					<col width="80"/>
					<col width="20"/>
					

					<tr>
						<th>#</th>
						<th>Клиника</th>
						<th>Вид отчёта</th>
						<th>Контракт</th>
						<th>Всего переведено</th>
						<th>Записано</th>
						<th>Приём состоялся</th>
						<th>Отказ</th>
						<th>
							<div style="margin: 0 1px 0 5px" id="checkuncheck" class="check checkuncheck" onclick="checkUncheck('data');" title="выделить все / снять выделение">&#160;</div>
						</th>
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
										<span>
											<xsl:if test="ParentId != '0'"><xsl:attribute name="style">margin-left: 40px</xsl:attribute></xsl:if>
											<xsl:value-of select="Name"/>
										</span>
									</td>
									<td>
										<a href="/reports/requestList.htm?crDateShFrom={/root/srvInfo/CrDateShFrom}&amp;crDateShTill={/root/srvInfo/CrDateShTill}&amp;shClinic={Name}&amp;shClinicId={@id}" target="_blank">
											Детализированный
										</a>
										<span class="delimiter">|</span>
										<a href="/reports/requestListSummary.htm?crDateShFrom={/root/srvInfo/CrDateShFrom}&amp;crDateShTill={/root/srvInfo/CrDateShTill}&amp;shClinic={Name}&amp;shClinicId={@id}" target="_blank">
											Суммарный за период
										</a>
									</td>
									<td>
										<xsl:value-of select="key('contract',ContractId)/."/>
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
									<td>
										<input type="checkbox" value="{@id}" name="line[{@id}]" id="chbox{@id}" style="border:0px; margin: 0; padding: 0" onchange="(this.checked)?($('#tr_{@id}').attr('class','trSelected')):($('#tr_{@id}').attr('class','{$class}'))" autocomplete="off"/>
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
				</form>
				
			</div>

		</div>
	</xsl:template>

</xsl:transform>

