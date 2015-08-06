<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	version="1.0">

	<xsl:output method="html" encoding="utf-8" />


	<xsl:template match="/">
		<style>
			.marker1 {width: 16px !important; height: 16px !important; cursor: pointer}
			.markerPassive {background:
			url('/img/icon/marker_passive.png') no-repeat 0 0;}
			.markerActive
			{background: url('/img/icon/marker_active_red.png') no-repeat 0 0;}
		</style>
		<xsl:apply-templates select="root" />
		<script>

			<xsl:choose>
				<xsl:when
					test="/root/dbInfo/DoctorList/Element[@id = /root/srvInfo/DoctorId]/Clinic/@id != ''">
					var clinicId =
					<xsl:value-of
						select="/root/dbInfo/DoctorList/Element[@id = /root/srvInfo/DoctorId]/Clinic/@id" />
					;
				</xsl:when>
				<xsl:otherwise>
					var clinicId = "";
				</xsl:otherwise>
			</xsl:choose>


			$("div.helper, span.helper").mouseover( function() {
				$(this).stop(true).delay(300).children().show();
			});
			$("div.helper, span.helper").mouseleave( function() {
				$(".helpEltR").hide();
			});
		</script>
	</xsl:template>




	<xsl:template match="root">
		<xsl:variable name="tdCount" select="12" />

		<table cellpadding="0" cellspacing="1" width="100%" border="0" class="resultSet">
			<tr>
				<th>#</th>
				<th>Врач (ФИО)</th>
				<th>Специализация</th>
				<th colspan="3">Клиника</th>
				<th>Метро</th>
				<th>Стаж</th>
				<th title="Стоимость приема">
					Стоим.
					<!-- <div class="null" style="width: 16px; height: 16px; background:url('/img/icon/currency-ruble.png') 
						no-repeat"/> -->
				</th>
				<!-- <th title="Дополнительный номер"><div class="null" style="width: 
					16px; height: 16px; background:url('/img/icon/phone.gif')"/></th> -->
				<th title="Стоимость приема со скидкой">Со скидкой</th>
				<th title="Рейтинг">Рейт.</th>
				<th>#</th>
				<xsl:if test="/root/srvInfo/TypeView = 'call_center'"><th>##</th></xsl:if>
			</tr>
			<xsl:choose>
				<xsl:when test="dbInfo/DoctorList/Element">
					<xsl:for-each select="dbInfo/DoctorList/Element">
						<xsl:variable name="class">
							<xsl:choose>
								<xsl:when test="(position() div 2) - floor(position() div 2) &gt; 0">odd</xsl:when>
								<xsl:otherwise>even</xsl:otherwise>
							</xsl:choose>
						</xsl:variable>
						<xsl:variable name="classWarn">
							<xsl:choose>
								<xsl:when test="Status = 7"> warn</xsl:when>
							</xsl:choose>
						</xsl:variable>
						<tr id="tr_{@id}" class="{$class}{$classWarn}" backclass="{$class}" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','{$class}{$classWarn}')">
							<td>
								<xsl:variable name="id" select="@id"/>
								<input id="selectedDoctor_{@id}" type="checkbox" name="selectedDoctor" value="{@id}" clinicId="{Clinic/@id}">

                                    <xsl:attribute name="class">doctorList</xsl:attribute>

									<xsl:if test="
												@id = /root/srvInfo/DoctorId 
												and 
												Clinic/@id = /root/srvInfo/ClinicId
												or (
													@id = /root/srvInfo/DoctorId
													and
													count(/root/dbInfo/DoctorList/Element[@id = $id]) = 1
												)
												">
										<xsl:attribute name="checked" />
									</xsl:if>
								</input>

                                <xsl:choose>
                                    <xsl:when test="HasSlots > 0">
                                        <br/>
                                        <img class="icon-schedule-period" data-doctor="{@id}" data-clinic="{Clinic/@id}">

                                        <xsl:choose>
                                            <xsl:when test="CanBooking > 0">
                                                <xsl:attribute name="class">icon-schedule-period with-schedule-slots</xsl:attribute>
                                                <xsl:attribute name="src">/img/icon/slot_booking.png</xsl:attribute>
                                            </xsl:when>
                                            <xsl:otherwise>
                                                <xsl:attribute name="class">icon-schedule-period with-schedule-period</xsl:attribute>
                                                <xsl:attribute name="src">/img/icon/slot_not_booking.png</xsl:attribute>
                                            </xsl:otherwise>
                                        </xsl:choose>

                                        </img>


                                    </xsl:when>
                                </xsl:choose>

								<!-- 
								<xsl:value-of select="@id"/>-
								<xsl:value-of select="Clinic/@id"/>-
								<xsl:value-of select="count(/root/dbInfo/DoctorList/Element[@id=$id])"/>
								 -->
							</td>
							<td>
								<!-- <xsl:if test="Status != ''"> <img src="/img/icon/status_{Status}.png" 
									style="margin: 0 5px 0 0" align="absbottom"/> </xsl:if> -->
								<div style="margin:0">
									<div style="float:left; margin: 0">
										<xsl:choose>

                                            <xsl:when test="Alias and Alias != '' ">
                                                <a href="{/root/dbInfo/DoctorHref}{Alias}" target="_blank" title="Карточка врача">
                                                    <span class="txt13">
                                                        <xsl:value-of select="Name" />
                                                    </span>
                                                </a>
                                            </xsl:when>

											<xsl:otherwise>
												<span class="txt13">
													<xsl:value-of select="Name" />
												</span>
											</xsl:otherwise>
										</xsl:choose>
									</div>
									<xsl:if test="OperatorOpenComment != ''">
										<div class="helper" style="float:left; margin: 0 0 0 5px">
											<div class="helpEltR hd" style="width: 200px; margin:0; text-align: left;">
												<xsl:copy-of select="OperatorOpenComment"/>
											</div>
											<div class="helpMarker"><img src="/img/icon/note.png"/></div>
										</div>
									</xsl:if>


								</div>
							</td>
							<td>
								<xsl:for-each select="SectorList/Sector">
									<xsl:value-of select="." />
									<xsl:if test="position() != last()">
										,
									</xsl:if>
								</xsl:for-each>
							</td>
							<td>
								<xsl:if test="Clinic/@status != '3'">
									<span class="i-status blocked"/>
								</xsl:if>
								<xsl:choose>
									<xsl:when test="ClinicUrl != ''">
										<a href="http://{ClinicUrl}" target="_blank" title="Сайт клиники">
											<xsl:value-of select="Clinic" />
										</a>
									</xsl:when>
									<xsl:otherwise><xsl:value-of select="Clinic" /></xsl:otherwise>
								</xsl:choose>
							</td>
							<td nowrap="">
								<xsl:for-each select="PhoneList/Element">
									<xsl:value-of select="PhoneFormat" />
									<xsl:if test="position() != last()">
										<br />
									</xsl:if>
								</xsl:for-each>
							</td>
							<td align="center" style="padding: 2px">
								<xsl:if test="ClinicAddress != ''">
									<div class="helper" style="margin:0; width: 16px">
										<div class="helpEltR hd"
											style="width: 200px; margin:0 0 0 30px; text-align: left;">
											<xsl:value-of select="ClinicAddress" />
										</div>
										<div class="helpMarker" style="width: 16px">
											<img src="/img/icon/information.png" />
										</div>
									</div>
								</xsl:if>
							</td>
							<td>
								<xsl:for-each select="StationList/Element">
									<xsl:value-of select="." />
									<xsl:if test="position() != last()">
										,
									</xsl:if>
								</xsl:for-each>
							</td>
							<td align="center">
								<xsl:value-of select="Experience" />
							</td>
							<td align="center">
								<xsl:value-of select="Price" />
							</td>
							<td align="center">
								<xsl:value-of select="SpecialPrice" />
							</td>
							<!-- <td align="center"> <xsl:value-of select="AddNumber"/> </td> -->
							<td align="center">
								<xsl:value-of select="Rating" />
							</td>
							<td>
								<div onclick="toogleMarker(this, '{@id}')">
									<xsl:attribute name="class">
										<xsl:choose>
											<xsl:when test="/root/srvInfo/DoctorList/DoctorId = @id">null marker1 markerActive</xsl:when>
											<xsl:when test="/root/srvInfo/DoctorId = @id">null marker1 markerActive</xsl:when>
											<xsl:otherwise>null marker1 markerPassive</xsl:otherwise>
										</xsl:choose>
									</xsl:attribute>
								</div>
								<input type="checkbox" name="doctorList[{@id}]" id="doctorList_{@id}"
									value="{@id}" class="hd">
									<xsl:if
										test="/root/srvInfo/DoctorList/DoctorId = @id or @id = /root/srvInfo/DoctorId">
										<xsl:attribute name="checked" />
									</xsl:if>

								</input>
							</td>
							<xsl:if test="/root/srvInfo/TypeView = 'call_center'">
								<td>
									<xsl:if test="Clinic/@status = '3'">
										<xsl:variable name="line" select="position()"/>
	                                    <xsl:variable name="clinicId" select="Clinic/@id"/>
										<xsl:if test="PhoneList/Element">
											<div id="btTransfer_{$line}" class="form" style="padding: 3px 10px 2px 10px; margin: 0;">
												<xsl:attribute name="onclick">
													<xsl:choose>
														<xsl:when test="count(PhoneList/Element) &gt; 1">
															$('#ceilWin_<xsl:value-of select="position()" />').show();
														</xsl:when>
														<xsl:otherwise>
															requestTransfer('<xsl:value-of select="PhoneList/Element/Phone" />','<xsl:value-of select="$line" />','<xsl:value-of select="concat($line,'_1')" />','<xsl:value-of select="$clinicId" />')
														</xsl:otherwise>
													</xsl:choose>
												</xsl:attribute>
												ПЕРЕВОД
											</div>
											<span id="transfetStatus_{$line}"></span>
											<xsl:if test="count(PhoneList/Element) &gt; 1">
												<div class="ancor" style="float: right">
												<div id="ceilWin_{position()}" class="infoElt hd" style="width: 270px; padding: 10px">

													<b>Выберите телефон для перевода:</b>
													<table width="100%">
														<xsl:for-each select="PhoneList/Element">
															<tr>
																<td>
																	<xsl:value-of select="Label" />
																	:&#160;&#160;
																</td>
																<td>
																	<span class="link">
																		<xsl:attribute name="onclick">
																			requestTransfer('<xsl:value-of select="Phone" />','<xsl:value-of select="$line" />', '<xsl:value-of select="concat($line,'_',position())" />', '<xsl:value-of select="$clinicId" />')
																		</xsl:attribute>
																		<xsl:value-of select="PhoneFormat" />
																	</span>
																</td>
																<td>
																	<span id="transfetStatus_{$line}_{position()}"></span>
																</td>
															</tr>
														</xsl:for-each>
													</table>
													<img src="/img/common/clBtBig.gif" width="20" height="20" alt="закрыть" style="position: absolute; cursor: pointer; right: 5px; top: 5px;" title="закрыть" onclick="$('#ceilWin_{position()}').hide();" border="0" />
												</div>
											</div>
											</xsl:if>
										</xsl:if>
									</xsl:if>
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
	</xsl:template>

</xsl:transform>

