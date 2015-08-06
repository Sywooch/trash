<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:import href="../../lib/xsl/modalWindow.xsl"/>
	<xsl:import href="../../lib/xsl/common.xsl"/>
	<xsl:import href="../../lib/xsl/pager.xsl"/>
	<xsl:import href="../../lib/xsl/filter.xsl"/>

	<xsl:output method="html" encoding="utf-8"/>

	<xsl:key name="statusList" match="/root/dbInfo/SMSStatusDict/Element" use="."/>

	<xsl:template match="/">
		<xsl:apply-templates select="root"/>
		<div style="position: absolute; margin: 0; top:0; left:0; z-index: 0;">
			<xsl:call-template name="modalWindow">
				<xsl:with-param name="id" select="'modalWin'"/>
				<xsl:with-param name="title" select="'Логи'"/>
				<xsl:with-param name="width" select="'650'"/>
			</xsl:call-template>
		</div>

		<link type="text/css" href="/css/jquery-ui-1.7.2.custom.css" rel="stylesheet" />

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

			function deleteSMSList(formId) {
				//alert("/adminservice/service/deleteSMSQuery.htm?"+$("#"+formId).serialize());
				if (confirm('Удалить? Точно, точно?')) {
					$.ajax({
				  		url: "/adminservice/service/deleteSMSQuery.htm",
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
			
			
			
			function sendSMSList(formId) {
				//alert("/adminservice/service/sendSMSQuery.htm?"+$("#"+formId).serialize());
				if (confirm('Отправить сообщения? Уверены?')) {
					$.ajax({
				  		url: "/adminservice/service/sendSMSQuery.htm",
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
			
			
			
			function checkSMSList(formId) {
				//alert("/adminservice/service/checkSMSQuery.htm?"+$("#"+formId).serialize());
				$.ajax({
			  		url: "/adminservice/service/checkSMSQuery.htm",
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
						str = str + ", " + $(this).next().text();
					} else {
						str = $(this).next().text();
					}
				})	
				$("#statusFilter").text(str);
			}
			
			<![CDATA[
			function actionSMS (formId, action) {
				var path = "/adminservice/service/checkSMS.htm"
				var key = true;
				var arr = ["new", "in_process", "sended", "error", "error_gate", "error_connect", "deleted", "delivered", "canceled"];
				switch( action ) {
					case 'send' : path = "/adminservice/service/sendSMS.htm"; break;
					case 'check' : path = "/adminservice/service/checkSMS.htm"; break;
					default : path = "/adminservice/service/checkSMS.htm";
				}
				
				$('#'+formId+' input:checkbox').each(function(index){
					
					if ( $(this).attr("checked") && key) {
						var id = $(this).val();
						$("#loader_"+id).addClass('loading');
						if ( 
							(	action == 'send' 
								&&
								( 	$("#tr_"+id+" span.i-status").hasClass("i-st-sended") 
									|| 
								 	$("#tr_"+id+" span.i-status").hasClass("i-st-delivered")
								 ) 
								 && 
								 confirm("Сообщение было отправлено ранее. Отправить еще раз?")
							 )
							 ||
							 (	action == 'send' 
								&&
								!( 	$("#tr_"+id+" span.i-status").hasClass("i-st-sended") 
									|| 
								 	$("#tr_"+id+" span.i-status").hasClass("i-st-delivered")
								) 
							 )
							 ||
							 action == 'check'
						) {
							$.ajax({
								type: "post",
								url: path,
								data: "id="+id,
								async: false,
								dataType: 'json',
								evalJSON: 	true,
								success: function(text) { 
									if ( $.inArray(text['status'], arr) >= 0 ) {
						  				$("#loader_"+id).removeClass('loading');
						  				$("#tr_"+id+" span.i-status").attr("class","i-status sms i-st-"+text['status']);
						  				
						  				//$("#chbox"+id).removeAttr("checked");
						  				//$("#tr_"+id).attr('class',$("#tr_"+id).attr('backclass'));
									} else if (text['status'] == "SMSstop" ) {
										alert("SMS сервис отключен. См. конфигурационный файл");
										$("#loader_"+id).removeClass('loading');
										key = false;
										
									} else {
										alert("Ошибка: статус ответа не определен: "+text['status']);
									}
								}
							});
						} else {
							$("#loader_"+id).removeClass('loading');
						}
						
						
					}
				});
			}
			]]>
		</script>
	</xsl:template>




	<xsl:template match="root">
		<div id="main">
			<h1>Очередь SMS сообщений</h1>
			
			
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
					
					<div class="inBlockFilter">
						<div>
							<label>Телефон:</label>
							<div>
							    <input name="shPhone" id="shPhone" style="width:120px" maxlength="20" value="{srvInfo/ShPhone}"/>
							</div>

						</div>
					</div>

					<div class="inBlockFilter">
						<label>Тип: </label>
						<div>
							<select name="type" id="idLogCode" style="width: 250px">
								<option value="">--- Любой тип ---</option>
								<xsl:for-each select="dbInfo/TypeDict/Element">
									<option value="{@id}">
									    <xsl:if test="@id = /root/srvInfo/Type">
											<xsl:attribute name="selected"/>
									    </xsl:if>
									    <xsl:value-of select="."/>
									</option>
								</xsl:for-each>
							</select>
						</div>
					</div>
					<div class="clear"/>
					<div class="inBlockFilter">
						<label>Статус: </label>
						<span id="statusFilter" class="link" onclick="$('#ceilWin_multy').show();">
							<xsl:choose>
								<xsl:when test="/root/srvInfo/StatusList/Status = 'all'">Все типы</xsl:when>
								<xsl:when test="/root/srvInfo/StatusList/Status">
									<xsl:for-each select="/root/srvInfo/StatusList/Status">
										<xsl:value-of select="key('statusList',.)/."/>
										<xsl:if test="position() != last()">,&#160;</xsl:if>
									</xsl:for-each>
								</xsl:when>

								<xsl:otherwise>Все типы</xsl:otherwise>
							</xsl:choose>
						</span>
						<div class="ancor">
							<xsl:call-template name="ceilInfo"><xsl:with-param name="id" select="'multy'"/></xsl:call-template>
						</div>
						<!-- 
						<div>
							<select name="status" id="idLogCode" style="width: 250px">
								<option value="">- - - Любой тип - - -</option>
								<xsl:for-each select="dbInfo/SMSStatusDict/Element">
									<option value="{@id}">
									    <xsl:if test="@id = /root/srvInfo/Status">
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

				</xsl:with-param>
			</xsl:call-template>
			
			
						

			<div class="m0">
				<div class="actionList">  
					<a href="javascript:deleteSMSList('data')">Удалить отмеченные</a>
					<span class="delimiter">|</span>
					<!-- <a href="javascript:sendSMSList('data')">Отправить отмеченные</a> -->
					<a href="javascript:actionSMS('data','send')">Отправить отмеченные</a>
					<span class="delimiter">|</span>
					<a href="javascript:actionSMS('data','check')">Проверить статус</a>
					<!-- <a href="javascript:checkSMSList('data')">Проверить статус</a> -->
					
				</div>
			</div>

			<div id="resultSet">
				<form method="post" name="data" id="data" action="#">
				<xsl:variable name="tdCount" select="12"/>
				<table cellpadding="0" cellspacing="1" width="100%" border="0" class="resultSet">
					<col width="30"/>
					<col width="30"/>
					<col width="100"/>
					<col width="100"/>
					<col width="120"/>
					<col width="200"/>
					<col />
					
					<col width="30"/>
					<col width="30"/>
					<col />
					<col width="50"/>
					<col width="50"/>
					<col width="20"/>
					

					<tr>
						<th rowspan="2">#</th>
						<th rowspan="2">Id</th>
						<th colspan="2">Дата/время</th>
						<th rowspan="2">Кому</th>
						<th rowspan="2">Тип</th>
						<th rowspan="2">Текст</th>
						<th rowspan="2">Приор.</th>
						<th rowspan="2">Gate</th>
						<th rowspan="2">GateSession</th>
						<th rowspan="2">Статус</th>
						<th rowspan="2">TTL</th>
						<th rowspan="2">
							<div style="margin: 0 1px 0 5px" id="checkuncheck" class="check checkuncheck" onclick="checkUncheck('data');" title="выделить все / снять выделение">&#160;</div>
						</th>

					</tr>
					<tr>
						<th>Создано</th>
						<th>Отправлено</th>
					</tr>
					
					<xsl:choose>
						<xsl:when test="dbInfo/SMSList/Element">
							<xsl:for-each select="dbInfo/SMSList/Element">
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
									<td nowrap="">
										<xsl:value-of select="SendDate"/>
									</td>
									<td>
										<xsl:value-of select="Phone"/>
									</td>
									
									<td>
										<xsl:value-of select="Type"/>
									</td>
									<td>
										<xsl:value-of select="Message"/>
									</td>
									
									<td class="r">
										<xsl:value-of select="Priority"/>
									</td>
									<td class="r">
										<xsl:value-of select="GateId"/>
									</td>
									<td>
										<xsl:value-of select="SystemId"/>
									</td>
									<td align="center">
										<span title="{Status}">
											<xsl:attribute name="class">i-status sms i-st-<xsl:value-of select="Status"/></xsl:attribute>
										</span>
										<span id="loader_{@id}" class="loader"/>
										<!-- <xsl:value-of select="Status"/> -->
									</td>
									<td>
										<xsl:value-of select="Ttl"/>
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
	
	
	
	
	<xsl:template name="ceilInfo">	  
		<xsl:param name="id"/>
		
		<style>
			.liList	{margin: 0 0 1px 0; padding: 0; list-style: none}
		</style>
		<div id="ceilWin_{$id}" class="m0 shd infoEltR hd" style="width: 250px;">
			<ul style="margin: 0 auto; padding: 0; width:100%">
				<li class="liList" style="margin-bottom: 5px">
					<input name="status[0]" id="status_0" type="Checkbox" value="all" onclick="$('#filter :input').attr('checked',false); $(this).attr('checked',true)">
						<xsl:if test="/root/srvInfo/StatusList/Status = 'all' or not(/root/srvInfo/StatusList/Status)"><xsl:attribute name="checked"/></xsl:if>
					</input>
					&#160;
					<span class="link" onclick="$('#filter :input').attr('checked',false); $('#status_0').attr('checked',true)">Все типы</span>
				</li>
				<xsl:for-each select="dbInfo/SMSStatusDict/Element">
					<xsl:variable name="pos" select="position()"/>
					<li class="liList">
						<input name="status['{.}']" id="status_{.}" type="Checkbox" value="{.}" onclick="setFilterListToNull()">
							<xsl:if test="/root/srvInfo/StatusList/Status = ."><xsl:attribute name="checked"/></xsl:if>
						</input>
						&#160;
						
						<span class="link" onclick="($('#status_{.}').attr('checked'))?$('#status_{.}').attr('checked',false):$('#status_{.}').attr('checked',true); setFilterListToNull();">
							<xsl:value-of select="."/>
						</span>
					</li>			
				</xsl:for-each>
			</ul>
			<img src="/img/common/clBt.gif" width="15" height="14"  alt="закрыть" style="position: absolute; cursor: pointer; right: 4px; top: 4px;" title="закрыть" onclick="$('#ceilWin_{$id}').hide(); setFilterLine()" border="0"/>
		</div>
	</xsl:template>

</xsl:transform>

