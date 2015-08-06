<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	   
	<xsl:import href="../../lib/xsl/common.xsl"/> 
	
	<xsl:output method="html" version="4.0" indent="yes" encoding="utf-8" omit-xml-declaration="yes"/>
	
	
	<xsl:key name="rights" match="/root/dbInfo/RightList/Element" use="@id"/>
	
	<xsl:template match="/">	
		<xsl:apply-templates select="root"/> 
 		<script type="text/javascript" language="JavaScript">
 		<![CDATA[
			function setPersonalPasswd () {	
				$.ajax({
			  		url: "/user/service/setPersonalPassword.htm",
					type: "post",
					data: $("#personalDataForm").serialize(),
			  		async: true,
			  		dataType: 'json',
					evalJSON: 	true,
					error: function(xml,text){
						alert(text);
					},
			  		success: function(text){
						if ( text['status'] == 'success' ) {
							$("#personalStatusWin").html("<span class='green'>Пароль изменен!</span>").show().delay(2000).fadeOut(400);
							$('#passwd').val('');
							$('#passwd2').val('');
							$('#passPersonalLine').hide();
						} else {
							$("#personalStatusWin").html("Внимание! Ошибки. "+text['pass_err']).show().delay(2000).fadeOut(400);
						}
			  			
			  		}
				});	
			}
			
			
			
			
			function savePersonalContent () {	
				$.ajax({
			  		url: "/user/service/editPersonalData.htm",
					type: "post",
					data: $("#personalDataForm").serialize(),
			  		async: true,
			  		dataType: 'json',
					evalJSON: 	true,
					error: function(xml,text){
						alert(text);
					},
			  		success: function(text){
				  		if ( text['status'] == 'success'  ) {
							$("#lastName_err").html('');
							$("#firstName_err").html('');
							$("#email_err").html('');
							$("#phone_err").html('');
							$("#skype_err").html('');
							$("#personalStatusWin").html("<span class='green'>Данные успешно сохранены!</span>").show().delay(2000).fadeOut(400);
						} else {
							//ошибки были, показываем их описание
							$("#personalStatusWin").html("Ошибки. Данные не сохранены").show().delay(2000).fadeOut(400);
//							$("#lastName_err").html(data.lastName);
//							$("#firstName_err").html(data.firstName);
//							$("#email_err").html(data.email);
//							$("#phone_err").html(data.phone);
//							$("#skype_err").html(data.skype);
						}
			  		}
				});
				
			}
			]]>	
		</script>
	</xsl:template>
	
	
	
	
	<xsl:template match="root">	
		<div style="position: relative">
			<style>	  
				#personalDataForm div.field	{float: left; width: 150px; margin:0}
				#personalDataForm div.line		{height: 20px; margin:0 0 5px 0}
			</style>
			<form name="personalDataForm" id="personalDataForm" method="post">
			<div id="status"></div>
				<table>	 
					<col width="120"/> 	 
					<tr>
						<td>Логин</td>	
						<td>
							<strong style="font-size:18px;color: #ff5656;"><xsl:value-of select="dbInfo/UserData/Login"/></strong>
							<a style="margin-left: 20px;" href="javascript:void(0)" onclick="$('#passwd').val('');$('#passwd2').val('');$('#passPersonalLine').toggle(); ">изменить пароль</a>
						</td>
					</tr> 
					<tr id="passPersonalLine" class="hd">
						<td colspan="2" style="padding:0">
							<table>
								<col width="120"/> 	 
								<tr>
									<td>Пароль <span class="red">*</span></td>	
									<td>
										<input type="Password" name="passwd" id="passwd" value="" style="width:150px" maxlength="50"/>
										<div id="passwd_err"></div>	 
									</td>
								</tr>
								<tr>
									<td>Пароль (контр.) <span class="red">*</span></td>	
									<td>
										<input type="Password" name="passwd2" id="passwd2" value="" style="width:150px" maxlength="50" />
										<!--<input type="Password" name="passwd2" id="passwd2" value="" style="width:150px" maxlength="50" onblur="checkLoginVal()" onfocus="$('#loginCheck').html('')"/>-->
										
									&#160;<span id="loginCheck"></span>	 
										<span class="form" style="width:100px; margin-left: 10px" onclick="setPersonalPasswd()">SET</span>
									</td>
								</tr>
							</table>
						</td>
						
					</tr>
						
					<tr><td colspan="2"><div class="null" style="height: 10px"/></td></tr>
					<tr>
						<td>Фамилия <span class="red">*</span></td>	
						 <td><input name="lastName" id="lastName" value="{dbInfo/UserData/LastName}" style="width:250px" maxlength="50" required="1"/>
						 <div id="lastName_err" style="color:red;"></div>
						 </td>
					</tr>
					<tr>
						<td>Имя <span class="red">*</span></td>	
						<td><input name="firstName" id="firstName" value="{dbInfo/UserData/FirstName}" style="width:250px" maxlength="50" required="1"/>
						<div id="firstName_err" style="color:red;"></div>
						</td>
					</tr> 
					<tr>
						<td>E-mail <span class="red">*</span></td>	
						<td><input name="email" id="email" value="{dbInfo/UserData/Email}" style="width:250px" maxlength="50" required="1"/>
						<div id="email_err" style="color:red;"></div>
						</td>
					</tr>  
					<tr>
						<td>Телефон</td>	
						<td><input name="phone" id="phone" value="{dbInfo/UserData/Phone}" style="width:250px" maxlength="50"/>
						<div id="phone_err" style="color:red;"></div>
						</td>
					</tr>
					<tr>
						<td>Skype</td>	
						<td><input name="skype" id="skype" value="{dbInfo/UserData/Skype}" style="width:250px" maxlength="50"/>
						<div id="skype_err" style="color:red;"></div>
						</td>
					</tr>
					<tr>
						<td valign="top">Мои полномочия</td>	
						<td valign="top">
							<xsl:for-each select="dbInfo/UserData/Rights/Right">
								<xsl:value-of select="key('rights',@id)/."/>
								<xsl:if test="position() != last()">&#60;br/&#62;</xsl:if>
							</xsl:for-each>
						</td>
					</tr>
					<tr>
						<td valign="top">Поток заявок</td>
						<td valign="top">
							<xsl:value-of select="dbInfo/UserData/OperatorStream/@title"/>
						</td>
					</tr>
					<tr><td collspan="2"><div class="null"/></td></tr>
					<tr>
						<td>&#160;</td>	
						<td><a href="/auth/logout.htm">Выйти из системы</a></td>
					</tr> 
				</table>	
			</form>	  		
		</div>	 
		<div id="ceilWin_Personaldata_container" class="error" style="margin-top: 10px"></div>
		
		<div id="personalStatusWin" class="error" style="margin-top: 10px"></div>
		<div style="position:relative; margin: 20px 10px 30px 0;">		  
			<div class="form" style="width:100px; float:right; margin-left: 10px" onclick="$('#ceilWin_Personaldata').hide()">ЗАКРЫТЬ</div>
			<div class="form" style="width:100px; float:right;" onclick="savePersonalContent()">СОХРАНИТЬ</div>
			<!--<div class="form" style="width:100px; float:right;" >СОХРАНИТЬ</div>-->
			
		</div>	  
	</xsl:template>

</xsl:transform>

