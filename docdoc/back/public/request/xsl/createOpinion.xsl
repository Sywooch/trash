<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:import href="request.xsl"/>
	<xsl:output method="html" encoding="utf-8"/>

	
	<xsl:template match="/">
		<style>	  
			.inputForm	{ color:#353535; width:550px; }
			.inputForm textarea	{ color:#353535; width:555px;  }
		</style>
		<xsl:apply-templates select="root"/>
		<script>
			$(document).ready(function(){
				$("#editModeOpinion input,textarea").bind("change", function() {
					chKey = true;
				}) 
				
				$("#rating_qul").focus();
			});
		</script>
	</xsl:template>


	<xsl:template match="root">	
		
		<xsl:choose>
			<xsl:when test="srvInfo/Id != '0'">
				<xsl:call-template name="commonData">
					<xsl:with-param name="context" select="dbInfo/Opinion"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<xsl:call-template name="commonData"/>
			</xsl:otherwise>
		</xsl:choose>

		<div class="clear"/>
		<div id="statusWin" class="warning" style="margin-top: 10px"></div>	
	</xsl:template>
	
	
	
	
	<xsl:template name="commonData">
		<xsl:param name="context" select="dbInfo/Request"/>
		 
			<div id="editModeOpinion" style="position: relative">
			<form name="editFormOpinion" id="editFormOpinion" method="post">
				<input type="hidden" name="id" value="{srvInfo/Id}"/>
				<input type="hidden" name="requestId" value="{$context/RequestId}"/>
				<input type="hidden" name="author" value="oper"/>
				<input type="hidden" name="origin" value="original"/>
				<input type="hidden" name="allowed" value="0"/>
				
				
				<table>	 
					<col width="180"/>

					<tr>
						<td>Заявка №:</td>	
						<td>
							<strong><xsl:value-of select="$context/RequestId"/></strong>
						</td>
					</tr>
					<tr>
						<td>Врач:</td>	
						<td>
							<xsl:value-of select="$context/Doctor"/>
							<input type="hidden" name="doctorId" id="doctorId" value="{$context/Doctor/@id}"/>
						</td>
					</tr>  
					<tr>
						<td>Специализация:</td>	
						<td><xsl:value-of select="$context/Sector"/></td>
					</tr>  
					<tr><td colspan="2"><div class="null"/></td></tr>
					<tr>
						<td>Пациент:</td>	
						<td>
							<input name="client" id="client" value="{$context/ClientName}" class="inputForm" maxlength="20"/>
						</td>
					</tr>
					<tr>
						<td>Телефон:</td>	
						<td>
							<input name="phone" id="phone" value="{$context/ClientPhone}" class="inputForm" maxlength="100"/>
						</td>
					</tr>
					<tr>
						<td>Рейтинг:</td>
						<td>
							<table>
								<tr>
									<th title="Врач">Врач</th>
									<th title="Внимание">Вним.</th>
									<th title="Цена / качество">Цен./Кач.</th>
								</tr>
								<tr>
									<td><input name="rating_qul" id="rating_qul" value="{$context/RatingQlf}" style="width: 60px" class="inputForm" maxlength="1" onkeyup="$('#rating_att').focus()"/></td>
									<td><input name="rating_att" id="rating_att" value="{$context/RatingAtt}" style="width: 60px" class="inputForm" maxlength="1" onkeyup="$('#rating_room').focus()"/></td>
									<td><input name="rating_room" id="rating_room" value="{$context/RatingRoom}" style="width: 60px" class="inputForm" maxlength="1" onkeyup="$('#opinionText').focus()"/></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td>Отзыв пациента:</td>	
						<td>
							<textarea name="description" id="opinionText" class="inputForm" style="height: 160px; resize:vertical;">
								<xsl:value-of select="$context/Note"/>
							</textarea>
						</td>
					</tr>
					<tr>
						<td>Пометка для менеджера:</td>	
						<td>
							<textarea name="operatorComment" class="inputForm" style="height: 40px; resize:vertical;">
								<xsl:value-of select="$context/OperatorComment"/>
							</textarea>
						</td>
					</tr>
				</table>	
			</form>
			<div style="position:relative; margin: 20px 10px 30px 0;">		  
				<div class="form" style="width:100px; float:right; margin-left: 10px" onclick="closeThisWindow()">ЗАКРЫТЬ</div>
				<xsl:if test="$context/Author = 'oper'">
					<div class="form" style="width:100px; float:right;" onclick="saveOpinion('#editFormOpinion')">СОХРАНИТЬ</div>
				</xsl:if>
			</div>
		</div>	 
			  
	</xsl:template>	 

</xsl:transform>

