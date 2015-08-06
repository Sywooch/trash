<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	   
	<xsl:import href="../../lib/xsl/common.xsl"/> 
	
	<xsl:output method="html" version="4.0" indent="yes" encoding="utf-8" omit-xml-declaration="yes"/>
	
	
	<xsl:template match="/">	
		<xsl:apply-templates select="root"/>  
		<script src="/lib/js/jquery.validate.js" type="text/javascript" language="JavaScript"></script>
		<script type="text/javascript">
			<![CDATA[
			function checkLoginVal(eltVal) {
				$.ajax({
			  		url: "/user/service/checkLogin.htm",
					type: "get",
					data: "q="+eltVal,
			  		async: true,
			  		success: function(text){
						if (text == '0') {
							$("#loginCheck").html("<span class='green'>Ок!</span>");
						} else if (text != '-1') {
							$("#loginCheck").html("<span class='red'>Занято</span>");
						}
			  		}
				});
			}
			
			
			
			function checkEmailVal(eltVal) {
				$.ajax({
			  		url: "/user/service/checkEmail.htm",
					type: "get",
					data: "q="+eltVal,
			  		async: true,
			  		success: function(text){
						if (text == '0') {
							$("#emailCheck").html("<span class='green'>Ок!</span>");
						} else if (text != '-1') {
							$("#emailCheck").html("<span class='red'>Занято</span>");
						}
			  		}
				});
			}
			
			
			$(document).ready(function() {
			
				$("#editForm").validate({
				    submitHandler: function(form) {
					  saveContent();
				    },
				    focusInvalid: false,
				    focusCleanup: true,
				    rules: {
				      lastName: { required: true, minlength: 2, maxlength: 50 },
					  firstName: { required: true, minlength: 2, maxlength: 50 }
				    },
				    messages: {
				      lastName: { required: "!", minlength: "!", maxlength: "!" },
					  firstName:{ required: "!", minlength: "!", maxlength: "!" }
				    },
				    errorPlacement: function(error, element) {
						//var er = element.attr("name");
						error.appendTo( element.next("span") );
						element.attr("class","inputForm");
						$("#statusWin").html("Ошибки заполнения формы").show().delay(2000).fadeOut(300);;
					}
				});
				
			});
			]]>
		</script>
	</xsl:template>
	
	
	
	
	<xsl:template match="root">	
		<xsl:call-template name="editMode"/>

		<xsl:choose>
			<xsl:when test="DebugMode = 'yes'">
				<div>	
					<a href="/user/user.htm?id={/root/srvInfo/Id}&amp;debug=yes" target="_blank">Debug mode</a>
				</div>
			</xsl:when>
			<xsl:otherwise>
				<div class="null" style="height: 20px"/>
			</xsl:otherwise>
		</xsl:choose>		
	</xsl:template>

	
	
	
	<xsl:template name="editMode"> 	  
		<div id="editMode" style="position: relative">
			<style>	  
				span.err	{ color:#a00; font-weight:bold; margin-left:10px; }
				.inputForm	{ color:#353535; width:350px}
			</style>
			<form name="editForm" id="editForm" method="post" action="/user/service/editData.htm">
				<input type="hidden" name="id" value="{dbInfo/UserData/@id}"/>
				
				<table>	 
					<col width="180"/> 	 
					<xsl:choose> 
						<xsl:when test="dbInfo/UserData/@id and dbInfo/UserData/@id != ''">
							<tr>
								<td>Логин</td>	
								<td>
									<strong><xsl:value-of select="dbInfo/UserData/Login"/></strong>
									<a style="margin-left: 20px" href="javascript:void(0)" onclick="$('#passwd').val('');$('#passLine').toggle(); ">изменить пароль</a>
								</td>
							</tr> 
							<tr id="passLine" class="hd">
								<td>Пароль</td>	
								<td>
									<input name="passwd" id="passwd" value="" style="width:150px" maxlength="50"/> 
									<span class="form" style="width:100px; margin-left: 10px" onclick="setPasswd()">SET</span>
									<br/> 
									<input type="Checkbox" name="sendInv" id="sendInv" value="1" style="margin: 2px 5px 0 0px" checked=""/>
									отправить уведомление
								</td>
							</tr>
						</xsl:when>		   
						<xsl:otherwise>	  
							<tr>
								<td>Логин</td>	
								<td>
									<input name="login" id="login" value="" style="width:150px" maxlength="50" required="1" onblur="checkLoginVal(this.value)" onfocus="$('#loginCheck').html('')"/>
									<span class="err"></span>
									&#160;<span id="loginCheck"></span>
									
								</td>
							</tr> 
							<tr>
								<td>Пароль</td>	
								<td>
									<input name="passwd" id="passwd" value="" style="width:150px" maxlength="50" required="1"/><span class="err"></span>
									<br/> 
									<input type="Checkbox" name="sendInv" id="sendInv" value="1" style="margin: 2px 5px 0 0px" checked=""/>
									отправить уведомление
								</td>
							</tr>
						</xsl:otherwise>
					</xsl:choose>
					<tr><td colspan="2"><div class="null" style="height: 10px"/></td></tr>
					<tr>
						<td>Фамилия</td>	
						<td><input name="lastName" id="lastName" value="{dbInfo/UserData/LastName}" class="inputForm" maxlength="50" required="1"/><span class="err"></span></td>
					</tr>
					<tr>
						<td>Имя</td>	
						<td><input name="firstName" id="firstName" value="{dbInfo/UserData/FirstName}" class="inputForm" maxlength="50" required="1"/><span class="err"></span></td>
					</tr> 
					<tr>
						<td>E-mail</td>	
						<td>
							<input name="email" id="email" value="{dbInfo/UserData/Email}" class="inputForm" maxlength="50"  onblur="checkEmailVal(this.value)" onfocus="$('#emailCheck').html('')"/><span class="err"></span>
							&#160;<span id="emailCheck"></span>
						</td>
					</tr>  
					<tr>
						<td>Телефон</td>	
						<td><input name="phone" id="phone" value="{dbInfo/UserData/Phone}" class="inputForm" maxlength="50"/></td>
					</tr>
					<tr>
						<td>Skype</td>	
						<td><input name="skype" id="skype" value="{dbInfo/UserData/Skype}" class="inputForm" maxlength="50"/></td>
					</tr>  
					<tr>
						<td>Права</td>	
						<td>
							<xsl:call-template name="rightList">    
								<xsl:with-param name="selectedRight" select="dbInfo/UserData/Rights/Right"/>
							</xsl:call-template>
						</td>
					</tr> 	
					<tr>
						<td>Статус</td>	
						<td>
							<input type="Radio" name="status" value="enable">
								<xsl:if test="dbInfo/UserData/Status = 'enable' or not(dbInfo/UserData/@id)"><xsl:attribute name="checked"/></xsl:if>
							</input>
							<span class="green" style="margin: 0 5px  0 5px">активен</span>
							<img align="absbottom" src="/img/common/iconGreenCheck.gif" width="16" height="16" alt="" title="Доступен для показа" border="0"/>
							&#160;&#160;&#160;&#160;&#160;
							<input type="Radio" name="status" value="disable">
								<xsl:if test="dbInfo/UserData/Status = 'disable'"><xsl:attribute name="checked"/></xsl:if>
							</input>	
							<span class="red" style="margin: 0 5px  0 5px">заблокирован</span>
							<img align="absbottom" src="/img/common/iconRedX.gif" width="16" height="16" alt="" title="Не доступен для показа" border="0"/>
						</td>
					</tr>
					<tr>
						<td>Поток заявок для оператора:</td>
						<td>
							<select name="operatorStream" id="operatorStream" style="width: 250px" >
								<option value="0">Не задан</option>
								<xsl:for-each select="/root/dbInfo/OperatorStreams/Element">
									<option value="{Value}">
										<xsl:if test="Value = /root/dbInfo/UserData/OperatorStream">
											<xsl:attribute name="selected"/>
										</xsl:if>
										<xsl:value-of select="Title"/>
									</option>
								</xsl:for-each>
							</select>
						</td>
					</tr>
				</table>
			</form>
		</div>
		<div style="position:relative; margin: 20px 10px 30px 0;">		  
			<div class="form" style="width:100px; float:right; margin-left: 10px" onclick="(modalWinKey === 'close') ? $('#modalWin').hide() : window.location.reload()">ЗАКРЫТЬ</div>
			<div class="form" style="width:100px; float:right;" onclick="$('#editForm').submit();">СОХРАНИТЬ</div>
		</div>	  
		<div id="statusWin" class="error" style="margin-top: 10px"></div>
	</xsl:template>	 
			
	
	
	
	<xsl:template name="rightList">	  
		<xsl:param name="id" select="''"/>
		<xsl:param name="contextRight" select="dbInfo/RightList"/>	
		<xsl:param name="selectedRight" select="/root/srvInfo/RightList"/>
		
		<style type="text/css">	
			#rightSelector	{overflow-y: scroll; width: 250px; height: 80px; padding:5px; margin :0; background-color: #ffffff;}
			#rightSelector div.activeLine	{background-color: #e9e9e9;}
		</style>	  
		
		<div id="rightSelector" style="width:340px" class="wb">
			<xsl:for-each select="$contextRight/Element">
				<div id="divRight{$id}_{@id}" style="margin: 0 0 4px 0;">
					<xsl:if test="@id=$selectedRight/@id"><xsl:attribute name="class">activeLine</xsl:attribute></xsl:if>
					<input type="Checkbox" name="Right{$id}[{@id}]" id="Right{$id}_{@id}" value="{@id}" style="margin:0" onclick="(this.checked)?$('#divRight{$id}_{@id}').addClass('activeLine'):$('#divRight{$id}_{@id}').removeClass('activeLine')">
						<xsl:if test="@id=$selectedRight/@id"><xsl:attribute name="checked"/></xsl:if>
					</input>&#160;
					
					<a href="javascript:void(0)" onclick="($('#Right{$id}_{@id}').attr('checked'))?$('#Right{$id}_{@id}').attr('checked',false):$('#Right{$id}_{@id}').attr('checked',true);($('#Right{$id}_{@id}').attr('checked'))?$('#divRight{$id}_{@id}').addClass('activeLine'):$('#divRight{$id}_{@id}').removeClass('activeLine')">
						<xsl:value-of select="."/>
					</a>
				</div>	 
			</xsl:for-each>
		</div>
	</xsl:template>
</xsl:transform>

