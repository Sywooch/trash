<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	   
	<xsl:import href="../../lib/xsl/common.xsl"/> 
	
	<xsl:output method="html" version="4.0" indent="yes" encoding="utf-8" omit-xml-declaration="yes"/>
	
	
	<xsl:template match="/">	
		<xsl:apply-templates select="root"/>  
		<script src="/lib/js/jquery.validate.js" type="text/javascript" language="JavaScript"></script>
		<script type="text/javascript">
			<![CDATA[
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
			<form name="editForm" id="editForm" method="post" action="/user/service/editOperatorStream.htm">
				<input type="hidden" name="id" value="{dbInfo/UserData/@id}"/>

				<h3>
					<xsl:value-of select="/root/dbInfo/UserData/LastName"/>&#160;<xsl:value-of select="/root/dbInfo/UserData/FirstName"/>
				</h3>

				<table>
					<col width="180"/>
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

</xsl:transform>

