<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:import href="../../lib/xsl/modalWindow.xsl"/>
	<xsl:import href="../../lib/xsl/common.xsl"/>
	<xsl:import href="../../lib/xsl/pager.xsl"/>
	<xsl:import href="../../lib/xsl/filter.xsl"/>

	<xsl:output method="html" encoding="utf-8"/>

	<xsl:key name="logDict" match="/root/dbInfo/LogDict/Element" use="@id"/>

	<xsl:template match="/">
		<xsl:apply-templates select="root"/>
		<div style="position: absolute; margin: 0; top:0; left:0; z-index: 0;">
			<xsl:call-template name="modalWindow">
				<xsl:with-param name="id" select="'modalWin'"/>
				<xsl:with-param name="title" select="'Логи'"/>
				<xsl:with-param name="width" select="'650'"/>
			</xsl:call-template>
		</div>
		<div style="position: absolute; margin: 0; top:0; left:0; z-index: 0;">
			<xsl:call-template name="modalWindow">
				<xsl:with-param name="id" select="'imgWin'"/>
				<xsl:with-param name="title" select="'Изображение'"/>
				<xsl:with-param name="width" select="'1010'"/>
			</xsl:call-template>
		</div>

		<link type="text/css" href="/css/jquery-ui-1.7.2.custom.css" rel="stylesheet" />
		<link rel="stylesheet" type="text/css" href="/css/jquery.autocomplete.css" media="screen"/>


		<script type="text/javascript" src="/lib/js/fileuploader.js"></script>
		<script type='text/javascript' src='/lib/js/jquery.ajaxQueue.js'></script>
		<script type='text/javascript' src='/lib/js/jquery.autocomplete.js'></script>
		<script type="text/javascript" src="/lib/js/jquery-ui-1.7.2.custom.min.js"></script>
		<script type="text/javascript" src="/lib/js/ui.datepicker-ru.js"></script>
		<script>

			$(document).ready(function() {
				$(function(){
					$.datepicker.setDefaults($.extend($.datepicker.regional["ru"]));
					$("#crDateShFrom").datepicker( {
						changeMonth : true,
						changeYear: true,
						duration : "fast",
						maxDate : "+1y",

						showButtonPanel: true
					});
					$("#crDateShTill").datepicker( {
						changeMonth : true,
						changeYear: true,
						duration : "fast",
						maxDate : "+1y",

						showButtonPanel: true
					});
				});
			});


		</script>
	</xsl:template>




	<xsl:template match="root">
		<div id="main">
			<h1>Лог-список</h1>

<!--	Фильтр	-->

			<xsl:call-template name="filter">
				<xsl:with-param name="formId" select="'filter'"/>
				<xsl:with-param name="startPage" select="dbInfo/Pager/@currentPageId"/>
				<xsl:with-param name="generalLine">
					<div class="inBlockFilter">
						<div>
							<label>Дата создания записи:</label>
							<div>
							    c:
							    <input name="crDateShFrom" id="crDateShFrom" style="width:80px" maxlength="12" value="{srvInfo/CrDateShFrom}"/>
							    &#160;по:
							    <input name="crDateShTill" id="crDateShTill" style="width:80px" maxlength="12" value="{srvInfo/CrDateShTill}"/>
							</div>

						</div>
					</div>
					<div class="inBlockFilter" style="margin-left: 20px">
					    <div>
						    <label>Пользователь (login):</label>
						    <div>
								<input name="login" id="login" style="width: 130px" maxlength="25"  value="{srvInfo/Login}"/>
						    </div>
					    </div>
					</div>
					<div class="inBlockFilter">
						<label>Событие: </label>
						<div>
							<select name="idLogCode" id="idLogCode" style="width: 250px">
								<option value="">--- Любой тип ---</option>
								<xsl:for-each select="dbInfo/LogDict/Element">
									<option value="{@id}">
									    <xsl:if test="@id = /root/srvInfo/IdLogCode">
											<xsl:attribute name="selected"/>
									    </xsl:if>
									    <xsl:value-of select="."/>
									    (<xsl:value-of select="@id"/>)
									</option>
								</xsl:for-each>
							</select>
						</div>
					</div>

				</xsl:with-param>

				<xsl:with-param name="addLine">

				</xsl:with-param>
			</xsl:call-template>







			<div id="resultSet">
				<xsl:variable name="tdCount" select="6"/>
				<table cellpadding="0" cellspacing="1" width="100%" border="0" class="resultSet">
					<col width="30"/>
					<col width="30"/>
					<col width="100"/>
					<col width="400"/>
					<col width="240"/>
					

					<tr>
						<th>#</th>
						<th>Id</th>
						<th>Дата/время</th>
						<th>Описание действия</th>
						<th>Событие</th>
						<th>Пользователь</th>

					</tr>
					<xsl:choose>
						<xsl:when test="dbInfo/LogList/Element">
							<xsl:for-each select="dbInfo/LogList/Element">
								<xsl:variable name="class">
									<xsl:choose>
										<xsl:when test="(position() div 2) - floor(position() div 2) &gt; 0">odd</xsl:when>
										<xsl:otherwise>even</xsl:otherwise>
									</xsl:choose>
								</xsl:variable>
								<tr id="tr_{@id}" class="{$class}" backclass="{$class}" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','{$class}')">

									<td><xsl:value-of select="position()+number(/root/dbInfo/Pager/Page[@id = ../@currentPageId]/@start)-1"/></td>
									<td align="right"><xsl:value-of select="@id"/></td>
									<td align="right" nowrap="">
										<xsl:value-of select="CrDate"/>
									</td>
									<td aligh="right">
										<xsl:value-of select="Message"/>
									</td>

									<td aligh="right">
										<xsl:value-of select="key('logDict',LogDict/@id)/."/>
										(<xsl:value-of select="LogDict/@id"/>)

									</td>
									<td aligh="right">
										<xsl:value-of select="NickName"/>
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

