<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:import href="index.xsl"/>

	<xsl:output method="html" encoding="utf-8"/>

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
					<col width="40"/>
					<col width="60"/>
					<col width="60"/>
					<col width="40"/>
					<col width="20"/>
					<col width="20"/>
					<col width="20"/>
					<col/>
					<col width="100"/>
					<col/>
					<col width="100"/>
					<col width="150"/>
					<col width="20"/>
					<col width="100"/>
					<col width="100"/>
					<col width="100"/>
					<col width="100"/>
					<col width="20"/>

					<tr>
						<th rowspan="2">Id
							<xsl:call-template name="sortBy">
								<xsl:with-param name="sortBy" select="/root/srvInfo/SortBy"/>
								<xsl:with-param name="sortType" select="/root/srvInfo/SortType"/>
								<xsl:with-param name="field" select="'id'"/>
							</xsl:call-template>
						</th>
						<th rowspan="2">Город</th>
						<th colspan="2">Создана
							<xsl:call-template name="sortBy">
								<xsl:with-param name="sortBy" select="/root/srvInfo/SortBy"/>
								<xsl:with-param name="sortType" select="/root/srvInfo/SortType"/>
								<xsl:with-param name="field" select="'crDate'"/>
							</xsl:call-template>
						</th>
						<th rowspan="2" title="Источник обращения">Ист.</th>
						<th rowspan="2">Вид</th>
						<th rowspan="2" title="Способ обращения">Сп.</th>
						<th colspan="2">Клиент</th>
						<th rowspan="2">Клиника</th>
						<th rowspan="2" title="Прием состоялся">Прием</th>
						<th rowspan="2">Коментарий</th>
						<th rowspan="2">&#160;</th>
						<th rowspan="2">Стоимость для партнёра</th>
						<th rowspan="2">Статус для партнёра</th>
						<th rowspan="2">Статус биллинга</th>
						<th rowspan="2">Статус</th>
						<th rowspan="2"><input type="checkbox" id="formSelectAll"/></th>
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
									<td>
										<xsl:variable name="CityId"><xsl:value-of select="CityId"/></xsl:variable>
										<xsl:value-of select="/root/dbInfo/CityList/Element[Id = $CityId]/Name"/>
									</td>
									<td align="center">
										<xsl:value-of select="CrDate"/>
									</td>
									<td align="center">
										<xsl:value-of select="CrTime"/>
									</td>
									<td align="center">
										<xsl:if test="SourceType != ''">
											<span title="{key('sourceType', SourceType)/.}">
												<xsl:attribute name="class">i-status req_source i-st-<xsl:value-of select="SourceType"/></xsl:attribute>
											</span>
										</xsl:if>
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
									<td align="center">
										<xsl:value-of select="AppointmentDate"/>
									</td>
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
									<td align="center">
										<xsl:value-of select="format-number(PartnerCost, '#0.00')"/>
									</td>
									<td align="center">
										<xsl:value-of select="PartnerStatus"/>
									</td>
									<td align="center">
										<xsl:value-of select="BillingStatus"/>
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
									<td align="center" class="trNoClick">
										<input type="checkbox" value="{@id}" name="ch[{@id}]" id="ch[{@id}]" class="selectRow" style="border:0px" onchange="(this.checked)?($('#tr_{@id}').attr('class','trSelected')):($('#tr_{@id}').attr('class','{$class}'));"/>
									</td>
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

					<xsl:if test="/root/srvInfo/Filter/partner_status">
						<div class="inBlockFilter">
							<label>Партнерский статус: </label>
							<xsl:call-template name="multi-select">
								<xsl:with-param name="param">shPartnerStatus[]</xsl:with-param>
								<xsl:with-param name="withoutItem" />
								<xsl:with-param name="context" select="dbInfo/PartnerStatusDict/Element"/>
								<xsl:with-param name="selectedItems" select="srvInfo/FilterParams/PartnerStatus/ElementId"/>
							</xsl:call-template>
						</div>
					</xsl:if>

					<xsl:if test="/root/srvInfo/Filter/billing_status">
						<div class="inBlockFilter">
							<label>Статус биллинга: </label>
							<xsl:call-template name="multi-select">
								<xsl:with-param name="param">shBillingStatus[]</xsl:with-param>
								<xsl:with-param name="withoutItem" />
								<xsl:with-param name="context" select="dbInfo/BillingStatusDict/Element"/>
								<xsl:with-param name="selectedItems" select="srvInfo/FilterParams/BillingStatus/ElementId"/>
							</xsl:call-template>
						</div>
					</xsl:if>

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

				</div>
			</xsl:with-param>

			<xsl:with-param name="clearFunction">
				clearFilterForm()
			</xsl:with-param>

		</xsl:call-template>
	</xsl:template>

</xsl:transform>

