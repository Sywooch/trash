<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:import href="priceShow.xsl"/>
	<xsl:output method="html" encoding="utf-8"/>

	<xsl:decimal-format decimal-separator='.' grouping-separator=' ' NaN=' '/>

	<xsl:template match="/">
		<xsl:apply-templates select="root"/>
	</xsl:template>


	<xsl:template match="root">

		<xsl:variable name="doctor" select="/root/dbInfo/Doctor"/>
		<xsl:variable name="clinic" select="/root/dbInfo/Clinic"/>


		<main class="l-main l-wrapper ptl" role="main">

			<p class="i-goback mvn">
				<!--<a href="#" class="link_goback">
					Вернуться на страницу врача
				</a>-->
				<a class="link_goback" href="{srvInfo/RefURL}">
					Вернуться
					<xsl:choose>
						<xsl:when test="dbInfo/formKind = 'ClinicForm'">
							на страницу клиники
						</xsl:when>
						<xsl:when test="dbInfo/formKind = 'DoctorForm'">
							на страницу врача
						</xsl:when>
						<xsl:otherwise>
							назад
						</xsl:otherwise>
					</xsl:choose>
				</a>
				<!-- home url -->
			</p>

			<h1 class="page_request_title mvx">
				Запись на приём
				<xsl:choose>
					<xsl:when test="dbInfo/formKind = 'ClinicForm'">
						в клинику
					</xsl:when>
					<xsl:when test="dbInfo/formKind = 'DoctorForm'">
						к врачу
					</xsl:when>
					<xsl:otherwise>
						к врачу
					</xsl:otherwise>
				</xsl:choose>
			</h1>

			<form class="req_form m-main" method="post" action="/routing.php?r=request/save">


				<p class="req_form_row mvl">
					<label class="label">
						<span class="req_form_label i-required">Имя:</span>
						<input class="dd_input req_form_input" type="text" autofocus="true" placeholder=""
							   name="requestName"/>
					</label>
				</p>
				<p class="req_form_row mvl">
					<label class="label">
						<span class="req_form_label i-required">Телефон:</span>
						<input class="dd_input req_form_input js-mask-phone" type="text" placeholder=""
							   name="requestPhone"/>
					</label>
				</p>

				<input type="hidden" id="requestCityId" name="requestCityId" value="{/root/dbHeadInfo/City/@id}"/>
				<input type="hidden" name="redirectToThanks" value="1"/>
				<input type="hidden" name="formType" value="{/root/dbInfo/FormType}" />
				<input type="hidden" name="requestBtnType" value="{/root/dbInfo/requestBtnType}" />
				<xsl:choose>
					<xsl:when test="dbInfo/formKind = 'DoctorForm'">
						<input type="hidden" name="doctor" value="{$doctor/Id}"/>
						<input type="hidden" name="clinic" value="{$clinic/Id}"/>
					</xsl:when>
					<xsl:when test="dbInfo/formKind = 'ClinicForm'">
						<input type="hidden" name="clinic" value="{$clinic/Id}"/>
					</xsl:when>

					<xsl:otherwise>

						<input type="hidden" id="sector" data-control-id="select-spec" name="sector" value=""
							   class="js-choose-input-spec"/>
						<input type="hidden" id="stations" data-control-id="select-metro" name="stations" value=""/>
						<p class="req_form_row mvl">
							<label class="label">
								<span class="req_form_label i-required">Врач:</span>
								<!--<input class="dd_input req_form_input" type="text" placeholder="" name="requestName" />-->
								<!--
                                <input class="search_input search_input_spec dd_input req_form_input" type="text" placeholder="любой специальности" name="requestSpec"  />
                                <i class="search_list_spec js-popup-tr" data-popup-id="js-popup-speclist" data-popup-width="700"></i>
                                -->
								<xsl:choose>
									<xsl:when test="/root/srvInfo/IsMobile = '1'">
										<span class="search_input_imit search_input_imit_spec jsm-select"
											  data-select-related="select-spec" data-autosubmit="false">
											<select class="search_input_spec" name="spec" data-select="select-spec">
												<option value="" selected="selected"
														data-select-placeholder="любой специальности">
													любой специальности
												</option>
												<xsl:for-each select="/root/dbHeadInfo/SpecialityList/Group/Element">
													<option value="{./@id}">
														<xsl:value-of select="./Name"/>
													</option>
												</xsl:for-each>
											</select>
										</span>
									</xsl:when>
									<xsl:otherwise>
										<input class="search_input_spec dd_input req_form_input" type="text"
											   placeholder="любой специальности" name="requestSpec"/>
										<i class="search_list_spec js-popup-tr" data-popup-id="js-popup-speclist"
										   data-popup-width="700"></i>
									</xsl:otherwise>
								</xsl:choose>
							</label>

						</p>
						<p class="req_form_row mvl">
							<label class="label">
								<span class="req_form_label i-required">Метро:</span>

								<xsl:choose>
									<xsl:when test="/root/srvInfo/IsMobile = '1'">
										<span class="search_input_imit search_input_imit_geo jsm-select"
											  data-stat="btnPopupGeo" data-select-related="select-geo"
											  data-autosubmit="false">
											<select class="search_input_geo" data-select="select-geo" name="stations[]"
													multiple="multiple" size="1">
												<option value="0" selected="selected"
														data-select-placeholder="любом районе">любом районе
												</option>
												<xsl:for-each
														select="/root/dbHeadInfo/MetroMapData/MetroListByAZ/Element">
													<option value="{@id}">
														<xsl:value-of select="."/>
													</option>
												</xsl:for-each>

											</select>
										</span>
									</xsl:when>
									<xsl:otherwise>
										<input class="search_input search_input_geo dd_input req_form_input" type="text"
											   placeholder="любом районе" name="requestGeo"/>
										<i class="search_list_metro js-popup-tr s-dynamic" data-popup-id="js-popup-geo"
										   data-popup-width="920" data-stat="btnPopupGeo"></i>
									</xsl:otherwise>
								</xsl:choose>
								<!--
                                <input class="search_input search_input_geo dd_input req_form_input" type="text" placeholder="любом районе" name="requestGeo" />
                                <i class="search_list_metro js-popup-tr s-dynamic" data-popup-id="js-popup-geo" data-popup-width="920" data-stat="btnPopupGeo"></i>
                                -->
							</label>
						</p>

						<p class="req_form_row mvl">
							<span class="req_form_label">Пациент:</span>

							<label class="label_radio strong">
								<input type="radio" class="input_radio" name="requestAgeSelector" value="adult"
									   checked=""/>
								Взрослый
							</label>

							<label class="label_radio strong">
								<input type="radio" class="input_radio" name="requestAgeSelector" value="child"/>
								Ребенок
							</label>
						</p>

					</xsl:otherwise>
				</xsl:choose>

				<p class="req_form_row mvl">
					<span class="req_form_label s-hidden">Комментарий:</span>
					<label for="reqComment" class="label_textarea js-slidedown-tr ps">
						Добавить комментарий
					</label>
					<textarea id="reqComment" class="dd_input textarea req_textarea js-slidedown-ct"
							  name="requestComments"></textarea>
				</p>

				<p class="req_form_row mtl mbn">
					<span class="req_form_label s-hidden">Записаться:</span>
					<input class="req_submit ui-btn ui-btn_green" data-stat="" data-stat-trigger="" type="submit"
						   value="Записаться к врачу">
						<xsl:choose>
							<xsl:when test="/root/dbInfo/requestBtnType">
								<xsl:attribute name="data-stat">
									<xsl:value-of select="/root/dbInfo/requestBtnType"/>
								</xsl:attribute>
							</xsl:when>
							<xsl:otherwise>
								<xsl:attribute name="data-stat">
									<xsl:value-of select="/root/dbInfo/requestBtnType"/>
								</xsl:attribute>
							</xsl:otherwise>
						</xsl:choose>
						<xsl:attribute name="data-stat-trigger">
							<xsl:choose>
								<xsl:when test="/root/dbInfo/requestBtnType = 'requestCardFullDoctor'">
									<xsl:text>btnCardFullDoctor</xsl:text>
								</xsl:when>
								<xsl:when test="/root/dbInfo/requestBtnType = 'requestCardShortDoctor'">
									<xsl:text>btnCardShortDoctor</xsl:text>
								</xsl:when>
								<xsl:when test="/root/dbInfo/requestBtnType = 'requestCardFullClinic'">
									<xsl:text>btnCardShortClinic</xsl:text>
								</xsl:when>
								<xsl:when test="/root/dbInfo/requestBtnType = 'requestCardShortClinic'">
									<xsl:text>btnCardFullClinic</xsl:text>
								</xsl:when>
								<xsl:when test="/root/dbInfo/requestBtnType = 'requestSelectDoctor'">
									<xsl:text>btnSendSelectDoctor</xsl:text>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="/root/dbInfo/requestBtnType"/>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:attribute>
						<xsl:attribute name="value">
							<xsl:choose>
								<xsl:when test="dbInfo/formKind = 'ClinicForm'">Записаться в клинику</xsl:when>
								<xsl:when test="dbInfo/formKind = 'DoctorForm'">Записаться к врачу</xsl:when>
								<xsl:otherwise>Записаться к врачу</xsl:otherwise>
							</xsl:choose>
						</xsl:attribute>
					</input>
				</p>

				<!-- tel number -->
				<xsl:if test="$doctor/Phone/Digit !='' and /root/srvInfo/Conf/ShowClinicPhone = '1'">
					<span class="req_form_label s-hidden">Записаться</span>
					<div class="request_tel l-ib">
						<p class="request_tel_text">
							или по телефону:
						</p>
						<span class="request_tel_number">
							<xsl:choose>
								<xsl:when test="/root/srvInfo/IsMobile = '1'">
									<a class="request_tel_call" href="tel:+{$doctor/Phone/Digit}">
										<xsl:value-of select="$doctor/Phone/Text"/>
									</a>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="$doctor/Phone/Text"/>
								</xsl:otherwise>
							</xsl:choose>
						</span>
					</div>
				</xsl:if>
				<!-- tel number END -->

			</form>

			<xsl:choose>
				<xsl:when test="dbInfo/formKind = 'DoctorForm'">
					<xsl:call-template name="formRequestSlctdDoctor"/>
				</xsl:when>
				<xsl:when test="dbInfo/formKind = 'ClinicForm'">
					<xsl:call-template name="formRequestSlctdClinic"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:call-template name="formRequestDefault"/>
				</xsl:otherwise>
			</xsl:choose>

			<xsl:if test="dbHeadInfo/Phone/Short != ''">
			<h3 class="help_docdoc">
				Нужна помощь? Позвоните по тел.:
				<span class="comagic_phone call_phone_1">
					<xsl:if test="dbHeadInfo/IsMobile = '1'">
						<xsl:attribute name="href">
							tel:<xsl:value-of select="dbHeadInfo/Phone/Numerically"/>
						</xsl:attribute>
					</xsl:if>
					<xsl:value-of select="dbHeadInfo/Phone/Full"/>
				</span>
				<!--
				или напишите нам <a class="help_docdoc_link" href="mailto:service@docdoc.com">service@docdoc.com</a>
				-->
			</h3>
			</xsl:if>

		</main>


	</xsl:template>


	<!-- ************************************************
	*************** TEMPLATES DECLARATION ***************
	************************************************* -->

	<xsl:template name="formRequestDefault">
		<aside class="request_aside i-doctor_request">
			<h2>
				Подбор врача за 15 минут!
			</h2>
			<p>
				Вам нужен хороший специалист? Мы поможем найти врача, отвечающего вашим требования.
			</p>
			<p>
				Для этого вам необходимо оформить заявку и наши специалисты свяжутся с вами и предложат подходящего
				врача
				<span class="strong">в течение 15 минут</span>
			</p>

			<xsl:if test="dbHeadInfo/Phone/Short != ''">

			<p class="mbn">
				Также вы можете связаться с нами по телефону
			</p>
			<p class="request_phone_number mtn">
				<xsl:value-of select="dbHeadInfo/Phone/Full"/>
			</p>
			</xsl:if>
		</aside>

	</xsl:template>


	<xsl:template name="formRequestSlctdDoctor">

		<xsl:variable name="doctor" select="/root/dbInfo/Doctor"/>

		<article class="doctor_card m-short">

			<div class="doctor_person">
				<span class="doctor_img_link">
					<img src="/img/doctorsFull/{$doctor/MedImg}" class="doctor_img" alt="{$doctor/Name}"
						 title="{$doctor/Name}"/>
				</span>

			</div>
			<div class="doctor_info">

				<h2 class="doctor_name mvn t-teal">
					<span class="t-nd">
						<xsl:value-of select="$doctor/Name"/>
					</span>
				</h2>

				<p class="mvn">
					<xsl:for-each select="$doctor/SpecList/Element">
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

				<!-- rating -->
				<xsl:variable name="rating">
					<xsl:choose>
						<xsl:when test="$doctor/ManualRating and $doctor/ManualRating != '0'">
							<xsl:value-of select="$doctor/ManualRating"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="$doctor/TotalRating"/>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:variable>

				<div class="rating_stars js-rating" data-score="{$rating}">
				</div>
				<!-- rating end -->


			</div>

			<div class="doctor_info_short">
				<p class="strong mvs t-def">
					<xsl:call-template name="priceShow">
						<xsl:with-param name="specialPrice" select="$doctor/SpecialPrice"/>
						<xsl:with-param name="price" select="$doctor/Price"/>
					</xsl:call-template>
				</p>


				<ul class="metro_list">
					<xsl:choose>
						<xsl:when test="count($doctor/StationList/Element) &gt; 0">
							<xsl:for-each select="$doctor/StationList/Element">
								<xsl:choose>
									<xsl:when test="position() = last()">
										<li class="metro_item">
											<span class="metro i-metro_purple">
												<xsl:attribute name="class">
													metro_link metro_line_<xsl:value-of select="LineId"/>
												</xsl:attribute>
												м.
												<xsl:value-of select="./Name"/>
											</span>
										</li>
									</xsl:when>
									<xsl:otherwise>
										<li class="metro_item">
											<span class="metro i-metro_purple">
												<xsl:attribute name="class">
													metro_link metro_line_<xsl:value-of select="LineId"/>
												</xsl:attribute>
												м.
												<xsl:value-of select="./Name"/>
											</span>
											,
										</li>
									</xsl:otherwise>
								</xsl:choose>
							</xsl:for-each>
						</xsl:when>
						<xsl:otherwise></xsl:otherwise>
					</xsl:choose>
				</ul>


				<p class="mvn js-sd">
					<span class="">
						<span class="strong">
							<xsl:value-of select="$doctor/ClinicAddress"/>
						</span>
					</span>
				</p>
			</div>


		</article>


	</xsl:template>


	<xsl:template name="formRequestSlctdClinic">

		<xsl:variable name="clinic" select="/root/dbInfo/Clinic"/>

		<article class="doctor_card m-short">

			<div class="doctor_person">
				<span class="doctor_img_link">
					<img src="/img/clinic/{$clinic/Logo}" class="doctor_img" alt="{$clinic/Title}"/>
				</span>

			</div>
			<div class="doctor_info">

				<h2 class="doctor_name mvn t-teal">
					<span class="t-nd">
						<xsl:value-of select="$clinic/Title"/>
					</span>
				</h2>

				<!-- rating -->
				<!--
				<xsl:variable name="rating">
					<xsl:choose>
						<xsl:when test="$clinic/ManualRating and $clinic/ManualRating != '0'"><xsl:value-of select="$clinic/ManualRating"/></xsl:when>
						<xsl:otherwise><xsl:value-of select="$clinic/TotalRating"/></xsl:otherwise>
					</xsl:choose>
				</xsl:variable>

				<div class="rating_stars js-rating" data-score="{$rating}">
				</div>
				-->
				<!-- rating end -->


			</div>

			<div class="doctor_info_short">

				<!--
				<xsl:if test="$clinic/Schedule">
					<dl class="clinic_schedule">
						<dt class="clinic_schedule_days">пн-пт:</dt>
						<dd class="clinic_schedule_time">
							<xsl:value-of select="$clinic/Schedule/Element[@day='0']/@startTime" /> - <xsl:value-of select="$clinic/Schedule/Element[@day='0']/@endTime" />
						</dd>
						<dt class="clinic_schedule_days">сб:</dt>
						<dd class="clinic_schedule_time">
							<xsl:value-of select="$clinic/Schedule/Element[@day='6']/@startTime" /> - <xsl:value-of select="$clinic/Schedule/Element[@day='6']/@endTime" />
						</dd>
						<dt class="clinic_schedule_days">вс:</dt>
						<dd class="clinic_schedule_time">
							<xsl:value-of select="$clinic/Schedule/Element[@day='7']/@startTime" /> - <xsl:value-of select="$clinic/Schedule/Element[@day='7']/@endTime" />
						</dd>
					</dl>
				</xsl:if>
				-->


				<span class="strong">
					<xsl:value-of select="$clinic/Street"/>,
					<xsl:value-of select="$clinic/House"/>
				</span>
				<ul class="metro_list">
					<xsl:if test="count($clinic/MetroList/Element) &gt; 0">
						<xsl:attribute name="class">l-b</xsl:attribute>
					</xsl:if>
					<xsl:choose>
						<xsl:when test="count($clinic/MetroList/Element) &gt; 0">
							<xsl:for-each select="$clinic/MetroList/Element">
								<li class="metro_item">
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
														select="key('spec', ../../Specialities/Element[position() = 1]/Id)/RewriteName"/>/<xsl:value-of
														select="./Alias"/>
												</xsl:attribute>
											</xsl:otherwise>
										</xsl:choose>
										<xsl:value-of select="./Name"/>
									</a>
									<xsl:if test="position() != last()">,</xsl:if>
								</li>
							</xsl:for-each>
						</xsl:when>
						<xsl:otherwise>

						</xsl:otherwise>
					</xsl:choose>
				</ul>


				<p class="mvn js-sd">
					<span class="">
						<span class="strong">
							<xsl:value-of select="$clinic/ClinicAddress"/>
						</span>
					</span>
				</p>
			</div>

		</article>


	</xsl:template>


</xsl:transform>

