<?xml version='1.0'  encoding="UTF-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:output method="html" encoding="utf-8"/>

	<xsl:template match="root">

        <xsl:call-template name="sitemap" />
	</xsl:template>


    <xsl:template name="sitemap">




        <div class="l-wrapper">

        <div class="sitemap">

        <h1>
            Карта сайта
        </h1>

		<xsl:variable name="entityUrl"><xsl:choose>
			<xsl:when test="/root/dbInfo/SelectedSpec/Entity = 'doctor'">doctor</xsl:when>
			<xsl:when test="/root/dbInfo/SelectedSpec/Entity = 'clinic'">clinic/spec</xsl:when>
			<xsl:otherwise>doctor</xsl:otherwise>
		</xsl:choose></xsl:variable>

        <xsl:choose>
            <xsl:when test="count(dbInfo/SpecList) > 0">

                <ul class="sitemap_list__top">
                    <li class="sitemap_item">
                        –
                        <a href="/" class="sitemap_link">
                            Главная
                        </a>
                        <ul class="sitemap_list">
                            <li class="sitemap_item">
                                –
                                <a href="/doctor" class="sitemap_link">
                                    Все врачи
                                </a>
                            </li>
                            <li class="sitemap_item">
                                <ul class="sitemap_list">
                                    <xsl:for-each select="/root/dbInfo/SpecList/Element">

                                        <li class="sitemap_item">
                                            –
                                            <a href="sitemap/{Id}" class="sitemap_link">
                                                <xsl:value-of select="Name"/>
                                            </a>
                                        </li>

                                    </xsl:for-each>
                                </ul>
                            </li>
                            <!--
                            <li class="sitemap_item">
                                <a href="/page/help" class="sitemap_link">
                                    Как найти врача на DocDoc.ru
                                </a>
                            </li>
                            -->
                        </ul>
                        <ul class="sitemap_list">
                            <!--
                            <li class="sitemap_item">
                                –
                                <a href="/clinic" class="sitemap_link">
                                    Все клиники
                                </a>
                            </li>
                            -->
                            <li class="sitemap_item">
                                <ul class="sitemap_list">
                                    <xsl:for-each select="/root/dbInfo/ClinicList/Element">

                                        <li class="sitemap_item">
                                            –
                                            <a href="sitemap/clinic/{@id}" class="sitemap_link">
                                                <xsl:value-of select="Name"/>
                                            </a>
                                        </li>

                                    </xsl:for-each>
                                </ul>
                            </li>
							<li class="sitemap_item">
								<a href="/sitemap/street" class="sitemap_link">
									Список улиц
								</a>
							</li>
                            <li class="sitemap_item">
                                <a href="/library" class="sitemap_link">
                                    Справочник пациента
                                </a>
                            </li>
                            <!--
                            <li class="sitemap_item">
                                <a href="/page/help" class="sitemap_link">
                                    Как найти врача на DocDoc.ru
                                </a>
                            </li>
                            -->
                        </ul>
                    </li>
                </ul>
            </xsl:when>

            <xsl:otherwise>

                <xsl:choose>
                    <xsl:when test="count(dbInfo/AreaList) > 0">
                    <ul class="sitemap_list__top">
                        <li class="sitemap_item">
                            –
                            <a href="/{$entityUrl}/{/root/dbInfo/SelectedSpec/Alias}" class="sitemap_link">
                                <xsl:value-of select="/root/dbInfo/SelectedSpec/Name"/>
                            </a>
                            <ul class="sitemap_list">
	                            <xsl:if test="(/root/dbInfo/SelectedSpec/Entity = 'doctor') and (/root/dbInfo/SelectedSpec/KidsReception = 'yes')">
		                            <li class="sitemap_item">
			                            –
			                            <a href="/{$entityUrl}/{/root/dbInfo/SelectedSpec/Alias}/deti" class="sitemap_link">Детские врачи</a>
		                            </li>
	                            </xsl:if>
                                <li class="sitemap_item">
                                    В округе Москвы
                                </li>
                                <li class="sitemap_item">
                                    <ul class="sitemap_list">
                                        <xsl:for-each select="/root/dbInfo/AreaList/Element">

                                            <li class="sitemap_item">
                                                –
                                                <a href="/{$entityUrl}/{/root/dbInfo/SelectedSpec/Alias}/area/{Alias}" class="sitemap_link">
                                                    <xsl:value-of select="Name"/>
                                                </a>
                                            </li>
                                            <xsl:if test="count(DistrictList/Element) &gt; 0 and string-length(DistrictList/Element) &gt; 0">
                                            <li class="sitemap_item">
                                                <ul class="sitemap_list">
                                                    <xsl:for-each select="DistrictList/Element">
                                                        <li class="sitemap_item">
                                                            –
                                                            <a href="/{$entityUrl}/{/root/dbInfo/SelectedSpec/Alias}/area/{../../Alias}/{Alias}" class="sitemap_link">
                                                                <xsl:value-of select="Name"/>
                                                            </a>
                                                        </li>
                                                    </xsl:for-each>
                                                </ul>
                                            </li>
                                            </xsl:if>

                                        </xsl:for-each>
                                    </ul>
                                </li>
                                <li class="sitemap_item">
                                    На станции метро:
                                </li>
                                <li class="sitemap_item">
                                    <ul class="sitemap_list">
                                        <xsl:for-each select="/root/dbInfo/StationList/Element">

                                            <li class="sitemap_item">
                                                –
                                                <a href="/{$entityUrl}/{/root/dbInfo/SelectedSpec/Alias}/{Alias}" class="sitemap_link">
                                                    <xsl:value-of select="Name"/>
                                                </a>
                                            </li>

                                        </xsl:for-each>
                                    </ul>
                                </li>
                                <li class="sitemap_item">
                                    В городах Подмосковья:
                                </li>
                                <li class="sitemap_item">
                                    <ul class="sitemap_list">
                                        <xsl:for-each select="/root/dbInfo/RegCityList/Element">

                                            <li class="sitemap_item">
                                                –
                                                <a href="/{$entityUrl}/{/root/dbInfo/SelectedSpec/Alias}/city/{Alias}" class="sitemap_link">
                                                    <xsl:value-of select="Name"/>
                                                </a>
                                            </li>

                                        </xsl:for-each>
                                    </ul>
                                </li>
                                <li class="sitemap_item">
                                    <a href="/library" class="sitemap_link">
                                        Справочник пациента
                                    </a>
                                </li>
                                <!--
                                <li class="sitemap_item">
                                    <a href="/page/help" class="sitemap_link">
                                        Как найти врача на DocDoc.ru
                                    </a>
                                </li>
                                -->
                            </ul>
                        </li>
                    </ul>
                    </xsl:when>

                    <xsl:otherwise>
						<xsl:choose>
							<xsl:when test="count(dbInfo/StreetList) > 0">
								<li class="sitemap_item">
									Список улиц
								</li>
								<li class="sitemap_item">
									<ul class="sitemap_list">
										<xsl:for-each select="/root/dbInfo/StreetList/Element">

											<li class="sitemap_item">
												–
												<a href="/clinic/street/{rewrite_name}" class="sitemap_link">
													<xsl:value-of select="title"/>
												</a>
											</li>

										</xsl:for-each>
									</ul>
								</li>
							</xsl:when>
							<xsl:otherwise>
								<ul class="sitemap_list__top">
									<li class="sitemap_item">
									–
										<a href="/{$entityUrl}/{/root/dbInfo/SelectedSpec/Alias}" class="sitemap_link">
											<xsl:value-of select="/root/dbInfo/SelectedSpec/Name"/>
										</a>
										<ul class="sitemap_list">
											<li class="sitemap_item">
												В районе <xsl:value-of select='/root/dbHeadInfo/City/NameInGenitive'/>
											</li>
											<li class="sitemap_item">
												<ul class="sitemap_list">
													<xsl:for-each select="/root/dbInfo/DistrictList/Element">
														<li class="sitemap_item">
															–
															<a href="/{$entityUrl}/{/root/dbInfo/SelectedSpec/Alias}/district/{Alias}" class="sitemap_link">
																<!--{../../Alias}-->
																<xsl:value-of select="Name"/>
															</a>
														</li>
													</xsl:for-each>
												</ul>
											</li>

											<xsl:choose>
												<xsl:when test="count(/root/dbInfo/StationList/Element) > 0">
													<li class="sitemap_item">
														На станции метро:
													</li>
													<li class="sitemap_item">
														<ul class="sitemap_list">
															<xsl:for-each select="/root/dbInfo/StationList/Element">

																<li class="sitemap_item">
																	–
																	<a href="/{$entityUrl}/{/root/dbInfo/SelectedSpec/Alias}/{Alias}" class="sitemap_link">
																		<xsl:value-of select="Name"/>
																	</a>
																</li>

															</xsl:for-each>
														</ul>
													</li>
												</xsl:when>
												<xsl:otherwise>
												</xsl:otherwise>
											</xsl:choose>


										</ul>
									</li>

								</ul>
							</xsl:otherwise>
						</xsl:choose>

                    </xsl:otherwise>
                </xsl:choose>

            </xsl:otherwise>
        </xsl:choose>




        </div> <!-- sitemap -->
         <div class="req_form"></div>
        </div> <!-- content -->

    </xsl:template>

</xsl:transform>

