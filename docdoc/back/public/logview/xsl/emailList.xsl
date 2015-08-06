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
		<script type="text/javascript" src="/lib/js/checkUncheck.js"></script>
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

			function deleteEmailList(formId) {
				if (confirm('Удалить? Точно, точно?')) {
					$.ajax({
				  		url: "/adminservice/service/deleteEmailQuery.htm",
						type: "post",
						data: $("#"+formId).serialize(),
				  		async: true,
				  		dataType: 'json',
						evalJSON: 	true,
						error: function(xml,text){
							alert(text);
						},
				  		success: function(text){
				  			if (text['status'] == 'success') {
				  				window.location.reload();
							} else {
								alert("Ошибка: "+text['error']);
							}
				  		}
					});	
				}
			}

		</script>
	</xsl:template>




	<xsl:template match="root">
		<div id="main">
			<h1>Очередь рассылки</h1>

			<div class="m0">
				<div class="actionList">  
					<a href="javascript:deleteEmailList('data')">Удалить отмеченные</a>
				</div>
			</div>

			<div id="resultSet">
				<form method="post" name="data" id="data" action="#">
				<xsl:variable name="tdCount" select="8"/>
				<table cellpadding="0" cellspacing="1" width="100%" border="0" class="resultSet">
					<col width="30"/>
					<col width="30"/>
					<col width="80"/>
					<col width="100"/>
					<col width="30"/>
					<col width="200"/>
					<col />
					<col width="20"/>
					

					<tr>
						<th>#</th>
						<th>Id</th>
						<th>Дата/время</th>
						<th>Кому</th>
						<th>Кол-во попыток</th>
						<th>Тема</th>
						<th>Текст</th>
						<th>
							<div style="margin: 0 1px 0 5px" id="checkuncheck" class="check checkuncheck" onclick="checkUncheck('data');" title="выделить все / снять выделение">&#160;</div>
						</th>

					</tr>
					<xsl:choose>
						<xsl:when test="dbInfo/EmailList/Element">
							<xsl:for-each select="dbInfo/EmailList/Element">
								<xsl:variable name="class">
									<xsl:choose>
										<xsl:when test="(position() div 2) - floor(position() div 2) &gt; 0">odd</xsl:when>
										<xsl:otherwise>even</xsl:otherwise>
									</xsl:choose>
								</xsl:variable>
								<tr id="tr_{@id}" class="{$class}" backclass="{$class}" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','{$class}')">

									<td><xsl:value-of select="position()+number(/root/dbInfo/Pager/Page[@id = ../@currentPageId]/@start)-1"/></td>
									<td class="r"><xsl:value-of select="@id"/></td>
									<td nowrap="">
										<xsl:value-of select="CrDate"/>
									</td>
									<td>
										<xsl:value-of select="EmailTo"/>
									</td>
									<td class="r">
										<xsl:value-of select="ResendCount"/>
									</td>
									<td>
										<xsl:value-of select="Subj"/>
									</td>
									<td>
										<xsl:value-of select="Message"/>
									</td>
									<td>
										<input type="checkbox" value="{@id}" name="line[{@id}]" id="chbox{@id}" style="border:0px" onchange="(this.checked)?($('#tr_{@id}').attr('class','trSelected')):($('#tr_{@id}').attr('class','{$class}'))" autocomplete="off"/>
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
			<xsl:call-template name="pager">
				<xsl:with-param name="context" select="dbInfo/Pager"/>
			</xsl:call-template>

		</div>
	</xsl:template>

</xsl:transform>

