<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	   
	<xsl:import href="../../lib/xsl/common.xsl"/> 
	
	<xsl:param name="debug" select="'no'"/>
	
	<xsl:output method="html" encoding="utf-8"/>
	<xsl:decimal-format decimal-separator = '.' grouping-separator = ' ' NaN = ''/>
	
	<xsl:template match="/">	
		<link href="/css/jquery.autocomplete.css" type="text/css" rel="stylesheet" media="screen"/>
		<link href="/css/jquery.cleditor.css" type="text/css" rel="stylesheet" media="screen"/>
		<style>	  
			span.err	{ color:#a00; font-weight:bold; margin-left:10px; }
			.inputForm	{ color:#353535; width:450px}
			.inputFormLong	{ color:#353535; width:570px; resize:vertical;}
			.inputForm textarea	{ color:#353535; width:455px; resize:vertical; }
			.inputFormReadOnly	 {width: 80px; color:#353535; background-color:#aaaaaa; text-align: right;}
		</style>
		
		
		<xsl:apply-templates select="root"/>
		  
		<script src="/lib/js/jquery.validate.js" type="text/javascript" language="JavaScript"></script>
		<script src='/lib/js/jquery.autocomplete.min.js' type='text/javascript' language="JavaScript"></script>
		<script src="/lib/js/jquery.cleditor.min.1.4.js" type="text/javascript"></script>
		
		<script type="text/javascript">
			<xsl:choose>
				<xsl:when test="root/dbInfo/Doctor/@id">
					$('#modalWin h1').html("<xsl:value-of select="/root/dbInfo/Doctor/Name"/> ");
				</xsl:when>		
				<xsl:otherwise>
					$('#modalWin h1').html("Карточка врача");
				</xsl:otherwise>
			</xsl:choose>
			$(document).ready(function(){ 
				
				$("#metro").autocomplete("/clinic/service/getMetroList.htm",{
					delay:10,
					minChars:1,
					max:20,
					autoFill:true,
					multiple: true,
					selectOnly:true,
					extraParams: { cityId: <xsl:value-of select="/root/srvInfo/City/@id"/>  },
					formatResult: function(row) {
						return row[0];
					},
					formatItem: function(row, i, max) {		
						return row[0];
					}
				}).result(function(event, item) {
				//	$("#cityStreetId").val(item[1]);
				});
				
				$("#sector").autocomplete("/doctor/service/getSectorList.htm",{
					delay:10,
					minChars:1,
					max:20,
					autoFill:true,
					multiple: true,
					selectOnly:true,
					formatResult: function(row) {
						return row[0];
					},
					formatItem: function(row, i, max) {		
						return row[0];
					}
				}).result(function(event, item) {
					//$("#sectorId").val(item[1]);
				});
				
				$("#clinicName_1").autocomplete("/service/getClinicList.htm",{
					delay:10,
					minChars:2,
					max:20,
					autoFill:true,
					selectOnly:true,
                    matchContains: true,
					extraParams: { cityId: <xsl:value-of select="/root/srvInfo/City/@id"/>  },
					formatResult: function(row) {
						return row[0];
					},
					formatItem: function(row, i, max) {		
						return row[0];
					}
				}).result(function(event, item) {
					$("#clinicId_1").val(item[1]);
//					setMetroList(item[1]);
				});
				
				$("#study").autocomplete("/doctor/service/getEducationList.htm",{
					delay:10,
					minChars:2,
					max:20,
					autoFill:true,
					selectOnly:true,
					formatResult: function(row) {
						return row[0];
					},
					formatItem: function(row, i, max) {		
						return row[0];
					}
				}).result(function(event, item) {
					$("#studyId").val(item[1]);
					$("#studyType option[value="+item[2]+"]").attr("selected", true);
				});
				
				<![CDATA[
					
					/*
					
					$("#textSpec").cleditor({
						width:        566,
						height:       150,
						controls:
									"bold italic "+
									"style | bullets numbering | " +
									" undo redo | " +
									" source ",
						styles:         [["Paragraph", "<div>"], ["Header 1", "<h1>"], ["Header 2", "<h2>"], ["Header 3", "<h3>"]],
								useCSS:       false,
								bodyStyle:    "margin:0px; font:10pt Arial,Verdana; cursor:text"
					});
					*/
				]]>

			});	   
			
			<![CDATA[
				function addClinic () {
					var numb = number_pos+1;
				 	var str = '<div style="margin: 0" id="phoneLine_'+numb+'">';
				 	str += '<input name="clinicName['+numb+']" id="clinicName_'+numb+'" value="" class="inputForm" maxlength="100" style="width: 345px" onblur="checkClear('+numb+')"/>';
				 	str += '<input type="text" name="clinicId['+numb+']" id="clinicId_'+numb+'" style="width: 30px; margin-left: 5px;" class="readOnly" value="" readonly=""/>';
				 	str += '<span class="link" style="margin-left: 10px" onclick="delClinic(\''+numb+'\')">удалить</span>';
				 	str += '</div>';
				 	$("#phoneList").append(str);
					
				 	$("#clinicName_"+numb).autocomplete("/service/getClinicList.htm",{
						delay:10,
						minChars:2,
						max:20,
						autoFill:true,
						selectOnly:true,
						matchContains: true,
						extraParams: { cityId: ]]><xsl:value-of select="/root/srvInfo/City/@id"/> <![CDATA[ },
						formatResult: function(row) { return row[0]; },
						formatItem: function(row, i, max) {	return row[0]; }
					}).result(function(event, item) {
						$("#clinicId_"+numb).val(item[1]);
					});
					
					number_pos++;
					
				 }

				 function delClinic ( id ) {
				 	$("#phoneLine_"+id).remove();
				 }
				 
				 function checkClear( id ) {
				 	if ( $("#clinicName_"+id).val() == '' ) {
				 		$("#clinicId_"+id).val("");
				 	}
				 }
			]]>
		</script>
	</xsl:template>
	
	
	
	
	<xsl:template match="root">	
		
		<xsl:call-template name="commonData">
			<xsl:with-param name="context" select="dbInfo/Doctor"/>
		</xsl:call-template>
		<div class="clear"/>
		<div id="statusWin" class="warning" style="margin-top: 10px"></div>	  

		<!-- 	DEBUG MODE	-->
		<xsl:if test="$debug = 'yes'">
			<div class="debug">
				<a href="/doctor/doctor.htm?id={srvInfo/Id}&amp;debug=yes" target="_blank">Debug mode</a>
			</div>
		</xsl:if>
	</xsl:template>

	
	
	

	
	<xsl:template name="commonData">
		<xsl:param name="context" select="dbInfo/Doctor"/>
		 			
		<div id="editMode" style="position: relative">
			<form name="editForm" id="editForm" method="post">
				<input type="hidden" name="id" id="doctorId" value="{$context/@id}"/>
				
				<div style="margin-left: 10px">
					<!-- Фотография  -->
					<div style="width: 140px; float: left">
						<div style="width: 110px; height: 149px; margin: 0 0px 0 0; background: url('/img/doctorsNew/{$context/@id}_small.jpg?param={srvInfo/Random}')" class="wb"/>
						<xsl:choose>
							<xsl:when test="$context/@id">
								<div style="text-align: right; padding: 0 30px 0 0"><span class="link" onclick="loadImg('{$context/@id}')">изменить</span></div>
								<xsl:if test="$context/IMG">
									<div style="text-align: right; padding: 0 30px 0 0"><span class="link" onclick="deleteImg('{$context/@id}')">удалить</span></div>
								</xsl:if>
							</xsl:when>
							<xsl:otherwise>
								<div style="text-align: right; padding: 0 30px 0 0"><span>сохраните врача</span></div>
							</xsl:otherwise>
						</xsl:choose>
						
						
					</div>
					<div style="float: left">
						<table>
							<col width="120"/>
							<col/>
							
							<tr>
								<td>Идентификатор:</td>
								<td>
									<strong><xsl:value-of select="$context/@id"/></strong>
								</td>
							</tr>
							<tr>
								<td>Имя:</td>
								<td>
									<input name="title" id="title" value="{$context/Name}" class="inputForm" maxlength="100" required="1">
										<xsl:if test="not($context/@id)">
											<xsl:attribute name="onblur">checkFIO(this.value)</xsl:attribute>
											<xsl:attribute name="onfocus">$('#fioCheck').html('')</xsl:attribute>
										</xsl:if>
									</input>
								</td>
							</tr>
							<tr>
								<td></td>
								<td><span id="fioCheck"></span></td>
							</tr>
							<tr>
								<td>Пол:</td>
								<td>
									<select name="sex" id="sex" style="width: 100px">
										<option value="1">
										    <xsl:if test="$context/Sex = 'm' or $context/Sex = '1'">
												<xsl:attribute name="selected"/>
										    </xsl:if>
										    Мужской
										</option>
										<option value="2">
										    <xsl:if test="$context/Sex = 'f' or $context/Sex = '2'">
												<xsl:attribute name="selected"/>
										    </xsl:if>
										    Женский
										</option>
										<option value="0">
										    <xsl:if test="$context/Sex = '-' or $context/Sex = '0'">
												<xsl:attribute name="selected"/>
										    </xsl:if>
										    Прочее
										</option>
									</select>
								</td>
							</tr>
							<tr>
								<td>E-mail:</td>
								<td>
									<input name="email" id="email" value="{$context/Email}" style="width: 265px" class="inputForm" maxlength="50"/>
								</td>
							</tr>
							<tr>
								<td>Телефон:</td>
								<td>
									<input name="phone" id="phone" value="{$context/Phone}" style="width: 265px" class="inputForm" maxlength="50"/>
								</td>
							</tr>
							<tr>
								<td>Alias:</td>
								<td>
									<input name="alias" id="alias" value="{$context/Alias}" style="width: 265px" class="inputForm" maxlength="100" onblur="checkAlias(this.value, '{$context/@id}')" onfocus="$('#aliasCheck').html('')"/>
									<sup class="link">
										<a href="http://{srvInfo/City/@prefix}docdoc.ru/doctor/{$context/Alias}" target="_blank" class="txt10">Анкета врача</a>
									</sup>
									&#160;
									<span id="aliasCheck"></span>
								</td>
							</tr>
							<tr>
								<td>Клиника:</td>
								<td>
									<div id="phoneList" style="margin: 0">
									<xsl:choose>
										<xsl:when test="$context/ClinicList/Element">
											<script>
												 var number_pos = <xsl:value-of select="count($context/ClinicList/Element)"/>
											</script>
											<xsl:for-each select="$context/ClinicList/Element">
												<div style="margin:0" id="phoneLine_{position()}">
													<input name="clinicName[{position()}]" id="clinicName_{position()}" value="{.}" class="inputForm" maxlength="100" style="width: 345px;"/>
													<input type="text" name="clinicId[{position()}]" id="clinicId_{position()}" style="width: 30px; margin-left: 5px;" class="readOnly" value="{@id}" readonly=""/>
													<xsl:if test="position() = 1"><span class="link" style="margin-left: 10px" onclick="addClinic()">добавить</span></xsl:if>
													<xsl:if test="position() &gt; 1"><span class="link" style="margin-left: 10px" onclick="delClinic('{position()}')">удалить</span></xsl:if>
												</div>
												<xsl:if test="position() != last()"><div class="null" style="height: 5px"/></xsl:if>
											</xsl:for-each>
										</xsl:when>
										<xsl:otherwise>
											<script>
												 var number_pos = 1;
											</script>
											<div style="margin: 0" id="phoneLine_1">
												<input name="clinicName[1]" id="clinicName_1" value="" class="inputForm" maxlength="100" style="width: 345px;"/>
												<input type="text" name="clinicId[1]" id="clinicId_1" style="width: 30px; margin-left: 5px;" class="readOnly" value="" readonly=""/>
												<span class="link" style="margin-left: 10px" onclick="addClinic()">добавить</span>
											</div>
										</xsl:otherwise>
										
									</xsl:choose>
								</div>
								</td>
							</tr>
							<tr>
								<td>Метро:</td>	
								<td>
									<xsl:for-each select="$context/MetroList/Element">
										<xsl:value-of select="."/>
										<xsl:if test="position() != last()">, </xsl:if>
									</xsl:for-each>
									
									<input name="metro" id="metro" class="inputForm" type="hidden" maxlength="150">
										<xsl:if test="$context/MetroList and $context/MetroList/Element">
											<xsl:attribute name="value">
												<xsl:for-each select="$context/MetroList/Element">
													<xsl:value-of select="."/>
													<xsl:if test="position() != last()">, </xsl:if>
												</xsl:for-each>
											</xsl:attribute>
										</xsl:if>
									</input>
								</td>
							</tr>
							<tr>
								<td>Доб. номер:</td>
								<td>
									<input type="text" name="addPhoneNumber" id="addPhoneNumber" maxlength="4"  style="width: 40px;" class="inputForm" value="{$context/AddPhoneNumber}" onblur="checkAddPhone(this.value, '{$context/@id}')"/>
									<span style="margin: 0 20px 0 20px"><em>Следующий свободный номер: <span class="link" onclick="$('#addPhoneNumber').val($(this).text())"><xsl:value-of select="/root/dbInfo/NextPhoneNumber"/></span></em></span>
									<span id="addPhoneCheck"></span>
								</td>
							</tr>
							<tr>
								<td>Статус:</td>	
								<td>
									<div style="width: 20px; height: 20px; float: left;">
										<xsl:choose>
											<xsl:when test="$context/Status != ''">
												<img src="/img/icon/status_{$context/Status}.png" style="margin-top: 2px"/>
											</xsl:when>
											<xsl:otherwise><img src="/img/icon/status_2.png" style="margin-top: 2px"/></xsl:otherwise>
										</xsl:choose>
										
									</div>
									<div style="float: left;">
										<label>изменить на:</label> 
										<select name="status" id="status" style="width: 160px">
											<xsl:for-each select="dbInfo/StatusDict/Element">
												<option value="{@id}" style="background:url('/img/icon/status_{@id}.png') no-repeat; padding-left: 20px">
												    <xsl:if test="@id = $context/Status or (not($context/Status) and @id = '2')">
														<xsl:attribute name="selected"/>
												    </xsl:if>
												    <xsl:value-of select="."/>
												</option>
											</xsl:for-each>
										</select>
									</div>
									
								</td>
							</tr> 
						</table>
					</div>
				</div>
				<div class="clear"/>
				<div class="null"/>
				<table>
					<col width="150"/>
					<col/>
					
					<tr>
						<td>Стоимость приема:</td>
						<td>
							<input name="price" id="price" value="{$context/Price}" style="width: 50px" class="inputForm" maxlength="10"/>
							&#160;руб.
							<span style="margin: 0 5px 0 30px">Специальная цена:</span>
							<input name="special_price" id="special_price" value="{$context/SpecialPrice}" style="width: 50px" class="inputForm" maxlength="10"/>
							&#160;руб.
						</td>
					</tr>
					<tr>
						<td>Выезд на дом:</td>
						<td>
							<input type="checkbox" name="departure" id="departure" value="1">
								<xsl:if test="$context/Departure = '1'"><xsl:attribute name="checked"/></xsl:if>
							</input>
						</td>
					</tr>
					<tr>
						<td>Детский врач:</td>
						<td>
							<input type="checkbox" name="kids_reception" id="kids_reception" value="1">
								<xsl:if test="$context/KidsReception = '1'"><xsl:attribute name="checked"/></xsl:if>
							</input>
							<span style="margin: 0 5px 0 30px">Возраст ребёнка:</span>
							с
							<input name="kids_age_from" id="kids_age_from" value="{$context/KidsAgeFrom}" style="width: 50px" class="inputForm" maxlength="5"/>
							по
							<input name="kids_age_to" id="kids_age_to" value="{$context/KidsAgeTo}" style="width: 50px" class="inputForm" maxlength="5"/>
							лет
						</td>
					</tr>
					<tr>
						<td>Рейтинг:</td>
						<td>
							<input name="rating" id="rating" value="{$context/Rating}" style="width: 50px" class="inputForm" maxlength="5"/>
						</td>
					</tr>
					<tr>
						<td>Интегральный рейтинг:</td>
						<td>
							<table>
								<tr>
									<th title="Образование">Обр.</th>
									<th title="Постдипломное образование">Пост.</th>
									<th title="Опыт работы по специальности">Опт.</th>
									<th title="Наличие научной степени">НС</th>
									<th title="Показатель клиники">Кл.</th>
									<th title="Показатель отзывово">Отз.</th>
									<th title="Итоговый рейтинг">Итоговый рейтинг</th>
								</tr>
								<tr>
									<td><input name="rating_edu" id="rating_edu" value="{$context/IntegralRating/@edu}" style="width: 50px" class="inputForm" maxlength="1"/></td>
									<td><input name="rating_ext_edu" id="rating_ext_edu" value="{$context/IntegralRating/@ext_edu}" style="width: 50px" class="inputForm" maxlength="1"/></td>
									<td><input name="rating_exp" id="rating_exp" value="{$context/IntegralRating/@exp}" style="width: 50px" class="inputForm" maxlength="1"/></td>
									<td><input name="rating_ac_deg" id="rating_ac_deg" value="{$context/IntegralRating/@ac_deg}" style="width: 50px" class="inputForm" maxlength="1"/></td>
									<td><input name="rating_cln" id="rating_cln" value="{$context/IntegralRating/@cln}" style="width: 50px;" class="inputForm" maxlength="1"/></td>
									<td><input name="rating_opin" id="rating_opin" value="{$context/IntegralRating/@opin}" style="width: 50px; background-color: #c2e6ff" class="inputForm" maxlength="2"/></td>
									<td>
										<input name="rating_int" id="rating_int" value="{$context/IntegralRating}" style="width: 110px; font-weight: bold" class="inputFormReadOnly" readonly=""/>
									</td>
								</tr>
								<tr>
									<td colspan="7">
										Система рекомендует: <em>поправка рейтинга на основе отзывов</em>&#160;&#160;&#160; 
											<span style="font-weight: bold; cursor: pointer" onclick="$('#rating_opin').val({$context/OpinionRating})" class="underline">
												<xsl:choose>
													<xsl:when test="number($context/OpinionRating) &gt; 0"><xsl:attribute name="class">green underline</xsl:attribute></xsl:when>
													<xsl:when test="number($context/OpinionRating) &lt; 0"><xsl:attribute name="class">red underline</xsl:attribute></xsl:when>
												</xsl:choose>
												<xsl:value-of select="$context/OpinionRating"/>
											</span>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					
					
				</table>
				
				<div class="null"/>
				<table>
					<col width="150"/>
					<col/>
					
					<tr>
						<td>Специальность:</td>
						<td>
							<input name="sector" id="sector" class="inputFormLong" type="Text" maxlength="50">
								<xsl:if test="$context/SectorList and $context/SectorList/Sector">
									<xsl:attribute name="value">
										<xsl:for-each select="$context/SectorList/Sector">
											<xsl:value-of select="."/>
											<xsl:if test="position() != last()">, </xsl:if>
										</xsl:for-each>
									</xsl:attribute>
								</xsl:if>
							</input>
						</td>
					</tr>
					<tr>
						<td>Научная степень (стар.):</td>
						<td>
							<input name="degree" id="degree" value="{$context/Degree}" class="inputFormLong" maxlength="50"/>
						</td>
					</tr>
					<tr>
						<td>Научная степень:</td>
						<td>
							<select name="categoryId" id="categoryId" style="width: 170px;">
								<option>&#160;</option>
								<xsl:for-each select="/root/dbInfo/CategoryDict/Element">
									<option value="{@id}">
										<xsl:if test="@id = $context/CategoryId"><xsl:attribute name="selected"/></xsl:if>
										<xsl:value-of select="."/>
									</option>
								</xsl:for-each>
							</select>
							
							<select name="degreeId" id="degreeId" style="width: 200px; margin-left: 10px">
								<option>&#160;</option>
								<xsl:for-each select="/root/dbInfo/DegreeDict/Element">
									<option value="{@id}">
										<xsl:if test="@id = $context/DegreeId"><xsl:attribute name="selected"/></xsl:if>
										<xsl:value-of select="."/>
									</option>
								</xsl:for-each>
							</select>
							
							<select name="rankId" id="rankId" style="width: 180px; margin-left: 10px">
								<option>&#160;</option>
								<xsl:for-each select="/root/dbInfo/RankDict/Element">
									<option value="{@id}">
										<xsl:if test="@id = $context/RankId"><xsl:attribute name="selected"/></xsl:if>
										<xsl:value-of select="."/>
									</option>
								</xsl:for-each>
							</select>
							
						</td>
					</tr>
					<tr>
						<td>Образование (стар.):</td>
						<td>
							<xsl:copy-of select="$context/TextEdu"/>
						</td>
					</tr>
					<tr>
						<td>Образование:</td>
						<td>
							<!-- <input name="study" id="study" value="" style="width: 370px" class="inputForm" maxlength="255"/> -->
							<textarea name="study" id="study" class="inputForm" style="width: 370px; height: 70px; margin:0"></textarea>
							<select name="studyType" id="studyType" style="width: 100px; margin-left: 10px">
								<xsl:for-each select="/root/dbInfo/EducationTypeDict/Element">
									<option value="{@id}"><xsl:value-of select="."/></option>
								</xsl:for-each>
							</select>
							<input name="studyYear" id="studyYear" value="" style="width: 40px; margin-left: 10px" class="inputForm" maxlength="4"/>
							<!--  <input type="text" name="studyId" id="studyId" style="width: 30px; margin-left: 5px;" class="readOnly" value="" readonly=""/> -->
							<span class="form" style="width:20px; margin: 0 0 2px 10px;" onclick="addCollegue()">+</span>
						</td>
					</tr>
					<tr>
						<td></td>
						<td>
							<sub>наименование учебного заведения</sub>
							<sub style="margin-left: 210px">тип</sub>
							<sub style="margin-left: 90px">год окончания</sub>
						</td>
					</tr>
					<tr>
						<td></td>
						<td>
							<script>
								<xsl:choose>
									<xsl:when test="$context/EducationList/Element">var pos = <xsl:value-of select="count($context/EducationList/Element)"/>;</xsl:when>
									<xsl:otherwise>var pos = 1;</xsl:otherwise>
								</xsl:choose>
								
							</script>
							<table style="margin:0; width: 570px" id="eduList">
								<col width="20"/>
								<col/>
								<col width="50"/>
								<col width="20"/>
								<tr>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<xsl:for-each select="$context/EducationList/Element">
									<tr id="eduLine_{position()}">
										<td>
											<input type="hidden" name="educationId[{position()}]" value="{@id}"/>
											<input type="hidden" name="educationYear[{position()}]" value="{@year}"/>
											<strong><xsl:value-of select="@typeCh"/></strong>
										</td>
										<td>
											<xsl:value-of select="."/>
										</td>
										<td>
											<xsl:value-of select="@year"/>
										</td>
										<td>
											<img style="cursor:pointer" onclick="$('#eduLine_{position()}').remove()" src="/img/icon/delete.png"/>
										</td>
									</tr>
								</xsl:for-each>
							</table>
						</td>
					</tr>
					<tr>
						<td>Год начала практики:</td>
						<td>
							<input name="expYear" id="expYear" value="{$context/ExperienceYear}" style="width: 50px" class="inputForm" maxlength="4"/>
						</td>
					</tr>
				</table>
				
				
				<div class="null"/>
				<table>
					<col width="150"/>
					<col/>
					
					<tr>
						<td>Специализация:</td>
						<td>
							<textarea name="textSpec" id="textSpec" class="inputFormLong" styl="height: 70px">
								<xsl:value-of select="$context/TextSpec"/>
							</textarea>
							<div class="err"></div>
						</td>
					</tr>
					<tr>
						<td>Ассоциации врачей:</td>
						<td>
							<textarea name="textAssoc" id="textAssoc" class="inputFormLong" style="height: 70px">
								<xsl:value-of select="$context/TextAssoc"/>
							</textarea>
							<div class="err"></div>
						</td>
					</tr>
					<tr>
						<td>Курсы повышения квалификации:</td>
						<td>
							<textarea name="textCource" id="textCource" class="inputFormLong" style="height: 70px">
								<xsl:value-of select="$context/TextCourse"/>
							</textarea>
							<div class="err"></div>
						</td>
					</tr>
					<tr>
						<td>Опыт работы:</td>
						<td>
							<textarea name="textExperience" id="textExperience" class="inputFormLong" style="height: 70px">
								<xsl:value-of select="$context/TextExperience"/>
							</textarea>
							<div class="err"></div>
						</td>
					</tr>
					<tr>
						<td>О враче:</td>
						<td>
							<textarea name="textCommon" id="textCommon" class="inputFormLong" style="height: 70px">
								<xsl:value-of select="$context/TextCommon"/>
							</textarea>
							<div class="err"></div>
						</td>
					</tr>
				</table>
				
				
				<div class="null"/>
				<table>
					<col width="150"/>
					<col/>
					
					<tr>
						<td>Комментарии:</td>
						<td>
							<textarea name="openNote" id="openNote" class="inputFormLong" style=" height: 50px">
								<xsl:value-of select="$context/OperatorOpenComment"/>
							</textarea>
							<div class="err"></div>
						</td>
					</tr>
					<tr>
						<td valign="top"><span class="link" onclick="$('#operatorComment').toggle();$('#operatorCommentVs').toggle();">Доп. Инфо</span>:</td>
						<td>
							<span id="operatorCommentVs" class="vs">
								<xsl:choose>
									<xsl:when test="$context/OperatorComment and string-length($context/OperatorComment) &gt; 80">
										<xsl:value-of select="substring($context/OperatorComment, 0, 80)"/> ...
									</xsl:when>
									<xsl:otherwise>
										<xsl:value-of select="$context/OperatorComment"/>
									</xsl:otherwise>
								</xsl:choose>
							</span>
							<textarea name="operatorComment" id="operatorComment" class="inputFormLong hd" style=" height: 50px">
								<xsl:value-of select="$context/OperatorComment"/>
							</textarea>
							<div class="err"></div>
						</td>
					</tr>
				</table>
				
			</form>
			<div style="position:relative; margin: 20px 10px 30px 0;">		  
				<div class="form" style="width:100px; float:right; margin-left: 10px" onclick="(modalWinKey === 'close') ? $('#modalWin').hide() : window.location.reload()">ЗАКРЫТЬ</div>
				<div class="form" style="width:100px; float:right;" onclick="saveContent()">СОХРАНИТЬ</div>
				<xsl:if test="(/root/srvInfo/UserData/Rights/Right = 'ADM' or /root/srvInfo/UserData/Rights/Right = 'ACM') and $context/@id and $context/Status != 3">
					<div class="form" style="width:100px; float:right; margin-right: 10px" onclick="deleteContent('{$context/@id}')">УДАЛИТЬ</div>
				</xsl:if>
			</div>
		</div>	 
			  
	</xsl:template>	 

</xsl:transform>

