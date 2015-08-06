<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:output method="html" encoding="utf-8"/>


	<xsl:template match="/">
		<script>
			$("#doctorName").autocomplete("/request/service/getDoctorList.htm",{
					delay:10,
					minChars:1,
					max:20,
					autoFill:false,
					multiple: false,
					selectOnly:false,
					extraParams: { cityId: <xsl:value-of select="/root/srvInfo/City/@id"/>  },
					formatResult: function(row) {
						return row[0];
					},
					<![CDATA[
					formatItem: function(row, i, max) {
						var str = "<i>Возможно, в системе уже есть такой врач:</i><br/>"+row[0]+"<img src=\"/img/icon/status_"+row[2]+".png\" style=\"margin: 0 5px 0 5px\" align=\"absbottom\"/>";
						$("#doctorLikeList").html(str);
					}
					]]>
				}).result(function(event, item) {
					$("#doctorLikeList").html("");
				});
				$("#clinicNameAddDoctor").autocomplete("/service/getClinicList.htm",{
					delay:10,
					minChars:1,
					max:20,
					autoFill:true,
					multiple: false,
					selectOnly:false,
                    matchContains: true,
					extraParams: { cityId: <xsl:value-of select="/root/srvInfo/City/@id"/>  },
					formatResult: function(row) {
						return row[0];
					},
					formatItem: function(row, i, max) {
						return row[0];
					}
				}).result(function(event, item) {
					$("#addDoctorClinicId").val(item[1]);
				});
		</script>
		<xsl:apply-templates select="root"/>
		
	</xsl:template>




	<xsl:template match="root">
		<div style="width: 420px;">
			<xsl:variable name="context" select="dbInfo/Request"/>
			<table>
				<col width="120px"/>
				<tr>
					<td>Фамилия имя врача:</td>
					<td>
						<input name="doctorName" id="doctorName" value="" style="width: 250px" maxlength="200" required="1"/>
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<div style="margin:0" id="doctorLikeList"></div>
					</td>
				</tr>
				<tr>
					<td>Специальность:</td>
					<td>
						<select name="anotherSectorId" id="anotherSectorId" style="width: 250px" >
							<option value="">--- Выберите специальность ---</option>
							<xsl:for-each select="/root/dbInfo/SectorList/Element">
								<option value="{@id}">
									<xsl:if test="@id = $context/Sector/@id">
										<xsl:attribute name="selected"/>
									</xsl:if>
									<xsl:value-of select="."/>
								</option>
							</xsl:for-each>
						</select>
					</td>
				</tr>
				<tr><td colspan="2"><div class="null"/></td></tr>
				<tr>
					<td>Клиника:</td>
					<td>
						<xsl:choose>
							<xsl:when test="$context/Clinic/@id and $context/Clinic/@id != ''">
								<xsl:value-of select="$context/Clinic"/>
								<!-- <input name="clinicName" id="clinicName" style="width: 250px" maxlength="25"  value="{$context/Clinic}"/> -->
								<input type="hidden" name="addDoctorClinicId" id="addDoctorClinicId" style="width: 30px; margin-left: 5px;" class="readOnly" value="{$context/Clinic/@id}" readonly=""/>
							</xsl:when>
							<xsl:otherwise>
								<input name="clinicNameAddDoctor" id="clinicNameAddDoctor" style="width: 250px" maxlength="255"  value="{$context/Clinic}"/>
								<input type="text" name="addDoctorClinicId" id="addDoctorClinicId" style="width: 30px; margin-left: 5px;" class="readOnly" value="{$context/Clinic/@id}" readonly=""/>
								<em>Необходимо выбрать клинику</em>
							</xsl:otherwise>
						</xsl:choose>
					</td>
				</tr>
				
				<tr>
					<td></td>
					<td>
						<span class="link" onclick="$('#commentBlockAddDoctor').toggle()">Добавить комментарий</span>
						<div id="commentBlockAddDoctor" class="hd" style="margin-top: 10px">
							<textarea name="commentNewDoctor" id="commentNewDoctor" style="color:#353535; width:250px; height: 50px; resize:vertical;"></textarea>
						</div>
					</td>
				</tr>
			</table>
			<div style="position:relative; margin: 20px 10px 30px 0;">		  
				<div class="form" style="width:100px; float:right; margin-left: 10px" onclick="clousePopup()">ЗАКРЫТЬ</div>
				<div class="form" style="width:100px; float:right;" onclick="setAnotherDoctor()">ДОБАВИТЬ</div>
			</div>
		</div>
	</xsl:template>

</xsl:transform>

