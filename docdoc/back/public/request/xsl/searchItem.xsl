<?xml version='1.0'  encoding="UTF-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:import href="../../lib/xsl/common.xsl"/>

	<xsl:output method="html" encoding="utf-8"/>

	<xsl:template match="root">
		<xsl:call-template name="searchItem"/>
	</xsl:template>

	<xsl:template name="searchItem">
		<xsl:param name="context" select="dbInfo/Request"/>

		<div id="searchItem">

			<div  class="doctor-search-filter">

				<div style="margin:10px;">

					<div sid="searchDoctorFilter">

						<xsl:choose>
							<xsl:when test="/root/dbInfo/Request/Kind = '0'">
								<h1>Подбор специалиста</h1>
							</xsl:when>
							<xsl:otherwise>
								<h1>Подбор клиники</h1>
							</xsl:otherwise>
						</xsl:choose>

                        <div class="ancor">
                            <xsl:call-template name="ceilInfo"><xsl:with-param name="id" select="'diagnosticaList'"/></xsl:call-template>
                        </div>

						<table id="table-filter" width="100%">
							<tr class="doctor-filter">
								<td>Специальность:</td>
								<td>
									<select name="shSectorId" id="shSectorId" class="inputForm" style="width: 100%">
										<option value="">--- Выберите специальность ---</option>
										<xsl:for-each select="/root/dbInfo/SectorList/Element">
											<option value="{@id}">
												<xsl:if test="@id = $context/SectorId">
													<xsl:attribute name="selected"/>
												</xsl:if>
												<xsl:value-of select="."/>
											</option>
										</xsl:for-each>
									</select>
								</td>
							</tr>

							<tr class="clinic-filter tr-diagnostics main-diagnostics" style="height: 30px;">
								<td colspan="2">
									<table>
										<tr>
											<td style="vertical-align: middle;">Диагностика:</td>
											<td>
												<span class="link diagnosticaText">
													<xsl:choose>
														<xsl:when test="$context/DiagnosticsId and $context/DiagnosticsId != 0">
															<input type="hidden" class="diagnostica" name="subdiagnostica[]" value="{$context/DiagnosticsId}"/>
															<input type="hidden" class="hidden-diagnostica" name="diagnosticaName" value=""/>

															<span>
																<xsl:if test="key('diagnosticaList', $context/DiagnosticsId)/../../Name">
																	<xsl:value-of select="key('diagnosticaList', $context/DiagnosticsId)/../../Name"/>&#160;
																</xsl:if>
																<xsl:value-of select="key('diagnosticaList', $context/DiagnosticsId)/Name"/>
															</span>
														</xsl:when>
														<xsl:when test="$context/DiagnosticsOther != ''">
															<input type="hidden" class="diagnostica" name="subdiagnostica[]" value="0"/>
															<input type="hidden" class="hidden-diagnostica" name="diagnosticaName" value="{$context/DiagnosticsOther}"/>
															<span>
																<xsl:value-of select="$context/DiagnosticsOther"/>
															</span>
														</xsl:when>
														<xsl:otherwise>
															<input type="hidden" class="diagnostica" name="subdiagnostica[]" value="-1"/>
															<input type="hidden" class="hidden-diagnostica" name="diagnosticaName" value=""/>
															<span>выбрать из списка</span>
														</xsl:otherwise>
													</xsl:choose>
												</span>
											</td>
										</tr>
										<tr height="38px">
											<td><span class="icon add"/></td>
											<td></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td valign="top" style="padding-top: 5px">Метро:</td>
								<td>
									<input name="shMetro" id="shMetro" class="inputForm" maxlength="100"
									       style="width: 100%">
										<xsl:attribute name="value">
											<xsl:for-each select="$context/MetroList/Metro">
												<xsl:value-of select="."/>
												<xsl:if test="position()!=last()">,</xsl:if>
											</xsl:for-each>
										</xsl:attribute>
									</input>
									<br/>
									<div>
										<div class="closest-stations"></div>
										<xsl:if test="$context/CityId = '1' or $context/CityId = '2'">
											<div style="float:right; width: 24px; margin: 0 0 0 10px; height: 24px; background:url('/img/icon/map_add_24.png') no-repeat; cursor: pointer;"
											     onclick="metroMap()"/>
										</xsl:if>
									</div>
									<div id="metroMap">
										<xsl:choose>
											<xsl:when test="$context/CityId = '2'">
												<xsl:call-template name="metroScemSpb"/>
											</xsl:when>
											<xsl:when test="$context/CityId = '1'">
												<xsl:call-template name="metroScem"/>
											</xsl:when>
										</xsl:choose>

									</div>

								</td>
							</tr>

							<xsl:if test="($context/CityId != '1' and $context/CityId != '2') or (srvInfo/City/@id != '1' and srvInfo/City/@id != '2')">
								<tr>
									<td>Район:</td>
									<td class="district-filter">
										<xsl:call-template name="multi-select">
											<xsl:with-param name="param">shDistrict[]</xsl:with-param>
											<xsl:with-param name="withoutItem" />
											<xsl:with-param name="context" select="/root/dbInfo/DistrictList/Element"/>
										</xsl:call-template>
									</td>
								</tr>
							</xsl:if>

							<tr class="clinic-filter">
								<td>Клиника:</td>
								<td>
									<table width="100%" cellpadding="0" cellspacing="0">
										<tr>
											<td style="padding:0">
												<input id="shClinicName" maxlength="100" style="width: 100%; margin-right: 0" value="{$context/Clinic}" autocomplete="off" />
												<input type="hidden" name="shClinicId" id="shClinicId" value="{$context/Clinic/@id}" />
											</td>
											<td style="width: 16px">
												<span class="eraser clear-doctor" title="очистить поле" />
											</td>
										</tr>
									</table>
								</td>
							</tr>

							<tr class="doctor-filter">
								<td>Вызов на дом:</td>
								<td>
									<input type="checkbox" name="shHome" id="shHome" value="1">
										<xsl:if test="$context/IsGoHome = '1'">
											<xsl:attribute name="checked"/>
										</xsl:if>
									</input>
								</td>
							</tr>

							<tr class="doctor-filter">
								<td>Прием детей:</td>
								<td>
									<input type="checkbox" name="shKidsReception" id="shKidsReception" value="1">
										<xsl:if test="$context/KidsReception = '1'"><xsl:attribute name="checked"/></xsl:if>
									</input>
									<span style="margin: 0 5px 0 20px">от:</span>
									<input name="shKidsAgeFrom" id="shKidsAgeFrom" value="{$context/KidsAgeFrom}" style="width: 50px" class="inputForm" maxlength="5"/>
									лет
								</td>
							</tr>

							<tr class="doctor-filter">
								<td>ФИО врача:</td>
								<td>
									<table width="100%" cellpadding="0" cellspacing="0">
										<tr>
											<td style="padding:0">
												<input name="shDoctor" id="shDoctor" sclass="inputForm" maxlength="100"
												       style="width: 100%; margin-right: 0">
													<xsl:if test="$context/Doctor">
														<xsl:attribute name="value">
															<xsl:value-of select="$context/Doctor"/>
														</xsl:attribute>
													</xsl:if>
												</input>
											</td>
											<td style="width: 16px">
												<span class="eraser" title="очистить поле"
												      onclick="$('#shDoctor').val(''); getItemResultset()"/>
											</td>
										</tr>
									</table>
									<!-- <input type="hidden" name="shDoctorId" id="shDoctorId"/> -->
								</td>
							</tr>
							<tr class="doctor-filter">
								<td style="padding: 10px 0 5px 0">Другой врач:</td>
								<td style="padding: 10px 0 5px 0">
									<div class="checkboxLab">
										<span class="link" onclick="requestAction('anotheDoctor')">добавить врача</span>
									</div>
								</td>
							</tr>
							<tr class="doctor-filter">
								<td style="padding: 10px 0 0px 0">Врач:</td>
								<td style="padding: 10px 0 0px 0">

									<xsl:choose>
										<xsl:when test="$context/AgeSelector = 'child'">
											<img src="/img/icon/girl.png" title="Детский врач" align="absbottom"
											     style="margin-left: 4px"/>
											для ребенка
										</xsl:when>
										<xsl:when test="$context/AgeSelector = 'adult'">
											для взрослого
											<img src="/img/icon/adult_clinic.png" title="Взрослый" align="absbottom"
											     style="margin-left: 4px"/>
										</xsl:when>
										<xsl:otherwise>для взрослого (возраст не указан)</xsl:otherwise>
									</xsl:choose>

								</td>
							</tr>
							<tr class="doctor-filter">
								<td style="padding: 15px 0 0px 0">Дата приема:</td>
								<td style="padding: 15px 0 0px 0">

									<input name="doctorWorkDate" id="doctorWorkDate" style="width:70px" maxlength="12" value="" onchange="this.focus()" />
									c <input name="doctorWorkHour" id="doctorWorkHour" style="width:20px;" maxlength="2" value="" onfocus="this.select()" onkeyup="if ($(this).val().length == 2) $('#doctorWorkMin').focus()" />
									:
									<input name="doctorWorkMin" id="doctorWorkMin" style="width:20px" maxlength="2" value="" onfocus="this.select()" />
									до
									<input name="doctorWorkToHour" id="doctorWorkToHour" style="width:20px;" maxlength="2" value="" onfocus="this.select()" onkeyup="if ($(this).val().length == 2) $('#doctorWorkToMin').focus()" />
									:
									<input name="doctorWorkToMin" id="doctorWorkToMin" style="width:20px" maxlength="2" value="" onfocus="this.select()" />

									<div class="eraser" title="очистить поле" onclick="$('#doctorWorkDate, #doctorWorkHour, #doctorWorkMin, #doctorWorkToHour, #doctorWorkToMin').val('');getItemResultset();" />

								</td>
							</tr>
							<tr>
								<td></td>
								<td style="padding: 10px 0 5px 0">
									<div class="checkboxLab">
										<span class="link clear-filters">сбросить фильтры</span>
									</div>
								</td>
							</tr>
						</table>

						<table id="tr-diagnostics-donor" style="display:none">
							<tr class="clinic-filter tr-diagnostics" style="height:30px;display:none;">
								<td colspan="2">
									<table>
										<tr>
											<td style="vertical-align: middle;">Диагностика:</td>
											<td>
												<span class="link diagnosticaText">
													<input type="hidden" class="diagnostica" name="multiple_diagnostics[]" value="-1"/>
													<input type="hidden" class="hidden-diagnostica"  value=""/>
													<span>выбрать из списка</span>
												</span>
											</td>
										</tr>
										<tr>
											<td>
												<span class="icon add"/>
												<span class="icon minus"/>
											</td>
											<td>
												<div class="checkboxLab">
													<input name="apDate" style="width:70px" maxlength="12"/>
													<input name="apHour" style="width:20px; margin-left: 8px" maxlength="2"/>
													:
													<input name="apMin" style="width:20px" maxlength="2"/>
													<div class="eraser" title="очистить поле" onclick="$('input',$(this).closest('div.checkboxLab')).val('');"/>
												</div>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>

						<xsl:if test="$context/ClientComment and $context/ClientComment != ''">
							<div style="margin-top: 5px">
								<u>Комментарий пациента:</u>
								<xsl:value-of select="$context/ClientComment"/>
							</div>
						</xsl:if>

					</div>
				</div>
			</div>
			<div class="doctor-list">
				<div id="searchItemResultset" class="scroll-pane wb">
					<div id="doctorSchedule" class="doctor-schedule">
						<div id="schedulePeriod">

						</div>

						<div id="scheduleSlots">
                            думаю......
						</div>

					</div>
					<div id="searchItemResultsetBlock" class="doctor-result-block">
						<div id="searchItemResultsetPane"></div>
					</div>

				</div>

			</div>

		</div>

	</xsl:template>

	<xsl:template name="ceilInfo">
		<xsl:param name="id"/>

		<style>
			#ceilWin_<xsl:value-of select="$id"/> input	{height: auto;}
		</style>
		<div id="ceilWin_{$id}" class="m0 shd infoEltR hd" style="width: 450px;">
			<xsl:for-each select="dbInfo/DiagnosticList/Element">
                <xsl:variable name="parentName" select="Name"/>
				<div class="mb10 checkBox4Text">
					<xsl:choose>
						<xsl:when test="count(DiagnosticList/Element) = 0">
							<label>
								<input class="checkBox4Text checkbox-diagnostic" type="Checkbox" value="{@id}" autocomplete="off">

								<xsl:if test="/root/dbInfo/Request/DiagnosticsId = @id">
										<xsl:attribute name="checked"/>
									</xsl:if>
								</input>
								<xsl:value-of select="Name"/>
							</label>
						</xsl:when>
						<xsl:otherwise>
							<span class="pnt link" onclick="$('#subPopupList_{@id}').toggle()">
								<xsl:value-of select="Name"/>
								(<xsl:value-of select="count(DiagnosticList/Element)"/>)
							</span>
						</xsl:otherwise>
					</xsl:choose>
				</div>
				<xsl:if test="DiagnosticList/Element">
					<div id="subPopupList_{@id}" class="hd ml20">
						<xsl:for-each select="DiagnosticList/Element[not(@id = /root/dbInfo/Clinic/Diagnostics/Element/@id)]">
							<div class="mb5 checkBox4Text">
								<label data-parent="{$parentName}">
									<input class="checkBox4Text checkbox-diagnostic" type="Checkbox" value="{@id}" autocomplete="off">

									<xsl:if test="/root/dbInfo/Request/DiagnosticsId = @id">
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

			<div class="mb5">
				<label>
					<input class="checkBox4Text" id="subdiagnostica_0" type="Checkbox" value="0" autocomplete="off">

					<xsl:if test="/root/dbInfo/Request/DiagnosticsId = '0'">
							<xsl:attribute name="checked"/>
						</xsl:if>
					</input>
					Другое
				</label>
				<input id="diagnosticaName" style="width: 250px; margin-left: 10px" type="text" autocomplete="off" />
				<span class="form ml20 addNewDiagnostic" style="width:30px;">ОК</span>
			</div>


			<div class="closeButton4Window" title="закрыть" />
		</div>
	</xsl:template>

</xsl:transform>

