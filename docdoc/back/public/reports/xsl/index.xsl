<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:import href="../../lib/xsl/common.xsl"/>
	<xsl:import href="../../lib/xsl/pager.xsl"/>
	<xsl:import href="../../lib/xsl/filter.xsl"/>

	<xsl:output method="html" encoding="utf-8"/>


	<xsl:template match="/">
		<link type="text/css" href="/css/jquery-ui-1.7.2.custom.css" rel="stylesheet" />
		
		<xsl:apply-templates select="root"/>

		<script src="/lib/js/jquery-ui-1.7.2.custom.min.js" type="text/javascript" ></script>
		<script src="/lib/js/ui.datepicker-ru.js" type="text/javascript" ></script>
		<script src="/reports/js/report.js" type="text/javascript"></script>
		<script>
			$(document).ready(function(){ });
			
			function exportData () {
				document.forms['data'].action = "/reports/service/exportReport.htm";
				document.forms['data'].submit();
			}	
		</script>
		
		<iframe name="export" id="export" height="200" width="1000" frameborder="0" scrolling="No"/>
	</xsl:template>




	<xsl:template match="root">
		<div id="main">
			<h1>Отчет по звонкам на диагностике</h1>

<!--	Фильтр	-->
			<xsl:call-template name="filter">
				<xsl:with-param name="formId" select="'filter'"/>
				<xsl:with-param name="startPage" select="dbInfo/Pager/@currentPageId"/>
				<xsl:with-param name="generalLine">
					<div class="inBlockFilter">
						<div>
							<label>Период звонков:</label>
							<div>
							    c:
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
				</form>
			</div>



			<div id="resultSet">
				<table cellpadding="0" cellspacing="1" width="100%" border="0" class="resultSet">
					<tr>
						<th colspan="2">Даты</th>
						<th>Всего</th>
						<xsl:for-each select="dbInfo/ClinicList/Element">
							<th style="width: 150px">
								<xsl:choose>
									<xsl:when test="ShortName != ''"><xsl:value-of select="ShortName"/></xsl:when>
									<xsl:otherwise><xsl:value-of select="@id"/></xsl:otherwise>
								</xsl:choose>
							</th>
						</xsl:for-each>
					</tr>
					<tr id="tr_1" class="odd" backclass="odd" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','odd')">
						<th colspan="2">20c</th>
						<td class="r">
							<strong>
								<xsl:value-of select="sum(/root/dbInfo/TotalData/Day/Clinic/ValidData/@uniq)"/>
							</strong>
						</td>
						<xsl:for-each select="dbInfo/ClinicList/Element">
							<xsl:variable name="id" select="@id"/>
							<td class="r">
								<strong>
									<xsl:value-of select="sum(/root/dbInfo/TotalData/Day/Clinic[@id=$id]/ValidData/@uniq)"/>
								</strong>
							</td>
						</xsl:for-each>
					</tr>
					
					
					<tr id="tr_2" class="even" backclass="even" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','even')">
						<th colspan="2">30c</th>
						<td class="r">
							<strong>
								<xsl:value-of select="sum(/root/dbInfo/TotalData/Day/Clinic/ValidDataII/@uniq)"/>
							</strong>
						</td>
						<xsl:for-each select="dbInfo/ClinicList/Element">
							<xsl:variable name="id" select="@id"/>
							<td class="r">
								<strong>
									<xsl:value-of select="sum(/root/dbInfo/TotalData/Day/Clinic[@id=$id]/ValidDataII/@uniq)"/>
								</strong>
							</td>
						</xsl:for-each>
					</tr>
					
					
					<xsl:for-each select="dbInfo/DayList/Day">
						<xsl:variable name="day" select="."/>
						<tr class="odd" backclass="odd" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','odd')">
							<th rowspan="4">
								<xsl:value-of select="substring(.,1,5)"/>
							</th>
							<th title="Всего звонков">В</th>
							<td class="r">
								<strong>
									<xsl:value-of select="sum(/root/dbInfo/TotalData/Day[@day = $day]/Clinic/Data/@total)"/>
								</strong>
							</td>
							<xsl:for-each select="/root/dbInfo/ClinicList/Element">
								<xsl:variable name="id" select="@id"/>
								<td class="r">
									<xsl:value-of select="/root/dbInfo/TotalData/Day[@day = $day]/Clinic[@id=$id]/Data/@total"/>
								</td>
							</xsl:for-each>
						</tr>
						<tr class="even" backclass="even" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','even')">
							<th title="Уникальные">У</th>
							<td class="r">
								<strong>
									<xsl:value-of select="sum(/root/dbInfo/TotalData/Day[@day = $day]/Clinic/Data/@uniq)"/>
								</strong>
							</td>
							<xsl:for-each select="/root/dbInfo/ClinicList/Element">
								<xsl:variable name="id" select="@id"/>
								<td class="r">
									<xsl:value-of select="/root/dbInfo/TotalData/Day[@day = $day]/Clinic[@id=$id]/Data/@uniq"/>
								</td>
							</xsl:for-each>
						</tr>
						<tr class="odd" backclass="odd" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','odd')">
							<th title="Дубли">Д</th>
							<td class="r">
								<strong>
									<xsl:value-of select="sum(/root/dbInfo/TotalData/Day[@day = $day]/Clinic/Data/@total) -  sum(/root/dbInfo/TotalData/Day[@day = $day]/Clinic/Data/@uniq)"/>
								</strong>
							</td>
							<xsl:for-each select="/root/dbInfo/ClinicList/Element">
								<xsl:variable name="id" select="@id"/>
								<td class="r">
									<xsl:value-of select="number(/root/dbInfo/TotalData/Day[@day = $day]/Clinic[@id=$id]/Data/@total) -  number(/root/dbInfo/TotalData/Day[@day = $day]/Clinic[@id=$id]/Data/@uniq)"/>
								</td>
							</xsl:for-each>
						</tr>
						<tr class="even" backclass="even" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','even')">
							<th title="Валидные &gt; 20 сек">20</th>
							<td class="r">
								<strong>
									<xsl:value-of select="sum(/root/dbInfo/TotalData/Day[@day = $day]/Clinic/ValidData/@uniq)"/>
								</strong>
							</td>
							<xsl:for-each select="/root/dbInfo/ClinicList/Element">
								<xsl:variable name="id" select="@id"/>
								<td class="r">
									<xsl:value-of select="/root/dbInfo/TotalData/Day[@day = $day]/Clinic[@id=$id]/ValidData/@uniq"/>
								</td>
							</xsl:for-each>
						</tr>
					</xsl:for-each>

					
					
					<!-- 
					<tr>
						<th colspan="2">Итого</th>
						<xsl:for-each select="dbInfo/ClinicList/Element">
							<xsl:variable name="id" select="@id"/>
							<td>
								<strong>
								</strong>
							</td>
								
						<xsl:for-each select="/root/dbInfo/DayList/Day">
							<xsl:variable name="day" select="."/>
							<th align="center">	
								<strong>								
									<xsl:value-of select="sum(/root/dbInfo/TotalData/Day[@day = $day]/Clinic/Data/@total)"/>
								</strong>
							</th>
							<th align="center">	
								<strong>								
									<xsl:value-of select="sum(/root/dbInfo/TotalData/Day[@day = $day]/Clinic/Data/@uniq)"/>
								</strong>
							</th>
							<th align="center">	
								<strong>								
									<xsl:value-of select="sum(/root/dbInfo/TotalData/Day[@day = $day]/Clinic/Data/@total) -  sum(/root/dbInfo/TotalData/Day[@day = $day]/Clinic/Data/@uniq)"/>
								</strong>
							</th>
							<th align="center">	
								<strong>								
									<xsl:value-of select="sum(/root/dbInfo/TotalData/Day[@day = $day]/Clinic/ValidData/@uniq)"/>
								</strong>
							</th>
						</xsl:for-each>
					</tr>
					 -->
				</table>
			</div>
			

		</div>
	</xsl:template>

</xsl:transform>

