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
				
				
				$(function(){
					$.datepicker.setDefaults($.extend($.datepicker.regional["ru"]));
					$("#dateFrom").datepicker( {
						changeMonth : true,
						changeYear: true,
						duration : "fast",
						maxDate : "+1y",
						showButtonPanel: true
					});
					$("#dateTill").datepicker( {
						changeMonth : true,
						changeYear: true,
						duration : "fast",
						maxDate : "+1y",
			
						showButtonPanel: true
					});
				});
				

			});
			
			function exportData (type) {
				var type = type || "";
				$('#reportType').val(type);
				switch (type) {
					case 'admission' : 	{
											if (document.forms['data'].dateFrom.value == '' || document.forms['data'].dateTill.value == '') {
												alert ('Необходимо установить дату приёма');
												return void(0);
											} 
										} break;
					case 'apointment' : 	{
											if (document.forms['data'].crDateShFrom.value == '' || document.forms['data'].crDateShTill.value == '') {
												alert ('Необходимо установить дату создания заявки');
												return void(0);
											}
										} break;
				}
				 
				document.forms['data'].action = "/reports/service/exportRequestFull.htm";
				document.forms['data'].submit();
			}	
		</script>
		
		<iframe name="export" id="export" height="0" width="0" frameborder="0" scrolling="No"/>
	</xsl:template>




	<xsl:template match="root">
		<div id="main">
			<h1>Отчет по дошедшим пациентам</h1>

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
							<label>Дата создания:</label>
							<div>
							    c:
							    <input name="crDateShFrom" id="crDateShFrom" style="width:70px" maxlength="12" value="{srvInfo/CrDateShFrom}"/>
							    &#160;по:
							    <input name="crDateShTill" id="crDateShTill" style="width:70px" maxlength="12" value="{srvInfo/CrDateShTill}"/>
							</div>

						</div>
					</div>
					<div class="inBlockFilter ml20">
						<div>
							<label>Дата приёма:</label>
							<div>
							    c:
							    <input name="dateFrom" id="dateFrom" style="width:70px" maxlength="12" value="{srvInfo/CrDateReciveFrom}"/>
							    &#160;по:
							    <input name="dateTill" id="dateTill" style="width:70px" maxlength="12" value="{srvInfo/CrDateReciveTill}"/>
							</div>

						</div>
					</div>
					<div class="inBlockFilter ml20">
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
					
					<div class="inBlockFilter ml20">
						<label>Cостояние заявки: </label>
						<div>
							<select name="shState" id="shState" style="width: 180px">
								<option value="">--- Любой ---</option>
								<xsl:for-each select="dbInfo/StatusRequest4Report/Element">
									<option value="{@id}">
									    <xsl:if test="/root/srvInfo/ShState = @id">
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
				<a href="javascript:exportData('apointment')">Счёт по траифу "за запись"</a>
				<span class="delimiter">|</span>
				<a href="javascript:exportData('admission')">Счёт по траифу "по дошедшим"</a>
				<span class="delimiter">|</span>
				<a href="javascript:exportData('')">Экспорт</a>
				
				<form method="get" name="data" id="data" target="export" action="#">
					<input type="hidden" name="crDateShFrom" value="{srvInfo/CrDateShFrom}"/>
					<input type="hidden" name="crDateShTill" value="{srvInfo/CrDateShTill}"/>
					<input type="hidden" name="dateFrom" value="{srvInfo/CrDateReciveFrom}"/>
					<input type="hidden" name="dateTill" value="{srvInfo/CrDateReciveTill}"/>
					<input type="hidden" name="shClinicId" value="{srvInfo/ShClinicId}"/>
					<input type="hidden" name="reportType" id ="reportType" value=""/>
					<xsl:if test="/root/srvInfo/Branch = '1'">
						<input type="hidden" name="shBranch" value="1"/>
					</xsl:if>
					<input type="hidden" name="shState" value="{srvInfo/ShState}"/>
					
					<input type="hidden" name="sortBy" value="{/root/srvInfo/SortBy}"/>
					<input type="hidden" name="sortType" value="{/root/srvInfo/SortType}"/>
				</form>
			</div>

			<div id="resultSet">
				<xsl:variable name="tdCount" select="11"/>
				<table cellpadding="0" cellspacing="1" width="100%" border="0" class="resultSet">
					<col width="30"/>
					<col width="30"/>
					<col width="120"/>
					<col width="120"/>
					<col/>
					<col width="200"/>
					<col/>
					<col/>
					<col width="200"/>
					<col/>
					<col width="30"/>
					

					<tr>
						<th>#</th>
						<th>Id
							<xsl:call-template name="sortBy">
								<xsl:with-param name="sortBy" select="/root/srvInfo/SortBy"/>
								<xsl:with-param name="sortType" select="/root/srvInfo/SortType"/>
								<xsl:with-param name="field" select="'id'"/>
							</xsl:call-template>
						</th>
						<th>Дата создания
							<xsl:call-template name="sortBy">
								<xsl:with-param name="sortBy" select="/root/srvInfo/SortBy"/>
								<xsl:with-param name="sortType" select="/root/srvInfo/SortType"/>
								<xsl:with-param name="field" select="'crDate'"/>
							</xsl:call-template>
						</th>
						<th>Дата приёма
							<xsl:call-template name="sortBy">
								<xsl:with-param name="sortBy" select="/root/srvInfo/SortBy"/>
								<xsl:with-param name="sortType" select="/root/srvInfo/SortType"/>
								<xsl:with-param name="field" select="'admDate'"/>
							</xsl:call-template>
						</th>
						<th>Врач</th>
						<th>Специальность</th>
						<th>Клиника</th>
						<th>Пациент</th>
						<th>Телефон</th>
						<th>Партнер</th>
						<th>Стоимость, руб.</th>
						<th>&#160;</th>
					</tr>
					<xsl:choose>
						<xsl:when test="dbInfo/RequestList/Element">
							<xsl:for-each select="dbInfo/RequestList/Element">
								<xsl:variable name="class">
									<xsl:choose>
										<xsl:when test="(position() div 2) - floor(position() div 2) &gt; 0">odd</xsl:when>
										<xsl:otherwise>even</xsl:otherwise>
									</xsl:choose>
								</xsl:variable>
								<tr id="tr_{@id}" class="{$class}" backclass="{$class}" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','{$class}')">

									<td><xsl:value-of select="position()+number(/root/dbInfo/Pager/Page[@id = ../@currentPageId]/@start)-1"/></td>
									<td align="right"><xsl:value-of select="@id"/></td>
									<td align="center">
										<xsl:value-of select="CrDate"/>&#160;<xsl:value-of select="CrTime"/>
									</td>
									<td align="center">
										<xsl:value-of select="AppointmentDate"/>&#160;<xsl:value-of select="AppointmentTime"/>
									</td>
									<td>
										<xsl:value-of select="Doctor"/>
									</td>
									<td>
										<xsl:value-of select="Sector"/>
									</td>
									<td>
										<xsl:variable name="clinicId" select="ClinicId"/>
										<xsl:choose>
											<xsl:when test="/root/dbInfo/ClinicList/Element[@id=$clinicId]/ShortName != ''">
												<xsl:value-of select="/root/dbInfo/ClinicList/Element[@id=$clinicId]/ShortName"/>
											</xsl:when>
											<xsl:otherwise>
												<xsl:value-of select="/root/dbInfo/ClinicList/Element[@id=$clinicId]/Name"/>
											</xsl:otherwise>
										</xsl:choose>
										
									</td>
									<td>
										<xsl:value-of select="Client"/>
									</td>
									<td>
										<xsl:value-of select="ClientPhone"/>
									</td>
									<td>
										<xsl:value-of select="PartnerName"/>
									</td>
									<td align="right">
										<xsl:value-of select="format-number(Price,'0.00')"/>
									</td>
									<td align="center">
										<xsl:call-template name="status">
											<xsl:with-param name="id" select="Status"/> 
											<xsl:with-param name="name" select="key('status',Status)/."/>
											<xsl:with-param name="withName" select="'no'"/>
										</xsl:call-template>
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

