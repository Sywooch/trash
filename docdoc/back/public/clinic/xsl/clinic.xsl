<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	   
	<xsl:import href="../../lib/xsl/common.xsl"/> 
	
	<xsl:param name="debug" select="'no'"/>
	
	 
	<xsl:decimal-format decimal-separator = '.' grouping-separator = ' ' NaN = ''/>
	
    <xsl:output method="html" encoding="utf-8"/>
	
	<xsl:key name="diagnostica" match="/root/dbInfo/DiagnosticList/descendant-or-self::Element" use="@id"/>
	
	<xsl:template match="/">	
		<link type="text/css" href="/css/jquery.autocomplete.css" rel="stylesheet" media="screen"/>
		<link type="text/css" href="/css/main.css" rel="stylesheet" media="screen"/>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css" />
		<style>	  
			span.err	{ color:#a00; font-weight:bold; margin-left:10px; }
			.inputForm	{ color:#353535; width:450px}
			.inputForm textarea	{ color:#353535; width:455px}
			.inputFormReadOnly	 {width: 80px; color:#353535; background-color:#aaaaaa; text-align: right;}

			div.checkbox	{float:left; width: 15px; margin: 0 10px 0 0; padding: 0 0 2px 0}
			div.checkboxLab	{float:left; margin:0; padding: 2px 0 0 10px}
			.timetable td {padding-right: 5px}
		</style>
		
		<xsl:apply-templates select="root"/>

		<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
		<script src="/lib/js/jquery.validate.js" type="text/javascript" language="JavaScript"></script>
		<script src='/lib/js/jquery.autocomplete.min.js' type='text/javascript' language="JavaScript"></script>
		<script src='/lib/js/slide.js' type='text/javascript' language="JavaScript"></script>
		<script src="/lib/js/jquery.maskedinput-1.3.min.js" type="text/javascript"></script>
		<script src="/clinic/js/clinic.js" type="text/javascript" language="JavaScript"></script>
		<script src="/static/js/dropzone.js"></script>
		<script type="text/javascript">
			<xsl:choose>
				<xsl:when test="root/dbInfo/Clinic/@id">
					$('#modalWin h1').html('<xsl:value-of select="root/dbInfo/Clinic/Title"/> ');
				</xsl:when>		
				<xsl:otherwise>
					$('#modalWin h1').html("Карточка клиники");
				</xsl:otherwise>
			</xsl:choose>
			var chKey = true;
			$(document).ready(function(){ 
				$("#cityStreet").autocomplete("/clinic/service/getStreetList.htm",{
					delay:10,
					minChars:1,
					max:20,
					autoFill:true,
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
				
				$("#metro").autocomplete("/clinic/service/getMetroList.htm",{
					delay:10,
					minChars:1,
					max:20,
					autoFill:true,
					multiple: true,
					selectOnly:true,
					extraParams: { cityId: <xsl:value-of select="/root/srvInfo/City/@id"/> , withId:'yes' },
					formatResult: function(row) {
						return row[0];
					},
					formatItem: function(row, i, max) {		
						return row[0];
					}
				}).result(function(event, item) {
				//	$("#cityStreetId").val(item[1]);
				});
				
				jQuery(function($){
				   $("#asteriskPhone").mask("+7 (999) 999-99-99",{placeholder:" "});
				   $("#clinicPhone").mask("+7 (999) 999-99-99",{placeholder:" "});
				});
				
				$('#editGeneralForm input, select, textarea').change(function() {
				  chKey = false;
				});
				$('#editContactForm input, select, textarea').change(function() {
				  chKey = false;
				});
				
			});	   
			
			<![CDATA[
				function addNotifyEmail () {
					var str = '';
				 	str += '<div style="margin:0" id="email_line_'+(notify_emails_number_pos+1)+'">';
				 	str += '<input name="notify_emails['+(notify_emails_number_pos+1)+']" id="notify_emails_'+(notify_emails_number_pos+1)+'" value="" class="inputForm" style="width: 250px;"/>';
				 	str += '<span class="link" style="margin-left: 10px" onclick="delNotifyEmail(\''+(notify_emails_number_pos+1)+'\')">удалить</span>';
				 	str += '</div>';
				 	$("#notifyEmailsList").append(str);
				 	notify_emails_number_pos++;
				 }

				 function addNotifyPhone () {
				    var str = '';
				 	str += '<div style="margin:0" id="notify_phone_line_'+(notify_phones_number_pos+1)+'">';
				 	str += '<input name="notify_phones['+(notify_phones_number_pos+1)+']" id="notify_phones_'+(notify_phones_number_pos+1)+'" value="" class="inputForm" style="width: 250px;"/>';
				 	str += '<span class="link" style="margin-left: 10px" onclick="delNotifyPhone(\''+(notify_phones_number_pos+1)+'\')">удалить</span>';
				 	str += '</div>';
				 	$("#notifyPhonesList").append(str);
				 	notify_phones_number_pos++;
				 }

				function addPhoneNumber () {
					var str = '<div class="null" style="height: 5px"/>';
				 	str += '<div style="margin:0" id="phoneLine_'+(number_pos+1)+'">';
				 	str += '<input name="label['+(number_pos+1)+']" id="label_'+(number_pos+1)+'" value="Основной" class="inputForm" maxlength="20" style="width: 120px"/>&nbsp;&nbsp;:';
				 	str += '<input name="phones['+(number_pos+1)+']" id="phones_'+(number_pos+1)+'" value="" class="inputForm" maxlength="20" style="width: 120px; margin-left: 13px"/>';
				 	str += '<span class="link" style="margin-left: 10px" onclick="delPhoneNumber(\''+(number_pos+1)+'\')">удалить</span>';
				 	str += '</div>';
				 	$("#phoneList").append(str);
				 	number_pos++;
				 }
				 
				 function delPhoneNumber ( id ) {
				 	$("#phoneLine_"+id).remove();
				 }

				  function delNotifyEmail ( id ) {
				 	$("#notify_emails_"+id).parent().remove();
				 }

				 function delNotifyPhone ( id ) {
				 	$("#notify_phones_"+id).parent().remove();
				 }
			
				function checkLine ( idElt ){
					($(idElt).attr('checked'))?$(idElt).attr('checked',false):$(idElt).attr('checked',true);
				}
			]]>
			
			function toggleTrue (elt) {
				(elt.attr("checked")) ? elt.attr("checked", false) : elt.attr("checked", true);  
			}
			
			function checkClinicType(parent) {
				if (parent == 'privat') {
					if ($("#isPrivatDoctor").attr("checked")) {
						$("#isClinic").attr("checked",false);
						$("#isDiagnostic").attr("checked",false);
					}
				} else {
					if ($("#isClinic").attr("checked") || $("#isDiagnostic").attr("checked")) {
						$("#isPrivatDoctor").attr("checked",false);
					}
					
				}
			}
		</script>
	</xsl:template>
	
	
	
	
	<xsl:template match="root">	
		<div id="slideNavigator">
			<table cellpadding="0" cellspacing="0" width="100%" border="0">
				<tr>
					<td class="start"><div class="null" style="height: 28px">&#160;</div></td>
					<td width="150">
						<xsl:attribute name="class"><xsl:choose><xsl:when test="srvInfo/Slide = '1'">open</xsl:when><xsl:otherwise>close</xsl:otherwise></xsl:choose></xsl:attribute>
						<![CDATA[ <span onclick=" if (chKey) { chSlide(this,'1');} else { if ( confirm('Данные НЕ были СОХРАНЕНЫ. Вы все равно хотите переключить закладку?')) {chKey = true; chSlide(this,'1');} } "> ]]>
							Клиника
							<xsl:if test="dbInfo/ParentClinic/Clinic/@id">
								(филиал)
							</xsl:if>
						<![CDATA[ </span> ]]>
					</td>
					<xsl:if test="srvInfo/ShowSettings = '1'">
						<td width="150">
							<xsl:attribute name="class"><xsl:choose><xsl:when test="srvInfo/Slide = '6'">open</xsl:when><xsl:otherwise>close</xsl:otherwise></xsl:choose></xsl:attribute>
							<![CDATA[ <span class="contracts-tab" onclick=" if (chKey) { chSlide(this,'6');} else { if ( confirm('Данные НЕ были СОХРАНЕНЫ. Вы все равно хотите переключить закладку?')) {chKey = true; chSlide(this,'6');} } ">Тарифы и реквизиты</span> ]]>
						</td>
						<td  width="150">
							<xsl:attribute name="class"><xsl:choose><xsl:when test="srvInfo/Slide = '5'">open</xsl:when><xsl:otherwise>close</xsl:otherwise></xsl:choose></xsl:attribute>
							<![CDATA[ <span onclick=" if (chKey) { chSlide(this,'5');} else { if ( confirm('Данные НЕ были СОХРАНЕНЫ. Вы все равно хотите переключить закладку?')) {chKey = true; chSlide(this,'5');} } ">Доп. настройки</span> ]]>
						</td>
						<td  width="150">
							<xsl:attribute name="class"><xsl:choose><xsl:when test="srvInfo/Slide = '2'">open</xsl:when><xsl:otherwise>close</xsl:otherwise></xsl:choose></xsl:attribute>
							<![CDATA[ <span onclick=" if (chKey) { chSlide(this,'2');} else { if ( confirm('Данные НЕ были СОХРАНЕНЫ. Вы все равно хотите переключить закладку?')) {chKey = true; chSlide(this,'2');} } ">Администратор</span> ]]>
						</td>
						<td  width="150">
							<xsl:attribute name="class"><xsl:choose><xsl:when test="srvInfo/Slide = '3'">open</xsl:when><xsl:otherwise>close</xsl:otherwise></xsl:choose></xsl:attribute>
							<![CDATA[ <span onclick=" if (chKey) { chSlide(this,'3');} else { if ( confirm('Данные НЕ были СОХРАНЕНЫ. Вы все равно хотите переключить закладку?')) {chKey = true; chSlide(this,'3');} } ">Филиалы</span> ]]>
						</td>
						<xsl:if test="dbInfo/Clinic/IsDiagnostic and dbInfo/Clinic/IsDiagnostic = 'yes'">
							<td  width="150">
								<xsl:attribute name="class"><xsl:choose><xsl:when test="srvInfo/Slide = '4'">open</xsl:when><xsl:otherwise>close</xsl:otherwise></xsl:choose></xsl:attribute>
								<![CDATA[ <span onclick=" if (chKey) { chSlide(this,'4');} else { if ( confirm('Данные НЕ были СОХРАНЕНЫ. Вы все равно хотите переключить закладку?')) {chKey = true; chSlide(this,'4');} } ">Исследования</span> ]]>
							</td>
						</xsl:if>
						<td  width="150">
							<xsl:attribute name="class"><xsl:choose><xsl:when test="srvInfo/Slide = '7'">open</xsl:when><xsl:otherwise>close</xsl:otherwise></xsl:choose></xsl:attribute>
							<![CDATA[ <span onclick=" if (chKey) { chSlide(this,'7');} else { if ( confirm('Данные НЕ были СОХРАНЕНЫ. Вы все равно хотите переключить закладку?')) {chKey = true; chSlide(this,'7');} } ">Фотографии</span> ]]>
						</td>
					</xsl:if>
				</tr>
			</table>   
		</div>	
		
		<div id="slides">
			<div id="slide_1" class="slide">
				<xsl:if test="srvInfo/Slide != '1'"><xsl:attribute name="style">display: none</xsl:attribute></xsl:if>
				<xsl:call-template name="commonData"/>
			</div>
			<div id="slide_6" class="slide"></div>
			<div id="slide_5" class="slide">
				<xsl:if test="srvInfo/Slide != '5'"><xsl:attribute name="style">display: none</xsl:attribute></xsl:if>
				<xsl:call-template name="settings"/>
			</div>
			<div id="slide_2" class="slide">
				<xsl:if test="srvInfo/Slide != '2'"><xsl:attribute name="style">display: none</xsl:attribute></xsl:if>
				<xsl:call-template name="adminUser"/>
			</div>
			<div id="slide_3" class="slide">
				<xsl:if test="srvInfo/Slide != '3'"><xsl:attribute name="style">display: none</xsl:attribute></xsl:if>
				<xsl:call-template name="branchList"/>
			</div>
			<xsl:if test="dbInfo/Clinic/IsDiagnostic and dbInfo/Clinic/IsDiagnostic = 'yes'">
				<div id="slide_4" class="slide">
					<xsl:if test="srvInfo/Slide != '4'"><xsl:attribute name="style">display: none</xsl:attribute></xsl:if>
					<xsl:call-template name="diagnosticList"/>
				</div>
			</xsl:if>
			<div id="slide_7" class="slide">
				<xsl:if test="srvInfo/Slide != '7'"><xsl:attribute name="style">display: none</xsl:attribute></xsl:if>
				<xsl:call-template name="gallery"/>
			</div>
			<input type="hidden" id="clinicId" value="{dbInfo/Clinic/@id}"/>
		</div>	   
		<div id="statusWin" class="warning" style="margin-top: 10px"></div>	  

		<!-- 	DEBUG MODE	-->
		<xsl:if test="$debug = 'yes'">
			<div class="debug">
				<a href="/clinic/clinic.htm?id={srvInfo/Id}&amp;parentId={srvInfo/ParentId}&amp;debug=yes" target="_blank">Debug mode</a>
			</div>
		</xsl:if>
	</xsl:template>

	
	
	

	
	<xsl:template name="commonData"> 	
		<xsl:variable name="isChild">
			<xsl:choose>
				<xsl:when test="dbInfo/ParentClinic/Clinic/@id">yes</xsl:when>
				<xsl:otherwise>no</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>  
		
		<div id="editMode" style="position: relative">
			<form name="editGeneralForm" id="editGeneralForm" method="post">
				<input type="hidden" name="id" value="{dbInfo/Clinic/@id}"/>
				<input type="hidden" name="slide" id="slide" value="1"/>

				<!-- Фотография  -->
				<div style="width: 140px; float: left">
					<div class="wb logo_frame">
					<xsl:if test="dbInfo/Clinic/FullLogoPath">
						<div style="background: url('{dbInfo/Clinic/FullLogoPath}?param={srvInfo/Random}') no-repeat; height: 149px;"></div>
					</xsl:if>
					</div>
					<xsl:choose>
						<xsl:when test="dbInfo/Clinic/@id">
							<div style="text-align: right; padding: 0 30px 0 0"><span class="link" onclick="loadImg({dbInfo/Clinic/@id})">загрузить</span></div>
							<xsl:if test="dbInfo/Clinic/LogoPath != ''">
								<div style="text-align: right; padding: 0 30px 0 0"><span class="link" onclick="deleteImg({dbInfo/Clinic/@id}, {dbInfo/Clinic/ParentClinicId})">удалить</span></div>
							</xsl:if>
						</xsl:when>
						<xsl:otherwise>
							<div style="text-align: right; padding: 0 30px 0 0"><span>сохраните клинику</span></div>
						</xsl:otherwise>
					</xsl:choose>
				</div>

				<div style="float: left">
					<table>
						<col width="120"/>

						<xsl:choose>
							<xsl:when test="srvInfo/ParentId and srvInfo/ParentId!= 0">
								<tr>
									<td>Идентификатор филиала:</td>
									<td>
										<strong><xsl:value-of select="dbInfo/Clinic/@id"/></strong>
										<input type="hidden" name="parentId" value="{srvInfo/ParentId}"/>
									</td>
								</tr>
							</xsl:when>
							<xsl:when test="dbInfo/Clinic/@id and dbInfo/Clinic/@id != ''">
								<tr>
									<td>Идентификатор:</td>
									<td>
										<strong><xsl:value-of select="dbInfo/Clinic/@id"/></strong>
									</td>
								</tr>
							</xsl:when>
						</xsl:choose>
						<xsl:if test="$isChild = 'yes'">
							<tr>
									<td>Главная клиника:</td>
									<td>
										<div style="width: 450px; background-color: #94ACD1; padding: 2px 0 2px 5px">
										<strong><span class="link" onclick="editContent('{dbInfo/ParentClinic/Clinic/@id}','0')"><xsl:value-of select="dbInfo/ParentClinic/Clinic/Title"/></span></strong>
										(<xsl:value-of select="dbInfo/ParentClinic/Clinic/@id"/>)
										</div>
									</td>
								</tr>
						</xsl:if>
						<tr><td colspan="2"><div class="null" style="height: 10px"/></td></tr>
						<tr>
							<td>Краткое название:</td>
							<td>
								<input name="shortName" id="shortName" value="{dbInfo/Clinic/ShortName}" class="inputForm" style="width:262px " maxlength="100"/><span class="err"></span>
							</td>
						</tr>
						<tr>
							<td>Название:</td>
							<td>
								<input name="title" id="title" value="{dbInfo/Clinic/Title}" class="inputForm" maxlength="255" required="1"/><span class="err"></span>
							</td>
						</tr>

						<tr>
							<td>Alias:</td>
							<td>
								<input name="alias" id="alias" value="{dbInfo/Clinic/RewriteName}" class="inputForm" style="width: 262px" maxlength="50" onblur="checkAlias(this.value, '{dbInfo/Clinic/@id}')" onfocus="$('#aliasCheck').html('')"/>
								<xsl:if test = "dbInfo/Clinic/IsDiagnostic ='yes'">
								<sup class="link">
									<a href="http://diagnostica.docdoc.ru/kliniki/{dbInfo/Clinic/RewriteName}" target="_blank" class="txt10">карточка ДЦ</a>
								</sup>
								</xsl:if>
								&#160;<span class="err"></span>
								&#160;<span id="aliasCheck"></span>
							</td>
						</tr>
						<tr>
							<td>Профиль:</td>
							<td>
								<div class="checkbox">
									<input type="checkbox" name="isClinic" id="isClinic" value="yes" onclick="checkClinicType('clinic')">
										<xsl:if test="dbInfo/Clinic/IsClinic ='yes'"><xsl:attribute name="checked"/></xsl:if>
									</input>
								</div>
								<div class="checkboxLab" style="padding:0">
									<span class="link" onclick="toggleTrue($(this).parent().prev().children()); checkClinicType('clinic')">Клиника</span>
								</div>
								<div class="checkbox" style="margin-left: 40px">
									<input type="checkbox" name="isDiagnostic" id="isDiagnostic" value="yes" onclick="checkClinicType('clinic')">
										<xsl:if test="dbInfo/Clinic/IsDiagnostic ='yes'"><xsl:attribute name="checked"/></xsl:if>
									</input>
								</div>
								<div class="checkboxLab" style="padding:0">
									<span class="link" onclick="toggleTrue($(this).parent().prev().children()); checkClinicType('clinic')">Диагностический центр (ДЦ)</span>
								</div>
								<div class="checkbox" style="margin-left: 40px">
									<input type="checkbox" name="isPrivatDoctor" id="isPrivatDoctor" value="yes" onclick="checkClinicType('privat')">
										<xsl:if test="dbInfo/Clinic/IsPrivatDoctor ='yes'"><xsl:attribute name="checked"/></xsl:if>
									</input>
								</div>
								<div class="checkboxLab" style="padding:0">
									<span class="link" onclick="toggleTrue($(this).parent().prev().children()); checkClinicType('privat')">Частный врач</span>
								</div>
							</td>
						</tr>
						<tr>
							<td>Специализация:</td>
							<td>
								<span>
									<input type="radio" name="age" id="multy" value="multy">
										<xsl:if test="not(dbInfo/Clinic/Age ) or dbInfo/Clinic/Age = 'multy'"><xsl:attribute name="checked"/></xsl:if>
									</input>
									<label style="cursor:pointer;" onclick="$(this).prev().attr('checked',true)">многопрофильная</label>
								</span>
								<span style="margin-left: 20px">
									<input type="radio" name="age" id="adult" value="adult">
										<xsl:if test="dbInfo/Clinic/Age = 'adult' "><xsl:attribute name="checked"/></xsl:if>
									</input>
									<label style="cursor:pointer" onclick="$(this).prev().attr('checked',true)">взрослые</label>
									<img src="/img/icon/adult_clinic.png" title="Для взрослых"/>
								</span>
								<span style="margin-left: 20px">
									<input type="radio" name="age" id="child" value="child">
										<xsl:if test="dbInfo/Clinic/Age = 'child' "><xsl:attribute name="checked"/></xsl:if>
									</input>
									<label style="cursor:pointer" onclick="$(this).prev().attr('checked',true)">дети</label>
									<img src="/img/icon/girl.png" title="Детская клиника"/>
								</span>
							</td>
						</tr>
						<tr>
							<td>Контактное лицо:</td>
							<td>
								<input name="contactName" id="contactName" value="{dbInfo/Clinic/ContactName}" class="inputForm" maxlength="100"/>
							</td>
						</tr>
						<tr>
							<td>Сайт:</td>
							<td>
								<input name="url" id="url" value="{dbInfo/Clinic/URL}" class="inputForm" maxlength="50" style="width: 262px" />
								<xsl:if test="dbInfo/Clinic/URL != ''">
									<a href="http://{dbInfo/Clinic/URL}" target="_blank" class="txt10" style="margin-left: 20px">перейти по ссылке</a>
								</xsl:if>
							</td>
						</tr>
						<tr>
							<td>Email клиники:</td>
							<td>
								<input name="email" id="email" value="{dbInfo/Clinic/Email}" type="Text" class="inputForm" maxlength="50" style="width: 262px"/>
							</td>
						</tr>
					</table>
				</div>
				<div class="clear"/>
				<div class="null"/>
				<table>
					<tr>
						<td>Время работы:</td>	
						<td>
							<table cellpadding="0" cellspacing="0" class="timetable">
								<col width="24%"/>
								<col width="24%"/>
								<col width="6%"/>
								<col width="24%"/>
								<col width="24%"/>
								<tr>
									<td><input name="weekdays" id="weekdays" value="{dbInfo/Clinic/WorkTime/WeekDays}" type="Text" class="inputForm" maxlength="20" style="width: 90px"/></td>
									<td><input name="weekend" id="weekend" value="{dbInfo/Clinic/WorkTime/WeekEnd}" type="Text" class="inputForm" maxlength="20" style="width: 90px"/></td>
									<td align="center"></td>
									<td><input name="saturday" id="saturday" value="{dbInfo/Clinic/WorkTime/Saturday}" type="Text" class="inputForm" maxlength="20" style="width: 90px"/></td>
									<td><input name="sunday" id="sunday" value="{dbInfo/Clinic/WorkTime/Sunday}" type="Text" class="inputForm" maxlength="20" style="width: 90px"/></td>
								</tr>
								<tr>
									<td align="center"><sup>будни</sup></td>
									<td align="center"><sup>выходные</sup></td>
									<td></td>
									<td align="center"><sup>суббота</sup></td>
									<td align="center"><sup>воскресенье</sup></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td>Логотип:</td>	
						<td>
							<input name="logoPath" id="logoPath" value="{dbInfo/Clinic/LogoPath}" type="Text" class="inputForm" maxlength="50"/>
						</td>
					</tr>
					<tr><td colspan="2"><div class="null"/></td></tr>
					<tr>
						<td>Подменный телефон:</td>
						<td>
							<input name="asteriskPhone" id="asteriskPhone" value="{dbInfo/Clinic/AsteriskPhone}" class="inputForm" style="width: 120px; border-color: #0000a0" maxlength="20"/>
							на номер клиники
							<input name="clinicPhone" id="clinicPhone" value="{dbInfo/Clinic/Phone}" class="inputForm" style="width: 120px;" maxlength="20"/>
						</td>
					</tr>
					<tr>
						<td>Телефоны партнеров:</td>
						<td>
							<xsl:element name="a">
								<xsl:attribute name="href">
									/2.0/clinicPartnerPhone?dfs_docdoc_models_ClinicPartnerPhoneModel%5Bclinic_id%5D=<xsl:value-of select="dbInfo/Clinic/@id"/>
								</xsl:attribute>
								Перейти в справочник
							</xsl:element>
						</td>
					</tr>
					<tr><td colspan="2"><div class="null"/></td></tr>
					<tr>
						<td>Телефоны:</td>	
						<td>
							<div id="phoneList" style="margin: 0">
								<xsl:choose>
									<xsl:when test="dbInfo/Clinic/PhoneList">
										<script>
											 var number_pos = <xsl:value-of select="count(dbInfo/Clinic/PhoneList/Element)"/>
										</script>
										<xsl:for-each select="dbInfo/Clinic/PhoneList/Element">
											<div style="margin:0" id="phoneLine_{position()}">
												<input name="label[{position()}]" id="label_{position()}" value="{Label}" class="inputForm" maxlength="20" style="width: 120px"/>&#160;&#160;:
												<input name="phones[{position()}]" id="phones_{position()}" value="{PhoneFormat}" class="inputForm" maxlength="20" style="width: 120px; margin-left: 10px"/>
												<xsl:if test="position() = 1"><span class="link" style="margin-left: 10px" onclick="addPhoneNumber()">добавить номер</span></xsl:if>
												<xsl:if test="position() &gt; 1"><span class="link" style="margin-left: 10px" onclick="delPhoneNumber('{position()}')">удалить</span></xsl:if>
											</div>
											<xsl:if test="position() != last()"><div class="null" style="height: 5px"/></xsl:if>
										</xsl:for-each>
									</xsl:when>
									<xsl:otherwise>
										<script>
											var number_pos = 1;
										</script>
										<div style="margin: 0" id="phoneLine_1">
											<input name="label[1]" id="label_1" value="Основной" class="inputForm" maxlength="20" style="width: 120px"/>&#160;&#160;:
											<input name="phones[1]" id="phones_1" value="" class="inputForm" maxlength="20" style="width: 120px; margin-left: 10px"/>
											<span class="link" style="margin-left: 10px" onclick="addPhoneNumber()">добавить номер</span>
										</div>
									</xsl:otherwise>
									
								</xsl:choose>
							</div>
						</td>
					</tr>
					<!-- <tr>
						<td>Адрес (старое):</td>	
						<td>
							<span id="oldAddress"><xsl:value-of select="dbInfo/Clinic/OldAddress"/></span>
						</td>
					</tr> -->
					<tr>
						<td>Город:</td>	
						<td>
							<input name="city" id="city" class="inputForm" maxlength="50">
								<xsl:attribute name="value">
									<xsl:choose>
										<xsl:when test="dbInfo/Clinic/City and dbInfo/Clinic/City!=''"><xsl:value-of select="dbInfo/Clinic/City"/></xsl:when>
										<xsl:otherwise><xsl:value-of select="/root/srvInfo/City"/></xsl:otherwise>
									</xsl:choose>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td>Район:</td>
						<td>
							<select name="districtId" id="districtId">
								<option value="">--- Не задан ---</option>
								<xsl:for-each select="dbInfo/DistrictDict/Element">
									<option value="{@id}">
										<xsl:if test="@id = /root/dbInfo/Clinic/DistrictId">
											<xsl:attribute name="selected"/>
										</xsl:if>
										<xsl:value-of select="."/>
									</option>
								</xsl:for-each>
							</select>
						</td>
					</tr>
					<tr>
						<td>Улица:</td>	
						<td>
							<input name="cityStreet" id="cityStreet" value="{dbInfo/Clinic/Street}"  class="inputForm" type="Text" maxlength="50"/>
						</td>
					</tr>
					<tr>
						<td>Дом, корп., стр. и т.п.:</td>	
						<td>
							<input name="addressEtc" id="addressEtc" value="{dbInfo/Clinic/House}" type="Text" class="inputForm" maxlength="50"/>
						</td>
					</tr>
					<tr>
						<td>Ближайшие станции метро:</td>	
						<td>
							<input name="metro" id="metro" value="{dbInfo/Clinic/Metro}"  class="inputForm" type="Text" maxlength="150">
								<xsl:if test="dbInfo/Clinic/MetroList and dbInfo/Clinic/MetroList/Element">
									<xsl:attribute name="value">
										<xsl:for-each select="dbInfo/Clinic/MetroList/Element">
											<xsl:value-of select="."/>[<xsl:value-of select="@id"/>]<xsl:if test="position() != last()">, </xsl:if>
										</xsl:for-each>
									</xsl:attribute>
								</xsl:if>
							</input>
						</td>
					</tr>
					<tr>
						<td>Долгота:</td>	
						<td>
							<input name="longitude" id="longitude" value="{dbInfo/Clinic/Longitude}"  class="inputFormReadOnly" type="Text" readonly=""/>
							<span class="link" onclick="getLongLat()" style="margin-left: 20px">обновить координаты</span>
							<span style="margin-left: 10px" id="loader"></span>
						</td>
					</tr>
					<tr>
						<td>Широта:</td>	
						<td>
							<input name="latitude" id="latitude" value="{dbInfo/Clinic/Latitude}"  class="inputFormReadOnly" type="Text" readonly=""/>
						</td>
					</tr>
					<tr>
						<td>Краткое описание:</td>	
						<td>
							<textarea name="shortDescription" class="inputForm" style="height: 80px"><xsl:value-of select="dbInfo/Clinic/ShortDescription"/></textarea>
						</td>
					</tr>
					<tr>
						<td>Описание:</td>	
						<td>
							<textarea name="description" class="inputForm" style="height: 80px"><xsl:value-of select="dbInfo/Clinic/Description"/></textarea>
						</td>
					</tr>
					<tr>
						<td>Как добраться пешком:</td>
						<td>
							<textarea name="wayOnFoot" class="inputForm" style="height: 80px"><xsl:value-of select="dbInfo/Clinic/WayOnFoot"/></textarea>
						</td>
					</tr>
					<tr>
						<td>Как добраться на машине:</td>
						<td>
							<textarea name="wayOnCar" class="inputForm" style="height: 80px"><xsl:value-of select="dbInfo/Clinic/WayOnCar"/></textarea>
						</td>
					</tr>
					<tr>
						<td>Комментарий менеджера:</td>	
						<td>
							<textarea name="operatorComment" class="inputForm" style="height: 50px"><xsl:value-of select="dbInfo/Clinic/OperatorComment"/></textarea>
						</td>
					</tr>
					<!-- <tr>
						<td>Порядок в выдаче (ДЦ):</td>	
						<td>
							<input name="resultSetSortPosition" id="resultSetSortPosition" value="{dbInfo/Clinic/SortPosition}"  class="inputForm" style="width: 20px" type="Text" maxlength="2"/>
							<span style="margin: 0 5px 0 5px">(1 высший / 99 низший приоритеты)</span>
						</td>
					</tr>
					 -->        
					<tr>
						<td>Статус:</td>	
						<td>
							<div style="width: 20px; height: 20px; float: left;">
								<xsl:choose>
									<xsl:when test="/root/dbInfo/Clinic/Status = '1'">
										<img src="/img/icon/check_question.png" style="margin-top: 2px"/>
									</xsl:when>
									<xsl:when test="/root/dbInfo/Clinic/Status = '2'">
										<img src="/img/icon/check_n.png" style="margin-top: 2px"/>
									</xsl:when>
									<xsl:when test="/root/dbInfo/Clinic/Status = '3'">
										<img src="/img/icon/check_ok.png" style="margin-top: 2px"/>
									</xsl:when>
									<xsl:when test="/root/dbInfo/Clinic/Status = '4'">
										<img src="/img/icon/check_no.png" style="margin-top: 2px"/>
									</xsl:when>
									<xsl:otherwise>
										<img src="/img/common/null.gif" style="margin-top: 2	px"/>
									</xsl:otherwise>
								</xsl:choose>
							</div>
							<div style="float: left;">
								<label>изменить на:</label> 
								<select name="statusClinic" id="statusClinic" style="width: 150px">
									<option value="">--- Любой ---</option>
									<xsl:for-each select="dbInfo/StatusDict/Element">
										<option value="{@id}">
										    <xsl:if test="@id = /root/dbInfo/Clinic/Status">
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
			</form>
			<div style="position:relative; margin: 20px 10px 30px 0;">		  
				<div class="form" style="width:100px; float:right; margin-left: 10px" onclick="(modalWinKey === 'close') ? $('#modalWin').hide() : window.location.reload()">ЗАКРЫТЬ</div>
				<div class="form" style="width:100px; float:right;" onclick="saveContent('#editGeneralForm','/clinic/service/editData.htm')">СОХРАНИТЬ</div>
				<xsl:if test="(/root/srvInfo/UserData/Rights/Right = 'ADM' or /root/srvInfo/UserData/Rights/Right = 'ACM') and dbInfo/Clinic/@id">
					<div class="form" style="width:100px; float:right; margin-right: 10px" onclick="deleteContent('{dbInfo/Clinic/@id}')">УДАЛИТЬ</div>
				</xsl:if>
			</div>
		</div>	 
			  
	</xsl:template>	 
			
	
	
	
	
	<xsl:template name="adminUser">
		<div id="editMode" style="position: relative">
			<form name="editContactForm" id="editContactForm" method="post">
				<input type="hidden" name="id" value="{dbInfo/Clinic/@id}"/>
				<xsl:if test="srvInfo/ParentId and srvInfo/ParentId!= 0">
					<input type="hidden" name="parentId" value="{srvInfo/ParentId}"/>
				</xsl:if>
				<input type="hidden" name="adminId" value="{dbInfo/Clinic/AdminList/Element/@id}"/>
				<input type="hidden" name="slide" id="slide" value="2"/>
			
			<table border="0">	 
				<col width="180"/>
				<col/>
				
				
				<tr>
					<td>E-mail:</td>	
					<td>
						<input name="adminEmail" id="adminEmail" value="{dbInfo/Clinic/AdminList/Element/Email}" style="width:200px" maxlength="50" required="1" onchange="checkEmail(this.value)" onfocus="$('#loginCheck').html('')"/>
						<xsl:if test="dbInfo/Clinic/AdminList/Element/Email">
							<span class="link" style="margin-left: 10px" onclick="$('#passwd').val('');$('#passLine').toggle()"> изменить пароль</span>
						</xsl:if>
						<span class="err"></span>
						&#160;<span id="loginCheck"></span>
						
					</td>
				</tr> 
				<tr id="passLine">
					<xsl:attribute name="class">
						<xsl:choose>
							<xsl:when test="dbInfo/Clinic/AdminList/Element/Email">hd</xsl:when>
						</xsl:choose>
					</xsl:attribute>
					<td>Пароль</td>	
					<td>
						<input name="passwd" id="passwd" value="" style="width:200px" maxlength="50"/>
						<span class="form" style="width:120px; margin-left: 10px" onclick="setPasswd()">УСТАНОВИТЬ</span>
						<br/> 
						<input type="Checkbox" name="sendInv" id="sendInv" value="1" style="margin: 2px 5px 0 0px"/>
						<![CDATA[ 
						<label style="cursor:pointer" onclick="( $('#sendInv').attr('checked')) ? $('#sendInv').attr('checked', false) : $('#sendInv').attr('checked', true)">отправить уведомление</label>
						<span class="delimiter">|</span>
						<span class="link" style="margin-left: 10px" onclick="$.get('/service/getPassword.htm',function(data){$('#passwd').val(data)})">генерация пароля</span>
						]]>
					</td>
				</tr>
						
					
				
				
				<tr><td colspan="2"><div class="null" style="height: 10px"/></td></tr>
				<tr>
					<td>Фамилия:</td>	
					<td><input name="lastName" id="lastName" value="{dbInfo/Clinic/AdminList/Element/LName}" class="inputForm" maxlength="50" required="1"/><span class="err"></span></td>
				</tr>
				<tr>
					<td>Имя:</td>	
					<td><input name="firstName" id="firstName" value="{dbInfo/Clinic/AdminList/Element/FName}" class="inputForm" maxlength="50" required="1"/><span class="err"></span></td>
				</tr>
				<tr>
					<td>Отчество:</td>	
					<td><input name="middleName" id="middleName" value="{dbInfo/Clinic/AdminList/Element/MName}" class="inputForm" maxlength="50" required="1"/><span class="err"></span></td>
				</tr>  
				<tr>
					<td>Моб. телефон:</td>	
					<td><input name="cellPhone" id="cellPhone" value="{dbInfo/Clinic/AdminList/Element/CellPhone}" class="inputForm" maxlength="50"/></td>
				</tr> 
				<tr>
					<td>Телефон:</td>	
					<td><input name="phone" id="phone" value="{dbInfo/Clinic/AdminList/Element/Phone}" class="inputForm" maxlength="50"/></td>
				</tr> 
				<tr>
					<td>Комментарий оператора:</td>	
					<td>
						<textarea name="adminOperatorComment" class="inputForm" style="height: 50px"><xsl:value-of select="dbInfo/Clinic/AdminList/Element/Operator_Comm"/></textarea>
					</td>
				</tr> 
			</table>	
			</form>
			<div style="position:relative; margin: 20px 10px 30px 0;">		  
				<div class="form" style="width:100px; float:right; margin-left: 10px" onclick="(modalWinKey === 'close') ? $('#modalWin').hide() : window.location.reload()">ЗАКРЫТЬ</div>
				<div class="form" style="width:100px; float:right;" onclick="saveContent('#editContactForm', '/clinic/service/editContactData.htm', '2')">СОХРАНИТЬ</div>
				<xsl:if test="(/root/srvInfo/UserData/Rights/Right = 'ADM' or /root/srvInfo/UserData/Rights/Right = 'ACM') and dbInfo/Clinic/@id">
					<div class="form" style="width:100px; float:right; margin-right: 10px" onclick="deleteAdmin('{dbInfo/Clinic/@id}','{srvInfo/ParentId}')">УДАЛИТЬ</div>
				</xsl:if>
			</div>
		</div>	 
	</xsl:template>
	
	
	
	
	
	<xsl:template name="branchList">
		<xsl:if test="dbInfo/ParentClinic">
			<table>
				<tr>
					<td>
						Главная клиника:
					</td>
					<td>
						<span class="link" onclick="editContent('{dbInfo/ParentClinic/Clinic/@id}','0')"><xsl:value-of select="dbInfo/ParentClinic/Clinic/Title"/></span>
						&#160;/&#160;
						<xsl:value-of select="dbInfo/ParentClinic/Clinic/City"/>,
						<xsl:value-of select="dbInfo/ParentClinic/Clinic/Street"/>,
						<xsl:value-of select="dbInfo/ParentClinic/Clinic/House"/>
					</td>
				</tr>
			</table>
		</xsl:if>
		<div class="actionList">  
			<xsl:variable name="clinicId">
				<xsl:choose>
					<xsl:when test="dbInfo/ParentClinic"><xsl:value-of select="dbInfo/ParentClinic/Clinic/@id"/></xsl:when>
					<xsl:when test="dbInfo/Clinic"><xsl:value-of select="dbInfo/Clinic/@id"/></xsl:when>
				</xsl:choose>
			</xsl:variable>
			<xsl:if test="dbInfo/Clinic/ParentClinicId = 0">
				<a href="javascript:editContent('0','{$clinicId}')">Добавить филиал</a>
			</xsl:if>
		</div>
		
			
		<xsl:variable name="tdCount" select="4"/>
		<table cellpadding="0" cellspacing="1" width="100%" border="0" class="resultSet">
			<col width="30"/>
			<col/>
			<col width="30"/>
			<col width="30"/>

			<tr>
				<th>Id</th>
				<th colspan="2">Название / адрес филиала</th>
				<th>Статус</th>
			</tr>
			<xsl:choose>
				<xsl:when test="not(dbInfo/ParentClinic) and dbInfo/Clinic/ClinicBranchList/Element">
					<xsl:for-each select="dbInfo/Clinic/ClinicBranchList/Element">
						<xsl:variable name="class">
							<xsl:choose>
								<xsl:when test="(position() div 2) - floor(position() div 2) &gt; 0">odd</xsl:when>
								<xsl:otherwise>even</xsl:otherwise>
							</xsl:choose>
						</xsl:variable>
						<tr id="tr_{@id}" class="{$class}" backclass="{$class}" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','{$class}')">

							<td align="right"><xsl:value-of select="@id"/></td>
							<td>
								<a href="javascript:editContent('{@id}','{/root/dbInfo/Clinic/@id}')">
									<xsl:choose>
										<xsl:when test="Title and Title != ''">
											<xsl:value-of select="Title"/>
										</xsl:when>
										<xsl:otherwise>
											Филиал клиники <xsl:value-of select="/root/dbInfo/Clinic/Title"/>
										</xsl:otherwise>
									</xsl:choose>
								</a>
								&#160;/&#160;
								<xsl:value-of select="City"/>,
								<xsl:value-of select="Street"/>,
								<xsl:value-of select="House"/>
							</td>
							<td>
								<xsl:choose>
									<xsl:when test="Age = 'child'">
										<img src="/img/icon/girl.png" title="Детская клиника"/>
									</xsl:when>
									<xsl:when test="Age = 'adult'">
										<img src="/img/icon/adult_clinic.png" title="Для взрослых"/>
									</xsl:when>
								</xsl:choose>
							</td>
							<td align="center">
								<xsl:variable name="status" select="Status"/>
								<img src="/img/icon/status_{Status}.png" title="{/root/dbInfo/StatusDict/Element[@id = $status]/.}"/>
							</td>
						</tr>
					</xsl:for-each>
				</xsl:when>
				
				<xsl:when test="dbInfo/ParentClinic and dbInfo/ParentClinic/Clinic/ClinicBranchList/Element">
					<xsl:for-each select="dbInfo/ParentClinic/Clinic/ClinicBranchList/Element">
						<xsl:variable name="class">
							<xsl:choose>
								<xsl:when test="(position() div 2) - floor(position() div 2) &gt; 0">odd</xsl:when>
								<xsl:otherwise>even</xsl:otherwise>
							</xsl:choose>
						</xsl:variable>
						<tr id="tr_{@id}" class="{$class}" backclass="{$class}" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','{$class}')">

							<td align="right"><xsl:value-of select="@id"/></td>
							<td>
								<xsl:choose>
									<xsl:when test="@id = /root/srvInfo/Id">
										<span>
											<xsl:choose>
												<xsl:when test="Title and Title != ''">
													<xsl:value-of select="Title"/>
												</xsl:when>
												<xsl:otherwise>
													Филиал клиники <xsl:value-of select="/root/dbInfo/ParentClinic/Clinic/Title"/>
												</xsl:otherwise>
											</xsl:choose>
										</span>
									</xsl:when>
									<xsl:otherwise>
										<a href="javascript:editContent('{@id}','{/root/dbInfo/ParentClinic/Clinic/@id}')">
											<xsl:choose>
												<xsl:when test="Title and Title != ''">
													<xsl:value-of select="Title"/>
												</xsl:when>
												<xsl:otherwise>
													Филиал клиники <xsl:value-of select="/root/dbInfo/ParentClinic/Clinic/Title"/>
												</xsl:otherwise>
											</xsl:choose>
										</a>
									</xsl:otherwise>
								</xsl:choose>
								&#160;/&#160;
								<xsl:value-of select="City"/>,
								<xsl:value-of select="Street"/>,
								<xsl:value-of select="House"/>
							</td>
							<td>
								<xsl:choose>
									<xsl:when test="Age = 'child'">
										<img src="/img/icon/girl.png" title="Детская клиника"/>
									</xsl:when>
									<xsl:when test="Age = 'adult'">
										<img src="/img/icon/adult_clinic.png" title="Для взрослых"/>
									</xsl:when>
								</xsl:choose>
							</td>
							<td align="center">
								<xsl:variable name="status" select="Status"/>
								<img src="/img/icon/status_{Status}.png" title="{/root/dbInfo/StatusDict/Element[@id = $status]/.}"/>
							</td>
						</tr>
					</xsl:for-each>
				</xsl:when>
				
				
				<xsl:otherwise>
					<tr>
						<td colspan="{$tdCount}" align="center">
							<div style="margin: 10px">Данных не найдено</div>
						</td>
					</tr>
					<tr>
						<td colspan="{$tdCount}" class="deep">
							<div class="null" style="height:2px"/>
						</td>
					</tr>
				</xsl:otherwise>
			</xsl:choose>
		</table>
		
		<!-- 
		<xsl:if test="dbInfo/Clinic/ClinicOldBranchList/Element">
		<div style="margin: 10px 0 5px 0">Старые данные:</div>
		<xsl:for-each select="dbInfo/Clinic/ClinicOldBranchList/Element">
			<div>
				<xsl:value-of select="@id"/>
				&#160;&#160;&#160;
				<xsl:value-of select="Title"/>
				<xsl:if test="@isNew = 'yes'">
					<span style="margin-left:20px"><sup>новый</sup></span>
				</xsl:if>
			</div>
		</xsl:for-each>
		</xsl:if>
		 -->	
	
	</xsl:template>
	
	
	
	
	<xsl:template name="diagnosticList">
		<xsl:param name="context" select="dbInfo/Clinic"/>
		<div style="position: relative">

		<form name="editAddDiagnosticForm" id="editAddDiagnosticForm" method="post">
			<input type="hidden" name="id" value="{$context/@id}"/>
			<input type="hidden" name="slide" id="slide" value="4"/>
			<div class="actionList">  
				<span style="color:#cc0000; font-weight: bold; cursor: pointer" id="addDiagnostickLink" onclick="$('#ceilWin_multy').show();">Добавить исследование</span>
				<div class="ancor" style="float: right">
					<xsl:call-template name="ceilInfo">
						<xsl:with-param name="id" select="'multy'"/>
					</xsl:call-template>
				</div>
			</div>
		</form>
			
		<form name="editDiagnosticForm" id="editDiagnosticForm" method="post">
			<input type="hidden" name="id" value="{$context/@id}"/>
			<input type="hidden" name="slide" id="slide" value="4"/>
					
			<table width="100%" border="0">
				<col/>
				<col width="100"/>
				<col width="100"/>
				<col width="50"/>
				<col width="50"/>
				
				<tr>
					<th>Исследование</th>
					<th>Спец. цена, руб.</th>
					<th>Цена, руб.</th>
					<th>Цена на онлайн-запись, руб.</th>
					<th></th>
				</tr>
				<xsl:for-each select="dbInfo/DiagnosticList/Element[@id = $context/Diagnostics/Element/@id or DiagnosticList/Element/@id = $context/Diagnostics/Element/@id]">
					<xsl:choose>
						<xsl:when test="DiagnosticList/Element">
							<xsl:variable name="class4" select="'group'"/>
							<tr class="{$class4}" backclass="{$class4}" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','{$class4}')">
								<td colspan="5" style="padding: 4px 0 4px 2px">
									<xsl:value-of select="Name"/>
								</td>
							</tr>
						</xsl:when>
						<xsl:otherwise>
							<xsl:variable name="class4">group</xsl:variable>
							<xsl:variable name="id" select="@id"/>
							<xsl:variable name="dc" select="$context/Diagnostics/Element[@id = $id]"/>
							<tr id="tr_{@id}" class="{$class4}" backclass="{$class4}" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','{$class4}')">
								<td>
									<xsl:value-of select="key('diagnostica',@id)/Name"/>
								</td>
								<td style="padding:0">
									<input type="text" name="diagnosticSpecialPrice[{@id}]" value="{$context/Diagnostics/Element[@id = $id]/SpecialPrice}" style="width: 80px; text-align:right;" onfocus="this.select()">
										<xsl:if test="$context/Diagnostics/Element[@id = $id]/SpecialPrice != '0.00'">
											<xsl:attribute name="class">red</xsl:attribute>
										</xsl:if>
									</input>
								</td>
								<td style="padding:0">
									<input type="text" name="diagnosticPrice[{@id}]" value="{$context/Diagnostics/Element[@id = $id]/Price}" style="width: 80px; text-align:right" onfocus="this.select()"/>
								</td>
								<td style="padding:0">
									<input type="text" name="priceForOnline[{@id}]" value="{$context/Diagnostics/Element[@id = $id]/PriceForOnline}" style="width: 80px; text-align:right" onfocus="this.select()"/>
								</td>
								<td align="center">
									<img src="/img/icon/delete.png" style="cursor:pointer" onclick="deleteDiagnostica('{@id}','{$context/@id}','0')"/>
								</td>
							</tr>
						</xsl:otherwise>
					</xsl:choose>
					
					<xsl:for-each select="DiagnosticList/Element[@id = $context/Diagnostics/Element/@id]">
						<xsl:variable name="id" select="@id"/>
						<xsl:variable name="dc" select="$context/Diagnostics/Element[@id = $id]"/>
						<xsl:variable name="class4">
							<xsl:choose>
								<xsl:when test="(position() div 2) - floor(position() div 2) &gt; 0">odd</xsl:when>
								<xsl:otherwise>even</xsl:otherwise>
							</xsl:choose>
						</xsl:variable>
						<tr id="tr_{@id}" class="{$class4}" backclass="{$class4}" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','{$class4}')">
							<td style="padding-left: 20px">
								<xsl:value-of select="key('diagnostica',@id)/Name"/>
							</td>
							<td style="padding:0">
								<input type="text" name="diagnosticSpecialPrice[{@id}]" value="{$context/Diagnostics/Element[@id = $id]/SpecialPrice}" style="width: 80px; text-align:right" onfocus="this.select()">
									<xsl:if test="$context/Diagnostics/Element[@id = $id]/SpecialPrice != '0.00'">
										<xsl:attribute name="class">red</xsl:attribute>
									</xsl:if>
								</input>
							</td>
							<td style="padding:0">
								<input type="text" name="diagnosticPrice[{@id}]" value="{$context/Diagnostics/Element[@id = $id]/Price}" style="width: 80px; text-align:right; margin:0" onfocus="this.select()"/>
							</td>
							<td style="padding:0">
								<input type="text" name="priceForOnline[{@id}]" value="{$context/Diagnostics/Element[@id = $id]/PriceForOnline}" style="width: 80px; text-align:right" onfocus="this.select()"/>
							</td>
							<td align="center">
								<img src="/img/icon/delete.png" style="cursor:pointer" onclick="deleteDiagnostica('{@id}','{$context/@id}','0')"/>
							</td>
						</tr>
					</xsl:for-each>
				</xsl:for-each>

			</table>	
			</form>
			<div style="position:relative; margin: 20px 10px 30px 0;">		  
				<div class="form" style="width:100px; float:right; margin-left: 10px" onclick="(modalWinKey === 'close') ? $('#modalWin').hide() : window.location.reload()">ЗАКРЫТЬ</div>
				<div class="form" style="width:100px; float:right;" onclick="saveContent('#editDiagnosticForm', '/clinic/service/editDiagnosticaData.htm', '4')">СОХРАНИТЬ</div>
			</div>
		</div>
	</xsl:template>
	
	
	
	
	<xsl:template name="settings">
		<xsl:param name="context" select="dbInfo/Clinic"/>
		<div style="position: relative">

			<form name="editSettingsForm" id="editSettingsForm" method="post">
				<input type="hidden" name="id" value="{$context/@id}"/>
				<input type="hidden" name="slide" id="slide" value="5"/>
				
				<table>	 
					<col width="180"/>
					
					<tr>
						<td>Порядок в выдаче (ДЦ):</td>	
						<td>
							<input name="resultSetSortPosition" id="resultSetSortPosition" value="{dbInfo/Clinic/SortPosition}"  class="inputForm" style="width: 20px" type="Text" maxlength="2"/>
							<span style="margin: 0 5px 0 5px">(1 высший / 99 низший приоритеты)</span>
						</td>
					</tr>
					<tr>
						<td>Отправлять SMS уведомления:</td>	
						<td>
							<div class="checkbox" style="margin-left: 0px"> 
								<input type="checkbox" name="sendSMS" id="scheduleShow" value="yes">
									<xsl:if test="dbInfo/Clinic/SendSMS ='yes'"><xsl:attribute name="checked"/></xsl:if>
								</input>
							</div>
						</td>
					</tr>   
					<tr>
						<td>Поддерживает расписание врачей:</td>	
						<td>
							<div class="checkbox" style="margin-left: 0px"> 
								<input type="checkbox" name="scheduleShow" id="scheduleShow" value="enable">
									<xsl:if test="dbInfo/Clinic/ShowSchedule ='enable'"><xsl:attribute name="checked"/></xsl:if>
								</input>
							</div>
						</td>
					</tr>
					<tr>
						<td>Вывод расписания врачей на сайте:</td>
						<td>
							<div class="checkbox" style="margin-left: 0px">
								<input type="checkbox" name="scheduleForDoctors" id="scheduleForDoctors" value="1">
									<xsl:if test="dbInfo/Clinic/ScheduleForDoctors ='1'"><xsl:attribute name="checked"/></xsl:if>
								</input>
							</div>
						</td>
					</tr>
					<tr>
						<td>Показывать для Yandex API:</td>	
						<td>
							<div class="checkbox" style="margin-left: 0px"> 
								<input type="checkbox" name="yaAPI_Show" id="yaAPI_Show" value="yes">
									<xsl:if test="dbInfo/Clinic/YaAPI ='yes'"><xsl:attribute name="checked"/></xsl:if>
								</input>
							</div>
						</td>
					</tr>
					<tr>
						<td>Показывать биллинг в ЛК:</td>	
						<td>
							<div class="checkbox" style="margin-left: 0px"> 
								<input type="checkbox" name="showBilling" id="showBilling" value="show">
									<xsl:if test="dbInfo/Clinic/Settings/showBilling ='show'"><xsl:attribute name="checked"/></xsl:if>
								</input>
							</div>
						</td>
					</tr>
					<tr>
						<td>Показывать в объявлениях:</td>
						<td>
							<div class="checkbox" style="margin-left: 0px">
								<input type="checkbox" name="showInAdvertising" id="showInAdvertising" value="1">
									<xsl:if test="dbInfo/Clinic/ShowInAdvertising ='1'"><xsl:attribute name="checked"/></xsl:if>
								</input>
							</div>
						</td>
					</tr>
					<tr>
						<td>Cкидка на онлайн-запись по диагностике:</td>
						<td>
							<input name="discountOnlineDiag" value="{dbInfo/Clinic/DiscountOnlineDiag}" style="width: 20px" maxlength="2"/>
							<span style="margin: 0 5px 0 5px">(Показывается, если скидка больше 0)</span>
						</td>
					</tr>
					<tr>
						<td>Телефон для уведомлений:</td>
						<td>
							<div id="notifyPhonesList" style="margin: 0">
								<xsl:choose>
									<xsl:when test="dbInfo/Clinic/NotifyPhones">
										<script>
											var notify_phones_number_pos = <xsl:value-of select="count(dbInfo/Clinic/NotifyPhones/Element)"/>
										</script>
										<xsl:for-each select="dbInfo/Clinic/NotifyPhones/Element">
											<div style="margin:0" id="notify_phone_line_{position()}">
												<input name="notify_phones[{position()}]" id="notify_phones_{position()}" value="{.}" class="inputForm" style="width: 250px;"/>
												<xsl:if test="position() = 1"><span class="link" style="margin-left: 10px" onclick="addNotifyPhone()">добавить телефон</span></xsl:if>
												<xsl:if test="position() &gt; 1"><span class="link" style="margin-left: 10px" onclick="delNotifyPhone('{position()}')">удалить</span></xsl:if>
											</div>
											<xsl:if test="position() != last()"><div class="null" style="height: 5px"/></xsl:if>
										</xsl:for-each>
									</xsl:when>
									<xsl:otherwise>
										<script>
											var notify_phones_number_pos = 1;
										</script>
										<div style="margin: 0" id="notify_phone_line_1">
											<input name="notify_phones[1]" id="notify_phones_1" value="" class="inputForm" style="width: 250px;"/>
											<span class="link" style="margin-left: 10px" onclick="addNotifyPhone()">добавить телефон</span>
										</div>
									</xsl:otherwise>

								</xsl:choose>
							</div>
						</td>
					</tr>
					<tr>
						<td>Email для уведомлений:</td>
						<td>
							<div id="notifyEmailsList" style="margin: 0">
								<xsl:choose>
									<xsl:when test="dbInfo/Clinic/NotifyEmails">
										<script>
											var notify_emails_number_pos = <xsl:value-of select="count(dbInfo/Clinic/NotifyEmails/Element)"/>
										</script>
										<xsl:for-each select="dbInfo/Clinic/NotifyEmails/Element">
											<div style="margin:0" id="email_line_{position()}">
												<input name="notify_emails[{position()}]" id="notify_emails_{position()}" value="{.}" class="inputForm" style="width: 250px;"/>
												<xsl:if test="position() = 1"><span class="link" style="margin-left: 10px" onclick="addNotifyEmail()">добавить email</span></xsl:if>
												<xsl:if test="position() &gt; 1"><span class="link" style="margin-left: 10px" onclick="delNotifyEmail('{position()}')">удалить</span></xsl:if>
											</div>
											<xsl:if test="position() != last()"><div class="null" style="height: 5px"/></xsl:if>
										</xsl:for-each>
									</xsl:when>
									<xsl:otherwise>
										<script>
											var notify_emails_number_pos = 1;
										</script>
										<div style="margin: 0" id="email_line_1">
											<input name="notify_emails[1]" id="notify_emails_1" value="" class="inputForm" style="width: 250px;"/>
											<span class="link" style="margin-left: 10px" onclick="addNotifyEmail()">добавить email</span>
										</div>
									</xsl:otherwise>

								</xsl:choose>
							</div>
						</td>
					</tr>
					<tr>
						<td>Контракт по врачам:</td>	
						<td>
							<div class="checkbox" style="margin-left: 0px"> 
								<select name="contractId"  style="width: 305px">
									<xsl:for-each select="/root/dbInfo/ContractDict/Element[IsClinic = 'yes']">
										<option value="{@id}">
											<xsl:if test="@id = /root/dbInfo/Clinic/Settings/contractId"><xsl:attribute name="selected"/></xsl:if>
											<xsl:value-of select="Name"/>
										</option>
									</xsl:for-each>
								</select>
							</div>
						</td>
					</tr>
					<tr>
						<td>Контракт по диагностике:</td>	
						<td>
							<div class="checkbox" style="margin-left: 0px">
								<input name="isDiagnostic" type="hidden" value ="{dbInfo/Clinic/IsDiagnostic}"/>
								<select name="diagContractId"  style="width: 305px">
									<option value="0">
										--- Выберите тип контракта ---
									</option>
									<xsl:for-each select="/root/dbInfo/ContractDict/Element[IsDiagnostic = 'yes']">
										<option value="{@id}">
											<xsl:if test="@id = /root/dbInfo/Clinic/DiagSettings/contractId"><xsl:attribute name="selected"/></xsl:if>
											<xsl:value-of select="Name"/>
										</option>
									</xsl:for-each>
								</select>
							</div>
						</td>
					</tr>
					<tr>
						<td>Персональный контракт по врачам:</td>	
						<td>
							<div class="checkbox" style="margin-left: 0px"> 
								<table>
									<tr>
										<td nowrap="">Пластический хирург</td>
										<td nowrap="">
											<input name="price[1]" value="{/root/dbInfo/Clinic/Settings/price1}"  style="width: 60px; text-align: right"/>
											руб. / приём
										</td>
									</tr>
									<tr>
										<td>Стоматолог</td>
										<td nowrap="">
											<input name="price[2]" value="{/root/dbInfo/Clinic/Settings/price2}" style="width: 60px; text-align: right"/>
											руб. / приём
										</td>
									</tr>
									<tr>
										<td nowrap="">Все остальные специалисты</td>
										<td nowrap="">
											<input name="price[3]" value="{/root/dbInfo/Clinic/Settings/price3}" style="width: 60px; text-align: right"/>
											руб. / приём
										</td>
									</tr>
								</table>
							</div>
						</td>
					</tr>        
					<tr>
						<td>Расписание работы:</td>	
						<td>
							<table>
								<col width="70"/>
								<col width="70"/>
								<tr>
									<th></th>
									<th>будни</th>
									<th>пн</th>
									<th>вт</th>
									<th>ср</th>
									<th>чт</th>
									<th>пт</th>
									<th>сб</th>
									<th>вс</th>
								</tr>
								<tr>
									<th>с</th>
									<xsl:for-each select="dbInfo/WeekDays/Element">
										<td>
											<xsl:if test="@id = 0 or @id &gt;= 6"><xsl:attribute name="class">light</xsl:attribute></xsl:if>
											<xsl:if test="@id = 0"><xsl:attribute name="align">center</xsl:attribute></xsl:if>
											<xsl:variable name="dayId" select="@id"/>
											<input name="wkDay[{@id}]" value="{@id}" type ="hidden"/>
											<input name="wkDay_From[{@id}]" class="scheduleInput" maxlength="5" value="{/root/dbInfo/Clinic/Schedule/Element[@id = $dayId]/StartTime}" onfocus="this.select()"/>
										</td>
									</xsl:for-each>
								</tr>
								<tr>
									<th>по</th>
									<xsl:for-each select="dbInfo/WeekDays/Element">
										<td>
											<xsl:if test="@id = 0 or @id &gt;= 6"><xsl:attribute name="class">light</xsl:attribute></xsl:if>
											<xsl:if test="@id = 0"><xsl:attribute name="align">center</xsl:attribute></xsl:if>
											<xsl:variable name="dayId" select="@id"/>
											<input name="wkDay_Till[{@id}]" class="scheduleInput" maxlength="5" value="{/root/dbInfo/Clinic/Schedule/Element[@id = $dayId]/EndTime}" onfocus="this.select()"/>
										</td>
									</xsl:for-each>
								</tr>
							</table>
						</td>
					</tr>
					<tr><td colspan="2"><div class="null"/></td></tr>        
					<tr>
						<td>Внутренний рейтинг:</td>	
						<td>
							<table>
								<col width="160"/>
								<xsl:for-each select="dbInfo/RatingDict/Element">
									<tr>
										<td>
											<xsl:value-of select="Title"/>
										</td>
										<td>
											<select name="rating[{@id}]"  style="width: 290px">
												<xsl:variable name="idLine" select="@id"/>
												<xsl:for-each select="Type">
													<option value="{@weight}">
														<xsl:if test="@weight = format-number(/root/dbInfo/Clinic/Rating/Rating[(number(@id)+1) = number($idLine)]/@value, '0.0')"><xsl:attribute name="selected"/></xsl:if>
														<xsl:value-of select="."/>
														(<xsl:value-of select="@weight"/>)
													</option>
												</xsl:for-each>
											</select>
										</td>
									</tr>
								</xsl:for-each>
									<tr>
										<td>Суммарный рейтинг</td>
										<td><strong><xsl:value-of select="/root/dbInfo/Clinic/Rating/Rating[@id='total']/@value"/></strong></td>
									</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td>Email для сверки:</td>
						<td>
							<input name="email_reconciliation" id="email_reconciliation" value="{dbInfo/Clinic/EmailReconciliation}" class="inputForm" style="width: 250px;"/>
						</td>
					</tr>
					<tr>
						<td>Менеджер для клиники</td>
						<td>
							<xsl:copy-of select="dbInfo/SelectManager" />
						</td>
					</tr>
				</table>
			</form>
			
		
			<div style="position:relative; margin: 20px 10px 30px 0;">		  
				<div class="form" style="width:100px; float:right; margin-left: 10px" onclick="(modalWinKey === 'close') ? $('#modalWin').hide() : window.location.reload()">ЗАКРЫТЬ</div>
				<div class="form" style="width:100px; float:right;" onclick="saveContent('#editSettingsForm', '/clinic/service/saveSettingsData.htm', '5')">СОХРАНИТЬ</div>
			</div>
		</div>
	</xsl:template>
	
	
	
	
	<xsl:template name="ceilInfo">	  
		<xsl:param name="id"/>
		
		<style>
			.liList	{margin: 0 0 1px 0; padding: 0; list-style: none; }
		</style>
		<div id="ceilWin_{$id}" class="infoElt hd" style="width: 320px">
				<xsl:for-each select="dbInfo/DiagnosticList/Element[not(@id = /root/dbInfo/Clinic/Diagnostics/Element/@id)]">
					<div style="width: 325px; height: 18px; margin: 0 0 5px 0; text-align: left;" onclick="$('#subList_{@id}').toggle()">
						<xsl:if test="count(DiagnosticList/Element) = 0">
							<div style="float:left; width: 15px; margin: 0 10px 0 0; padding: 0 0 2px 0">
								<input name="diagnostica[{@id}]" id="diagnostica_{@id}" type="Checkbox" value="{@id}" style="margin:0;"/>
							</div>
						</xsl:if>
						<div style="float:left; margin:0; width: 300px; padding: 2px 0 0 0">
							<xsl:if test="count(DiagnosticList/Element) = 0">
								<xsl:attribute name="onclick">checkLine('#diagnostica_<xsl:value-of select="@id"/>');</xsl:attribute>
							</xsl:if>
							<span style="cursor:pointer">
								<xsl:if test="count(DiagnosticList/Element[not(@id = /root/dbInfo/Clinic/Diagnostics/Element/@id)]) &gt; 0">
									<xsl:attribute name="class">link</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="Name"/> 
								<xsl:if test="count(DiagnosticList/Element) &gt; 0">
									(<xsl:value-of select="count(DiagnosticList/Element[not(@id = /root/dbInfo/Clinic/Diagnostics/Element/@id)])"/>)
								</xsl:if>
							</span>
						</div>
					</div>
					<xsl:if test="DiagnosticList/Element">
						<div id="subList_{@id}" style="position: reletive; margin: 0 0 0 20px; padding: 0; width: 305px; text-align: left;" class="hd">
							<xsl:for-each select="DiagnosticList/Element[not(@id = /root/dbInfo/Clinic/Diagnostics/Element/@id)]">
								<div style="width: 100%">
									<div style="float:left; width: 15px; margin: 0 10px 0 0; padding: 0 0 2px 0">
										<input name="diagnostica[{@id}]" id="diagnostica_{@id}" type="Checkbox" value="{@id}" style="margin:0"/>
									</div>
									<div style="float:left; margin:0; width: 280px; padding: 2px 0 0 0" onclick="checkLine('#diagnostica_{@id}')">
										<span style="cursor:pointer"><xsl:value-of select="Name"/></span>
									</div>
								</div>
								<div class="clear"/>
							</xsl:for-each>
						</div> 
					</xsl:if>

				</xsl:for-each>
			<img src="/img/common/clBt.gif" width="15" height="14"  alt="закрыть" style="position: absolute; cursor: pointer; right: 4px; top: 4px;" title="закрыть" onclick="$('#ceilWin_{$id}').hide();" border="0"/>
			<div style="position:relative; margin: 20px 10px 30px 0;">		  
				<div class="form" style="width:100px; float:right; margin-left: 10px" onclick="$('#ceilWin_{$id}').hide();">ЗАКРЫТЬ</div>
				<div class="form" style="width:100px; float:right;" onclick="saveContent('#editAddDiagnosticForm', '/clinic/service/editAddDiagnosticaData.htm', '4')">СОХРАНИТЬ</div>
			</div>
		</div>
		
		
	</xsl:template>

	<xsl:template name="diagnostics">
		<xsl:param name="id"/>

		<style>
			#ceilWin_<xsl:value-of select="$id"/> input	{height: auto;}
			#ceilWin_<xsl:value-of select="$id"/> .mb10	{margin-bottom: 10px !important;}
			#ceilWin_<xsl:value-of select="$id"/> .mb5	{margin-bottom: 5px !important;}
		</style>
		<div id="ceilWin_{$id}" class="m0 shd infoEltR hd" style="width: 450px;">
			<div class="mb5 checkBox4Text">
				<label>
					<input class="checkBox4Text" name="diagnostica[0]" id="diagnostica_0" type="Checkbox" value="0" autocomplete="off" txt="Все типы" onclick="$('#ceilWin_multy :input').attr('checked',false); $(this).attr('checked',true)">
						<xsl:if test="/root/srvInfo/DiagnosticaList = '' or /root/srvInfo/DiagnosticaList = '0'  or not(/root/srvInfo/FilterParams/Diagnostica/ElementId)"><xsl:attribute name="checked"/></xsl:if>
					</input>
					Все типы
				</label>
			</div>


			<xsl:for-each select="dbInfo/DiagnosticList/Element[not(@id = /root/dbInfo/Clinic/Diagnostics/Element/@id)]">
				<div class="mb10 checkBox4Text">
					<xsl:choose>
						<xsl:when test="count(DiagnosticList/Element) = 0">
							<label>
								<input class="checkBox4Text" name="diagnostica[{@id}]" type="Checkbox" value="{@id}" autocomplete="off" txt="{Name}" onchange="setFilterListToNull()">
									<xsl:if test="/root/srvInfo/FilterParams/Diagnostica/ElementId = @id">
										<xsl:attribute name="checked"/>
									</xsl:if>
								</input>
								<xsl:value-of select="Name"/>
							</label>
						</xsl:when>
						<xsl:otherwise>
							<span class="pnt link" onclick="$('#subList_{@id}').toggle()">
								<xsl:value-of select="Name"/>
								(<xsl:value-of select="count(DiagnosticList/Element)"/>)
							</span>
						</xsl:otherwise>
					</xsl:choose>
				</div>
				<xsl:if test="DiagnosticList/Element">
					<div id="subList_{@id}" class="hd ml20">
						<xsl:for-each select="DiagnosticList/Element">
							<div class="mb5 checkBox4Text">
								<label>
									<input class="checkBox4Text" name="diagnostica[{@id}]" type="Checkbox" value="{@id}" autocomplete="off" txt="{../../Name} {Name}" onchange="setFilterListToNull()">
										<xsl:if test="/root/srvInfo/FilterParams/Diagnostica/ElementId = @id">
											<xsl:attribute name="checked"/>
										</xsl:if>
									</input>
									<xsl:value-of select="Name"/>
								</label>
							</div>
						</xsl:for-each>
					</div>
				</xsl:if>
			</xsl:for-each>

			<div class="closeButton4Window" title="закрыть" onclick="$('#ceilWin_{$id}').hide(); setFilterLine()"/>
		</div>
	</xsl:template>

	<xsl:template name="gallery">
		<div class="actionList">
			<span style="color:#cc0000; font-weight: bold; cursor: pointer" id="addPhotoLink" onclick="loadPhotos({dbInfo/Clinic/@id});">Добавить фотографию</span>
		</div>
		<ul id="ClinicPhotoList">
			<xsl:for-each select="dbInfo/Clinic/Photos/Element">
				<li style="float: left; margin: 0 20px 20px 0; height: 180px; width: 160px; text-align: right;" id="ClinicPhoto_{@id}" data-ImgId="{@id}">
					<img src="{Url}" style="width: 160px; max-height: 160px; margin-bottom: 7px;"/>
					<br/>
					<p><a href="#" onclick="deletePhoto({@id});" style="color:#cc0000;">удалить</a></p>
				</li>
			</xsl:for-each>

			<li style="float: left; margin: 0 20px 20px 0; height: 180px; width: 160px; text-align: right; display: none;" id="ClinicPhotoTemlate">
				<img src="" style="width: 160px; max-height: 160px; margin-bottom: 7px;"/>
				<br/>
				<p><a href="#" onclick="deletePhoto($(this).closest('li').data('ImgId'));" style="color:#cc0000;">удалить</a></p>
			</li>
		</ul>
		<div class="clear"></div>
	</xsl:template>

</xsl:transform>

