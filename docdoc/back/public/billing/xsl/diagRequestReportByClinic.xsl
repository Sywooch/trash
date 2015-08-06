<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:import href="../../lib/xsl/common.xsl"/>
	<xsl:import href="../../lib/xsl/filter.xsl"/>

	<xsl:decimal-format decimal-separator = '.' grouping-separator = ' ' NaN = '0'/>

	<xsl:output method="html" encoding="utf-8"/>

	<xsl:key name="status" match="/root/dbInfo/StatusDict/Element" use="@id"/>
	<xsl:key name="clinic" match="/root/dbInfo/ClinicList/Element" use="@id"/>
	<xsl:key name="contract" match="/root/dbInfo/ContractListDict/Element" use="Id"/>
	<xsl:key name="reject" match="/root/dbInfo/RejectDict/Element" use="Id"/>

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
		<script src="/reports/js/monthDiagReport.js" type="text/javascript"></script>
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
			
			function exportData(clinicId) {
				$("#data input[name='shClinicId']").val(clinicId);
				var $form = $('#data');
				//alert ($form.serialize());
				$form.attr("action","/billing/service/exportDiagrequestList4Clinic.htm");
				$form.submit();
			}
			
			$("div.helpMarker").click( function() {
				$(this).stop(true).delay(300).parent().children().show();
			});
			$("div.helper .closeButton4Window").click( function() {
				$(".helpEltR").hide();
			});
		</script>
		
		<iframe name="export" id="export" height="300" width="800" frameborder="0" scrolling="No"/>
	</xsl:template>




	<xsl:template match="root">
		<div id="main">
			<h1>Звонки по диагностики по клиникам</h1>

