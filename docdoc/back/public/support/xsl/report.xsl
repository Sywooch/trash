<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	   
	<xsl:import href="../../lib/xsl/common.xsl"/> 
	
	<xsl:output method="html" version="4.0" indent="yes" encoding="utf-8" omit-xml-declaration="yes"/>
	
	
	<xsl:template match="/">	
		<xsl:apply-templates select="root"/>  
		
		<style>	  
			span.err	{ color:#a00; font-weight:bold; margin-left:10px; }
			.inputForm	{ color:#353535; width:450px}
			.inputForm textarea	{ color:#353535; width:455px}
			.inputFormReadOnly	 {width: 80px; color:#353535; background-color:#aaaaaa; text-align: right;}
		</style>
		
		<script src="/lib/js/jquery.validate.js" type="text/javascript" language="JavaScript"></script>
		<script type="text/javascript">

			function sendReport () {
				$.ajax({
			  		url: "/support/service/sendReport.htm",
					type: "post",
					data: $("#editForm").serialize(),
			  		async: true,
			  		dataType: 'json',
					evalJSON: 	true,
					beforeSend: function(){
						if ($("#sendProblem").attr('disabled') == 'disabled') {
							return false;
						} else {
							$("#sendProblem").attr('disabled', 'disabled');
							return true;
						}
					},
					error: function(xml,text){
						alert(text);
					},
			  		success: function(text){
				  		if ( text['status'] == 'success'  ) {
							$("#sendProblem").removeAttr('disabled');
				  			$('#ceilWin_SupportReportPanel').hide()
				  			alert("Сообщение отправлено");
						} else {
							//ошибки были, показываем их описание
							alert("Ошибка отправки. Зови администратора");
						}
			  		}
				});
				
			}
		</script>
	</xsl:template>
	
	
	
	
	<xsl:template match="root">	
		<xsl:call-template name="editMode"/>

		<xsl:choose>
			<xsl:when test="DebugMode = 'yes'">
				<div>	
					<a href="/support/report.htm?debug=yes" target="_blank">Debug mode</a>
				</div>
			</xsl:when>
			<xsl:otherwise>
				<div class="null" style="height: 20px"/>
			</xsl:otherwise>
		</xsl:choose>		
	</xsl:template>

	
	
	
	<xsl:template name="editMode"> 	  
		<div id="editMode" style="position: relative">
			<form name="editForm" id="editForm" method="post">
				<table>	 
					<col width="180"/> 	 
					<tr>
						<td>ФИО</td>	
						<td>
							<xsl:value-of select="srvInfo/UserData/LastName"/>&#160;<xsl:value-of select="srvInfo/UserData/FirstName"/>
						</td>
					</tr>
					<tr>
						<td>Ссылка</td>	
						<td>
							<xsl:value-of select="srvInfo/URL"/>
							<input name="page" id="page" type ="hidden" value="{srvInfo/URL}"/>
						</td>
					</tr> 
					<tr>
						<td>Категория</td>	
						<td>
							<select name="category" id="category" style="width:455px">
								<option value="backend">backEnd - ошибки со стороны бекофиса</option>
								<option value="docdoc">docdoc.ru - ошибки в работе сайта</option>
								<option value="diagnostica">diagnostica.docdoc.ru - ошибки в работе сайта</option>
								<option value="phones">телефония - проблемы телефонии</option>
                                <option value="office_offer">Офис/Предложения</option>
                                <option value="anonymous">Анонимно: сообщение</option>
								<option value="other">другое</option>
							</select>
						</td>
					</tr> 
					<tr>
						<td>Критичная</td>	
						<td>
							<input name="isCritical" id="isCritical" type ="checkbox" value="yes"/>
						</td>
					</tr>
					<tr>
						<td>Описание проблемы</td>	
						<td>
							<textarea name="problem" style="color:#353535; width:455px; height: 150px"/>
						</td>
					</tr>  
				</table>	
			</form>	  		
		</div>	 
		<div style="position:relative; margin: 20px 10px 30px 0;">		  
			<div class="form" style="width:100px; float:right; margin-left: 10px" onclick="$('#ceilWin_SupportReportPanel').hide()">ЗАКРЫТЬ</div>
			<div id="sendProblem" class="form" style="width:100px; float:right;" onclick="sendReport();">ОТПРАВИТЬ</div>
		</div>	  
		<div id="statusWin" class="error" style="margin-top: 10px"></div>
	</xsl:template>	 
			

</xsl:transform>

