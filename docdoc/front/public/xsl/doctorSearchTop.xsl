<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:import href="../lib/xsl/common.xsl" />

	<xsl:output method="html" encoding="utf-8"/>


	<xsl:template name="doctorSearchTop">
		<xsl:param name="doctorSearchType" select="''" />

		<xsl:choose>
			<xsl:when test="$doctorSearchType = 'usual'">
				<div class="seo-header">

					<h1>
						<xsl:choose>
							<xsl:when test="/root/dbHeadInfo/SEO/Head != ''">
								<xsl:value-of select="/root/dbHeadInfo/SEO/Head" />
							</xsl:when>
							<xsl:when test="/root/srvInfo/SearchParams/SelectedSpeciality and /root/srvInfo/SearchParams/SelectedSpeciality != '' and /root/dbInfo/Params/Departure = 0">
								Врачи-<xsl:value-of select="/root/srvInfo/SearchParams/SelectedSpeciality/InPluralLC"/>

								<xsl:choose>
									<xsl:when test="/root/srvInfo/SearchParams/RegCity/Name">
										(<xsl:value-of select="/root/srvInfo/SearchParams/RegCity/Name"/>)
									</xsl:when>
									<xsl:when test="count(/root/srvInfo/SearchParams/District) = 1">
										Район: <xsl:value-of select="/root/srvInfo/SearchParams/District/Name"/>
									</xsl:when>
									<xsl:when test="count(/root/srvInfo/SearchParams/Area) = 1">
										(<xsl:value-of select="/root/srvInfo/SearchParams/Area/Name"/>)
									</xsl:when>
									<xsl:when test="count(/root/srvInfo/SearchParams/SelectedStations/Element) = 1">
										(<xsl:value-of select="/root/srvInfo/SearchParams/SelectedStations/Element/Name"/>)
									</xsl:when>

								</xsl:choose>
							</xsl:when>
							<xsl:when test="/root/dbInfo/Params/Departure = 1">
								Вызов <xsl:value-of select="/root/srvInfo/SearchParams/SelectedSpeciality/InGenitiveLC"/> на дом
							</xsl:when>
						</xsl:choose>

					</h1>

				</div>

				<xsl:choose>
					<xsl:when test="/root/srvInfo/IsMobile = '1'">
						<!-- mobile device -->

						<!-- mobile device end -->
					</xsl:when>
					<xsl:otherwise>
						<!-- not a mobile device -->
						<xsl:value-of select="/root/dbHeadInfo/SEO/Texts/Element[Position='1']/Text"/>
						<!-- not a mobile device end -->
					</xsl:otherwise>
				</xsl:choose>

			</xsl:when>
			<xsl:when test="$doctorSearchType = 'context'">

				<h1>
					<xsl:choose>
						<xsl:when test="/root/dbHeadInfo/SEO/Head != ''">
							<xsl:value-of select="/root/dbHeadInfo/SEO/Head" />
						</xsl:when>
						<xsl:when test="count(/root/srvInfo/SearchParams/District) = 1 or count(/root/srvInfo/SearchParams/Area) = 1 or count(/root/srvInfo/SearchParams/SelectedStations/Element) = 1">
							Врачи-<xsl:value-of select="/root/srvInfo/SearchParams/SelectedSpeciality/InPluralLC"/>

							<xsl:choose>
								<xsl:when test="count(/root/srvInfo/SearchParams/District) = 1">
									Район: <xsl:value-of select="/root/srvInfo/SearchParams/District/Name"/>
								</xsl:when>
								<xsl:when test="count(/root/srvInfo/SearchParams/Area) = 1">
									(<xsl:value-of select="/root/srvInfo/SearchParams/Area/Name"/>)
								</xsl:when>
								<xsl:when test="count(/root/srvInfo/SearchParams/SelectedStations/Element) = 1">
									на метро <xsl:value-of select="/root/srvInfo/SearchParams/SelectedStations/Element/Name"/>
								</xsl:when>
							</xsl:choose>

						</xsl:when>

						<xsl:otherwise>
							Вас приветствует docdoc – online-сервис по поиску врачей
						</xsl:otherwise>

					</xsl:choose>

				</h1>

				<!-- noindex -->
				<![CDATA[
            		<!--noindex-->
            	]]>
				<ul class="context_list">
					<li class="context_item i-context_stars">
						<h2 class="context_title">
							Все <xsl:value-of select="/root/srvInfo/SearchParams/SelectedSpeciality/InPluralLC"/><![CDATA[&nbsp;]]><xsl:value-of select="/root/dbHeadInfo/City/NameInGenitive"/> на одном сайте
						</h2>
						<p class="mvs">
							Здесь собраны анкеты всех <xsl:value-of select="/root/srvInfo/SearchParams/SelectedSpeciality/InGenitivePluralLC"/> из лучших клиник и медицинских центров <xsl:value-of select="/root/dbHeadInfo/City/NameInGenitive"/>. В анкетах  написана подробная информация о врачах и отзывы пациентов.
						</p>
					</li>
					<li class="context_item i-context_search">
						<h2 class="context_title">
							Удобный поиск по вашим критериям
						</h2>
						<p class="mvs">
							Для быстрого поиска вы можете уточнить критерии подбора врача, указать удобные для вас станции метро или отсортировать анкеты врачей по стажу, рейтингу или стоимости приема.  </p>
					</li>
					<li class="context_item i-context_request">
						<h2 class="context_title">
							Запись на прием online
						</h2>
						<p class="mvs">
							Вы можете сразу записаться на прием к выбранному врачу, оставив заявку на запись через  анкету врача или позвонив по номеру <span class="comagic_phone call_phone_1"><xsl:value-of
								select="/root/dbHeadInfo/Phone/Full"/></span>
						</p>
					</li>
				</ul>
				<![CDATA[
            		<!--/noindex-->
            	]]>
				<!-- noindex END -->

			</xsl:when>
			<xsl:when test="$doctorSearchType = 'landing'">

				<h1>Запишитесь на прием к врачу, сделав всего один звонок</h1>

				<!-- noindex -->
				<![CDATA[
            		<!--noindex-->
            	]]>
				<ul class="context_list">
					<li class="context_item i-landing_best">
						<h2 class="context_title">
							Только надежные врачи
						</h2>
						<p class="mvs">
							Каждый оставленный отзыв о докторе проходит проверку на подлинность. <b>Отзывы других пациентов помогут Вам определиться с выбором наилучшего специалиста.</b>
						</p>
					</li>
					<li class="context_item i-landing_search">
						<h2 class="context_title">
							Полная информация о специалистах
						</h2>
						<p class="mvs">
							Информацию о месте приема врача Вы найдете в его персональной карточке. Также Вы сможете узнать об его образовании, специализации, наградах и ученых степенях, практической и научной деятельности и т.д.
						</p>
					</li>
					<li class="context_item i-landing_sale">
						<h2 class="context_title">
							Скидки на первичный прием!
						</h2>
						<p class="mvs">
							Только посетители ДокДок получают специальную скидку на первичную консультацию врача. <b>Чтобы получить скидку, скажите, что Вы звоните с портала ДокДок.</b>
						</p>
					</li>
				</ul>
				<![CDATA[
            		<!--/noindex-->
            	]]>
				<!-- noindex END -->

			</xsl:when>
		</xsl:choose>

		<!-- NEW -->

	</xsl:template>

</xsl:stylesheet>