<!--	Фильтр	-->
			<xsl:call-template name="filter">
				<xsl:with-param name="formId" select="'formFilter'"/>
				<xsl:with-param name="generalLine">
					<div class="inBlockFilter">
						<div>
						    Счет за дату c:
						    <input name="crDateShFrom" id="crDateShFrom" style="width:70px" maxlength="12" value="{/root/srvInfo/CrDateShFrom}"/>
						    &#160;по:
						    <input name="crDateShTill" id="crDateShTill" style="width:70px" maxlength="12" value="{/root/srvInfo/CrDateShTill}"/>
						    <span class="eraser" title="очистить поле" onclick="$('#crDateShFrom').val('');$('#crDateShTill').val('');"/>
						</div>
						
					</div>
					<div class="clear"/>
					<div class="inBlockFilter">
						Диагностические центры: 
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

								<xsl:otherwise>Выбрать центр</xsl:otherwise>
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
				<form method="get" name="data" id="data" target="export" action="#">
					<input type="hidden" name="dateFrom" value="{/root/srvInfo/CrDateShFrom}"/>
					<input type="hidden" name="dateTill" value="{/root/srvInfo/CrDateShTill}"/>
					<input type="hidden" name="shClinicId" value="{/root/srvInfo/ShClinicId}"/>
					<!-- 
					<xsl:if test="/root/srvInfo/Branch = '1'">
						<input type="hidden" name="shBranch" value="1"/>
					</xsl:if>
					 -->
				</form>
			</div>

			<div id="resultSet">
				
				
				
				<xsl:variable name="tdCount" select="14"/>
				<table cellpadding="0" cellspacing="1" width="100%" border="0" class="resultSet">
					<col width="300" />
					<col width="180" />
					<col width="20" />
					<col width="100"/>
					<col width="100"/>
					<col width="100"/>
					<col width="80"/>
					<col width="80"/>
					<col width="80"/>
					<col width="80"/>
					<col width="80"/>
					<col width="80"/>
					<col width="80"/>
		
					<tr>
						<th rowspan="2">Клиника</th>
						<th rowspan="2">Контракт</th>
						<th rowspan="2"></th>
						<th colspan="2">Всего обращений</th>
						<th colspan="2">Звонки</th>
						<th colspan="2">Отказ</th>
						<th colspan="2">Записано</th>
						<th colspan="2">Дошедшие пациенты</th>
					</tr>
					<tr>
						<th class="sub">всего</th>
						<th class="sub">&gt;30</th>
						<th class="sub">всего</th>
						<th class="sub">&gt;30</th>
						<th class="sub">всего</th>
						<th class="sub">процент</th>
						<th class="sub">всего</th>
						<th class="sub">конверсия</th>
						<th class="sub">всего</th>
						<th class="sub">конверсия</th>
					</tr>
					
					<xsl:for-each select="dbInfo/ClinicReports/Clinic">
						<xsl:variable name="clId" select="@id"/>
						
						<xsl:variable name="contract">
							<xsl:choose>
								<xsl:when test="Settings/ContractId = '4'">ADMISSION</xsl:when>
								<xsl:when test="Settings/ContractId = '3'">RING</xsl:when>
								<xsl:when test="Settings/ContractId = '5'">COMPLETE</xsl:when>
								<xsl:otherwise>OTHER</xsl:otherwise>
							</xsl:choose>
						</xsl:variable>
						
						<xsl:variable name="class">
							<xsl:choose>
								<xsl:when test="(position() div 2) - floor(position() div 2) &gt; 0">odd</xsl:when>
								<xsl:otherwise>even</xsl:otherwise>
							</xsl:choose>
						</xsl:variable>
						
						<tr id="tr_{position()}" class="{$class}" backclass="{$class}" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','{$class}')">
						
								<td>
									<xsl:value-of select="/root/dbInfo/ClinicList/Element[@id = $clId]"/>
									<xsl:if test="count(/root/dbInfo/ClinicList/Element[@parentId = $clId]) &gt; 0">
										<span class="em txt12">
											(с учетом <xsl:value-of select="count(/root/dbInfo/ClinicList/Element[@parentId = $clId])"/>&#160;<xsl:call-template name="digitVariant">
												<xsl:with-param name="one" select="'филиала'"/>
												<xsl:with-param name="two" select="'филиалов'"/>
												<xsl:with-param name="five" select="'филиалов'"/>
												<xsl:with-param name="digit" select="count(/root/dbInfo/ClinicList/Element[@parentId = $clId])"/>
											</xsl:call-template>)
										</span>
									</xsl:if>
								</td>
								<td>
									 <div>
										<xsl:call-template name="statusState">
											<xsl:with-param name="class">diag_contracts i-st-<xsl:value-of select="Settings/ContractId"/></xsl:with-param>
											<xsl:with-param name="withName" select="'yes'"/>
											<xsl:with-param name="name" select="key('contract',Settings/ContractId)/Name"/>
										</xsl:call-template>
									</div>
								</td>
								<td class="r b">
									<span class="i-status excel pnt" onclick="exportData('{@id}')"/>
								</td>
								<td class="r"><xsl:value-of select="Report/Total"/></td>
								<td class="r">
									<xsl:attribute name="class">
										<xsl:choose>
											<xsl:when test="$contract = 'RING'">r bgBlue</xsl:when>
											<xsl:otherwise>r</xsl:otherwise>
										</xsl:choose>
									</xsl:attribute>
									<xsl:value-of select="Report/Total30"/>
								</td>
								<td class="r"><xsl:value-of select="Report/Rings"/></td>
								<td>
									<xsl:value-of select="Report/Rings30"/>
								</td>
								<td class="r"><xsl:value-of select="Report/Reject"/></td>
								<td class="r">
									<xsl:if test="round(Report/Reject div Report/Total*100)">
										<xsl:value-of select="format-number(Report/Reject div Report/Total,'#.0%')"/>
									</xsl:if>
								</td>
								<td>
									<xsl:attribute name="class">
										<xsl:choose>
											<xsl:when test="$contract = 'ADMISSION'">r bgBlue</xsl:when>
											<xsl:otherwise>r</xsl:otherwise>
										</xsl:choose>
									</xsl:attribute>
									<xsl:value-of select="Report/Admission"/>
									
								</td>
								<td class="r">
									<xsl:if test="round(Report/Admission div Report/Total*100)">
										<xsl:value-of select="format-number(Report/Admission div Report/Total,'#.0%')"/>
									</xsl:if>
								</td>
								<td>
									<xsl:attribute name="class">
										<xsl:choose>
											<xsl:when test="$contract = 'COMPLETE'">r bgBlue</xsl:when>
											<xsl:otherwise>r</xsl:otherwise>
										</xsl:choose>
									</xsl:attribute>
									<xsl:value-of select="Report/Complete"/>
								</td>
								<td class="r">
									<xsl:if test="round(Report/Complete div Report/Total*100)">
										<xsl:value-of select="format-number(Report/Complete div Report/Total,'#.0%')"/>
									</xsl:if>
								</td>
							</tr>
							
							
							
						
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
						<input name="clinicType" type="hidden" value="center" />
						<input name="withBranch" type="hidden" value="1" />
						<span class="link" onclick="selectAllClinic()">выбрать все</span>
					</div>
					<div class="null"/>
					<div id="searchDoctorResultset" class="scroll-pane wb" style="min-height: 230px; width: 390px; ">
						<div id="searchDoctorResultsetBlock">
							<div id="resultsetPane"></div>
						</div>
					</div> 
					
				</div>
				
				
				<div style="float:right; width: 300px">
					<div class="txt13 b mb20">Список выбранных центров</div>
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
					<div style="margin: 20px 0 10px 0; ">
						<input class="form button fr" style="width: 100px;" type="submit" value="ВЫБРАТЬ"/>
						<div><span class="link" onclick="removeAllClinic()">удалить все</span></div>
					</div>
					
				</div>
			</div>
			
			<div class="closeButton4Window" title="закрыть" onclick="$('#ceilWin_{$id}').hide();setFilterLine();"/>
		</div>
	</xsl:template>

</xsl:transform>

