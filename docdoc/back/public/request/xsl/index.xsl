<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:import href="../../lib/xsl/modalWindow.xsl"/>
	<xsl:import href="../../lib/xsl/common.xsl"/>
	<xsl:import href="../../lib/xsl/pager.xsl"/>
	<xsl:import href="../../lib/xsl/filter.xsl"/>
	<xsl:import href="../../lib/xsl/sortBy.xsl"/>
	<xsl:import href="common.xsl"/>

	<xsl:output method="html" encoding="utf-8"/>

	<xsl:key name="type" match="/root/dbInfo/TypeDict/Element" use="@id"/>
	<xsl:key name="kind" match="/root/dbInfo/KindList/Element" use="Id"/>
	<xsl:key name="diagnosticaList" match="/root/dbInfo/DiagnosticList/descendant-or-self::Element" use="@id"/>
	<xsl:key name="sourceType" match="/root/dbInfo/SourceTypeDict/Element" use="@id"/>
	<xsl:key name="status" match="/root/dbInfo/StatusDict/Element" use="@id"/>
	<xsl:key name="action" match="/root/dbInfo/ActionDict/Element" use="@id"/>
	

	<xsl:template match="/">
		<xsl:call-template name="css"/>
		<xsl:apply-templates select="root"/>
		<xsl:call-template name="js"/>
	</xsl:template>

	<xsl:template name="commonRoot" match="root">
		<div id="main">
			<div id="reminderBlock" class="hd"></div>

			<div style="float:right; margin: 0">
				<div class="form" style="width: 100px; float: right; padding: 3px 10px 2px 10px; margin: 0;" onclick="editRequest('')">СОЗДАТЬ ЗАЯВКУ</div>
			</div>
			<xsl:if test="/root/srvInfo/TypeView = 'call_center'">
				<div style="float:right; margin: 0 20px 0 0">
					<div class="form" id="queueButton" style="width: 200px; float: right; padding: 3px 10px 2px 10px; margin: 0;">
						<xsl:choose>
							<xsl:when test="dbInfo/Queue4User">Выйти из очереди</xsl:when>
							<xsl:otherwise>Зарегистрироваться в очереди</xsl:otherwise>
						</xsl:choose>
					</div>
					<script>
						<xsl:choose>
							<xsl:when test="dbInfo/Queue4User">
								$('#queueButton').bind("click", function(e){unsetQueue()});
							</xsl:when>
							<xsl:otherwise>
								$('#queueButton').bind("click", function(e){setQueue()});
							</xsl:otherwise>
						</xsl:choose>
					</script>
				</div>
			</xsl:if>

			<!-- Групповое изменение статусов  -->
			<xsl:if test="/root/srvInfo/UserData/Rights/Right  = 'ADM' or /root/srvInfo/UserData/Rights/Right  = 'SOP'">
				<div style="float:right;  margin: 0 20px 0 0">
					<div class="form" style="width: 160px; float: right; padding: 3px 10px 2px 10px; margin: 0;" onclick="setGroupAction('{/root/srvInfo/TypeView}')">ГРУППОВЫЕ ОПЕРАЦИИ</div>
				</div>
			</xsl:if>

			<xsl:if test="/root/srvInfo/UserData/Rights/Right  = 'ADM' or /root/srvInfo/UserData/Rights/Right  = 'SOP'">
				<div class="request-export">
					<div class="form" onclick="javascript:exportData()">ЭКСПОРТ</div>
				</div>
			</xsl:if>

			<xsl:if test="/root/srvInfo/LastSipRequestId != ''">
				<div style="float:right; margin:3px 40px 0 0;">
					<a href="/request/request.htm?id={/root/srvInfo/LastSipRequestId}&amp;type={/root/srvInfo/TypeView}" style="color:#080;">
						Последний звонок по заявке (<xsl:value-of select="/root/srvInfo/LastSipRequestId"/>)
					</a>
				</div>
			</xsl:if>

			<h1>Заявки</h1>

			<xsl:call-template name="pageFilters"/>

			<xsl:call-template name="quickSearch"/>

			<xsl:call-template name="resultSet"/>

			<xsl:call-template name="pager">
				<xsl:with-param name="formName" select="'filterForm'"/>
				<xsl:with-param name="context" select="dbInfo/Pager"/>
			</xsl:call-template>

		</div>
	</xsl:template>

	<xsl:template name="css">
		<link type="text/css" href="/css/jquery-ui-1.7.2.custom.css" rel="stylesheet" />
		<link type="text/css" href="/css/jquery.autocomplete.css" rel="stylesheet" media="screen"/>
		<link type="text/css" href="/css/callCenterSpetial.css" rel="stylesheet" media="screen"/>
		<link type="text/css" href="/css/popup.css" rel="stylesheet" media="screen"/>
		<style>
			.modWinTitle {margin: 0; background:#b3b3b3; height: 32px; padding:2px;}
			.modWinTitle h1 {margin: 0; padding: 10px 0 2px 10px; width: 100%; font-size:12px; font-weight:bold; color:#fff;}
			div.trNoClick	{margin: 0}

			#reminderBlock {width: 225px; height: 250px;  z-index: 999; position: fixed; bottom: 0; right: 0;}
		</style>
	</xsl:template>

	<xsl:template name="js">
		<script src="/lib/js/jquery-ui-1.7.2.min.js" type="text/javascript" ></script>
		<script src='/lib/js/jquery.autocomplete.min.js' type='text/javascript' language="JavaScript"></script>
		<script src="/lib/js/ui.datepicker-ru.js" type="text/javascript" ></script>
		<script src="/lib/js/popup.js" type="text/javascript"></script>
		<script src="/lib/js/multiple_checkbox.js" type="text/javascript"></script>
		<script src="js/index.js" type="text/javascript" ></script>

		<script>
			function exportData(clinicId) {
			$form = $('#filterForm');
			$form.attr("action", "/request/service/exportRequests.htm");
			$form.submit();
			$form.attr("action", "/request/index.htm");
			}
		</script>
	</xsl:template>

	<!--	Фильтр	-->
	<xsl:template name="pageFilters">
		<xsl:call-template name="filter">
			<xsl:with-param name="formId" select="'filterForm'"/>
			<xsl:with-param name="startPage" select="dbInfo/Pager/@currentPageId"/>
			<xsl:with-param name="generalLine">
				<xsl:call-template name="sortInit">
					<xsl:with-param name="form" select="'filterForm'"/>
					<xsl:with-param name="sortBy" select="/root/srvInfo/SortBy"/>
					<xsl:with-param name="sortType" select="/root/srvInfo/SortType"/>
				</xsl:call-template>
				<input type="hidden" id="typeView" name="type" value="{/root/srvInfo/TypeView}"/>
				<div>
					<xsl:if test="/root/srvInfo/Filter/kind">
						<div class="inBlockFilter">
							<label>Вид: </label>
							<div style="width: 100%; position: relative;">
								<xsl:call-template name="multi-select">
									<xsl:with-param name="param">shKind[]</xsl:with-param>
									<xsl:with-param name="withoutItem" />
									<xsl:with-param name="context" select="dbInfo/KindList/Element"/>
									<xsl:with-param name="selectedItems" select="srvInfo/FilterParams/Kind/ElementId"/>
								</xsl:call-template>
							</div>
						</div>
					</xsl:if>
					<xsl:if test="/root/srvInfo/Filter/req_type">
						<div class="inBlockFilter">
							<label>Способ обращения: </label>
							<xsl:call-template name="multi-select">
								<xsl:with-param name="param">shType[]</xsl:with-param>
								<xsl:with-param name="withoutItem" />
								<xsl:with-param name="context" select="dbInfo/TypeDict/Element"/>
								<xsl:with-param name="selectedItems" select="srvInfo/FilterParams/Type/ElementId"/>
							</xsl:call-template>
						</div>
					</xsl:if>
					<xsl:if test="/root/srvInfo/Filter/req_status">
						<div class="inBlockFilter">
							<label>Статус: </label>
							<xsl:call-template name="multi-select">
								<xsl:with-param name="param">shStatus[]</xsl:with-param>
								<xsl:with-param name="withoutItem" />
								<xsl:with-param name="context" select="dbInfo/StatusDict/Element"/>
								<xsl:with-param name="selectedItems" select="srvInfo/FilterParams/Status/ElementId"/>
							</xsl:call-template>
						</div>
					</xsl:if>

					<xsl:if test="/root/srvInfo/Filter/clinic_id">
						<div class="inBlockFilter">
							<div>
								<label>Клиника: </label>
								<div>
									<input id="shClinicName" style="width: 250px" value="{srvInfo/ClinicShortName}" autocomplete="off"/>
								</div>
							</div>
						</div>

						<div class="inBlockFilter">
							<div>
								<label>ID клиники:</label>
								<div>
									<input name="shClinicId" id="shClinicId" style="width: 50px" maxlength="6"  value="{srvInfo/ClinicId}"/>
								</div>
							</div>
						</div>

						<div class="inBlockFilter" style="margin: 0 15px 6px;">
							<div>
								<label for="withBranches">С филиалами:</label>
								<div>
									<input type="checkbox" name="withBranches" id="withBranches">
										<xsl:if test="/root/srvInfo/WithBranches = '1'">
											<xsl:attribute name="checked">checked</xsl:attribute>
										</xsl:if>
										<xsl:if test="/root/srvInfo/ClinicId = ''">
											<xsl:attribute name="disabled">true</xsl:attribute>
										</xsl:if>
									</input>
								</div>
							</div>
						</div>
					</xsl:if>

					<xsl:if test="/root/srvInfo/Filter/req_sector_id">
						<div class="inBlockFilter">
							<label>Специализация: </label>
							<xsl:call-template name="multi-select">
								<xsl:with-param name="param">shSectorId[]</xsl:with-param>
								<xsl:with-param name="withoutItem">Без специализации</xsl:with-param>
								<xsl:with-param name="context" select="dbInfo/SectorList/Element"/>
								<xsl:with-param name="selectedItems" select="srvInfo/FilterParams/Spec/ElementId"/>
							</xsl:call-template>
						</div>
					</xsl:if>
					<xsl:if test="/root/srvInfo/Filter/req_user_id">
						<div class="inBlockFilter">
							<label>Оператор: </label>
							<xsl:call-template name="multi-select">
								<xsl:with-param name="param">shOwner[]</xsl:with-param>
								<xsl:with-param name="withoutItem">Без оператора</xsl:with-param>
								<xsl:with-param name="context" select="dbInfo/OperatorList/Element"/>
								<xsl:with-param name="selectedItems" select="srvInfo/FilterParams/Owner/ElementId"/>
							</xsl:call-template>
						</div>
					</xsl:if>
					<xsl:if test="/root/srvInfo/Filter/req_created">
						<div class="inBlockFilter">
							<div>
								<label>Дата обращения:</label>
								<div>
									c:
									<xsl:choose>
										<xsl:when test="/root/srvInfo/CrDateShFrom = ''">
											<input name="crDateShFrom" id="crDateShFrom" class="datePicker" style="width:70px" maxlength="12" value="{srvInfo/CrDateShFrom}"/>
										</xsl:when>
										<xsl:otherwise>
											<input name="crDateShFrom" id="crDateShFrom" class="datePicker" style="width:70px" maxlength="12" value="{srvInfo/CrDateShFrom}"/>
										</xsl:otherwise>
									</xsl:choose>
									&#160;по:
									<input name="crDateShTill" id="crDateShTill" class="datePicker" style="width:70px" maxlength="12" value="{srvInfo/CrDateShTill}"/>
								</div>

							</div>
						</div>
					</xsl:if>
					<xsl:if test="/root/srvInfo/Filter/date_admission">
						<div class="inBlockFilter">
							<div>
								<label>Дата приёма:</label>
								<div>
									c:
									<input name="crDateReciveFrom" id="crDateReciveFrom" class="datePicker" style="width:70px" maxlength="12" value="{srvInfo/DateReciveFrom}"/>
									&#160;по:
									<input name="crDateReciveTill" id="crDateReciveTill" class="datePicker" style="width:70px" maxlength="12" value="{srvInfo/DateReciveTill}"/>
								</div>

							</div>
						</div>
					</xsl:if>
					<xsl:if test="/root/srvInfo/Filter/city">
						<div class="inBlockFilter">
							<label>Город: </label>
							<xsl:call-template name="multi-select">
								<xsl:with-param name="param">shCity[]</xsl:with-param>
								<xsl:with-param name="withoutItem" />
								<xsl:with-param name="context" select="dbInfo/CityList/Element"/>
								<xsl:with-param name="selectedItems" select="srvInfo/FilterParams/City/ElementId"/>
							</xsl:call-template>
						</div>
					</xsl:if>
				</div>


			</xsl:with-param>

			<xsl:with-param name="addLine">
				<xsl:if test="/root/srvInfo/Filter/req_id">
					<div class="inBlockFilter">
						<div>
							<label>ID:</label>
							<div>
								<input name="id" id="id" style="width: 50px" maxlength="6"  value="{srvInfo/Id}"/>
							</div>
						</div>
					</div>
				</xsl:if>
				<xsl:if test="/root/srvInfo/Filter/client_phone">
					<div class="inBlockFilter" style="margin-left: 10px">
						<div>
							<label>Номер телефона:</label>
							<div>
								<input name="phone" id="phone" style="width: 150px" maxlength="15"  value="{srvInfo/Phone}"/>
							</div>
						</div>
					</div>
				</xsl:if>
				<xsl:if test="/root/srvInfo/Filter/client_name">
					<div class="inBlockFilter" style="margin-left: 10px">
						<div>
							<label>Пациент:</label>
							<div>
								<input name="client" id="client" style="width: 150px" maxlength="15"  value="{srvInfo/Client}"/>
							</div>
						</div>
					</div>
				</xsl:if>
				<xsl:if test="/root/srvInfo/Filter/source_type">
					<div class="inBlockFilter">
						<label>Источник: </label>
						<xsl:call-template name="multi-select">
							<xsl:with-param name="param">shSourceType[]</xsl:with-param>
							<xsl:with-param name="withoutItem" />
							<xsl:with-param name="context" select="dbInfo/SourceTypeDict/Element"/>
							<xsl:with-param name="selectedItems" select="srvInfo/FilterParams/SourceType/ElementId"/>
						</xsl:call-template>
					</div>
				</xsl:if>
				<xsl:if test="/root/srvInfo/Filter/destination_phone_id">
					<div class="inBlockFilter">
						<label>Номер телефона адресата: </label>
						<xsl:call-template name="multi-select">
							<xsl:with-param name="param">destinationPhoneId[]</xsl:with-param>
							<xsl:with-param name="withoutItem" />
							<xsl:with-param name="context" select="dbInfo/DestinationPhone/Element"/>
							<xsl:with-param name="selectedItems" select="srvInfo/FilterParams/DestinationPhone/ElementId"/>
						</xsl:call-template>
					</div>
				</xsl:if>

				<xsl:if test="/root/srvInfo/Filter/date_record">
					<div class="inBlockFilter">
						<div>
							<label>Дата записи:</label>
							<div>
								c:
								<input name="recDateShFrom" id="recDateShFrom" class="datePicker" style="width:80px" maxlength="12" value="{srvInfo/RecDateShFrom}"/>
								&#160;по:
								<input name="recDateShTill" id="recDateShTill" class="datePicker" style="width:80px" maxlength="12" value="{srvInfo/RecDateShTill}"/>
							</div>

						</div>
					</div>
				</xsl:if>
				<xsl:if test="/root/srvInfo/Filter/partner">
					<div class="inBlockFilter">
						<label>Партнер: </label>
						<xsl:call-template name="multi-select">
							<xsl:with-param name="param">partner[]</xsl:with-param>
							<xsl:with-param name="withoutItem">Без партнера</xsl:with-param>
							<xsl:with-param name="context" select="dbInfo/PartnerList/Element"/>
							<xsl:with-param name="selectedItems" select="srvInfo/FilterParams/Partner/ElementId"/>
						</xsl:call-template>
					</div>
				</xsl:if>

			</xsl:with-param>

			<xsl:with-param name="addLine2">
				<xsl:if test="/root/srvInfo/Filter/clinic_not_found">
					<div class="inBlockFilter" style="">
						<div>
							<label for="clinicNotFound" style="padding-bottom: 5px;">Без клиники:&#160;</label>
							<input type="checkbox" name="clinicNotFound" id="clinicNotFound">
								<xsl:if test="/root/srvInfo/ClinicNotFound = '1'">
									<xsl:attribute name="checked">checked</xsl:attribute>
								</xsl:if>
							</input>
						</div>
						<div>
							<input type="checkbox" name="hasDeparture" id="hasDeparture">
								<xsl:if test="/root/srvInfo/HasDeparture = '1'">
									<xsl:attribute name="checked">checked</xsl:attribute>
								</xsl:if>
							</input>
							<label for="hasDeparture">С выездом на дом</label>
						</div>
					</div>
				</xsl:if>
				<xsl:if test="/root/srvInfo/Filter/diagnostic_id">
					<div class="inBlockFilter">
						<div style="width: 600px">
							<label>Диагностика:&#160; </label>
							<span id="statusFilter" class="link" onclick="$('#ceilWin_multy').show();">
								<xsl:choose>
									<xsl:when test="not(/root/srvInfo/FilterParams/Diagnostica/ElementId) or /root/srvInfo/DiagnosticaList = ''">Все типы</xsl:when>
									<xsl:when test="/root/srvInfo/DiagnosticaList = '0'">Все типы</xsl:when>
									<xsl:when test="/root/srvInfo/FilterParams/Diagnostica/ElementId">
										<xsl:for-each select="/root/srvInfo/FilterParams/Diagnostica/ElementId">
											<xsl:if test="key('diagnosticaList',.)/../../Name">
												<xsl:value-of select="key('diagnosticaList',.)/../../Name"/>&#160;
											</xsl:if>
											<xsl:value-of select="key('diagnosticaList',.)/Name"/>
											<xsl:if test="position() != last()">,&#160;</xsl:if>
										</xsl:for-each>
									</xsl:when>
									<xsl:otherwise>Все типы</xsl:otherwise>
								</xsl:choose>
							</span>
							<div class="ancor">
								<xsl:call-template name="diagnostics"><xsl:with-param name="id" select="'multy'"/></xsl:call-template>
							</div>
						</div>
					</div>
				</xsl:if>
			</xsl:with-param>

			<xsl:with-param name="clearFunction">
				clearFilterForm()
			</xsl:with-param>

		</xsl:call-template>
	</xsl:template>

	<xsl:template name="resultSet">
		<div id="resultSet">
			<xsl:variable name="tdCount">
				<xsl:choose>
					<xsl:when test="/root/srvInfo/ShStatus = '7' and (/root/srvInfo/UserData/Rights/Right  = 'ADM' or /root/srvInfo/UserData/Rights/Right  = 'SOP')">14</xsl:when>
					<xsl:when test="/root/srvInfo/ShStatus = '7'">13</xsl:when>
					<xsl:when test="/root/srvInfo/ShStatus != '7' and (/root/srvInfo/UserData/Rights/Right  = 'ADM' or /root/srvInfo/UserData/Rights/Right  = 'SOP')">13</xsl:when>
					<xsl:otherwise>12</xsl:otherwise>
				</xsl:choose>
			</xsl:variable>



			<form name="formRequestList" id ="formRequestList" method="post">
				<table cellpadding="0" cellspacing="1" width="100%" border="0" class="resultSet">
					<!-- <col width="30"/> -->
					<col width="40"/>
					<col width="60"/>
					<col width="40"/>
					<col width="20"/>
					<col width="70"/>
					<col width="20"/>
					<col width="20"/>
					<col/>
					<col width="100"/>
					<col/>

					<col width="60"/>
					<xsl:if test="/root/srvInfo/FilterParams/Status/ElementId = '7'">
						<col width="100"/>
					</xsl:if>
					<col/>
					<col width="20"/>
					<col width="20"/>
					<col width="150" />
					<col width="20"/>
					<xsl:if test="/root/srvInfo/UserData/Rights/Right  = 'ADM' or /root/srvInfo/UserData/Rights/Right  = 'SOP'">
						<col width="20"/>
					</xsl:if>



					<tr>
						<!-- <th rowspan="2">#</th> -->
						<th rowspan="2">Id
							<xsl:call-template name="sortBy">
								<xsl:with-param name="sortBy" select="/root/srvInfo/SortBy"/>
								<xsl:with-param name="sortType" select="/root/srvInfo/SortType"/>
								<xsl:with-param name="field" select="'id'"/>
							</xsl:call-template>
						</th>
						<th colspan="2">Создана
							<xsl:call-template name="sortBy">
								<xsl:with-param name="sortBy" select="/root/srvInfo/SortBy"/>
								<xsl:with-param name="sortType" select="/root/srvInfo/SortType"/>
								<xsl:with-param name="field" select="'crDate'"/>
							</xsl:call-template>
						</th>
						<th rowspan="2" title="Источник обращения">Ист.</th>
						<th rowspan="2">Партнёр</th>
						<th rowspan="2">Вид</th>
						<th rowspan="2" title="Способ обращения">Сп.</th>
						<th colspan="2">Клиент</th>
						<th rowspan="2">Клиника</th>
						<th rowspan="2">
							<xsl:choose>
								<xsl:when test="/root/srvInfo/TypeView = 'diag_listener'">Дианостика</xsl:when>
								<xsl:otherwise>Специализация</xsl:otherwise>
							</xsl:choose>
						</th>
						<th rowspan="2" title="Прием состоялся">Прием</th>
						<xsl:if test="/root/srvInfo/FilterParams/Status/ElementId = '7'">
							<th rowspan="2">Перезвонить
								<xsl:call-template name="sortBy">
									<xsl:with-param name="sortBy" select="/root/srvInfo/SortBy"/>
									<xsl:with-param name="sortType" select="/root/srvInfo/SortType"/>
									<xsl:with-param name="field" select="'call_later'"/>
								</xsl:call-template>
							</th>
						</xsl:if>
						<th rowspan="2">Коментарий</th>
						<th rowspan="2">&#160;</th>
						<th rowspan="2">Оператор</th>
						<th rowspan="2" title="Суммарная продолжительность">Прод.</th>
						<th rowspan="2">Статус
						</th>
						<xsl:if test="/root/srvInfo/UserData/Rights/Right  = 'ADM' or /root/srvInfo/UserData/Rights/Right  = 'SOP'">
							<th rowspan="2"><input type="checkbox" id="formSelectAll"/></th>
						</xsl:if>
					</tr>
					<tr>
						<th>дата</th>
						<th>чч:мм</th>
						<th>фамилия имя</th>
						<th>телефон</th>
					</tr>
					<xsl:choose>
						<xsl:when test="dbInfo/RequestList/Element">
							<xsl:for-each select="dbInfo/RequestList/Element">
								<xsl:variable name="class">
									<xsl:choose>
										<xsl:when test="Status = '0' or IsHot = '1'">trSelectedLight</xsl:when>
										<xsl:when test="(position() div 2) - floor(position() div 2) &gt; 0">odd</xsl:when>
										<xsl:otherwise>even</xsl:otherwise>
									</xsl:choose>
								</xsl:variable>

								<tr id="tr_{@id}" class="{$class}" backclass="{$class}" data-owner="{Owner/@id}" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','{$class}')" style="cursor:pointer;">
									<xsl:attribute name="onclick">
										(editOrderKey)?editRequest('<xsl:value-of select="@id"/>', true):void(0)
									</xsl:attribute>

									<td align="right">
										<xsl:value-of select="@id"/>
									</td>
									<td align="center">
										<xsl:value-of select="CrDate"/>
									</td>
									<td align="center">
										<xsl:value-of select="CrTime"/>
									</td>
									<td align="center">
										<xsl:if test="SourceType != ''">
											<span>
												<xsl:attribute name="class">i-status req_source i-st-<xsl:value-of select="SourceType"/></xsl:attribute>
												<xsl:attribute name="title">
													<xsl:variable name="CityId"><xsl:value-of select="CityId"/></xsl:variable>
													<xsl:value-of select="key('sourceType', SourceType)/."/>, <xsl:value-of select="/root/dbInfo/CityList/Element[Id = $CityId]/Name"/>
												</xsl:attribute>
											</span>
										</xsl:if>
									</td>
									<td align="center">
										<span><xsl:value-of select="PartnerName"/></span>
									</td>
									<td>
										<xsl:if test="Kind != ''">
											<div style="width: 20px; height: 20px; background: url('/img/icon/req_kind_{Kind}.png') no-repeat " class="null" title="{key('kind',Kind)/Name}"/>
										</xsl:if>
									</td>
									<td align="center">
										<xsl:if test="Type != ''">
											<div style="width: 16px; height: 16px; background: url('/img/icon/req_type3_{Type}.png') no-repeat " class="null" title="{key('type',Type)/Name}"/>
										</xsl:if>
									</td>
									<td>
										<xsl:value-of select="Client"/>
									</td>
									<td nowrap="">
										<xsl:value-of select="ClientPhone"/>
									</td>
									<td>
										<xsl:choose>
											<xsl:when test="ClinicName != ''">
												<xsl:value-of select="ClinicName"/>
											</xsl:when>
											<xsl:when test="ClinicName = '' and Type = '3'">
												<b>Центр не распознан</b>
											</xsl:when>
										</xsl:choose>
									</td>
									<td>
										<xsl:choose>
											<xsl:when test="Kind = '1' or Kind = '2'">
												<xsl:choose>
													<xsl:when test="DiagnosticsId != '0'">
														<xsl:if test="key('diagnosticaList', DiagnosticsId)/../../Name">
															<xsl:value-of select="key('diagnosticaList', DiagnosticsId)/../../Name"/>&#160;
														</xsl:if>
														<xsl:value-of select="key('diagnosticaList',DiagnosticsId)/Name"/>
													</xsl:when>
													<xsl:when test="DiagnosticsId = '0' and DiagnosticsOther != ''">
														<xsl:value-of select="DiagnosticsOther"/>
													</xsl:when>
												</xsl:choose>
											</xsl:when>
											<xsl:otherwise>
												<xsl:value-of select="Sector"/>
											</xsl:otherwise>
										</xsl:choose>
									</td>

									<td align="center">
										<!-- <xsl:if test="AppointmentStatus != '0'"> -->
										<xsl:value-of select="AppointmentDate"/>
										<!-- </xsl:if> -->
									</td>
									<xsl:if test="/root/srvInfo/FilterParams/Status/ElementId = '7'">
										<td nowrap="">
											<xsl:if test="CallLaterDate">
												<xsl:value-of select="CallLaterDate"/>&#160;
												<xsl:value-of select="CallLaterTime"/>
											</xsl:if>
										</td>
									</xsl:if>
									<td>
										<xsl:value-of select="CommentList/Element[Type = '2'][position() = 1]/Text"/>
									</td>
									<td>
										<div class="trNoClick">
											<xsl:if test="count(CommentList/Element) &gt; 1">
												<img src="/img/icon/note.png" align="absbottom" style="cursor:pointer; margin: 0 5px 0 5px">
													<xsl:attribute name="onclick">$('#ceilWin_<xsl:value-of select="@id"/>').show();</xsl:attribute>
												</img>
											</xsl:if>
											<div class="ancor" style="float: right">
												<xsl:call-template name="ceilInfo">
													<xsl:with-param name="id" select="@id"/>
													<xsl:with-param name="context" select="CommentList"/>
												</xsl:call-template>
											</div>
										</div>
									</td>
									<td>
										<xsl:value-of select="Owner"/>
									</td>
									<td class="r">
										<xsl:value-of select="Duration"/>
									</td>
									<td>
										<xsl:choose>
											<xsl:when test="Status = '7'">
												<xsl:choose>
													<xsl:when test="number(RemainTime) &gt; 0 or (number(RemainTime) &lt; 0 and number(RemainTime) &gt;= (-300) )">
														<xsl:call-template name="status">
															<xsl:with-param name="id" select="Status"/>
															<xsl:with-param name="style" select="'background-image:url(/img/icon/req_status_16_7_hot.png)'"/>
															<xsl:with-param name="name" select="key('status',Status)/."/>
														</xsl:call-template>
													</xsl:when>
													<xsl:when test="number(RemainTime) &lt; (-300) and number(RemainTime) &gt;= (-600)">
														<xsl:call-template name="status">
															<xsl:with-param name="id" select="Status"/>
															<xsl:with-param name="style" select="'background-image:url(/img/icon/req_status_16_7_stadyn.png)'"/>
															<xsl:with-param name="name" select="key('status',Status)/."/>
														</xsl:call-template>
													</xsl:when>
													<xsl:when test="number(RemainTime) &lt; (-600) and number(RemainTime) &gt; (-3600)">
														<xsl:call-template name="status">
															<xsl:with-param name="id" select="Status"/>
															<xsl:with-param name="style" select="'background-image:url(/img/icon/req_status_16_7_ready.png)'"/>
															<xsl:with-param name="name" select="key('status',Status)/."/>
														</xsl:call-template>
													</xsl:when>
													<xsl:otherwise>
														<xsl:call-template name="status">
															<xsl:with-param name="id" select="Status"/>
															<xsl:with-param name="name" select="key('status',Status)/."/>
														</xsl:call-template>
													</xsl:otherwise>
												</xsl:choose>
											</xsl:when>
											<xsl:when test="Status != ''">
												<xsl:call-template name="status">
													<xsl:with-param name="id" select="Status"/>
													<xsl:with-param name="name" select="key('status',Status)/."/>
												</xsl:call-template>
											</xsl:when>
										</xsl:choose>
									</td>
									<xsl:if test="/root/srvInfo/UserData/Rights/Right  = 'ADM' or /root/srvInfo/UserData/Rights/Right  = 'SOP'">
										<td align="center" class="trNoClick">
											<input type="checkbox" value="{@id}" name="ch[{@id}]" id="ch[{@id}]" class="selectRow" style="border:0px" onchange="(this.checked)?($('#tr_{@id}').attr('class','trSelected')):($('#tr_{@id}').attr('class','{$class}'));"/>
										</td>
									</xsl:if>
								</tr>
							</xsl:for-each>
						</xsl:when>
						<xsl:otherwise>
							<tr>
								<td colspan="{$tdCount}" align="center">
									<div class="error" style="margin: 20px">Данных не найдено</div>
								</td>
							</tr>
						</xsl:otherwise>
					</xsl:choose>
				</table>
			</form>
		</div>
	</xsl:template>

	<xsl:template name="ceilInfo">	  
		<xsl:param name="id"/>
		<xsl:param name="context" select="CommentList"/>
		
		<div id="ceilWin_{$id}" class="infoElt hd" style="width: 800px; padding: 0">
			<div class="modWinTitle"><h1>История изменений заявки № <xsl:value-of select="$id"/></h1></div>
				<div  style="margin: 10px">
					<table width="100%">
						<col width="60"/>
						<col width="30"/>
						<col width="30"/>
						<col />
						<col swidth="150"/>
						
						<tr>
							<th colspan="2">Время</th>
							<th>Тип</th>
							<th>Текст</th>
							<th>Пользователь</th>
						</tr>
					
					<xsl:for-each select="$context/Element">
						<xsl:variable name="class">
							<xsl:choose>
								<xsl:when test="(position() div 2) - floor(position() div 2) &gt; 0">odd</xsl:when>
								<xsl:otherwise>even</xsl:otherwise>
							</xsl:choose>
						</xsl:variable>
						
						<tr id="tr_cell_{@id}" class="{$class}" backclass="{$class}" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','{$class}')">
							<td><xsl:value-of select="CrDate"/></td>
							<td><xsl:value-of select="CrTime"/></td>
							<td align="center">
								<img src="/img/icon/req_comment_type_{Type}.png" title="{key('action', Type)/.}"/>
							</td>
							<td>
								<xsl:value-of select="Text"/>
							</td>
							<td>
								<xsl:value-of select="Owner"/>
							</td>
						</tr>
					</xsl:for-each>
				</table>
				<!-- <div style="position:relative; margin: 20px 2px 30px 0;">		  
					<div class="form" style="width:100px; float:right; margin-left: 10px" onclick="$('#ceilWin_{$id}').hide();">ЗАКРЫТЬ</div>
				</div> -->
			</div>
			<img src="/img/common/clBtBig.gif" width="20" height="20"  alt="закрыть" style="position: absolute; cursor: pointer; right: 5px; top: 5px;" title="закрыть" onclick="$('#ceilWin_{$id}').hide();" border="0"/>
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
							<input class="checkBox4Text" type="Checkbox" value="{@id}" txt="{Name}" onchange="setFilterSubList(this)"/>
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

	<!-- Панель быстрого поиска по статусу заявки -->
	<xsl:template name="quickSearch">
		<div style="margin: 0 0 10px 0; padding: 4px 10px 4px 10px" class="wb">
			<table align="center">
				<tr>
					<td style="padding-left: 20px">Быстрый поиск по <strong>статусу</strong>:</td>
					<xsl:for-each select="dbInfo/StatusDict[@mode='requestDict']/Element[not(@display) or @display!= 'no']" >
						<td align="center" style="padding: 0 10px 0 10px">
							<div class="null" title="{key('status', @id)/.}" onclick="window.location.href = '/request/index.htm?type={/root/srvInfo/TypeView}&amp;shStatus%5B%5D={@id}'">
								<xsl:attribute name="style">
								<xsl:choose>
									<xsl:when test="@id = /root/srvInfo/ShStatus">
										width: 24px; height: 24px; background: url('/img/icon/req_status_<xsl:value-of select="@id"/>.png') no-repeat; cursor: pointer; outline: 1px solid #FF0000;
									</xsl:when>
									<xsl:otherwise>
										width: 24px; height: 24px; background: url('/img/icon/req_status_<xsl:value-of select="@id"/>.png') no-repeat; cursor: pointer; 
									</xsl:otherwise>
								</xsl:choose>
								</xsl:attribute>
							</div>
						</td>	
					</xsl:for-each>
					<td style="padding-left: 20px">
						<span class="link" onclick="window.location.href = '/request/index.htm?type={/root/srvInfo/TypeView}'">показать все</span>
					</td>
					<td style="padding-left: 20px">
						<span class="link" onclick="window.location.href = '/request/index.htm?type={/root/srvInfo/TypeView}&amp;crDateShFrom={/root/srvInfo/Date}&amp;crDateShTill={/root/srvInfo/Date}'">за сегодня</span>
					</td>
					<td style="padding-left: 20px">
						<span class="link" onclick="window.location.href = '/request/index.htm{/root/srvInfo/RequestFilterString}&amp;type={/root/srvInfo/TypeView}'">последний</span>
					</td>
					<td style="padding-left: 20px">
						Показать
						<select id="stepList">
							<xsl:for-each select="srvInfo/StepList/Step">
								<option value="{.}">
									<xsl:if test=". = /root/srvInfo/Step">
										<xsl:attribute name="selected">selected</xsl:attribute>
									</xsl:if>
									<xsl:value-of select="." />
								</option>
							</xsl:for-each>
						</select>
					</td>
					<xsl:if test="/root/dbInfo/Pager and /root/dbInfo/Pager/@total">
						<div style="float:right; line-height: 27px; margin: 0 10px 0 0">ВСЕГО: <strong><xsl:value-of select="/root/dbInfo/Pager/@total"/></strong></div>
					</xsl:if>
				</tr>
				<xsl:if test="srvInfo/TypeView = 'default'">
					<tr>
						<td colspan="12"></td>
						<td style="padding-left: 30px">
							<a class="link" href="/request/index.htm?type=default&amp;shKind%5B%5D=0&amp;shType%5B%5D=0&amp;shType%5B%5D=1&amp;shType%5B%5D=2&amp;shSourceType%5B%5D=2&amp;shSourceType%5B%5D=1">DocDoc</a>
						</td>
						<td style="padding-left: 20px">
							<a class="link" href="/request/index.htm?type=default&amp;shKind%5B%5D=1">Диагностика</a>
						</td>
						<td style="padding-left: 20px">
							<a class="link" href="/request/index.htm?type=default&amp;shKind%5B%5D=0&amp;shSourceType%5B%5D=3">Партнерка</a>
						</td>
					</tr>
				</xsl:if>
			</table>
		</div>
	</xsl:template>

	<!--  Статистика для панели быстрого поиска  -->
	<xsl:template name="quickStat"> 
		<span>
			<xsl:for-each select="/root/dbInfo/StatusDict/Element">
				<xsl:variable name="id" select="@id"/>
				<div style="float: left; margin-left: 10px; padding: 2px 0 0 25px; background: url(/img/icon/req_status_16_{@id}.png) no-repeat 0 0px; height: 20px">
					<xsl:value-of select="."/> (<strong><xsl:value-of select="/root/dbInfo/RequestStat/Element[@status = $id]/."/></strong>)
					<xsl:if test="position() != last()">,&#160;</xsl:if>
				</div>
			</xsl:for-each>
			
			<div style="float: left; margin-left: 10px;"><strong>ВСЕГО: <xsl:value-of select="sum(/root/dbInfo/RequestStat/Element/.)"/></strong></div>
		</span>
	</xsl:template>

</xsl:transform>
