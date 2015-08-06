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
		<link type="text/css" href="/css/jquery.jscrollpane.css" rel="stylesheet" media="screen"/>
		<style>
			.redLabel	{cursor: pointer; margin: 2px -5px 0 5px;}
		</style>
		
		<xsl:apply-templates select="root"/>

		<script src="/lib/js/jquery-ui-1.7.2.custom.min.js" type="text/javascript" ></script>
		<script src="/lib/js/ui.datepicker-ru.js" type="text/javascript" ></script>
		<script src='/lib/js/jquery.autocomplete.min.js' type='text/javascript' language="JavaScript"></script>
		<script src='/lib/js/jquery.mousewheel-3.0.js' type='text/javascript' language="JavaScript"></script>
		<script src='/lib/js/jquery.jscrollpane.js' type='text/javascript' language="JavaScript"></script>
		<script src="/reports/js/monthReport.js" type="text/javascript"></script>
		<script>
			$(document).ready(function(){ 
				var pane = $('.scroll-pane').jScrollPane(
					{
						showArrows: true,
						maintainPosition: false,
						stickToBottom: false
					}
				);
			});
			
			function exportData () {
				document.forms['formFilter'].action = "/reports/service/exportMonthRequestReportByClinic.htm";
				document.forms['formFilter'].submit();
				document.forms['formFilter'].action = "";
			}	
		</script>
		
		<iframe name="export" id="export" height="0" width="0" frameborder="0" scrolling="No"/>
	</xsl:template>




	<xsl:template match="root">
		<div id="main">
			<h1>Анализ обращений в клиники по месяцам </h1>

