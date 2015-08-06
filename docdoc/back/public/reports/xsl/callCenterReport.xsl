<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:import href="../../lib/xsl/common.xsl"/>
	<xsl:import href="../../request/xsl/common.xsl"/>
	<xsl:import href="../../lib/xsl/filter.xsl"/>

	<xsl:decimal-format decimal-separator = '.' grouping-separator = ' ' NaN = '0'/>

	<xsl:output method="html" encoding="utf-8"/>


	<xsl:template match="/">
		<link type="text/css" href="/css/jquery-ui-1.7.2.custom.css" rel="stylesheet" />
		
		<xsl:apply-templates select="root"/>

		<script src="/lib/js/jquery-ui-1.7.2.custom.min.js" type="text/javascript" ></script>
		<script src="/lib/js/ui.datepicker-ru.js" type="text/javascript" ></script>
		<script src="/reports/js/report.js" type="text/javascript"></script>
		<script></script>
		
		<iframe name="export" id="export" height="0" width="0" frameborder="0" scrolling="No"/>
	</xsl:template>




	<xsl:template match="root">
		<div id="main">
			<h1>Эффективность работы операторов</h1>

<!--	Фильтр	-->
			<xsl:call-template name="filter">
				<xsl:with-param name="formId" select="'filter'"/>
				<xsl:with-param name="generalLine">
					
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
				</xsl:with-param>
			</xsl:call-template>
			
			

 
			
			<h2>Отчет по реакции операторов на заявку с сайта или подбор врача</h2>
			<div id="resultSet">
				<xsl:variable name="tdCount" select="8"/>
				<table cellpadding="0" cellspacing="1" width="100%" border="0" class="resultSet">
					<col width="30"/>
					<col width="200"/>
					<col width="100"/>
					<col width="80"/>
					<col width="80"/>
					<col width="80"/>
					<col width="80"/>
					<col width="80"/>
					<col width="80"/>
					<col/>

					<tr>
						<th rowspan="2">#</th>
						<th rowspan="2">Оператор</th>
						<th rowspan="2">Всего за период</th>
						<th colspan = "6">Время реакции на заявку c 9:00 до 21:00 <br/>(без учета созданных в ночное время)</th>
						<th rowspan="2"></th>
					</tr>
					<tr>
						<th class="sub">всего</th>
						<th class="sub"> &lt; 1 мин.</th>
						<th class="sub">1 - 5 мин.</th>
						<th class="sub">5 - 15 мин.</th>
						<th class="sub">15 - 60 мин.</th>
						<th class="sub">более часа</th>
					</tr>
					<xsl:choose>
						<xsl:when test="dbInfo/OwnerData/Report">
							<xsl:for-each select="dbInfo/OwnerData/Report">
								<xsl:variable name="class">
									<xsl:choose>
										<xsl:when test="(position() div 2) - floor(position() div 2) &gt; 0">odd</xsl:when>
										<xsl:otherwise>even</xsl:otherwise>
									</xsl:choose>
								</xsl:variable>
								<tr id="tr_{position()}" class="{$class}" backclass="{$class}" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','{$class}')">

									<td><xsl:value-of select="position()"/></td>
									<td>
										<a href="/request/index.htm?shOwner={Owner/@id}&amp;crDateShFrom={/root/srvInfo/CrDateShFrom}&amp;crDateShTill={/root/srvInfo/CrDateShTill}&amp;shType=0" target="_blanc"><xsl:value-of select="Owner"/></a>
									</td>
									<td align="center">
										<xsl:value-of select="Total"/>
									</td>
									<td align="center">
										<xsl:value-of select="number(Reports/Extra)+number(Reports/Fast)+number(Reports/Middle)+number(Reports/Slow)+number(Reports/FuckUp)"/>
									</td>
									<td align="center">
										<xsl:value-of select="Reports/Extra"/>
									</td>
									<td align="center">
										<xsl:value-of select="Reports/Fast"/>
									</td>
									<td align="center">
										<xsl:value-of select="Reports/Middle"/>
									</td>
									<td align="center">
										<xsl:value-of select="Reports/Slow"/>
									</td>
									<td align="center">
										<xsl:value-of select="Reports/FuckUp"/>
									</td>
									<td align="center">
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
			
			
			
			
			<h2>Отчет по времени обработки заявки оператором при входящем звонке</h2>
			<div id="resultSet">
				<xsl:variable name="tdCount" select="7"/>
				<table cellpadding="0" cellspacing="1" width="100%" border="0" class="resultSet">
					<col width="30"/>
					<col width="200"/>
					<col width="100"/>
					<col width="80"/>
					<col width="80"/>
					<col width="80"/>
					<col width="80"/>
					<col width="80"/>
					<col width="80"/>
					<col/>

					<tr>
						<th rowspan="2">#</th>
						<th rowspan="2">Оператор</th>
						<th rowspan="2">Всего за период</th>
						<th colspan = "6">Время реакции на заявку c 9:00 до 21:00 <br/>(без учета созданных в ночное время)</th>
						<th rowspan="2"></th>
					</tr>
					<tr>
						<th class="sub">всего</th>
						<th class="sub"> &lt; 1 мин.</th>
						<th class="sub">1 - 5 мин.</th>
						<th class="sub">5 - 15 мин.</th>
						<th class="sub">15 - 60 мин.</th>
						<th class="sub">более часа</th>
					</tr>
					<xsl:choose>
						<xsl:when test="dbInfo/OwnerDataTwo/Report">
							<xsl:for-each select="dbInfo/OwnerDataTwo/Report">
								<xsl:variable name="class">
									<xsl:choose>
										<xsl:when test="(position() div 2) - floor(position() div 2) &gt; 0">odd</xsl:when>
										<xsl:otherwise>even</xsl:otherwise>
									</xsl:choose>
								</xsl:variable>
								<tr id="tr_{position()}" class="{$class}" backclass="{$class}" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','{$class}')">

									<td><xsl:value-of select="position()"/></td>
									<td>
										<a href="/request/index.htm?shOwner={Owner/@id}&amp;crDateShFrom={/root/srvInfo/CrDateShFrom}&amp;crDateShTill={/root/srvInfo/CrDateShTill}&amp;shType=2" target="_blanc"><xsl:value-of select="Owner"/></a>
									</td>
									<td align="center">
										<xsl:value-of select="Total"/>
									</td>
									<td align="center">
										<xsl:value-of select="number(Reports/Extra)+number(Reports/Fast)+number(Reports/Middle)+number(Reports/Slow)+number(Reports/FuckUp)"/>
									</td>
									<td align="center">
										<xsl:value-of select="Reports/Extra"/>
									</td>
									<td align="center">
										<xsl:value-of select="Reports/Fast"/>
									</td>
									<td align="center">
										<xsl:value-of select="Reports/Middle"/>
									</td>
									<td align="center">
										<xsl:value-of select="Reports/Slow"/>
									</td>
									<td align="center">
										<xsl:value-of select="Reports/FuckUp"/>
									</td>
									<td align="center">
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
				
				
				
				
			<h2>Отчет по времени обработки заявки оператором при записи с сайта</h2>
			<div id="resultSet">
				<xsl:variable name="tdCount" select="7"/>
				<table cellpadding="0" cellspacing="1" width="100%" border="0" class="resultSet">
					<col width="30"/>
					<col width="200"/>
					<col width="100"/>
					<col width="80"/>
					<col width="80"/>
					<col width="80"/>
					<col width="80"/>
					<col width="80"/>
					<col width="80"/>
					<col/>

					<tr>
						<th rowspan="2">#</th>
						<th rowspan="2">Оператор</th>
						<th rowspan="2">Всего за период</th>
						<th colspan = "6">Время реакции на заявку c 9:00 до 21:00 <br/>(без учета созданных в ночное время)</th>
						<th rowspan="2"></th>
					</tr>
					<tr>
						<th class="sub">всего</th>
						<th class="sub"> &lt; 1 мин.</th>
						<th class="sub">1 - 5 мин.</th>
						<th class="sub">5 - 15 мин.</th>
						<th class="sub">15 - 60 мин.</th>
						<th class="sub">более часа</th>
					</tr>
					<xsl:choose>
						<xsl:when test="dbInfo/OwnerDataThree/Report">
							<xsl:for-each select="dbInfo/OwnerDataThree/Report">
								<xsl:variable name="class">
									<xsl:choose>
										<xsl:when test="(position() div 2) - floor(position() div 2) &gt; 0">odd</xsl:when>
										<xsl:otherwise>even</xsl:otherwise>
									</xsl:choose>
								</xsl:variable>
								<tr id="tr_{position()}" class="{$class}" backclass="{$class}" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','{$class}')">

									<td><xsl:value-of select="position()"/></td>
									<td>
										<a href="/request/index.htm?shOwner={Owner/@id}&amp;crDateShFrom={/root/srvInfo/CrDateShFrom}&amp;crDateShTill={/root/srvInfo/CrDateShTill}&amp;shType=2" target="_blanc"><xsl:value-of select="Owner"/></a>
									</td>
									<td align="center">
										<xsl:value-of select="Total"/>
									</td>
									<td align="center">
										<xsl:value-of select="number(Reports/Extra)+number(Reports/Fast)+number(Reports/Middle)+number(Reports/Slow)+number(Reports/FuckUp)"/>
									</td>
									<td align="center">
										<xsl:value-of select="Reports/Extra"/>
									</td>
									<td align="center">
										<xsl:value-of select="Reports/Fast"/>
									</td>
									<td align="center">
										<xsl:value-of select="Reports/Middle"/>
									</td>
									<td align="center">
										<xsl:value-of select="Reports/Slow"/>
									</td>
									<td align="center">
										<xsl:value-of select="Reports/FuckUp"/>
									</td>
									<td align="center">
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


		</div>
	</xsl:template>

</xsl:transform>

