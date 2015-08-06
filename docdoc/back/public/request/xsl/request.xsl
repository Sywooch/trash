<?xml version="1.0" encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:import href="../../lib/xsl/modalWindow.xsl"/>
	<xsl:import href="../../lib/xsl/common.xsl"/>
	<xsl:import href="index.xsl"/>
	<xsl:import href="metro_mos.xsl"/>
	<xsl:import href="metro_spb.xsl"/>
	<xsl:import href="searchItem.xsl" />

	<xsl:output method="html" encoding="utf-8"/>

	<xsl:key name="type" match="/root/dbInfo/TypeDict/Element" use="@id"/>
	<xsl:key name="kind" match="/root/dbInfo/KindList/Element" use="Id"/>
	<xsl:key name="sourceType" match="/root/dbInfo/SourceTypeDict/Element" use="@id"/>
	<xsl:key name="status" match="/root/dbInfo/StatusDict[@mode = 'requestDict']/Element" use="@id"/>
	<xsl:key name="action" match="/root/dbInfo/ActionDict/Element" use="@id"/>
	<xsl:key name="lkStatus" match="/root/dbInfo/StatusDict[@mode = 'LKrequestDict']/Element" use="@id"/>
	<xsl:key name="diagnosticaList" match="/root/dbInfo/DiagnosticList/descendant-or-self::Element" use="@id"/>
	

	<xsl:template match="/">
		<link type="text/css" href="/css/jquery-ui-1.7.2.custom.css" rel="stylesheet" />
		<link type="text/css" href="/css/jquery.autocomplete.css" rel="stylesheet" media="screen"/>
		<link type="text/css" href="/css/jquery.jscrollpane.css" rel="stylesheet" media="screen"/>
		<link type="text/css" href="/css/popup.css" rel="stylesheet" media="screen"/>
		<link type="text/css" href="/css/callCenterSpetial.css" rel="stylesheet" media="screen"/>
		<link type="text/css" href="/css/metromap.css" rel="stylesheet" />
		<link type="text/css" href="/css/fileuploader.css" rel="stylesheet" media="screen"/>
		<link type="text/css" href="/css/schedule.css" rel="stylesheet" media="screen"/>

		<script>
			<xsl:choose>
				<xsl:when test="/root/dbInfo/Queue4User">var Queue4User = true;</xsl:when>
				<xsl:otherwise>var Queue4User = false;</xsl:otherwise>
			</xsl:choose>
		</script>
		<xsl:apply-templates select="root"/>
		
		<div style="position: absolute; margin: 0; top:0; left:0; z-index: 0;">
			<xsl:call-template name="modalWindow">
				<xsl:with-param name="id" select="'modalWin'"/>
				<xsl:with-param name="title" select="'Отзыв'"/>
				<xsl:with-param name="width" select="'700'"/>
			</xsl:call-template>
		</div>
		
		<script src="/lib/js/jquery-ui-1.7.2.custom.min.js" type="text/javascript" ></script>
		<script src='/lib/js/jquery.autocomplete.js' type='text/javascript' language="JavaScript"></script>
		<script src='/lib/js/jquery.mousewheel-3.0.js' type='text/javascript' language="JavaScript"></script>
		<script src='/lib/js/jquery.jscrollpane.js' type='text/javascript' language="JavaScript"></script>
		<script src="/lib/js/ui.datepicker-ru.js" type="text/javascript" ></script>
		<script src="/lib/js/jquery.maskedinput-1.3.min.js" type="text/javascript"></script>
		<script src="/lib/js/fileuploader.js" type="text/javascript"></script>
		<script src="/lib/js/popup.js" type="text/javascript"></script>
		<script src="js/request.js" type="text/javascript"></script>
		<script src="/lib/js/multiple_checkbox.js" type="text/javascript"></script>

		<script>
			var winDeltaY = 150;
			var winDeltaX = 500;



			$(document).ready(function() {
				initAudioPayer();

				$("#shMetro").autocomplete("/clinic/service/getMetroList.htm",{
					delay:10,
					minChars:1,
					max:20,
					autoFill:true,
					multiple: true,
					selectOnly:true,
					extraParams: { cityId: <xsl:choose><xsl:when test="/root/dbInfo/Request/CityId != ''"><xsl:value-of select="/root/dbInfo/Request/CityId"/></xsl:when><xsl:otherwise><xsl:value-of select="/root/srvInfo/City/@id"/></xsl:otherwise> </xsl:choose>  },
					formatResult: function(row) {
						return row[0];
					},
					formatItem: function(row, i, max) {
						return row[0];
					}
				}).result(function(event, item) {
					getItemResultset();
				});


				$("#clinicName").autocomplete("/service/getClinicList.htm",{
					delay:10,
					minChars:2,
					max:20,
					autoFill:true,
					multiple: false,
					selectOnly:true,
                    matchContains: true,
					extraParams: { cityId: <xsl:choose><xsl:when test="/root/dbInfo/Request/CityId != ''"><xsl:value-of select="/root/dbInfo/Request/CityId"/></xsl:when><xsl:otherwise><xsl:value-of select="/root/srvInfo/City/@id"/></xsl:otherwise> </xsl:choose>  },
					formatResult: function(row) {
						return row[0];
					},
					formatItem: function(row, i, max) {
						return row[0];
					}
				}).result(function(event, item) {
					$("#clinicId").val(item[1]);
				});


				$(function(){
					$.datepicker.setDefaults($.extend($.datepicker.regional["ru"]));
					$("#recallDate").datepicker( {
						changeMonth : true,
						changeYear: true,
						duration : "fast",
						maxDate : "+1y",
						showButtonPanel: true
					});
					$(".apointmentDate").datepicker( {
						changeMonth : true,
						changeYear: true,
						duration : "fast",
						maxDate : "+1y",
						showButtonPanel: true
					});
					$("#recordDate").datepicker( {
						changeMonth : true,
						changeYear: true,
						duration : "fast",
						maxDate : "+1y",
						showButtonPanel: true
					});
					$("#doctorWorkDate").datepicker( {
						changeMonth : true,
						changeYear: true,
						duration : "fast",
						maxDate : "+1y",
						showButtonPanel: true,
						minDate: 0,
						onSelect: function(date) {
							getItemResultset();
						},
					});
				});

		        getHistory();

		        jQuery(function($){
				   $(".phone-format").mask("+7 (999) 999-99-99",{placeholder:" "});
				});

			});

			$("div.helper").mouseover( function() {
				$(this).stop(true).delay(300).children().show();
			});
			$("div.helper").mouseleave( function() {
				$(".helpEltR").hide();
			});

			function toggleTrue (elt) {
				(elt.attr("checked")) ? elt.attr("checked", false) : elt.attr("checked", true);
			}

			function setMetroFilter(LineList) {
				$("#shMetro").val(LineList);
				getItemResultset();
				clousePopup();
			}

			function nextTime(elt,nextElt) {
				if ( $(elt).length == 2 ) {
					nextElt.focus();
				}
			}

			function createUploader(requestId){
				var uploader = new qq.FileUploader({
                element: document.getElementById('file-uploader'),
                action: '/request/service/audioUpload.php?id='+requestId,
				allowedExtensions:  ['mp3','MP3'],
				sizeLimit: 50000000,
				multiple : false,
				onError: function (id, fileName, errorReason) {
					alert(errorReason);
				},
				onComplete: function(id, fileName, responseJSON){
					<![CDATA[
					if (responseJSON['success']) {
						reloadAudioList();
					} else {
						$('.qq-upload-list').html("<span class=\"red\">Случилась какая-то шняга.</span>");
					}
					]]>
				},
                debug: true
            });
			}
			
			
		</script>
		<xsl:choose>
			<xsl:when test="/root/dbInfo/Request/CityId and /root/dbInfo/Request/CityId = '2'">
				<script src="/lib/js/metro_spb.js" type="text/javascript"></script>
			</xsl:when>
			<xsl:otherwise>
				<script src="/lib/js/metromap.js" type="text/javascript"></script>
			</xsl:otherwise>
		</xsl:choose>
		
		<script type="text/javascript">
			$(document).ready(function() {
				var idMetroList = new Array;
				<xsl:for-each select="/root/dbInfo/Request/MetroList/Metro">idMetroList['<xsl:value-of select="position() - 1"/>'] = '<xsl:value-of select="@id"/>';</xsl:for-each>
				initMetroMap (idMetroList);
			});
		</script>
	</xsl:template>




	<xsl:template match="root">
		<style>
			.rasporDiv		{width: 1%; min-width:10px; float: left; }
			#topLineBlock	{width: 100%; min-width: 1150px; min-height: 352px;}
			.topBlock		{min-width: 280px; height: 350px; margin: 0 5px 0 5px; overflow: hidden;}
			
			#middleLineBlock{width: 100%; min-width: 1150px; min-height: 420px; margin: 20px 1px 0 0;}
			

			#searchBlock	{height: 360px; sbackground-color: #cbd7dd; margin: 20px 0 0 0; }
				#searchDoctorFilter	{width: 30%; min-width: 250px; float: left;}
			
			#postBlock		{width: 100%; min-width: 1150px; min-height: 200px; sbackground-color: #cbd7dd; margin: 20px 0 0 0}
				#recordsList {width: 30%; min-width: 350px; float: left;}
				#historyList {margin: 0 0px 0 360px; min-width: 500px;height: 400px; }
				#historyBlock {margin:0; }
				#historyPane	{margin: 0; min-width: 500px; }
			#postBlockPanel	{background-color: #cbd7dd; margin: 20px 0 0 0; padding: 10px; min-width: 1130px;}
			#postBlockPanel h1	{margin: 0;}
			
			.scroll-pane-audio { height: 230px; width: 100%; overflow: auto; }
			.tdPadding	{padding: 0 0 5px 0}
		</style>
		
		<style>	  
			div.loader	{width:20px ; height: 18px; margin: 3px 0 0 10px; padding: 0px 0 0 0; float: left}
			div.load	{background: url('/img/common/indicator.gif'); width: 16px; height: 16px}
			span.err	{ color:#a00; font-weight:bold; margin-left:10px; }
			.inputForm	{ color:#353535; width:170px}
			.inputFormLong	{ color:#353535; width:570px; resize:vertical;}
			.inputForm textarea	{ color:#353535; width:255px; resize:vertical; }
			.inputFormReadOnly	 {width: 80px; color:#353535; background-color:#aaaaaa; text-align: right;}
			
			.errorMessage	{background-color: #ff9292; border-color: red; padding: 5px 2px 5px 0; margin: 10px 0 10px 0;}
			.successMessage	{background-color: #dbebd7; border-color: #125400; padding: 5px 2px 5px 0; margin: 10px 0 10px 0;}

			.closest-stations {float: left; width: 200px; margin-top: 5px;}
			.closest-stations li span {font-style:italic; color: #999;}
		</style>
		
		
		
		<div id="main">
			<form name="requestEditForm" id="requestEditForm" method="post" action="">
			<xsl:call-template name="quickSearch"/>

			<div style="height: 30px; padding: 0 2px 0 0;">
				<div id="topButton" class="form">СОХРАНИТЬ</div>
			</div>
			
			<div id="report" class="wb hd">
				<div style="margin:0; text-align: center;"></div>
			</div>
				
			<xsl:if test="errorInfo/Error">
				<div class="wb" style="background-color: #ff9292; border-color: red; padding: 5px 2px 5px 0; margin: 10px 0 10px 0; color: #ff0000">
					<div class="error" style="margin:0; text-align: center;">ВНИМАНИЕ!!! <xsl:value-of select="errorInfo/Error"/></div>
				</div>
			</xsl:if>
			
			
			<div id="topLineBlock">
				<div style="width: 50%; float: left;">
					<div style="width: 50%; float: left;">
						<div class="topBlock wb" style="margin-left: 0; overflow: visible !important;">
							<xsl:call-template name="requestData">
								<xsl:with-param name="context" select="dbInfo/Request"/>
							</xsl:call-template>
						</div>
					</div>
					<div style="width: 50%; float: right;">
						<div class="topBlock wb">
							<xsl:call-template name="clientData">
								<xsl:with-param name="context" select="dbInfo/Request"/>
							</xsl:call-template>
						</div>
					</div>
				</div>
				<div style="width: 50%; float: right">
					<div style="width: 50%; float: left">
						<div class="topBlock wb">
							<xsl:choose>
								<xsl:when test="/root/dbInfo/DisableRequest = 1">
									<xsl:call-template name="requestActionDisable">
										<xsl:with-param name="context" select="dbInfo/Request"/>
									</xsl:call-template>
								</xsl:when>
								<xsl:otherwise>
									<xsl:call-template name="requestAction">
										<xsl:with-param name="context" select="dbInfo/Request"/>
									</xsl:call-template>
								</xsl:otherwise>
							</xsl:choose>

						</div>
					</div>
					<div style="width: 50%; float: right">
						<div id="audioBlock" class="topBlock wb" style="margin-right: 0; sbackground: url('/img/womenBG3.jpg') no-repeat right">
							<xsl:call-template name="audioData">
								<xsl:with-param name="context" select="dbInfo/Request"/>
							</xsl:call-template>
						</div>
					</div>
				</div>
			</div>

			<div id="middleLineBlock" class="wb">

				<xsl:call-template name="searchItem">
					<xsl:with-param name="context" select="dbInfo/Request"/>
				</xsl:call-template>

			</div>
			
			<div id="bottomLineBlock">
				<div>
					<div id="postBlock" class="wb hd">
						<xsl:call-template name="postWork">
							<xsl:with-param name="context" select="dbInfo/Request"/>
						</xsl:call-template>
					</div>
					<div id="postBlockPanel" class="wb">
						<h1>История действий <span style="font-size: 12px; margin-left: 125px" class="link" onclick="$('#postBlock').show();$('#postBlockPanel').hide()">развернуть</span></h1>
					</div>
				</div>
			</div>
			</form>
		</div>
	</xsl:template>
	
	
	
	
	
	
	
	
	<xsl:template name="requestData">
		<xsl:param name="context" select="dbInfo/Request"/>

		<div style="margin:10px">
			<h1>
				Заявка № <xsl:value-of select="$context/@id"/>
				[ <a href="#" class="duplicate-link" data-href="/2.0/request/duplicate/{$context/@id}?type={/root/srvInfo/TypeView}">Дублировать</a> ]
			</h1>
			<input type="hidden" name="requestId" id="requestId" value="{$context/@id}"/>
			<input type="hidden" name="typeView" id="typeView" value="{/root/srvInfo/TypeView}"/>

			<table border="0" width="100%">
				<tr>
					<td>Дата создания:</td>
					<td>
						<xsl:value-of select="$context/CrDate"/>
						&#160;
						<xsl:value-of select="$context/CrTime"/>
					</td>
				</tr>
				<tr>
					<td valign="top" style="padding-top: 5px">Источник / Вид:</td>
					<td nowrap="">
						<div class="i-status req_source i-st-{$context/SourceType}" style="float: left; height: 16px; padding: 4px 0 0 24px; margin:0 10px 0 0; width: auto; ">
							<xsl:value-of select="key('sourceType',$context/SourceType)/."/>
						</div>
						<div class="i-status req_kind i-st-{$context/Kind}" style="float: left; height: 20px; padding: 4px 0 0 24px; margin:0 10px 0 0; width: auto; ">
							<xsl:value-of select="key('kind',$context/Kind)/Name"/>
							<span class="link change-field" style="margin-left: 10px">изменить</span>
						</div>
						<div id="kind-selector" style="display: none;">
							<input id="kind" name="kind" value="{$context/Kind}" type="hidden" autocomplete="off"/>
							<select style="width: 150px">
								<xsl:for-each select="dbInfo/KindList/Element">
									<option value="{Id}" style="background:url('/img/icon/req_kind_{Id}.png') no-repeat 4px 4px; padding: 4px 0 0 30px; margin: 2px 0 2px 0; height: 20px">
										<xsl:if test="/root/dbInfo/Request/Kind = Id">
											<xsl:attribute name="selected"/>
										</xsl:if>
										<xsl:value-of select="Name"/>
									</option>
								</xsl:for-each>
							</select>
						</div>
					</td>
				</tr>
				<xsl:if test="$context/Partner/@id != ''">
				<tr>
					<td>Партнёр:</td>
					<td><xsl:value-of select="$context/Partner"/></td>
				</tr>
				</xsl:if>
				<tr>
					<td valign="top" style="padding-top: 5px">Способ обращения:</td>
					<td nowrap="">
						<div class="i-status req_type i-st-{$context/Type}" style="float: left; height: 16px; padding: 4px 0 0 24px; margin:0 0 0 0px; width: auto; ">
							<xsl:value-of select="key('type',$context/Type)/."/>
						</div>
					</td>
				</tr>
				<tr>
					<td>Оператор:</td>
					<td>
						<xsl:choose>
							<xsl:when test="/root/srvInfo/UserData/Rights/Right = 'ADM' or /root/srvInfo/UserData/Rights/Right = 'SOP' ">
								<select name="owner" id="owner"  class="inputForm" style="width: 100%">
									<option value="0">-- Оператор не выбран</option>
									<xsl:for-each select="/root/dbInfo/OperatorList/Element[Status = 'enable']">
										<option value="{@id}">
											<xsl:choose>
												<xsl:when test="$context/Owner/@id">
													<xsl:if test="@id = $context/Owner/@id">
														<xsl:attribute name="selected"/>
												    </xsl:if>
												</xsl:when>
												<xsl:otherwise>
													<xsl:if test="@id = /root/srvInfo/UserData/@id">
														<xsl:attribute name="selected"/>
												    </xsl:if>
												</xsl:otherwise>
											</xsl:choose>

										    <xsl:value-of select="LName"/>&#160;<xsl:value-of select="FName"/>
										</option>
									</xsl:for-each>
								</select>
							</xsl:when>
							<xsl:otherwise>
								<xsl:choose>
									<xsl:when test="$context/Owner/@id">
										<input type="hidden" value="{$context/Owner/@id}" name="owner"/>
										<xsl:value-of select="$context/Owner"/>
									</xsl:when>
									<xsl:otherwise>
										<xsl:value-of select="/root/srvInfo/UserData/LastName"/>
										&#160;
										<xsl:value-of select="/root/srvInfo/UserData/FirstName"/>
									</xsl:otherwise>
								</xsl:choose>

							</xsl:otherwise>
						</xsl:choose>
					</td>
				</tr>
				<tr>
					<td>Статус:</td>
					<td>
						<xsl:choose>
							<xsl:when test="/root/srvInfo/UserData/Rights/Right = 'ADM' or /root/srvInfo/UserData/Rights/Right = 'SOP' ">
								<div id="ownerInput">
									<xsl:if test="$context/Status and $context/Status != ''">
										<img src="/img/icon/req_status_16_{$context/Status}.png" style="margin: 0 5px 0 0" align="absbottom"/>
									</xsl:if>
									<xsl:value-of select="key('status',$context/Status)/."/>
									<span class="link" style="margin-left: 10px" onclick="chStatusSelector()">изменить</span>
									<input type="hidden" value="{$context/Status}" name="status"/>
								</div>
								<div id="ownerSelector" class="hd">
									<input id="chManual" name="chManual" value="0" type="hidden"  autocomplete="off"/>
									<select name="statusSel" id="statusSel" class="inputForm" style="width: 100%">
										<xsl:for-each select="dbInfo/StatusDict[@mode = 'requestDict']/Element">
											<option value="{@id}" style="background:url('/img/icon/req_status_{@id}.png') no-repeat; padding: 4px 0 0 30px; margin: 2px 0 2px 0; height: 24px">
											    <xsl:if test="@id = $context/Status">
													<xsl:attribute name="selected"/>
											    </xsl:if>
											    <xsl:value-of select="."/>
											</option>
										</xsl:for-each>
									</select>
								</div>
							</xsl:when>
							<xsl:otherwise>
								<xsl:if test="$context/Status and $context/Status != ''">
									<img src="/img/icon/req_status_16_{$context/Status}.png" style="margin: 0 5px 0 0" align="absbottom"/>
								</xsl:if>
								<xsl:value-of select="key('status',$context/Status)/."/>
								<input type="hidden" value="{$context/Status}" name="status"/>
							</xsl:otherwise>
						</xsl:choose>
					</td>
				</tr>
				<xsl:if test="contains('ADM CNM ACM SAL', /root/srvInfo/UserData/Rights/Right)">
					<tr>
						<td>Статус биллинга:</td>
						<td><xsl:value-of select="dbInfo/BillingStatusList/Element[@id = $context/BillingStatus]"/></td>
					</tr>
					<xsl:if test="$context/Partner/@id != ''">
					<tr>
						<td>Статус для партнёра:</td>
						<td><xsl:value-of select="dbInfo/PartnerStatusList/Element[@id = $context/PartnerStatus]"/></td>
					</tr>
					</xsl:if>
				</xsl:if>

				<tr><td colspan="2"><div class="null"/></td></tr>

				<tr class="kind-doctor">
					<xsl:if test="$context/Kind != '0'">
						<xsl:attribute name="style">display: none;</xsl:attribute>
					</xsl:if>
					<td valign="top">Врач:</td>
					<td>
						<xsl:value-of select="$context/Doctor"/>
						<input type="hidden" name="requestDoctorId" id="requestDoctorId" value="{$context/Doctor/@id}"/>
					</td>
				</tr>
				<tr class="kind-doctor">
					<xsl:if test="$context/Kind != '0'">
						<xsl:attribute name="style">display: none;</xsl:attribute>
					</xsl:if>
					<td valign="top">Специальность:</td>
					<td>
						<xsl:value-of select="$context/Sector"/>
					</td>
				</tr>

				<tr>
					<td valign="top">Клиника:</td>
					<td class="branches">
						<div id="companyInput">
							<xsl:if test="$context/Clinic/@id">
								<xsl:value-of select="$context/Clinic"/>
								<input id="chManualClinic" name="chManualClinic" value="0" type="hidden" autocomplete="off"/>
								<input name="clinicId" id="clinicId" type="hidden" value="{$context/Clinic/@id}" autocomplete="off"/>
								<xsl:if test="count($context/AnotherClinicList/Element) &gt; 1">
									<span class="link" style="margin-left: 10px" onclick="chCompanySelector()">изменить</span>
								</xsl:if>
								<xsl:if test="$context/AnotherClinicList/Element[@id= $context/Clinic/@id]/Address">
									<br/>
									<span class="em grey txt11" style="line-height: 16px"><xsl:value-of select="$context/AnotherClinicList/Element[@id= $context/Clinic/@id]/Address"/></span>
								</xsl:if>
							</xsl:if>
						</div>
						<div id="companySelector" class="hd">
							<select name="clinicName" id="clinicName" class="inputForm" style="width: 100%" onchange="$('#clinicId').val(this.value); $('#chManualClinic').val(1); $('#clinicName').next().text($('#clinicName option:selected').attr('address'));">
								<xsl:for-each select="$context/AnotherClinicList/Element">
									<option value="{@id}" address="{Address}">
										<xsl:if test="@id = $context/Clinic/@id">
											<xsl:attribute name="selected"/>
										</xsl:if>
										<xsl:value-of select="Clinic"/>
									</option>
								</xsl:for-each>
							</select>
							<em><xsl:if test="$context/AnotherClinicList/Element[@id= $context/Clinic/@id]/Address"><xsl:value-of select="$context/AnotherClinicList/Element[@id= $context/Clinic/@id]/Address"/></xsl:if></em>
						</div>
					</td>
				</tr>
				<tr>
					<td valign="top">Приём:</td>
					<td nowrap="">
						<xsl:value-of select="$context/AppointmentDate"/>
						&#160;
						<xsl:value-of select="$context/AppointmentTime"/>
						<xsl:if test="$context/AppointmentStatus = '1'">
							<span style="margin-left: 10px">состоялся</span>
						</xsl:if>
					</td>
				</tr>
			</table>
			<!-- <div style="height: 20px; margin: 0; position: reletive">
				<div id="statusLine" style="float:left; width: 200px; height: 20px; margin: 0;"></div>
				<div class="form" style="width: 100px; float: right; padding: 3px 10px 2px 10px; margin: 0;" onclick="requestAction('chStatus')">СОХРАНИТЬ</div>
			</div> -->
		</div>
	</xsl:template>
	
	
	
	
	<xsl:template name="clientData">
		<xsl:param name="context" select="dbInfo/Request"/>
			
		<div style="margin:10px;">	
			<h1>Клиент</h1>
			<table width="100%" border="0">
				<tr>
					<td>ФИО:</td>
					<td>
						<input name="clientName" id="clientName" value="{$context/Client/Name}" class="inputForm" style="width: 100%; min-width: 205px; text-transform: capitalize;"/>
						<input name="clientId" type="hidden" readonly="" value="{$context/Client/@id}" class="readOnly" style="width: 40px; margin-left: 10px; text-align: center" />
					</td>
				</tr>
				<tr>
					<td>Телефон:</td>
					<td nowrap="">
						<input name="phoneFrom" id="phoneFrom" value="{$context/ClientPhone/@phoneNum}" type="hidden"/>
						<input name="clientPhone" id="clientPhone" value="{$context/ClientPhone}" class="inputForm phone-format" style="width: 115px"/>
						<xsl:if test="/root/srvInfo/TypeView = 'call_center'">
							<span class="form callBtn" style="background-image:url('/img/icon/audio-headset.png'); background-repeat: no-repeat; background-position: 2px; width:100px; margin-left: 10px; padding-left: 24px">ЗВОНОК</span>
							<span class="callStatus"></span>
						</xsl:if>
					</td>
				</tr>
				<tr>
					<td>Контактный<br />телефон:</td>
					<td>
						<div id="addClientPhoneDiv" class="m0">
							<input name="addClientPhone" id="addClientPhone" value="{$context/AddClientPhone}" class="inputForm phone-format" style="width: 115px"/>
							<xsl:if test="/root/srvInfo/TypeView = 'call_center'">
								<span class="form callBtn" style="background-image:url('/img/icon/audio-headset.png'); background-repeat: no-repeat; background-position: 2px; width:100px; margin-left: 10px; padding-left: 24px">ЗВОНОК</span>
								<span class="callStatus"></span>
							</xsl:if>
						</div>
					</td>
				</tr>
				<tr><td colspan="2"><div class="null"/></td></tr>
				<xsl:if test="dbInfo/AnotherRequest/Element[CrDate = $context/CrDate]">
					<tr>
						<td>Заявки:</td>
						<td>
							<xsl:for-each select="dbInfo/AnotherRequest/Element[CrDate = $context/CrDate]">
								<a class="red" href="/request/request.htm?type={/root/srvInfo/TypeView}&amp;id={Id}" target="_blank"><xsl:value-of select="Id"/></a>
								<xsl:if test="position() != last()">, </xsl:if>
							</xsl:for-each>
						</td>
					</tr>
				</xsl:if>
				<tr>
					<td>Город:</td>
					<td nowrap="">
						<select name="clientCity" id="clientCity" class="inputForm" style="width: 100%; min-width: 205px;" autocomplete="off" onchange="saveRequest(true);">
							<xsl:for-each select="/root/srvInfo/CityList/Element">
								<option value="{Id}">
									<xsl:if test="(not($context/CityId) and /root/srvInfo/City/@id = Id) or $context/CityId = Id">
										<xsl:attribute name="selected"/>
									</xsl:if>
									<xsl:value-of select="Name" />
								</option>
							</xsl:for-each>
						</select>
					</td>
				</tr>
				<xsl:if test="/root/srvInfo/AddActions = '1'">
					<tr>
						<td>&#160;</td>
						<td>
							<div style="margin: 20px 0 0 0px;">
								<xsl:if test="dbInfo/OpinionList/Opinion">
									Изменить отзывы:
									<xsl:for-each select="dbInfo/OpinionList/Opinion">
										<span class="link" onclick="editOpinion('{@id}')"><xsl:value-of select="@id"/></span>
										<xsl:if test="position() != last()">, </xsl:if>
									</xsl:for-each>
									<br/><br/>
								</xsl:if>
								<span class="link" onclick="editOpinion('')">создать отзыв от клиента</span>
							</div>
						</td>
					</tr>
				</xsl:if>
				<xsl:if test="/root/dbInfo/Request/Status = '2'">
					<tr>
						<td>&#160;</td>
						<td>
							<div style="margin: 20px 0 0 0px;">
								<span class="link sendMessage act" data-action="clientNotAvailable">отправить СМС о недозвоне</span>
							</div>
						</td>
					</tr>
				</xsl:if>
			</table>
			
		</div>
	</xsl:template>

	<xsl:template name="requestAction">
		<xsl:param name="context" select="dbInfo/Request"/>
			
		<div style="margin:10px">
			<h1>
				Действия по заявке
				<xsl:if test="/root/srvInfo/UserData/OperatorStream != '0'">
					<a href="/2.0/request/backToStream/{$context/@id}?type={/root/srvInfo/TypeView}" class="link" style="margin-left: 20px;">
						<span class="form">Освободить заявку</span>
					</a>
				</xsl:if>
			</h1>

			<table width="100%">
				<xsl:if test="/root/srvInfo/AddActions = '1'">
					<tr>
						<td>Перезвонить:</td>
						<td>
							<div class="checkboxLab">
								<input name="recallDate" id="recallDate" style="width:70px" maxlength="12" value="{$context/CallLaterDate}" onchange="this.focus()"/>
								<input name="recallHour" id="recallHour" style="width:20px; margin-left: 8px" maxlength="2" value="{$context/CallLaterTime/@Hour}" onfocus="this.select()" onkeyup="if ($(this).val().length == 2) $('#recallMin').focus()"/>:<input name="recallMin" id="recallMin" style="width:20px" maxlength="2" value="{$context/CallLaterTime/@Min}" onfocus="this.select()"/>
								<div class="eraser" title="очистить поле" onclick="eraseRecallTime();"/>
							</div>
						</td>
					</tr>
				</xsl:if>
				<tr>
					<td style="width:120">Состояние:</td>
					<td>
						<xsl:if test="/root/srvInfo/AddActions = '1'">
							<div class="checkbox" style="margin: 0 0 0 10px; width: 10px; height: 10px; padding-top: 4px">
								<input type="checkbox" name="isTransfer" id="isTransfer" value="1">
									<xsl:if test="$context/IsTransfer = '1'"><xsl:attribute name="checked"/></xsl:if>
								</input>
							</div>
							<div class="checkboxLab" style="margin: 0 30px 0 0px; padding-top: 4px">
								<span class="link red" onclick="toggleCheckBox('#isTransfer')">переведён</span>
							</div>
						</xsl:if>

						<div class="checkbox" style="margin: 0; width: 10px; height: 10px; padding-top: 4px">
							<input type="checkbox" name="isRejection" id="isRejection" value="1" onclick="toggleRejectReasons()">
								<xsl:if test="$context/RejectReasonId != '0'">
									<xsl:attribute name="checked"/>
								</xsl:if>
							</input>
						</div>
						<div class="checkboxLab" style="margin-left: 0px; padding-top: 4px">
							<span class="link rejectLink"
							      onclick="toggleCheckBox('#isRejection');toggleRejectReasons()">отказ
							</span>
						</div>
					</td>
				</tr>
                <tr class="rejectReasons" style="display:none;">
                    <td>Причина отказа:</td>
                    <td>
                        <select id="rejectReason" name="rejectReason" class="inputForm" style="width: 100%; margin-top:5px;">
	                        <option value="0">--- Выберите причину ---</option>
                            <xsl:for-each select="/root/dbInfo/RejectReasons/Element">
                                <option value="{@id}">
                                    <xsl:if test="@id = /root/dbInfo/Request/RejectReasonId"><xsl:attribute name="selected"/></xsl:if>
                                    <xsl:value-of select="Name"/>
                                </option>
                            </xsl:for-each>
                        </select>
                    </td>
                </tr>
				<tr>
					<td nowrap="">Дата посещения:</td>
					<td>
                        <xsl:choose>
                            <xsl:when test="/root/dbInfo/Request/Booking">
                                <div>
                                    <xsl:choose>
                                        <xsl:when test="/root/dbInfo/Request/Booking/IsReserved = 1">
                                            Резерв на дату
                                        </xsl:when>
                                        <xsl:otherwise>
                                            Бронь на дату
                                        </xsl:otherwise>
                                    </xsl:choose>
                                     <xsl:value-of select="/root/dbInfo/Request/Booking/Slot/StartTime"/>

                                    <xsl:if test="/root/dbInfo/Request/Booking/IsReserved = 1">
                                        <input type="button" class="booking-confirm" value="Подтвердить" style="background-color:green;">
                                            <xsl:attribute name="data-booking"><xsl:value-of select="/root/dbInfo/Request/Booking/Id"/></xsl:attribute>
                                        </input>
                                    </xsl:if>

                                    <xsl:if test="/root/dbInfo/Request/Booking/CanCancel">
                                        <input type="button" class="booking-cancel" value="Отмена" style="background-color:red;">
                                            <xsl:attribute name="data-booking"><xsl:value-of select="/root/dbInfo/Request/Booking/Id"/></xsl:attribute>
                                        </input>
                                    </xsl:if>
                                </div>
                            </xsl:when>

                            <xsl:when test="/root/dbInfo/Request/ReservedSlot">
                                    Резерв на <xsl:value-of select="/root/dbInfo/Request/ReservedSlot/StartTime"/>
                                <input type="button" class="slot-reserve" data-method="cancel" value="Отмена">
                                    <xsl:attribute name="data-slot-id">
                                        <xsl:value-of select="/root/dbInfo/Request/ReservedSlot/Id"/>
                                    </xsl:attribute>
                                </input>

                                <input type="button" class="slot-reserve" data-method="confirm" value="Подтвердить">
                                    <xsl:attribute name="data-slot-id">
                                        <xsl:value-of select="/root/dbInfo/Request/ReservedSlot/Id"/>
                                    </xsl:attribute>
                                </input>
                            </xsl:when>

                            <xsl:otherwise>
                                <div class="checkboxLab">
                                    <input name="apointmentDate" id="apointmentDate" class="apointmentDate" style="width:70px" maxlength="12" value="{$context/AppointmentDate}" onchange="this.focus()"/>
                                    <input name="apointmentHour" id="apointmentHour" style="width:20px; margin-left: 8px" maxlength="2" value="{$context/AppointmentTime/@Hour}" onfocus="this.select()" onkeyup="if ($(this).val().length == 2) $('#apointmentMin').focus()"/>:<input name="apointmentMin" id="apointmentMin" style="width:20px" maxlength="2" value="{$context/AppointmentTime/@Min}" onfocus="this.select()"/>
                                    <!-- <span class="form" style="padding: 3px 10px 2px 10px; margin: 0 0 0px 10px;" onclick="requestAction('apointment')">ОК</span> -->
                                    <div class="eraser" title="очистить поле" onclick="$('#apointmentDate').val('');$('#apointmentHour').val('');$('#apointmentMin').val('')"/>
                                </div>
                            </xsl:otherwise>
                        </xsl:choose>
						
					</td>
				</tr>
				<xsl:if test="/root/srvInfo/AddActions = '1'">
					<tr>
						<td style="padding: 0 0 5px 0">Приём:</td>
						<td style="padding: 0 0 5px 0">
							<select name="appointmentStatus" id="appointmentStatus" style="width: 150px">
								<option value="">-</option>
								<option value="yes">
									<xsl:if test="$context/AppointmentStatus = '1'"><xsl:attribute name="selected"/></xsl:if>
									прием состоялся
								</option>
								<option value="no">
									<xsl:if test="$context/Status = '13'"><xsl:attribute name="selected"/></xsl:if>
									нет ответа
								</option>
							</select>

							<!-- <div id="appointmentStatusIndentificator" class="loader"></div> -->
						</td>
					</tr>
				</xsl:if>
				<tr>
					<td valign="top" colspan="2" style="padding-top: 10px">Комментарий к заявке:</td>
				</tr>
				<tr>
					<td colspan="2">
						<xsl:call-template name="commentTpl" />
					</td>
				</tr>
			</table>
		</div>
	</xsl:template>


	<xsl:template name="requestActionDisable">
		<xsl:param name="context" select="dbInfo/Request"/>

		<div style="margin:10px">
			<h1>Действия по заявке</h1>
			<table width="100%">
				<xsl:if test="/root/srvInfo/AddActions = '1'">
					<tr>
						<td>Перезвонить:</td>
						<td>
							<div class="checkboxLab">
								<input name="recallDate" id="recallDate" style="width:70px" maxlength="12" disabled="disabled" value="{$context/CallLaterDate}"/>
								<input name="recallHour" id="recallHour" style="width:20px; margin-left: 8px" maxlength="2"  disabled="disabled" value="{$context/CallLaterTime/@Hour}" />:<input name="recallMin" id="recallMin" style="width:20px" maxlength="2" value="{$context/CallLaterTime/@Min}" disabled="disabled" />
							</div>
						</td>
					</tr>
				</xsl:if>
				<tr>
					<td style="width:120">Состояние:</td>
					<td>
						<xsl:if test="/root/srvInfo/AddActions = '1'">
							<div class="checkbox" style="margin: 0 0 0 10px; width: 10px; height: 10px; padding-top: 4px">
								<input type="checkbox" name="isTransfer" id="isTransfer" value="1"  disabled="disabled">
									<xsl:if test="$context/IsTransfer = '1'"><xsl:attribute name="checked"/></xsl:if>
								</input>
							</div>
							<div class="checkboxLab" style="margin: 0 30px 0 0px; padding-top: 4px"  disabled="disabled">
								<span>переведён</span>
							</div>
						</xsl:if>

						<div class="checkbox" style="margin: 0; width: 10px; height: 10px; padding-top: 4px">
							<input type="checkbox" name="isRejection" id="isRejection" value="1"  disabled="disabled">
								<xsl:if test="$context/RejectReasonId != '0'">
									<xsl:attribute name="checked"/>
								</xsl:if>
							</input>
						</div>
						<div class="checkboxLab" style="margin-left: 0px; padding-top: 4px">
							<span>отказ</span>
						</div>
					</td>
				</tr>
				<tr class="rejectReasons" style="display:none;">
					<td>Причина отказа:</td>
					<td>
						<select id="rejectReason" name="rejectReason" class="inputForm" style="width: 100%; margin-top:5px;"  disabled="disabled">
							<option value="0">--- Выберите причину ---</option>
							<xsl:for-each select="/root/dbInfo/RejectReasons/Element">
								<option value="{@id}">
									<xsl:if test="@id = /root/dbInfo/Request/RejectReasonId"><xsl:attribute name="selected"/></xsl:if>
									<xsl:value-of select="Name"/>
								</option>
							</xsl:for-each>
						</select>
					</td>
				</tr>
				<tr>
					<td nowrap="">Дата посещения:</td>
					<td>
						<xsl:choose>
							<xsl:when test="/root/dbInfo/Request/Booking">
								<div>
									<xsl:choose>
										<xsl:when test="/root/dbInfo/Request/Booking/IsReserved = 1">
											Резерв на дату
										</xsl:when>
										<xsl:otherwise>
											Бронь на дату
										</xsl:otherwise>
									</xsl:choose>
									<xsl:value-of select="/root/dbInfo/Request/Booking/Slot/StartTime"/>

									<xsl:if test="/root/dbInfo/Request/Booking/IsReserved = 1">
										<input type="button" class="booking-confirm" value="Подтвердить" style="background-color:green;" >
											<xsl:attribute name="data-booking"><xsl:value-of select="/root/dbInfo/Request/Booking/Id"/></xsl:attribute>
										</input>
									</xsl:if>

									<xsl:if test="/root/dbInfo/Request/Booking/CanCancel">
										<input type="button" class="booking-cancel" value="Отмена" style="background-color:red;">
											<xsl:attribute name="data-booking"><xsl:value-of select="/root/dbInfo/Request/Booking/Id"/></xsl:attribute>
										</input>
									</xsl:if>
								</div>
							</xsl:when>

							<xsl:when test="/root/dbInfo/Request/ReservedSlot">
								Резерв на <xsl:value-of select="/root/dbInfo/Request/ReservedSlot/StartTime"/>
								<input type="button" class="slot-reserve" data-method="cancel" value="Отмена">
									<xsl:attribute name="data-slot-id">
										<xsl:value-of select="/root/dbInfo/Request/ReservedSlot/Id"/>
									</xsl:attribute>
								</input>

								<input type="button" class="slot-reserve" data-method="confirm" value="Подтвердить">
									<xsl:attribute name="data-slot-id">
										<xsl:value-of select="/root/dbInfo/Request/ReservedSlot/Id"/>
									</xsl:attribute>
								</input>
							</xsl:when>

							<xsl:otherwise>
								<div class="checkboxLab">
									<input name="apointmentDate" id="apointmentDate" style="width:70px" maxlength="12" value="{$context/AppointmentDate}"  disabled="disabled"/>
									<input name="apointmentHour" id="apointmentHour" style="width:20px; margin-left: 8px" maxlength="2" value="{$context/AppointmentTime/@Hour}"  disabled="disabled" />:<input name="apointmentMin" id="apointmentMin" style="width:20px" maxlength="2" value="{$context/AppointmentTime/@Min}"  disabled="disabled"/>
								</div>
							</xsl:otherwise>
						</xsl:choose>

					</td>
				</tr>
				<xsl:if test="/root/srvInfo/AddActions = '1'">
					<tr>
						<td style="padding: 0 0 5px 0">Приём:</td>
						<td style="padding: 0 0 5px 0">
							<select name="appointmentStatus" id="appointmentStatus" style="width: 150px"  disabled="disabled">
								<option value="">-</option>
								<option value="yes">
									<xsl:if test="$context/AppointmentStatus = '1'"><xsl:attribute name="selected"/></xsl:if>
									прием состоялся
								</option>
								<option value="no">
									<xsl:if test="$context/Status = '13'"><xsl:attribute name="selected"/></xsl:if>
									нет ответа
								</option>
							</select>

							<!-- <div id="appointmentStatusIndentificator" class="loader"></div> -->
						</td>
					</tr>
				</xsl:if>
				<tr>
					<td valign="top" colspan="2" style="padding-top: 10px">Комментарий к заявке:</td>
				</tr>
				<tr>
					<td colspan="2">
						<xsl:call-template name="commentTpl" />
					</td>
				</tr>
			</table>
		</div>
	</xsl:template>

	<xsl:template name="commentTpl">
		<xsl:param name="context" select="dbInfo/Request"/>
		<textarea name="requestComment" id="requestComment" class="inputForm" style="width: 100%; min-width:260px; height: 40px; resize:vertical; margin-top: 4px; padding: 2px" autocomplete="off"></textarea>
		<xsl:if test="count($context/CommentList/Element[Type = '2']) &gt;= 1">
			<div id="commentBlock" class="hd">
				<div class="wb shd" style="position:absolute; z-index: 20; width: 450px; background-color: white; padding: 10px 25px 10px 10px">
					<xsl:if test="$context/CommentList and count($context/CommentList/Element[Type=2]) &gt;= 2">
						<xsl:for-each select="$context/CommentList/Element[Type=2]">
							<div>
								<img src="/img/icon/req_comment_type_2.png" align="abstop" style="margin-right: 5px"/>
								<xsl:value-of select="Text"/>
							</div>
						</xsl:for-each>
						<img src="/img/common/clBt.gif" width="15" height="14"  alt="закрыть" title="закрыть" class="modWinCloseLocal" onclick="$('#commentBlock').hide()" border="0"/>
					</xsl:if>
				</div>
			</div>
			<div>
				<img src="/img/icon/req_comment_type_2.png" align="abstop" style="margin-right: 5px"/>
				<xsl:choose>
					<xsl:when test="string-length($context/CommentList/Element[Type = '2'][position() = 1]/Text) &gt; 100">
						<xsl:value-of select="substring($context/CommentList/Element[Type = '2'][position() = 1]/Text, 0, 100)"/> ...
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="$context/CommentList/Element[Type = '2'][position() = 1]/Text"/>
					</xsl:otherwise>
				</xsl:choose>


				<xsl:if test="$context/CommentList and count($context/CommentList/Element[Type=2]) &gt;= 2">
					<span class="link m0505 txt10" onclick="$('#commentBlock').toggle()">показать все</span>
				</xsl:if>
			</div>

		</xsl:if>

	</xsl:template>
	
	<xsl:template name="audioData">
		<xsl:param name="context" select="dbInfo/Request"/>
		
		<div style="margin:10px">	
			<h1>Записи звонков <span class="link txt12" style="margin-left: 10px" onclick="reloadAudioList()">обновить список записей</span><span id="loadAudioStatus"></span></h1>
			
			<div id="audioResultset" class="scroll-pane-audio">
				<table width="100%">
					<xsl:for-each select="$context/RecordList/Element">
						<xsl:variable name="class" select="no"/>
						<tr onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','{$class}')">
							<td style="padding-bottom: 10px">
								<div style="margin:0; width:200px; overflow:hidden; float:left; ">
										<audio controls="true" style="background:#fff;" id="audio-{Record_id}" playbackRate="1.0">
											<source src="{Filename}" type="audio/mpeg" />
										</audio>
								</div>
								<div style="float:left;">
									<button type="button" class="audio-rate-button" data-record="audio-{Record_id}">x1</button>
								</div>
								<div style="float:left;">
									<a href="{Filename}" target="_export" title="Скачать запись">
											<div  style="width: 16px; height:16px; background: url('/img/icon/download.png') no-repeat; margin: 2px 0 0 5px;"></div>
									</a>
								</div>
								<br clear="all" />
								<div style="margin:0">
									<label>
										<input class="mr5 appointment-checkbox" type="checkbox" name="isAppointment[{Record_id}]" value="yes" data-date="{FDate}" data-hour="{CrHour}" data-min="{CrMin}" data-id="{Record_id}" data-clinic="{Clinic_id}">
											<xsl:if test="IsAppointment = 'yes'"><xsl:attribute name="checked"/></xsl:if>
										</input>
										<xsl:call-template name="statusState">
											<xsl:with-param name="class" select="'record_action i-st-appointment'"/>
											<xsl:with-param name="withName" select="'no'"/>
											<xsl:with-param name="name" select="'приём'"/>
										</xsl:call-template>
									</label>
									<xsl:if test="/root/srvInfo/AddActions = '1'">
										<label class="ml20">
											<input class="mr5 isVisit" type="checkbox" name="isVisit[{Record_id}]" value="yes" >
												<xsl:if test="IsVisit = 'yes'"><xsl:attribute name="checked"/></xsl:if>
											</input>
											<xsl:call-template name="statusState">
												<xsl:with-param name="class" select="'record_action i-st-visit'"/>
												<xsl:with-param name="withName" select="'no'"/>
												<xsl:with-param name="name" select="'дошёл'"/>
											</xsl:call-template>
										</label>
										<label class="ml20">
											<input class="mr5" type="checkbox" name="isOpinion['{Request_id}_{Filename}']" value="yes">
												<xsl:if test="IsOpinion = 'yes'"><xsl:attribute name="checked"/></xsl:if>
											</input>
											<xsl:call-template name="statusState">
												<xsl:with-param name="class" select="'record_action i-st-opinion'"/>
												<xsl:with-param name="withName" select="'no'"/>
												<xsl:with-param name="name" select="'отзыв'"/>
											</xsl:call-template>

										</label>
									</xsl:if>
									<xsl:if test="FDateTime != ''">
									<span class="ml10" >
										дата: <xsl:value-of select="FDateTime"/>
									</span>
									</xsl:if>
								</div>
							</td>
						</tr>
					</xsl:for-each>
					<tr>
						<td style="padding-top: 10px">
							<form name="loadAudioFile" id="loadAudioFile" method="post" enctype="multipart/form-data">
								<div style="float:left; padding: 4px 10px 10px 0">Загрузить аудио-запись (.mp3)</div>
								<div style="float:left; margin-left: 10px" id="file-uploader"></div>
							    <script type="text/javascript">
							       $(document).ready(function() {
							    		createUploader(<xsl:value-of select="$context/@id"/>);
							    	});     
							    </script>
							</form>
						</td>
					</tr>
					
				</table> 
			</div> 
			
			<iframe name="export" id="export" height="0" width="0" frameborder="0" scrolling="No"/>
		</div>

	</xsl:template>

	<xsl:template name="postWork">
		<xsl:param name="context" select="dbInfo/Request"/>
		
		<div style="margin:10px;">
			<h1>История действий <span style="font-size: 12px; margin-left: 125px" class="link" onclick="$('#postBlock').hide();$('#postBlockPanel').show()">свернуть</span></h1>
			<div sid="historyList" class="wb">
				<div id="historyBlock">
					<div id="historyPane"></div>
				</div>
			</div> 
			
		</div>
	</xsl:template>
	
	
	
</xsl:transform>