<!--	Фильтр	-->
			<xsl:call-template name="filter">
				<xsl:with-param name="formId" select="'formFilter'"/>
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
					
					<div class="clear"/>
					<div class="inBlockFilter">
						Клиники: 
						<span id="statusFilter" class="link" onclick="$('#ceilWin_multy').show();">
							<xsl:choose>
								<xsl:when test="srvInfo/ClinicList/Clinic and count(srvInfo/ClinicList/Clinic) &lt;= 2">
									<xsl:for-each select="srvInfo/ClinicList/Clinic">
										<xsl:variable name="clinicId" select="."/>
										<xsl:value-of select="/root/dbInfo/ClinicList/Element[@id = $clinicId]/."/>
										<xsl:if test="position() != last()">,&#160;</xsl:if>
									</xsl:for-each>
								</xsl:when>
								<xsl:when test="srvInfo/ClinicList/Clinic and count(srvInfo/ClinicList/Clinic) &gt; 2">
									<xsl:for-each select="srvInfo/ClinicList/Clinic[position() &lt;= 2]">
										<xsl:variable name="clinicId2" select="."/>
										<xsl:value-of select="/root/dbInfo/ClinicList/Element[@id = $clinicId2]/."/>
										<xsl:if test="position() != last()">,&#160;</xsl:if>
									</xsl:for-each>
									[и еще <xsl:value-of select="(count(/root/srvInfo/ClinicList/Clinic) - 2)"/>]
								</xsl:when>

								<xsl:otherwise>Выбрать клинику</xsl:otherwise>
							</xsl:choose>
						</span>
						<div class="ancor">
							<xsl:call-template name="ceilInfo">
								<xsl:with-param name="id" select="'multy'"/>
							</xsl:call-template>
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
				
				
				
				<xsl:variable name="tdCount" select="8"/>
				<table cellpadding="0" cellspacing="1" width="100%" border="0" class="resultSet">
					<col width="200" />
					<col width="150"/>
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
						<th colspan="2">Дошедшиих</th>
					</tr>
					<tr>
						<th class="sub">всего</th>
						<th class="sub">конверсия</th>
						<th class="sub">всего</th>
						<th class="sub">конверсия</th>
						<th class="sub">всего</th>
						<th class="sub">конверсия</th>
					</tr>
					
					<xsl:for-each select="dbInfo/ClinicReports/Clinic">
						<xsl:variable name="clId" select="@id"/>
						<tr>
							<td colspan="{$tdCount}" class="txt16">
								<xsl:value-of select="/root/dbInfo/ClinicList/Element[@id = $clId]"/>
								<xsl:if test="count(/root/dbInfo/ClinicList/Element[@parentId = $clId]) &gt; 0">
									<span class="em txt12">
										(с учетом<xsl:value-of select="count(/root/dbInfo/ClinicList/Element[@parentId = $clId])"/>&#160;<xsl:call-template name="digitVariant">
											<xsl:with-param name="one" select="'филиала'"/>
											<xsl:with-param name="two" select="'филиалов'"/>
											<xsl:with-param name="five" select="'филиалов'"/>
											<xsl:with-param name="digit" select="count(/root/dbInfo/ClinicList/Element[@parentId = $clId])"/>
										</xsl:call-template>)
									</span>
								</xsl:if>
							</td>
						</tr>
						<xsl:for-each select="Report">
							<xsl:variable name="class">
								<xsl:choose>
									<xsl:when test="(position() div 2) - floor(position() div 2) &gt; 0">odd</xsl:when>
									<xsl:otherwise>even</xsl:otherwise>
								</xsl:choose>
							</xsl:variable>
	
							<tr id="tr_{position()}" class="{$class}" backclass="{$class}" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','{$class}')">
								<td class="r b">
									<xsl:value-of select="Month"/>
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
							</tr>
							</xsl:for-each>
							<tr class="txt14 light">
								<td class="l b">
									ИТОГО:
								</td>
								<td class="r b"><xsl:value-of select="sum(Report/Total)"/></td>
								<td class="r b"><xsl:value-of select="sum(Report/Transfer)"/></td>
								<td></td>
								<td class="r b"><xsl:value-of select="sum(Report/Apointment)"/></td>
								<td></td>
								<td class="r b"><xsl:value-of select="sum(Report/Complete)"/></td>
								<td></td>
							</tr>
							<tr><td colspan="{$tdCount}"><div class="null mb20"/></td></tr>
						
					</xsl:for-each>
				</table>
			</div>
			

		</div>
	</xsl:template>
	
	
	
	
	<xsl:template name="ceilInfo">	  
		<xsl:param name="id"/>
		
		<div id="ceilWin_{$id}" class="m0 shd infoEltR hd" style="width: 720px; padding: 10px; min-height: 200px">
			<div>
				<div style="float:left; width: 400px">
					<div>
						<input name="clinicShName" id="clinicShName" value="" class="mr10" style="width: 300px" onkeydown="getClinicByParams()" autocomplete="off"></input>
						<!-- <span class="form" onclick="getClinicByParams()">ПОИСК</span> -->
					</div>
					<div id="searchDoctorResultset" class="scroll-pane wb" style="min-height: 230px; width: 298px; ">
						<div id="searchDoctorResultsetBlock">
							<div id="resultsetPane"></div>
						</div>
					</div> 
					
				</div>
				
				
				<div style="float:right; width: 300px">
					<div class="txt13 b mb20">Список выбранных клиник</div>
					<div>
						<table id="selectedClinicTable" border="0" cellpading ="1" cellspacing="1" width ="100%">
							<col/>
							<col width="20px"/>
							
							<xsl:for-each select="/root/srvInfo/ClinicList/Clinic">
								<xsl:variable name="clId" select="."/>
								<tr id="selectedClinicTr{$clId}" class="" backclass="" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','')">
									<td>
										<input name="clinicList[{.}]" value="{.}" type="hidden"/>
										<xsl:value-of select="/root/dbInfo/ClinicList/Element[@id = $clId]/."/>
									</td>
									<td class="r">
										<span class="i-status arrow-cancel redLabel" onclick="removeFromSelectedClinic({$clId})"/>
									</td>
								</tr>
							</xsl:for-each>
						</table>
					</div>
					<input class="form button" style="width: 100px; margin: 20px 0 10px 0; float: right" type="submit" value="ВЫБРАТЬ"/>
				<!--  	<div class="form" style="width: 100px; margin: 20px 0 10px 0; float: right" sonclick="setFilterLine();$('#ceilWin_{$id}').hide();">ВЫБРАТЬ</div>-->
				</div>
			</div>
			
			<img src="/img/common/clBt.gif" width="15" height="14"  alt="закрыть" style="position: absolute; cursor: pointer; right: 4px; top: 4px;" title="закрыть" onclick="$('#ceilWin_{$id}').hide();setFilterLine();" border="0"/>
		</div>
	</xsl:template>

</xsl:transform>

