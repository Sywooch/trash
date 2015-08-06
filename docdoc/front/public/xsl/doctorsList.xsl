<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:import href="../lib/xsl/common.xsl"/>
	<xsl:import href="../xsl/priceShow.xsl"/>
	<xsl:import href="../xsl/doctorAddress.xsl"/>
	<xsl:output method="html" encoding="utf-8"/>

	<xsl:key name="spec" match="/root/dbInfo/SectorList/Element" use="@id"/>

	<xsl:template name="doctorShortCard">
		<xsl:param name="context"/>
		<!-- we pass here a doctor node -->

		<article class="doctor_card">

			<div class="doctor_person">
				<a href="/doctor/{$context/Alias}" class="doctor_img_link">
					<img src="/img/doctorsNew/{$context/MedImg}" class="doctor_img" />
				</a>

			</div>
			<div class="doctor_info">
				<h2 class="doctor_name">
					<a href="/doctor/{$context/Alias}">
						<span class="doctor_name_word">
							<xsl:value-of select="substring-before($context/Name, ' ')"/>
						</span><br/>
						<xsl:value-of select="substring-after($context/Name, ' ')"/>
					</a>
				</h2>

				<div class="doctor_info_aside">
					<div class="doctor_rating_wrap">
						<xsl:choose>
							<xsl:when test="$context/ReviewsCount = 0">
								<div class="reviews_count reviews_counter_no">
									<a href="/doctor/{$context/Alias}#reviews" class="reviews_counter">нет</a>
									<a href="/doctor/{$context/Alias}#reviews" class="reviews_counter_text">отзывов</a>
								</div>
							</xsl:when>
							<xsl:otherwise>
								<!-- добавлять класс reviews_counter_no когда нет отзывов, блок не скрывать -->
								<div class="reviews_count">
									<a href="/doctor/{$context/Alias}#reviews" class="reviews_counter">
										<xsl:value-of select="$context/ReviewsCount"/>
									</a>
									<a href="/doctor/{$context/Alias}#reviews" class="reviews_counter_text">
										<xsl:call-template name="digitVariant">
											<xsl:with-param name="digit" select="$context/ReviewsCount"/>
											<xsl:with-param name="one" select="'отзыв'"/>
											<xsl:with-param name="two" select="'отзыва'"/>
											<xsl:with-param name="five" select="'отзывов'"/>
										</xsl:call-template>
									</a>
								</div>
							</xsl:otherwise>
						</xsl:choose>

						<xsl:if test="$context/EditedRating2 != ''">
							<div class="doctor_rating js-tooltip-tr"
							     title="Рейтинг врача сформирован на основании следующих показателей: образование, опыт работы, научная степень.">
								<p class="doctor_rating_numbers">
									<span class="doctor_rating_main">
										<xsl:value-of select="$context/EditedRating2/Element/BeforeDot"/>
									</span>
									<span class="doctor_rating_sub">.<xsl:value-of
											select="$context/EditedRating2/Element/AfterDot"/>
									</span>
								</p>
								<span class="doctor_rating_disclaimer">
									рейтинг
								</span>
							</div>
						</xsl:if>
					</div>
					<xsl:choose>
						<xsl:when test="number($context/Status) = 4 or number($context/ClinicStatus) = 4">
							<div class="doctor_desc__request">
								<p class="request_unaviable">Уважаемые посетители, в настоящий момент запись к данному
									врачу ограничена.
									<br/>
									Вы можете
									<a href="/doctor/{$context/SpecList/Element/Alias}">выбрать из доступных</a>
									<xsl:value-of select="$context/SpecList/Element/NameInGenitive"/> или записаться по
									телефону <!-- phone number -->
								</p>
							</div>
						</xsl:when>
						<xsl:otherwise>
							<form class="doctor_desc__request" method="post" action="/request?doctor={$context/@id}">
								<input type="hidden" name="doctor" value="{$context/@id}"/>
								<input type="hidden" name="clinicId" value="{$context/ClinicId}"/>
								<input
								       type="submit" value="Запись на приём"
								       data-doctor="{$context/Id}"
									   data-clinic="{$context/ClinicId}"
									   data-doctor-name="{$context/Name}"
								       data-clinic-name="{$context/ClinicName}"
								       data-clinic-metro="{$context/Stations/Element/Name}"
								       data-doctor-id="{$context/@id}"
								       data-doctor-reviews="{$context/ReviewsCount}"
								       data-doctor-rating="{$context/EditedRating2/Element/Value}"
								       data-doctor-experience="{$context/Experience}"
								       data-doctor-awards="{$context/Category}"
								       data-doctor-price="{$context/Price}"
								       data-doctor-special-price="{$context/SpecialPrice}"
								       data-doctor-image="{$context/Image}"
								       data-doctor-spec="{$context/Spec}"
								       data-popup-id="js-popup-request"
								       data-popup-width="440"
								       data-request-type="doctor"
									   id="btn_{$context/Id}">
									<xsl:choose>
										<xsl:when
												test="$context/Phone/Digit !='' and (/root/srvInfo/Conf/ShowClinicPhone = '1' or /root/srvInfo/IsLandingPage)">
											<xsl:attribute name="data-request-tel">
												<xsl:value-of select="$context/Phone/Text"/>
											</xsl:attribute>
											<xsl:attribute name="data-request-tel-digit">+<xsl:value-of
													select="$context/Phone/Digit"/>
											</xsl:attribute>
										</xsl:when>
										<xsl:otherwise>
											<xsl:attribute name="data-request-tel">+7
												<xsl:value-of select="/root/dbHeadInfo/Phone/Short"/>
											</xsl:attribute>
											<xsl:attribute name="data-request-tel-digit">
												<xsl:value-of select="/root/dbHeadInfo/Phone/Numerically"/>
											</xsl:attribute>
										</xsl:otherwise>
									</xsl:choose>
									<xsl:attribute name="data-clinic-id">
										<xsl:value-of select="$context/ClinicId"/>
									</xsl:attribute>
									<xsl:choose>
										<xsl:when test="$context/CanOnlineBooking = 1 and $context/ClinicCount = 1">
											<xsl:attribute name="data-stat">btnCardShortDoctorOnline</xsl:attribute>
											<xsl:attribute name="class">ui-btn ui-btn_green request-button request-online</xsl:attribute>
										</xsl:when>
										<xsl:otherwise>
											<xsl:attribute name="data-stat">btnCardShortDoctor</xsl:attribute>
											<xsl:attribute name="class">ui-btn ui-btn_green js-request-popup js-popup-tr request-button</xsl:attribute>
										</xsl:otherwise>
									</xsl:choose>
								</input>
								<input type="hidden" name="requestBtnType" value="requestCardShortDoctor"/>

								<xsl:if test="/root/dbHeadInfo/IsAbTest != -1 and $context/Phone/Digit !='' and (/root/srvInfo/Conf/ShowClinicPhone = '1' or /root/srvInfo/IsLandingPage)">
									<div class="request_tel">
										<p class="request_tel_text">
											или по телефону
										</p>
										<span class="request_tel_number clinic_phone_{$context/ClinicId}">
											<xsl:choose>
												<xsl:when test="/root/srvInfo/IsMobile = '1'">
													<a class="request_tel_call" href="tel:+{$context/Phone/Digit}">
														<xsl:value-of select="$context/Phone/Text"/>
													</a>
												</xsl:when>
												<xsl:otherwise>
													<xsl:value-of select="$context/Phone/Text"/>
												</xsl:otherwise>
											</xsl:choose>
										</span>
									</div>
								</xsl:if>
							</form>
						</xsl:otherwise>
					</xsl:choose>

					<xsl:if test="$context/Tips/Message != ''">
						<div class="tips_message" style="color: #289B4C;">
							<xsl:if test="$context/Tips/Color != ''">
								<xsl:attribute name="style">
									color: <xsl:value-of select="$context/Tips/Color"/>;
								</xsl:attribute>
							</xsl:if>
							<xsl:value-of select="$context/Tips/Message"/>
						</div>
					</xsl:if>
				</div>

				<div class="doctor_card_info_wrap">
					<p class="mvn t-fs-s">
						<xsl:for-each select="$context/Specialities/Element">
							<xsl:choose>
								<xsl:when test="position() != last()">
									<xsl:value-of select="./Name"/>,
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="./Name"/>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:for-each>
					</p>

					<p class="mbm mtn t-fs-s">
						<xsl:if test="$context/Experience != 'нет'">
							Стаж <xsl:value-of select="$context/Experience"/>
						</xsl:if>
						<xsl:if test="$context/Experience != 'нет' and (string-length($context/Category) &gt; 0 or string-length($context/Degree) &gt; 0)"> / </xsl:if>
						<xsl:if test="string-length($context/Category) &gt; 0">
							<xsl:value-of select="$context/Category"/>
							<xsl:if test="string-length($context/Degree) &gt; 0">,
							</xsl:if>
						</xsl:if>
						<xsl:if test="string-length($context/Degree) &gt; 0">
							<xsl:value-of select="$context/Degree"/>
						</xsl:if>
					</p>

					<p class="strong">
						<xsl:call-template name="priceShow">
							<xsl:with-param name="specialPrice" select="$context/SpecialPrice"/>
							<xsl:with-param name="price" select="$context/Price"/>
						</xsl:call-template>
					</p>

					<p class="doctor_desc t-fs-s" data-ellipsis-height="40">
						<xsl:value-of select="$context/Description"/>
					</p>
				</div>
			</div>

			<div class="doctor_address_wrap doctor_address_dotted">
				<xsl:for-each select="$context/ClinicList/Element">
					<xsl:if test="position() &lt; 3">
						<xsl:call-template name="doctorAddress"></xsl:call-template>
					</xsl:if>
				</xsl:for-each>
				<xsl:if test="count($context/ClinicList/Element) > 2">
					<div class="doctor_address_hide">
						<xsl:for-each select="$context/ClinicList/Element">
							<xsl:if test="position() &gt; 2">
								<xsl:call-template name="doctorAddress"></xsl:call-template>
							</xsl:if>
						</xsl:for-each>
					</div>
					<span class="doctor_address_switch_show">Показать все</span>
				</xsl:if>
			</div>
		</article>

	</xsl:template>


</xsl:transform>