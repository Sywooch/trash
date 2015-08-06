<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

    <xsl:output method="html" encoding="utf-8"/>


    <xsl:template name="doctorsTableIndex">

        <ul class="spec_list columns_5">
            <xsl:for-each select="/root/dbInfo/SpecialityList/Group">
                <li class="column">
					<xsl:for-each select="Group">
						<ul class="column_group">
							<xsl:for-each select="Element">
								<li class="spec_list_item">
									<a class="spec_list_link" href="/doctor/{RewriteName}">
										<xsl:choose><!-- looking looking for speciality alt-name to drop it off -->
											<xsl:when test="contains(Name, '(')">
												<xsl:value-of select="substring-before(Name, '(')"/>
											</xsl:when>
											<xsl:otherwise>
												<xsl:value-of select="Name"/>
											</xsl:otherwise>
										</xsl:choose><!-- END looking for speciality alt-name to drop it off END -->
									</a>
								</li>
							</xsl:for-each>
						</ul>
					</xsl:for-each>
                </li>
            </xsl:for-each>
        </ul><!-- spec_list end -->

    </xsl:template>

	<xsl:template name="doctorsTableJson">

		<div class="xml-data-speclist s-hidden">[<xsl:for-each select="/root/dbHeadInfo/SpecialityList/Group/Group"><xsl:for-each select="Element">{"id":<xsl:value-of select="@id"/>,"name":"<xsl:value-of select="Name"/>"}<xsl:if test="position() != last()">,</xsl:if></xsl:for-each><xsl:if test="position() != last()">,</xsl:if></xsl:for-each><xsl:if test="position() != last()">,</xsl:if>]
		</div>

	</xsl:template>

    <xsl:template name="doctorsTablePopup">

        <h2 class="popup_title ui-border_b">
           Выберите специальность врача
        </h2>

		<ul class="spec_list columns_3">
			<xsl:for-each select="/root/dbHeadInfo/SpecialityList/Group">
				<li class="column">
					<xsl:for-each select="Group">
						<ul class="column_group">
							<xsl:for-each select="Element">
								<li class="spec_list_item js-specselect" data-spec-id="{@id}">
									<xsl:if test="not(/root/srvInfo/IsMainPage) or /root/srvInfo/IsMainPage != '1'">
										<xsl:attribute name="data-related-form">search_form</xsl:attribute>
									</xsl:if>
									<a class="spec_list_link" href="/doctor/{RewriteName}">
										<xsl:choose><!-- looking looking for speciality alt-name to drop it off -->
											<xsl:when test="contains(Name, '(')">
												<xsl:value-of select="substring-before(Name, '(')"/>
											</xsl:when>
											<xsl:otherwise>
												<xsl:value-of select="Name"/>
											</xsl:otherwise>
										</xsl:choose><!-- END looking for speciality alt-name to drop it off END -->
									</a>
								</li>
							</xsl:for-each>
						</ul>
					</xsl:for-each>
				</li>
			</xsl:for-each>
		</ul><!-- spec_list end -->

    </xsl:template>


</xsl:transform>