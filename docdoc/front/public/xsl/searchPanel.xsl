<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:import href="choose.xsl"/>
	<xsl:import href="doctorsTable.xsl"/>
	<xsl:output method="html" encoding="utf-8"/>


	<xsl:template match="/">
		<xsl:apply-templates select="root"/>
	</xsl:template>


	<xsl:template name="searchPanel">

		<xsl:choose>
			<xsl:when test="/root/dbHeadInfo/City/SearchType = '3'"><!-- metro map -->
				<xsl:choose>
					<xsl:when test="/root/srvInfo/IsMainPage = '1'">
						<xsl:call-template name="searchPanelHomepage"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:choose>
							<xsl:when test="/root/srvInfo/IsLandingPage = '1'">
								<xsl:call-template name="searchPanelLanding"/>
							</xsl:when>
							<xsl:otherwise>
								<xsl:call-template name="searchPanelDefault"/>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:when>

			<xsl:when test="/root/dbHeadInfo/City/SearchType = '2'"><!-- html select metro -->
				<xsl:call-template name="searchPanelMetroSelect"/>
			</xsl:when>

			<xsl:otherwise> <!-- select regions -->
				<xsl:call-template name="searchPanelRegions"/>
			</xsl:otherwise>
		</xsl:choose>

	</xsl:template>


	<!-- searchPanelLanding start -->
	<xsl:template name="searchPanelLanding">
		<div class="hr"></div>
	</xsl:template>
	<!-- searchPanelLanding end -->


	<!-- searchPanelHomepage start -->
	<xsl:template name="searchPanelHomepage">

		<div class="search l-wrapper mainpage">
			<form class="search_form" method="post" action="/service/redirect.php">
				<input type="hidden" name="search_location_type" value="Metro"/>

				<span class="strong">
					Ищу врача
				</span>
				<label>
					<xsl:choose>
						<xsl:when test="/root/srvInfo/IsMobile = '1'">
							<span class="search_input_imit search_input_imit_spec jsm-select"
							      data-select-related="select-spec">
								<select class="search_input search_input_spec" name="spec" data-select="select-spec">
									<option value="" selected="selected" data-select-placeholder="любой специальности">
										любой специальности
									</option>
									<xsl:for-each select="/root/dbHeadInfo/SpecialityList/Group/Group/Element">
										<option value="{./@id}">
											<xsl:value-of select="./Name"/>
										</option>
									</xsl:for-each>
								</select>
							</span>
						</xsl:when>
						<xsl:otherwise>
							<input class="search_input search_input_spec" type="text"
							       placeholder="любой специальности"/>
						</xsl:otherwise>
					</xsl:choose>
				</label>
				<i class="search_list_spec js-popup-tr" data-popup-id="js-popup-speclist" data-popup-width="700"></i>
				<span class="strong">
					в
				</span>
				<label>
					<xsl:choose>
						<xsl:when test="/root/srvInfo/IsMobile = '1'">
							<span class="search_input_imit search_input_imit_geo jsm-select" data-stat="btnPopupGeo"
							      data-select-related="select-geo">
								<select class="search_input search_input_geo" data-select="select-geo" name="stations[]"
								        multiple="multiple" size="1">
									<option value="0" selected="selected" data-select-placeholder="любом районе">любом
										районе
									</option>
									<xsl:for-each
											select="/root/dbHeadInfo/MetroMapData/MetroListByAZ/Group/Group/Element">
										<option value="{@id}">
											<xsl:value-of select="."/>
										</option>
									</xsl:for-each>

								</select>
							</span>
						</xsl:when>
						<xsl:otherwise>
							<input class="search_input search_input_geo" type="text" placeholder="любом районе"/>
						</xsl:otherwise>
					</xsl:choose>

				</label>
				<i class="search_list_metro js-popup-tr s-dynamic" data-popup-id="js-popup-geo" data-popup-width="920"
				   data-stat="btnPopupGeo"></i>
				<input type="submit" value="Найти врача" class="search_btn_find ui-btn ui-btn_teal"/>

				<xsl:call-template name="choose"/>

				<xsl:call-template name="doctorsTableJson"/>

			</form>
		</div>
	</xsl:template>
	<!-- searchPanelHomepage end -->


	<!-- searchPanelDefault start -->
	<xsl:template name="searchPanelDefault">

		<div class="search m-simple l-wrapper">
			<form class="search_form" method="post" action="/service/redirect.php">
				<input type="hidden" name="search_location_type" value="Metro"/>
				<input type="hidden" name="spec_init" value="{srvInfo/SearchParams/SelectedSpeciality/Name}"/>
				<input type="hidden" name="geo_init" value="{srvInfo/SearchParams/Geo}"/>
				Я ищу
				<span class="search_input_imit search_input_imit_spec js-popup-tr jsm-select"
				      data-popup-id="js-popup-speclist" data-popup-width="700" data-select-related="select-spec">

					<span>
						<xsl:choose>
							<xsl:when
									test="srvInfo/SearchParams/SelectedSpeciality and srvInfo/SearchParams/SelectedSpeciality!=''">
								<xsl:choose>
									<xsl:when test="srvInfo/ABtest/Search/@id = '1' or srvInfo/ABtest/Search/@id = '3'">
										<xsl:value-of select="srvInfo/Sector/InGenitive"/>
									</xsl:when>
									<xsl:otherwise>
										<xsl:value-of select="srvInfo/SearchParams/SelectedSpeciality/InGenitive"/>
									</xsl:otherwise>
								</xsl:choose>
							</xsl:when>
							<xsl:otherwise>
								любого врача
							</xsl:otherwise>
						</xsl:choose>
					</span>
					<xsl:choose>
						<xsl:when test="/root/srvInfo/IsMobile = '1'">
							<!-- mobile device -->
							<select class="search_input search_input_spec" data-select="select-spec" name="spec">
								<xsl:choose>
									<xsl:when
											test="/root/srvInfo/SearchParams/SelectedSpeciality and srvInfo/SearchParams/SelectedSpeciality!=''">
										<option value="" data-select-placeholder="любого врача">любого врача</option>
										<xsl:for-each select="/root/dbHeadInfo/SpecialityList/Group/Group/Element">
											<option value="{./@id}">
												<xsl:if test="number(/root/srvInfo/SearchParams/SelectedSpeciality/Id) = number(./@id)">
													<xsl:attribute name="selected">selected</xsl:attribute>
												</xsl:if>
												<xsl:value-of select="./Name"/>
											</option>
										</xsl:for-each>
									</xsl:when>
									<xsl:otherwise>
										<option value="" data-select-placeholder="любой специальности">любой
											специальности
										</option>
										<xsl:for-each select="/root/dbHeadInfo/SpecialityList/Group/Group/Element">
											<option value="{./@id}">
												<xsl:value-of select="./Name"/>
											</option>
										</xsl:for-each>
									</xsl:otherwise>
								</xsl:choose>


							</select>
							<!-- mobile device end -->
						</xsl:when>
						<xsl:otherwise>
							<!-- not a mobile device -->
							<input type="hidden" id="spec" name="spec"/>
							<!-- not a mobile device end -->
						</xsl:otherwise>
					</xsl:choose>

				</span>
				в
				<xsl:if test="srvInfo/SearchParams/SelectedStations/Element">
					районе метро
				</xsl:if>
				<span class="search_input_imit search_input_imit_geo js-popup-tr jsm-select"
				      data-popup-id="js-popup-geo" data-popup-width="920" data-stat="btnPopupGeo"
				      data-select-related="select-geo">
					<span>
						<xsl:choose>
							<xsl:when test="srvInfo/SearchParams/SelectedStations/Element">
								<xsl:variable name="cnt">
									<xsl:choose>
										<xsl:when
												test="srvInfo/SearchParams/SelectedStations/Element and srvInfo/SearchParams/SelectedStations/Element[position()=2] and (string-length(srvInfo/SearchParams/SelectedStations/Element[position() = 1]) + string-length(srvInfo/SearchParams/SelectedStations/Element[position() = 2]) &lt;= 16)">
											2
										</xsl:when>
										<xsl:otherwise>1</xsl:otherwise>
									</xsl:choose>
								</xsl:variable>

								<xsl:choose>
									<xsl:when test="srvInfo/ABtest/Search/@id = '3'">
										<xsl:choose>
											<xsl:when
													test="$cnt = '2' and not(srvInfo/SearchParams/SelectedStations/Element[position() = 3])">
												<xsl:value-of
														select="srvInfo/SearchParams/SelectedStations/Element[position() = 1]/Name"/>,
												<xsl:value-of
														select="srvInfo/SearchParams/SelectedStations/Element[position() = 2]/Name"/>
											</xsl:when>
											<xsl:when test="$cnt = '2'">
												<xsl:value-of
														select="srvInfo/SearchParams/SelectedStations/Element[position() = 1]/Name"/>,
												<xsl:value-of
														select="srvInfo/SearchParams/SelectedStations/Element[position() = 2]/Name"/>

												[ и еще
												<xsl:value-of
														select="count(srvInfo/SearchParams/SelectedStations/Element) - 2"/>

												<xsl:call-template name="digitVariant">
													<xsl:with-param name="one">
														станция
													</xsl:with-param>
													<xsl:with-param name="two">
														станции
													</xsl:with-param>
													<xsl:with-param name="five">
														станций
													</xsl:with-param>
													<xsl:with-param name="digit"
													                select="count(srvInfo/SearchParams/SelectedStations/Element) - 2"/>
												</xsl:call-template>
												]
											</xsl:when>
											<xsl:when
													test="$cnt = '1' and not(srvInfo/SearchParams/SelectedStations/Element[position() = 2])">
												<xsl:value-of
														select="srvInfo/SearchParams/SelectedStations/Element[position() = 1]/Name"/>
											</xsl:when>

											<xsl:otherwise>
												<xsl:value-of
														select="srvInfo/SearchParams/SelectedStations/Element[position() = 1]/Name"/>
												[ и еще
												<xsl:value-of
														select="count(srvInfo/SearchParams/SelectedStations/Element) - 1"/>

												<xsl:call-template name="digitVariant">
													<xsl:with-param name="one">
														станция
													</xsl:with-param>
													<xsl:with-param name="two">
														станции
													</xsl:with-param>
													<xsl:with-param name="five">
														станций
													</xsl:with-param>
													<xsl:with-param name="digit"
													                select="count(srvInfo/SearchParams/SelectedStations/Element) - 1"/>
												</xsl:call-template>
												]
											</xsl:otherwise>
										</xsl:choose>
									</xsl:when>
									<xsl:otherwise>
										<xsl:choose>
											<xsl:when
													test="$cnt = '2' and not(srvInfo/SearchParams/SelectedStations/Element[position() = 3])">
												<xsl:value-of
														select="srvInfo/SearchParams/SelectedStations/Element[position() = 1]/Name"/>,
												<xsl:value-of
														select="srvInfo/SearchParams/SelectedStations/Element[position() = 2]/Name"/>
											</xsl:when>
											<xsl:when test="$cnt = '2'">
												<xsl:value-of
														select="srvInfo/SearchParams/SelectedStations/Element[position() = 1]/Name"/>,
												<xsl:value-of
														select="srvInfo/SearchParams/SelectedStations/Element[position() = 2]/Name"/>

												[и еще <xsl:value-of
													select="count(srvInfo/SearchParams/SelectedStations/Element) - 2"/>]
											</xsl:when>
											<xsl:when
													test="$cnt = '1' and not(srvInfo/SearchParams/SelectedStations/Element[position() = 2])">
												<xsl:value-of
														select="srvInfo/SearchParams/SelectedStations/Element[position() = 1]/Name"/>
											</xsl:when>

											<xsl:otherwise>
												<xsl:value-of
														select="srvInfo/SearchParams/SelectedStations/Element[position() = 1]/Name"/>
												[и еще <xsl:value-of
													select="count(srvInfo/SearchParams/SelectedStations/Element) - 1"/>]

											</xsl:otherwise>

										</xsl:choose>
									</xsl:otherwise>
								</xsl:choose>
							</xsl:when>
							<xsl:otherwise>
								любом районе
							</xsl:otherwise>
						</xsl:choose>
					</span>

					<xsl:choose>
						<xsl:when test="/root/srvInfo/IsMobile = '1'">
							<!-- mobile device -->
							<select class="search_input search_input_geo" data-select="select-geo" name="stations[]"
							        multiple="multiple">
								<xsl:choose>
									<xsl:when
											test="/root/srvInfo/SearchParams/SelectedStations and srvInfo/SearchParams/SelectedStations!=''">
										<option value="0" data-select-placeholder="любом районе">любом районе</option>
										<xsl:for-each
												select="/root/dbHeadInfo/MetroMapData/MetroListByAZ/Group/Group/Element">
											<option value="{@id}">
												<xsl:if test="@id = /root/srvInfo/SearchParams/SelectedStations/descendant-or-self::Element/Id">
													<xsl:attribute name="selected">selected</xsl:attribute>
												</xsl:if>
												<xsl:value-of select="."/>
											</option>
										</xsl:for-each>
									</xsl:when>
									<xsl:otherwise>
										<option value="0" data-select-placeholder="любом районе">любом районе</option>
										<xsl:for-each
												select="/root/dbHeadInfo/MetroMapData/MetroListByAZ/Group/Group/Element">
											<option value="{@id}">
												<xsl:if test="@id = /root/srvInfo/SearchParams/SelectedStations/descendant-or-self::Element/Id">
													<xsl:attribute name="selected">selected</xsl:attribute>
												</xsl:if>
												<xsl:value-of select="."/>
											</option>
										</xsl:for-each>
									</xsl:otherwise>
								</xsl:choose>


							</select>
							<!-- mobile device end -->
						</xsl:when>
						<xsl:otherwise>
							<!-- not a mobile device -->

							<!-- not a mobile device end -->
						</xsl:otherwise>
					</xsl:choose>

					<!--
					<xsl:choose>
                        <xsl:when test="/root/srvInfo/IsMobile = '1'">
						<select class="search_input search_input_spec" data-select="select-spec" name="spec">
							<xsl:choose>
								<xsl:when test="/root/srvInfo/SearchParams/SelectedSpeciality and srvInfo/SearchParams/SelectedSpeciality!=''">
									<option value="" data-select-placeholder="любого врача">любого врача</option>
									<xsl:for-each select="/root/dbHeadInfo/SpecialityList/Group/Group/Element">
										<option value="{./@id}">
											<xsl:if test="number(/root/srvInfo/SearchParams/SelectedSpeciality/Id) = number(./@id)">
												<xsl:attribute name="selected">selected</xsl:attribute>
											</xsl:if>
											<xsl:value-of select="./Name"/>
										</option>
									</xsl:for-each>
								</xsl:when>
								<xsl:otherwise>
									<option value="" data-select-placeholder="любой специальности">любой специальности</option>
									<xsl:for-each select="/root/dbHeadInfo/SpecialityList/Group/Group/Element">
										<option value="{./@id}">
											<xsl:value-of select="./Name"/>
										</option>
									</xsl:for-each>
								</xsl:otherwise>
							</xsl:choose>
						</select>
						</xsl:when>
					</xsl:choose>
					-->
				</span>

				<!--
                <input class="search_input search_input_spec" type="text" placeholder="любой специальности" value="" />-->
				<input class="search_input search_input_geo" type="text" placeholder="любом районе" value=""/>


				<xsl:if test="/root/srvInfo/IsMainPage = '1'">
					<div class="search_actions">
						<input type="submit" value="Найти врача" class="search_btn_find ui-btn ui-btn_teal"/>
					</div>
				</xsl:if>

				<xsl:call-template name="choose"/>
				<xsl:call-template name="doctorsTableJson"/>

			</form>

			<!--
			<div class="search_onmap_small ui-bl">
				<a href="#" class="search_onmap_small_link i-searchonmap" title="Посмотрите всех найденных врачей на карте">
					<span class="link-cta">Показать на карте</span>
				</a>
			</div>
			-->

		</div>

	</xsl:template>
	<!-- searchPanelDefault end -->


	<!-- searchPanelMetroSelect start -->
	<xsl:template name="searchPanelMetroSelect">

		<div class="search l-wrapper mainpage">
			<form class="search_form" method="post" action="/service/redirect.php">
				<input type="hidden" name="search_location_type" value="Metro"/>
				<span class="strong">
					Ищу врача
				</span>
				<label>
					<xsl:choose>
						<xsl:when test="/root/srvInfo/IsMobile = '1'">
							<span class="search_input_imit search_input_imit_spec jsm-select"
							      data-select-related="select-spec">
								<select class="search_input search_input_spec" name="spec" data-select="select-spec">
									<xsl:choose>
										<xsl:when
												test="srvInfo/SearchParams/SelectedSpeciality/Id and srvInfo/SearchParams/SelectedSpeciality/Id !=''">
										</xsl:when>
										<xsl:otherwise>
											<option value="" selected="selected"
											        data-select-placeholder="любой специальности">любой специальности
											</option>
										</xsl:otherwise>
									</xsl:choose>

									<xsl:variable name="specIdSelected"
									              select="number(srvInfo/SearchParams/SelectedSpeciality/Id)"/>

									<xsl:for-each select="/root/dbHeadInfo/SpecialityList/Group/Group/Element">
										<xsl:variable name="specId" select="number(./@id)"/>
										<option value="{./@id}">
											<xsl:if test="$specIdSelected = $specId">
												<xsl:attribute name="selected">selected</xsl:attribute>
											</xsl:if>
											<xsl:value-of select="./Name"/>
										</option>
									</xsl:for-each>
								</select>
							</span>
						</xsl:when>
						<xsl:otherwise>
							<input class="search_input search_input_spec" type="text" placeholder="любой специальности"
							       data-autocomplete-id="autocomplete-spec">

								<xsl:attribute name="value">
									<xsl:choose>

										<xsl:when
												test="srvInfo/SearchParams/SelectedSpeciality and srvInfo/SearchParams/SelectedSpeciality!=''">
											<xsl:value-of select="srvInfo/SearchParams/SelectedSpeciality/InGenitive"/>
										</xsl:when>
										<xsl:otherwise></xsl:otherwise>
									</xsl:choose>
								</xsl:attribute>
							</input>
							<i class="search_list_spec js-autocomplete-trigger" data-autocomplete-id="autocomplete-spec"
							   data-autocomplete-submit="1"></i>
						</xsl:otherwise>
					</xsl:choose>
				</label>

				<span class="strong">
					в
				</span>

				<label>
					<xsl:choose>
						<xsl:when test="/root/srvInfo/IsMobile = '1'">
							<span class="search_input_imit search_input_imit_geo jsm-select" data-stat="btnPopupGeo"
							      data-select-related="select-geo">

								<select class="search_input search_input_geo" data-select="select-geo" name="stations[]"
								        multiple="multiple" size="1">
									<option value="0" data-select-placeholder="любом районе">
										<xsl:if test="count(/root/dbHeadInfo/MetroMapData/MetroListByAZ/Group/Group/Element[@selected = '1']) = 0">
											<xsl:attribute name="selected">selected</xsl:attribute>
										</xsl:if>
										любом районе
									</option>
									<xsl:for-each
											select="/root/dbHeadInfo/MetroMapData/MetroListByAZ/Group/Group/Element">
										<option value="{@id}">
											<xsl:if test="@selected = '1'">
												<xsl:attribute name="selected">selected</xsl:attribute>
											</xsl:if>
											<xsl:value-of select="."/>
										</option>
									</xsl:for-each>
								</select>

							</span>
						</xsl:when>

						<xsl:otherwise>
							<input class="search_input search_input_geo js-complete-geo" type="text"
							       placeholder="любом районе" data-autocomplete-id="autocomplete-geo">
								<!-- -->
								<xsl:attribute name="value">
									<xsl:choose>
										<xsl:when test="srvInfo/SearchParams/SelectedStations/Element">
											<xsl:variable name="cnt">
												<xsl:choose>
													<xsl:when
															test="srvInfo/SearchParams/SelectedStations/Element and srvInfo/SearchParams/SelectedStations/Element[position()=2] and (string-length(srvInfo/SearchParams/SelectedStations/Element[position() = 1]) + string-length(srvInfo/SearchParams/SelectedStations/Element[position() = 2]) &lt;= 16)">
														2
													</xsl:when>
													<xsl:otherwise>1</xsl:otherwise>
												</xsl:choose>
											</xsl:variable>

											<xsl:choose>
												<xsl:when
														test="$cnt = '2' and not(srvInfo/SearchParams/SelectedStations/Element[position() = 3])">
													<xsl:value-of
															select="srvInfo/SearchParams/SelectedStations/Element[position() = 1]/Name"/>,
													<xsl:value-of
															select="srvInfo/SearchParams/SelectedStations/Element[position() = 2]/Name"/>
												</xsl:when>
												<xsl:when test="$cnt = '2'">
													<xsl:value-of
															select="srvInfo/SearchParams/SelectedStations/Element[position() = 1]/Name"/>,
													<xsl:value-of
															select="srvInfo/SearchParams/SelectedStations/Element[position() = 2]/Name"/>

													[и еще <xsl:value-of
														select="count(srvInfo/SearchParams/SelectedStations/Element) - 2"/>]
												</xsl:when>
												<xsl:when
														test="$cnt = '1' and not(srvInfo/SearchParams/SelectedStations/Element[position() = 2])">
													<xsl:value-of
															select="srvInfo/SearchParams/SelectedStations/Element[position() = 1]/Name"/>
												</xsl:when>

												<xsl:otherwise>
													<xsl:value-of
															select="srvInfo/SearchParams/SelectedStations/Element[position() = 1]/Name"/>
													[и еще <xsl:value-of
														select="count(srvInfo/SearchParams/SelectedStations/Element) - 1"/>]

												</xsl:otherwise>

											</xsl:choose>
										</xsl:when>
										<xsl:otherwise></xsl:otherwise>
									</xsl:choose>
									<!-- -->
								</xsl:attribute>
							</input>
							<i class="search_list_metro js-autocomplete-trigger" data-autocomplete-id="autocomplete-geo"
							   data-autocomplete-submit="1"></i>
						</xsl:otherwise>
					</xsl:choose>
				</label>

				<input type="submit" value="Найти врача" class="search_btn_find ui-btn ui-btn_teal"/>

				<xsl:call-template name="choose"/>
				<xsl:call-template name="doctorsTableJson"/>

				<div class="xml-data-provider s-hidden">
					[
					<xsl:for-each select="/root/dbHeadInfo/MetroMapData/MetroListByAZ/Group/Group">
						<xsl:for-each select="Element">{"id":<xsl:value-of select="@id"/>,"name":"<xsl:value-of
								select="."/>"}
							<xsl:if test="position() != last()">,</xsl:if>
						</xsl:for-each>
						<xsl:if test="position() != last()">,</xsl:if>
					</xsl:for-each>
					<xsl:if test="position() != last()">,</xsl:if>]
				</div>
			</form>
		</div>
	</xsl:template>
	<!-- searchPanelMetroSelect end -->


	<!-- searchPanelRegions start -->
	<xsl:template name="searchPanelRegions">

		<div class="search l-wrapper mainpage">
			<form id="main_search_form" class="search_form" method="post" action="/service/redirect.php">
				<input type="hidden" name="search_location_type" value="Area"/>
				<span class="strong">
					Ищу врача
				</span>
				<label>
					<xsl:choose>
						<xsl:when test="/root/srvInfo/IsMobile = '1'">
							<span class="search_input_imit search_input_imit_spec jsm-select"
							      data-select-related="select-spec">
								<select class="search_input search_input_spec" name="spec" data-select="select-spec">
									<xsl:choose>
										<xsl:when
												test="srvInfo/SearchParams/SelectedSpeciality/Id and srvInfo/SearchParams/SelectedSpeciality/Id !=''">
										</xsl:when>
										<xsl:otherwise>
											<option value="" selected="selected"
											        data-select-placeholder="любой специальности">любой специальности
											</option>
										</xsl:otherwise>
									</xsl:choose>

									<xsl:variable name="specIdSelected"
									              select="number(srvInfo/SearchParams/SelectedSpeciality/Id)"/>

									<xsl:for-each select="/root/dbHeadInfo/SpecialityList/Group/Group/Element">
										<xsl:variable name="specId" select="number(./@id)"/>
										<option value="{./@id}">
											<xsl:if test="$specIdSelected = $specId">
												<xsl:attribute name="selected">selected</xsl:attribute>
											</xsl:if>
											<xsl:value-of select="./Name"/>
										</option>
									</xsl:for-each>
								</select>
							</span>
						</xsl:when>
						<xsl:otherwise>
							<input class="search_input search_input_spec" type="text" placeholder="любой специальности"
							       data-autocomplete-id="autocomplete-spec" data-autocomplete-submit="1">

								<xsl:attribute name="value">
									<xsl:choose>

										<xsl:when
												test="srvInfo/SearchParams/SelectedSpeciality and srvInfo/SearchParams/SelectedSpeciality!=''">
											<xsl:value-of select="srvInfo/SearchParams/SelectedSpeciality/InGenitive"/>
										</xsl:when>
										<xsl:otherwise></xsl:otherwise>
									</xsl:choose>
								</xsl:attribute>
							</input>
							<i class="search_list_spec js-autocomplete-trigger" data-autocomplete-id="autocomplete-spec"
							   data-autocomplete-submit="1"></i>
						</xsl:otherwise>
					</xsl:choose>
				</label>

				<span class="strong">
					в
				</span>

				<label>
					<xsl:choose>
						<xsl:when test="/root/srvInfo/IsMobile = '1'">
							<span class="search_input_imit search_input_imit_geo jsm-select" data-stat="btnPopupGeo"
							      data-select-related="select-geo">

								<select class="search_input search_input_geo" data-select="select-geo" name="stations[]"
								        multiple="multiple" size="1">
									<option value="0" data-select-placeholder="любом районе">
										<xsl:if test="count(/root/dbHeadInfo/MetroMapData/DistrictList/Group/Element[@selected = '1']) = 0">
											<xsl:attribute name="selected">selected</xsl:attribute>
										</xsl:if>
										любом районе
									</option>
									<xsl:for-each select="/root/dbHeadInfo/MetroMapData/DistrictList/Group/Element">
										<option value="{@id}">
											<xsl:if test="@selected = '1'">
												<xsl:attribute name="selected">selected</xsl:attribute>
											</xsl:if>
											<xsl:value-of select="."/>
										</option>
									</xsl:for-each>
								</select>

							</span>
						</xsl:when>

						<xsl:otherwise>
							<input class="search_input search_input_geo js-complete-geo js-autocomplete-trigger"
							       type="text" placeholder="любом районе" data-autocomplete-id="autocomplete-geo"
							       data-autocomplete-submit="1">
								<!-- -->
								<xsl:attribute name="value">
									<xsl:choose>
										<xsl:when
												test="srvInfo/SearchParams/District and srvInfo/SearchParams/District != '' ">
											<xsl:value-of select="srvInfo/SearchParams/District/Name"/>
										</xsl:when>
										<xsl:otherwise></xsl:otherwise>
									</xsl:choose>
									<!-- -->
								</xsl:attribute>
							</input>

						</xsl:otherwise>
					</xsl:choose>
				</label>

				<input type="submit" value="Найти врача" class="search_btn_find ui-btn ui-btn_teal"/>

				<xsl:call-template name="choose"/>
				<xsl:call-template name="doctorsTableJson"/>


				<input type="hidden" name="dist" value="{srvInfo/SearchParams/District/Id}"></input>
				<div class="xml-data-provider s-hidden" data-search-param-name="dist">
					[
					<xsl:for-each select="/root/dbHeadInfo/MetroMapData/DistrictList/Group">
						<xsl:for-each select="Element">{"id":<xsl:value-of select="@id"/>,"name":"<xsl:value-of
								select="Title"/>"}
							<xsl:if test="position() != last()">,</xsl:if>
						</xsl:for-each>
						<xsl:if test="position() != last()">,</xsl:if>
					</xsl:for-each>
					<xsl:if test="position() != last()">,</xsl:if>]
				</div>

			</form>
		</div>
	</xsl:template>
	<!-- searchPanelRegions end -->

</xsl:transform>

