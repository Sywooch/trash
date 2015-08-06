<?xml version='1.0'  encoding="UTF-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:output method="html" encoding="utf-8"/>

	<xsl:template name="doctorAddress">

		<div class="doctor_address_item">

			<xsl:if test="/root/srvInfo/Conf/ShowClinicName = '1'">
				<div class="doctor_address_clinic t-fs-n i-address-doctor">
					<xsl:if test="position() > 1">
						<xsl:attribute name="class">doctor_address_clinic t-fs-n i-address-doctor i-ext-address</xsl:attribute>
					</xsl:if>
					<xsl:value-of select="Name"/>
				</div>
			</xsl:if>

			<div class="t-fs-s">
				<xsl:if test="/root/srvInfo/Conf/ShowClinicName = '0'">
					<xsl:choose>
						<xsl:when test="count(Schedule/Element) &gt; 0">
							<xsl:attribute name="class">t-fs-s i-address-doctor doctor_address_schedule</xsl:attribute>
							<xsl:if test="position() > 1">
								<xsl:attribute name="class">t-fs-s i-address-doctor i-ext-address doctor_address_schedule</xsl:attribute>
							</xsl:if>
						</xsl:when>
						<xsl:otherwise>
							<xsl:attribute name="class">t-fs-s i-address-doctor</xsl:attribute>
							<xsl:if test="position() > 1">
								<xsl:attribute name="class">t-fs-s i-address-doctor i-ext-address</xsl:attribute>
							</xsl:if>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:if>
				<xsl:if test="string-length(ClinicAddress) > 5">
					<span class="clinic-address"><xsl:value-of select="ClinicAddress"/></span>
				</xsl:if>
				<xsl:choose>
					<xsl:when test="count(StationList/Element) &gt; 0">
						<xsl:for-each select="StationList/Element">
							<div class="metro_item">
								<a href="/doctor/{/root/srvInfo/SearchParams/SelectedSpeciality/Alias}/{./Alias}"
								   class="">
									<xsl:attribute name="class">
										metro_link metro_line_<xsl:value-of select="LineId"/>
									</xsl:attribute>
									<xsl:choose>
										<xsl:when test="/root/srvInfo/SearchParams/SelectedSpeciality/Alias !=''">
											<xsl:attribute name="href">
												/doctor/<xsl:value-of
													select="/root/srvInfo/SearchParams/SelectedSpeciality/Alias"/>/<xsl:value-of
													select="./Alias"/>
											</xsl:attribute>
										</xsl:when>
										<xsl:otherwise>
											<xsl:attribute name="href">
												/doctor/<xsl:value-of
													select="key('spec', ../../../../Specialities/Element[position() = 1]/Id)/RewriteName"/>/<xsl:value-of
													select="./Alias"/>
											</xsl:attribute>
										</xsl:otherwise>
									</xsl:choose>
									<xsl:value-of select="./Name"/>
									<xsl:if test="./Distance &gt; 0">
										<span class="metro_link_dist t-fs-xs">
											<xsl:choose>
												<xsl:when test="./Distance &lt; 1000">
													(<xsl:value-of select="./Distance"/> м)
												</xsl:when>
												<xsl:otherwise>
													(<xsl:value-of select="format-number(./Distance div 1000, '#.0')"/> км)
												</xsl:otherwise>
											</xsl:choose>
										</span>
									</xsl:if>
								</a>
								<xsl:if test="position() != last()">,</xsl:if>
							</div>
						</xsl:for-each>
					</xsl:when>
					<xsl:otherwise>

					</xsl:otherwise>
				</xsl:choose>
			</div>

			<xsl:if test="count(Schedule/Element) &gt; 0 and /root/srvInfo/IsMobile = 0">
				<div class="schedule_doctor_wrap">
					<div class="schedule_doctor_tab">
						<span class="schedule_doctor_tab_ico l-ib"></span><span class="l-ib schedule_doctor_tab_txt t-fs-xs">Время работы</span>
					</div>
					<div class="schedule_doctor_slider_wrap schedule_doctor_slider_loading">
						<ul class="schedule_doctor_slider">
							<xsl:for-each select="Schedule/Element">
								<li class="l-ib schedule_doctor_slider_item">

									<div class="schedule_doctor_slider_day"><xsl:value-of select="Day"/></div>

									<div class="schedule_doctor_slider_time">
										<xsl:choose>
											<xsl:when test="Work = '1'">
												<span class="schedule_doctor_slider_txt l-ib">с</span>
												<a href="#" data-stat="btnCardShortScheduleOnline">
													<xsl:attribute name="data-doctor">
														<xsl:value-of select="DoctorId" />
													</xsl:attribute>
													<xsl:attribute name="data-clinic">
														<xsl:value-of select="ClinicId" />
													</xsl:attribute>
													<xsl:attribute name="data-date">
														<xsl:value-of select="Date" />
													</xsl:attribute>
													<xsl:value-of select="Begin"/>
												</a>
												<br/>
												<span class="schedule_doctor_slider_txt l-ib">до</span>
												<a href="#" data-stat="btnCardShortScheduleOnline">
													<xsl:attribute name="data-doctor">
														<xsl:value-of select="DoctorId" />
													</xsl:attribute>
													<xsl:attribute name="data-clinic">
														<xsl:value-of select="ClinicId" />
													</xsl:attribute>
													<xsl:attribute name="data-date">
														<xsl:value-of select="Date" />
													</xsl:attribute>
													<xsl:value-of select="End"/>
												</a>
											</xsl:when>
											<xsl:otherwise>
												<div class="schedule_doctor_slider_holiday">Выходной<br/> день</div>
											</xsl:otherwise>
										</xsl:choose>
									</div>

								</li>
							</xsl:for-each>
						</ul>
					</div>

				</div>
			</xsl:if>

		</div>

	</xsl:template>

</xsl:transform>

