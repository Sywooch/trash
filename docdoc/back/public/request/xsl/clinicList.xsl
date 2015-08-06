<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:output method="html" encoding="utf-8" />

	<xsl:template match="/">
		<xsl:apply-templates select="root" />
	</xsl:template>

	<xsl:template match="root">
		<xsl:variable name="tdCount" select="12" />

		<xsl:variable name="shClinicId">
			<xsl:choose>
				<xsl:when test="/root/srvInfo/ClinicId and /root/srvInfo/ClinicId != ''"><xsl:value-of select="/root/srvInfo/ClinicId"/></xsl:when>
				<xsl:when test="/root/srvInfo/ClinicId and count(/root/dbInfo/ClinicList/Element[@id = /root/srvInfo/ClinicId]) = 1"><xsl:value-of select="dbInfo/ClinicList/Element[@id = /root/srvInfo/ClinicId]/Clinic/@id"/></xsl:when>
			</xsl:choose>
		</xsl:variable>

		<table cellpadding="0" cellspacing="1" width="100%" border="0" class="resultSet">
			<tr>
				<th>#</th>
				<th>Клиника</th>
				<th>Метро</th>
				<th>Адрес</th>
				<th>Телефон</th>
				<th title="Приоритет в выдаче">Пр.</th>
				<th>#</th>
				<th>##</th>
			</tr>
			<xsl:choose>
				<xsl:when test="dbInfo/ClinicList">
					<xsl:for-each select="dbInfo/ClinicList/Element">
						<xsl:variable name="class">
							<xsl:choose>
								<xsl:when test="(position() div 2) - floor(position() div 2) &gt; 0">odd</xsl:when>
								<xsl:otherwise>even</xsl:otherwise>
							</xsl:choose>
						</xsl:variable>
						<tr id="tr_{@id}" class="{$class}" backclass="{$class}" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','{$class}')">
							<td>
								<xsl:variable name="id" select="@id"/>
								<input id="selectedClinic_{@id}" type="checkbox" name="selectedClinic" value="{@id}" onclick="checkStatus(this)" class="clinicList" clinicId="{@id}">

									<xsl:if test="@id = /root/srvInfo/ClinicId or (@id = /root/srvInfo/ClinicId and count(/root/dbInfo/ClinicList/Element[@id = $id]) = 1)">
										<xsl:attribute name="checked" />
									</xsl:if>
								</input>
							</td>
							<td>
								<a href="http://diagnostica.docdoc.ru/kliniki/{Alias}/" target="_blank" title="Анкета клиники">
									<xsl:value-of select="Name" />
								</a>
							</td>
							<td>
								<xsl:for-each select="StationList/Element">
									<xsl:value-of select="." />
									<xsl:if test="position() != last()">, </xsl:if>
								</xsl:for-each>
							</td>
							<td><xsl:value-of select="Address" /></td>
							<td nowrap="">
								<xsl:for-each select="PhoneList/Element">
									<xsl:value-of select="PhoneFormat" />
									<xsl:if test="position() != last()">
										<br />
									</xsl:if>
								</xsl:for-each>
							</td>
							<td><xsl:value-of select="Priority" /></td>
							<td>
								<div onclick="toogleMarker(this, '{@id}')">
									<xsl:attribute name="class">
										<xsl:choose>
											<xsl:when test="/root/srvInfo/ClinicList/ClinicId = @id">null marker1 markerActive</xsl:when>
											<xsl:when test="/root/srvInfo/ClinicId = @id">null marker1 markerActive</xsl:when>
											<xsl:otherwise>null marker1 markerPassive</xsl:otherwise>
										</xsl:choose>
									</xsl:attribute>
								</div>
								<input type="checkbox" name="clinicList[{@id}]" id="clinicList_{@id}"
								       value="{@id}" class="hd">
									<xsl:if
											test="/root/srvInfo/ClinicList/ClinicId = @id or @id = /root/srvInfo/ClinicId">
										<xsl:attribute name="checked" />
									</xsl:if>

								</input>
							</td>
							<td>
								<xsl:variable name="line" select="position()"/>
								<xsl:variable name="clinicId" select="@id"/>
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
	</xsl:template>

</xsl:transform>

