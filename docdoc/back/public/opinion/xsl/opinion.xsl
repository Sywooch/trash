<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	   
	<xsl:import href="../../lib/xsl/common.xsl"/> 
	
	<xsl:param name="debug" select="'no'"/>
	
	<xsl:output method="html" encoding="utf-8"/>
	
	
	<xsl:template match="/">	
		<link type="text/css" href="/css/jquery.autocomplete.css" rel="stylesheet" media="screen"/>
		<style>	  
			span.err	{ color:#a00; font-weight:bold; margin-left:10px; }
			.inputForm	{ color:#353535; width:450px; }
			.inputForm textarea	{ color:#353535; width:455px;  }
			.inputFormReadOnly	 {width: 80px; color:#353535; background-color:#aaaaaa; text-align: right;}
		</style>
		
		
		<xsl:apply-templates select="root"/>
		  
		<script src="/lib/js/jquery.validate.js" type="text/javascript" language="JavaScript"></script>
		<script src='/lib/js/jquery.autocomplete.min.js' type='text/javascript' language="JavaScript"></script>
		<script type="text/javascript">
			var chKey = true;
			$(document).ready(function(){ 
				
				$("#doctor").autocomplete("/opinion/service/getDoctorList.htm",{
					delay:10,
					minChars:1,
					max:20,
					autoFill:true,
					multiple: false,
					selectOnly:true,
					extraParams: { cityId: <xsl:value-of select="/root/srvInfo/City/@id"/>  },
					formatResult: function(row) {
						return row[0];
					},
					formatItem: function(row, i, max) {		
						return row[0];
					}
				}).result(function(event, item) {
					$("#doctorId").val(item[1]);
					getSpecialization (item[1]);
				});
			});	   
		</script>
	</xsl:template>
	
	
	
	
	<xsl:template match="root">	
		
		<xsl:call-template name="commonData"/>
		<div class="clear"/>
		<div id="statusWin" class="warning" style="margin-top: 10px"></div>	
		
		<!-- 	DEBUG MODE	-->
		<xsl:if test="$debug = 'yes'">
			<div class="debug">
				<a href="/opinion/opinion.htm?id={srvInfo/Id}&amp;debug=yes" target="_blank">Debug mode</a>
			</div>
		</xsl:if>
	</xsl:template>

	
	
	

	
	<xsl:template name="commonData"> 	
		<xsl:param name="context" select="dbInfo/Opinion"/>
		
		<div id="editMode" style="position: relative">
			<form name="editForm" id="editForm" method="post">
				<input type="hidden" name="id" value="{$context/@id}"/>
				
				<table>	 
					<col width="180"/>

					<tr>
						<td>Идентификатор:</td>	
						<td>
							<strong><xsl:value-of select="$context/@id"/></strong>
						</td>
					</tr> 
					<tr>
						<td>Происхождение отзыва:</td>
						<td>
							<img style="width: 16px; margin-right: 14px" id="imgType">
								<xsl:attribute name="src">
									<xsl:choose>
										<xsl:when test="$context/Author = 'oper'">/img/icon/receptionist.png</xsl:when>
										<xsl:when test="$context/Author = 'cont' or (not($context/@id))">/img/icon/business-contact.png</xsl:when>
										<xsl:when test="$context/Author = 'gues'">/img/icon/earth.png</xsl:when>
										<xsl:otherwise>/img/icon/business-contact.png</xsl:otherwise>
									</xsl:choose>
								</xsl:attribute>
							</img>
							<select name="author" id="author" class="inputForm" style="width: 420" onchange="chImgType($(this).val())">
								<option value="oper" style="background:url('/img/icon/receptionist.png') no-repeat; padding-left: 20px">
								    <xsl:if test="$context/Author = 'oper'">
										<xsl:attribute name="selected"/>
								    </xsl:if>
								    Оператор
								</option>
								<option value="cont" style="background:url('/img/icon/business-contact.png') no-repeat; padding-left: 20px">
								    <xsl:if test="$context/Author = 'cont' or (not($context/@id))">
										<xsl:attribute name="selected"/>
								    </xsl:if>
								    Контент менеджер
								</option>
								<option value="gues" style="background:url('/img/icon/earth.png') no-repeat; padding-left: 20px">
								    <xsl:if test="$context/Author = 'gues'">
										<xsl:attribute name="selected"/>
								    </xsl:if>
								    С сайта
								</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><span class="underline" onclick="window.location.href='/doctor/index.htm?id='+$('#doctorId').val()">Врач</span>:</td>	
						<td>
							<input name="doctor" id="doctor" value="{$context/Doctor}" class="inputForm" maxlength="100"/><span class="err"></span>
							<input type="hidden" name="doctorId" id="doctorId" value="{$context/Doctor/@id}"/>
						</td>
					</tr>
					<tr>
						<td>Специализация:</td>	
						<td>
							<span id="specialization">
								<xsl:for-each select="$context/SectorList/Sector">
									<xsl:value-of select="."/>
									<xsl:if test="position() != last()">, </xsl:if>
								</xsl:for-each>
							</span>
						</td>
					</tr>  
					<tr><td colspan="2"><div class="null"/></td></tr>
					<tr>
						<td>Пациент:</td>	
						<td>
							<input name="client" id="client" value="{$context/Client}" class="inputForm" maxlength="100"/><span class="err"></span>
						</td>
					</tr>
					<tr>
						<td>Телефон:</td>	
						<td>
							<input name="phone" id="phone" value="{$context/Phone}" class="inputForm" maxlength="100"/><span class="err"></span>
						</td>
					</tr>
					 
					<xsl:if test="$context/RequestList">
						<tr>
							<td>Обращения пациента №№:</td>
							<td>
								<xsl:for-each select="$context/RequestList/Request">
									<span class="link" onclick="getRequestDetail('{.}')" sonclick="$('#requestId').val($(this).text())"><xsl:value-of select="."/></span>
									<xsl:if test="position() != last()">,&#160; </xsl:if>
								</xsl:for-each>
							</td>
						</tr>
						<tr>
							<td></td>
							<td>
								<div id="requestDetail" style="margin: 0"></div>
							</td>
						</tr>
					</xsl:if>
					
					<tr><td colspan="2"><div class="null"/></td></tr>
					<tr>
						<td>Рейтинг:</td>
						<td>
							<table>
								<tr>
									<th title="Врач">Врач</th>
									<th title="Внимание">Вним.</th>
									<th title="Цена / качество">Цен./Кач.</th>
									<td style="padding-left: 10px"></td>
								</tr>
								<tr>
									<td><input name="rating_qul" id="rating_qul" value="{$context/RatingQlf}" style="width: 60px" class="inputForm" maxlength="1"/></td>
									<td><input name="rating_att" id="rating_att" value="{$context/RatingAtt}" style="width: 60px" class="inputForm" maxlength="1"/></td>
									<td><input name="rating_room" id="rating_room" value="{$context/RatingRoom}" style="width: 60px" class="inputForm" maxlength="1"/></td>
									<td style="padding-left: 10px">
										Оценка отзыва системой:
										<xsl:choose>
											<xsl:when test="$context/RatingColor/@recomend = '1'"><span class="green">положительный</span></xsl:when>
											<xsl:when test="$context/RatingColor/@recomend = '0'"><span class="black">нейтральный</span></xsl:when>
											<xsl:when test="$context/RatingColor/@recomend = '-1'"><span class="red">отрицательный</span></xsl:when>
										</xsl:choose>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td>Оценка отзыва:</td>
						<td>
							<span>
								<xsl:if test="$context/RatingColor = '1'">
									<xsl:attribute name="class">green</xsl:attribute>
								</xsl:if>
								<input type="radio" name="ratingColor" value="1">
									<xsl:if test="$context/RatingColor = '1' or ($context/RatingColor = '' and $context/RatingColor/@recomend = '1')"><xsl:attribute name="checked"/></xsl:if>
								</input> 
								<label style="cursor:pointer;" onclick="$(this).prev().attr('checked',true)" class="underline">положительный</label>
								<img src="/img/icon/good.png" title="Положительный отзыв"/>
							</span>
							<span style="margin-left: 30px">
								<xsl:if test="$context/RatingColor = '0'">
									<xsl:attribute name="class">black</xsl:attribute>
								</xsl:if>
								<input type="radio" name="ratingColor" value="0">
									<xsl:if test="$context/RatingColor = '0' or ($context/RatingColor = '' and $context/RatingColor/@recomend = '0')"><xsl:attribute name="checked"/></xsl:if>
								</input> 
								<label style="cursor:pointer" onclick="$(this).prev().attr('checked',true)" class="underline">нейтральный</label>
								<img src="/img/icon/clock.png" title="Нейтральный отзыв"/>
							</span>
							<span style="margin-left: 30px">
								<xsl:if test="$context/RatingColor = '-1'">
									<xsl:attribute name="class">red</xsl:attribute>
								</xsl:if>
								<input type="radio" name="ratingColor" value="-1">
									<xsl:if test="$context/RatingColor = '-1' or ($context/RatingColor = '' and $context/RatingColor/@recomend = '-1') "><xsl:attribute name="checked"/></xsl:if>
								</input> 
								<label style="cursor:pointer" onclick="$(this).prev().attr('checked',true)" class="underline">отрицательный</label>
								<img src="/img/icon/bad.png" title="Отрицательный отзыв"/>
							</span>
						</td>
					</tr>
					<tr><td colspan="2"><div class="null"/></td></tr>
					<tr>
						<td>Обращение №:
							<xsl:if test="$context/RequestId and $context/RequestId != '0' and $context/RequestId != ''"> 
								<sup class="link" onclick="$('#requestId').attr('readonly', false); $('#requestId').attr('class', 'inputForm')">изменить</sup>
							</xsl:if>
						</td>
						<td>
							<input type="text" name="requestId" id="requestId" value="{$context/RequestId}" class="inputForm" style="width: 50px">
								<xsl:if test="$context/RequestId and $context/RequestId != '0' and $context/RequestId != ''">
									<xsl:attribute name="readonly"/>
									<xsl:attribute name="class">inputFormReadOnly</xsl:attribute>
								</xsl:if>
							</input>
							<xsl:if test="$context/RequestId and $context/RequestId != '0'">
								<span class="link" style="margin-left: 5px" onclick="getRequestDetail('{$context/RequestId}')">показать записи</span>
							</xsl:if>
						</td>
					</tr>
					<tr>
						<td></td>
						<td>
							<div id="requestDetail" style="margin: 0"></div>
						</td>
					</tr>
					
					<xsl:if test="$context/CrDate != ''">
						<tr>
							<td>Дата:</td>	
							<td>
								создания: <strong><xsl:value-of select="$context/CrDate"/></strong>
								
								<xsl:choose>
									<xsl:when test="$context/Allow = '1' and $context/PubDate != ''">
										<span style="margin-left: 20px">
											публикации: <strong><xsl:value-of select="$context/PubDate"/></strong>
										</span>
									</xsl:when>
									<xsl:when test="$context/Allow != '1' and $context/PubDate != ''">
										<span style="margin-left: 20px">
											последняя публикация: <em><xsl:value-of select="$context/PubDate"/></em>
										</span>
									</xsl:when>
								</xsl:choose>

							</td>
						</tr>
					</xsl:if>
					<tr>
						<td>Отзыв:</td>	
						<td>
							<textarea name="description" class="inputForm" style="height: 80px; resize:vertical;"><xsl:value-of select="$context/Note"/></textarea>
						</td>
					</tr>
					<tr>
						<td>Комментарий:</td>	
						<td>
							<textarea name="operatorComment" class="inputForm" style="height: 40px; resize:vertical;"><xsl:value-of select="$context/OperatorComment"/></textarea>
						</td>
					</tr>
					<tr>
						<td>Тип:</td>
						<td>
							<img style="width: 16px; margin-right: 14px" id="imgOrigin">
								<xsl:attribute name="src">
									<xsl:choose>
										<xsl:when test="$context/Origin = 'original'">/img/icon/woman.png</xsl:when>
										<xsl:when test="$context/Origin = 'editor'">/img/icon/editor.png</xsl:when>
										<xsl:when test="$context/Origin = 'combine'">/img/icon/yin-yang.png</xsl:when>
										<xsl:otherwise>/img/icon/woman.png</xsl:otherwise>
									</xsl:choose>
								</xsl:attribute>
							</img>
							<select name="origin" id="origin" style="width: 200px" onchange="chImgOrigin($(this).val())">
								<option value="original">
									<xsl:if test="$context/Origin = 'original'"><xsl:attribute name="selected"/></xsl:if>
									Оригинальный
								</option>
								<option value="editor">
									<xsl:if test="$context/Origin = 'editor'"><xsl:attribute name="selected"/></xsl:if>
									Редакторский
								</option>
								<option value="combine">
									<xsl:if test="$context/Origin = 'combine'"><xsl:attribute name="selected"/></xsl:if>
									Смешанный
								</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>Состояние:</td>	
						<td>
							<input type="hidden" name="oldStatus" value="{$context/Allow }"/>
							<span>
								<input type="radio" name="allowed" value="0">
									<xsl:if test="$context/Allow = '0'"><xsl:attribute name="checked"/></xsl:if>
								</input> 
								<label style="cursor:pointer;" onclick="$(this).prev().attr('checked',true)" class="underline">скрыт</label>
								<img src="/img/icon/check_no.png" title="Скрыт"/>
							</span>
							<span style="margin-left: 20px">
								<input type="radio" name="allowed" value="1">
									<xsl:if test="$context/Allow = '1' "><xsl:attribute name="checked"/></xsl:if>
								</input> 
								<label style="cursor:pointer" onclick="$(this).prev().attr('checked',true)" class="underline">показывается</label>
								<img src="/img/icon/check_ok.png" title="Показывается"/>
							</span>
							<span style="margin-left: 20px">
								<input type="radio" name="allowed" value="2">
									<xsl:if test="not($context/Allow) or $context/Allow = '2' "><xsl:attribute name="checked"/></xsl:if>
								</input> 
								<label style="cursor:pointer" onclick="$(this).prev().attr('checked',true)" class="underline">заблокирован</label>
								<img src="/img/icon/disable.png" title="Заблокирован"/>
							</span>
						</td>
					</tr>
				</table>	
			</form>
			<div style="position:relative; margin: 20px 10px 30px 0;">		  
				<div class="form" style="width:100px; float:right; margin-left: 10px" onclick="(modalWinKey === 'close') ? $('#modalWin').hide() : window.location.reload()">ЗАКРЫТЬ</div>
				<div class="form" style="width:100px; float:right;" onclick="saveContent('#editForm')">СОХРАНИТЬ</div>
				<xsl:if test="(/root/srvInfo/UserData/Rights/Right = 'ADM' or /root/srvInfo/UserData/Rights/Right = 'ACM')  and $context/@id">
					<div class="form" style="width:100px; float:right; margin-right: 10px" onclick="deleteContent('{$context/@id}')">УДАЛИТЬ</div>
				</xsl:if>
			</div>
		</div>	 
			  
	</xsl:template>	 
</xsl:transform>

