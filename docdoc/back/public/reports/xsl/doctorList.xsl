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
						if (row[2] == 0) {
							<![CDATA[	return row[0]; ]]>
						} else {
							<![CDATA[	return "<span class=\"grey\" style=\"margin-left: 5px\">"+row[0]+"</span>"; ]]>
						}		
					}
				}).result(function(event, item) {
					$("#shClinicId").val(item[1]);
					$('#startPage').val("1");
				});
			});
			
			
			/*	Фильтр. Сброс данных	*/
			function clearFilterForm () {
				$("#shClinic").val("");
				$("#shClinicId").val("");
				$("#shBranch").val("");
				$("#shBranch").attr("checked", false);
				
				$("#startPage").val("");
				$("#sortBy").val("");
				$("#sortType").val("");
				
				// сброс диагностик  
//				$('#filter :input').attr('checked',false); 
//				$("#status_0").attr('checked',true);
//				setFilterLine();
				
			}
			
			function clearOther (elt) {
				$("#ceilWin_multy :checkbox").attr("checked", false);	
				$(elt).attr("checked", true);
			}
			
			function setFilterListToNull() {
				$('#status_0').attr('checked',false);
				if ( $("#filter :checkbox[checked]").length == 0 ) {
					$('#status_0').attr('checked',true);
				}
								
			}
			
			function setFilterLine () {
				var str = "";
				$("#filter :checkbox[checked]").each(function (i) {
					if ( str != "" ) {
						str = str + ", " + $(this).parent().text();
					} else {
						str = $(this).parent().text();
					}
				})	
				$("#statusFilter").text(str);
			}
		
			
			function exportData (dataType) {
				$('#dataType').val(dataType); 
				document.forms['data'].action = "/reports/service/exportDoctorListReport.htm";
				document.forms['data'].submit();
			}	
		</script>
		
		<iframe name="export" id="export" height="400" width="1000" frameborder="0" scrolling="No"/>
	</xsl:template>




	<xsl:template match="root">
		<div id="main">
			<h1>Отчет по врачам в клиниках</h1>

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
					<!-- 
					<div class="inBlockFilter ml20">
					    <div>
						    <label title="Изображение установлено">Из.</label>
						    <div>
								<input type="checkbox" name="shImg" id="shImg" value="1">
									<xsl:if test="/root/srvInfo/ShImg = '1'">
										<xsl:attribute name="checked"/>
									</xsl:if>
								</input>
						    </div>
					    </div>
					</div>
					<div class="inBlockFilter ml5">
					    <div>
						    <label title="Установлен год начала практики">Год</label>
						    <div>
								<input type="checkbox" name="shExp" id="shExp" value="1">
									<xsl:if test="/root/srvInfo/ShExp = '1'">
										<xsl:attribute name="checked"/>
									</xsl:if>
								</input>
						    </div>
					    </div>
					</div>
					<div class="inBlockFilter ml5">
					    <div>
						    <label title="Установлена научная степень">Н/с.</label>
						    <div>
								<input type="checkbox" name="shRank" id="shRank" value="1">
									<xsl:if test="/root/srvInfo/ShRank = '1'">
										<xsl:attribute name="checked"/>
									</xsl:if>
								</input>
						    </div>
					    </div>
					</div>
					 -->
					<div class="inBlockFilter ml5">
					    <div>
						    <label title="Возможен выезд на дом">В/д.</label>
						    <div>
								<input type="checkbox" name="shDepart" id="shDepart" value="1">
									<xsl:if test="/root/srvInfo/ShDepart = '1'">
										<xsl:attribute name="checked"/>
									</xsl:if>
								</input>
						    </div>
					    </div>
					</div>            
					<div class="inBlockFilter ml20">
						<label>Статус: </label>
						<div>
							<span id="statusFilter" class="link" onclick="$('#ceilWin_multy').show();">
								<xsl:choose>
									<xsl:when test="/root/srvInfo/StatusList/Status = 'all'">Все состояния</xsl:when>
									<xsl:when test="/root/srvInfo/StatusList/Status">
										<xsl:for-each select="/root/srvInfo/StatusList/Status">
											<xsl:value-of select="key('status',.)/."/>
											<xsl:if test="position() != last()">,&#160;</xsl:if>
										</xsl:for-each>
									</xsl:when>
									<xsl:otherwise>Все состояния</xsl:otherwise>
								</xsl:choose>
							</span>
						</div>
						<div class="ancor">
							<xsl:call-template name="ceilInfo"><xsl:with-param name="id" select="'multy'"/></xsl:call-template>
						</div>
					
						<!-- 
							<label>Статус: </label>
							<div>
								<select name="shStatus" id="shStatus" style="width: 180px">
									<option value="">- - - Любой - - -</option>
									<xsl:for-each select="dbInfo/StatusDict/Element">
										<option value="{@id}">
										    <xsl:if test="/root/srvInfo/ShStatus = @id">
												<xsl:attribute name="selected"/>
										    </xsl:if>
										    <xsl:value-of select="."/>
										</option>
									</xsl:for-each>
								</select>
							</div>
							 -->
						</div>
				</xsl:with-param>
				<xsl:with-param name="addLine">
				<div class="inBlockFilter" style="margin-left: 10px; line-height: 20px">
						<xsl:for-each select="srvInfo/MonthList/Element">
							<span class="link" onclick="$('#crDateShFrom').val('{@start}'); $('#crDateShTill').val('{@end}')"><xsl:value-of select="."/></span>
							<xsl:if test="position() != last()">, </xsl:if>
						</xsl:for-each>
					</div>
				</xsl:with-param>
				<xsl:with-param name="clearFunction">
					clearFilterForm()
				</xsl:with-param>
			</xsl:call-template>
			
			

 
			<div class="actionList">  
				<a href="javascript:exportData('short')">Экспорт основных данных</a>
				<span class="delimiter">|</span>
				<a href="javascript:exportData('full')">Экспорт расширенных данных</a>
				
				<form method="get" name="data" id="data" target="export" action="#">
					<input type="hidden" name="shClinicId" value="{srvInfo/ShClinicId}"/>
					<input type="hidden" name="dataType" id="dataType" value="full"/>

					<input type="hidden" name="shCityId" value="{srvInfo/City/@id}"/>
					
					<xsl:if test="/root/srvInfo/Branch = '1'">
						<input type="hidden" name="shBranch" value="1"/>
					</xsl:if>
					
											
					<xsl:for-each select="/root/srvInfo/StatusList/Status">
						<input type="hidden" name="status['{.}']" value="{.}"/>
					</xsl:for-each>
					
					
					<input type="hidden" name="sortBy" value="{/root/srvInfo/SortBy}"/>
					<input type="hidden" name="sortType" value="{/root/srvInfo/SortType}"/>
				</form>
			</div>

			<div id="resultSet">
				<xsl:variable name="tdCount" select="15"/>
				<table cellpadding="0" cellspacing="1" width="100%" border="0" class="resultSet">
					<col width="30"/>
					<col width="30"/>
					<col/>
					<col width="300"/>
					<col width="100"/>
					<col width="100"/>
					<col/>
					<col width="30"/>
					<col width="30"/>
					<col width="30"/>
					<col width="20"/>
					<col width="20"/>
					<col width="20"/>
					<col width="120"/>
					

					<tr>
						<th>#</th>
						<th>Id
							<xsl:call-template name="sortBy">
								<xsl:with-param name="sortBy" select="/root/srvInfo/SortBy"/>
								<xsl:with-param name="sortType" select="/root/srvInfo/SortType"/>
								<xsl:with-param name="field" select="'id'"/>
							</xsl:call-template>
						</th>
						<th>Врач
							<xsl:call-template name="sortBy">
								<xsl:with-param name="sortBy" select="/root/srvInfo/SortBy"/>
								<xsl:with-param name="sortType" select="/root/srvInfo/SortType"/>
								<xsl:with-param name="field" select="'name'"/>
							</xsl:call-template>
						</th>
						<th>Специальность
							<xsl:call-template name="sortBy">
								<xsl:with-param name="sortBy" select="/root/srvInfo/SortBy"/>
								<xsl:with-param name="sortType" select="/root/srvInfo/SortType"/>
								<xsl:with-param name="field" select="'sector'"/>
							</xsl:call-template>
						</th>
						<th>Цена</th>
						<th>Спец.цена</th>
						
						<th>Клиника</th>
						<th>Изобр.</th>
						<th>Выезд.</th>
						<th>Детский</th>
						<th title="Год начала практики">Год...</th>
						<th colspan="3">Детал.</th>
						<th>Статус
							<xsl:call-template name="sortBy">
								<xsl:with-param name="sortBy" select="/root/srvInfo/SortBy"/>
								<xsl:with-param name="sortType" select="/root/srvInfo/SortType"/>
								<xsl:with-param name="field" select="'status'"/>
							</xsl:call-template>
						</th>
					</tr>
					<xsl:choose>
						<xsl:when test="dbInfo/DoctorList/Element">
							<xsl:for-each select="dbInfo/DoctorList/Element">
								<xsl:variable name="class">
									<xsl:choose>
										<xsl:when test="(position() div 2) - floor(position() div 2) &gt; 0">odd</xsl:when>
										<xsl:otherwise>even</xsl:otherwise>
									</xsl:choose>
								</xsl:variable>
								<tr id="tr_{@id}" class="{$class}" backclass="{$class}" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','{$class}')">

									<td><xsl:value-of select="position()"/></td>
									<td align="right"><xsl:value-of select="@id"/></td>
									<td>
										<a href="{Url}">
											<xsl:value-of select="Name"/>
										</a>
									</td>
									<td>
										<xsl:for-each select="SectorList/Sector">
											<xsl:value-of select="."/>
											<xsl:if test="position() != last()">, </xsl:if>	
										</xsl:for-each>
									</td>
									<td class="r">
										<xsl:if test="Price != ''">
											<xsl:value-of select="format-number(Price, '0.00')"/>
										</xsl:if>
									</td>
									<td class="r">
										<xsl:if test="SpecialPrice != ''">
											<xsl:value-of select="format-number(SpecialPrice, '0.00')"/>
										</xsl:if>
									</td>
									
									<td>
										<xsl:for-each select="ClinicList/Clinic">
											<xsl:value-of select="ShortName"/>
											<xsl:if test="position() != last()">, </xsl:if>	
										</xsl:for-each>
									</td>
									<td align="center">
										<xsl:choose>
											<xsl:when test="Image and Image != '' ">
												<img src="/img/icon/agent.png"/>
											</xsl:when>
										</xsl:choose>
									</td>
									<td align="center">
										<xsl:choose>
											<xsl:when test="IsDeparture = '1' "><img src="/img/icon/ok.png"/></xsl:when>
											<xsl:otherwise/>
										</xsl:choose>
									</td>
									<td align="center">
										<xsl:choose>
											<xsl:when test="IsKidsReception = '1' "><img src="/img/icon/ok.png"/></xsl:when>
											<xsl:otherwise/>
										</xsl:choose>
									</td>
									<td align="center">
										<xsl:choose>
											<xsl:when test="ExperienceYear and ExperienceYear != '0'">
												<xsl:value-of select="ExperienceYear"/>
											</xsl:when>
											<xsl:otherwise>
												-
											</xsl:otherwise>
										</xsl:choose>
									</td>
									<td>
										<xsl:if test="Degree and Degree != 0">D</xsl:if>
									</td>
									<td>
										<xsl:if test="Category and Category != 0">С</xsl:if>
									</td>
									<td>
										<xsl:if test="Rank and Rank != 0">R</xsl:if>
									</td>
									<td>
										<xsl:variable name="status" select="Status"/>
										<xsl:call-template name="statusState">
											<xsl:with-param name="class">doctor i-st-<xsl:value-of select="Status"/></xsl:with-param>
											<xsl:with-param name="withName" select="'yes'"/>
											<xsl:with-param name="name" select="key('status',Status)/."/>
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
				<div class="mt20">ИТОГО: 
					<span>врачей - <xsl:value-of select="count(dbInfo/DoctorList/Element)"/></span>
				</div>
			</div>
		</div>
	</xsl:template>
	
	
	
	
	
	<xsl:template name="ceilInfo">	  
		<xsl:param name="id"/>
		
		<style>
			#ceilWin_<xsl:value-of select="$id"/> input	{height: auto;}
			.liList	{list-style: none} 
		</style>
		<div id="ceilWin_{$id}" class="m0 shd infoEltR hd" style="width: 250px;">
			<ul style="margin: 0 auto; padding: 0; width:100%">
				<li class="liList mb5">
					<label>
						<input class="checkBox4Text" name="status[0]" id="status_0" type="Checkbox" value="all" autocomplete="off" onchange="clearOther(this)">
							<xsl:if test="/root/srvInfo/StatusList/Status = 'all' or not(/root/srvInfo/StatusList/Status)">
								<xsl:attribute name="checked"/>
							</xsl:if>
						</input>
						Все состояния
					</label>
				</li>
				<xsl:for-each select="dbInfo/StatusDict/Element">
					<xsl:variable name="pos" select="position()"/>
					<li class="liList mb5">
						<label>
							<input class="checkBox4Text" name="status['{@id}']" id="status_{@id}" type="Checkbox" value="{@id}" onclick="setFilterListToNull()">
								<xsl:if test="/root/srvInfo/StatusList/Status = @id"><xsl:attribute name="checked"/></xsl:if>
							</input>
							<xsl:value-of select="."/>
						</label>
					</li>			
				</xsl:for-each>
			</ul>
			<div class="closeButton4Window" title="закрыть" onclick="$('#ceilWin_{$id}').hide(); setFilterLine()"/>
		</div>
	</xsl:template>

</xsl:transform>

