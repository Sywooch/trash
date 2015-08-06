<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:import href="doctorsTable.xsl" />

    <xsl:output method="html" encoding="utf-8"/>


    <xsl:template match="/">
        <xsl:apply-templates select="root"/>
    </xsl:template>



<xsl:template name="choose">

    <xsl:choose>
        <xsl:when test="/root/srvInfo/IsMainPage = '1'">
            <xsl:call-template name="chooseSpec"/>
            <xsl:call-template name="chooseGeo"/>
        </xsl:when>
        <xsl:otherwise>
            <!--
            <xsl:call-template name="searchPanelDefault"/>
            -->
            <xsl:call-template name="chooseSpec"/>
            <xsl:call-template name="chooseGeo"/>
        </xsl:otherwise>
    </xsl:choose>

</xsl:template>






<xsl:template name="chooseSpec">
    <xsl:choose>
        <xsl:when test="/root/srvInfo/IsMobile = '1'">
        </xsl:when>
        <xsl:otherwise>
            <input class="js-choose-input-spec" type="hidden" id="spec" name="spec" value="{srvInfo/SearchParams/SelectedSpeciality/Id}"/>
        </xsl:otherwise>
    </xsl:choose>
</xsl:template>





<xsl:template name="chooseGeo">

    <input type="hidden" id="areaMoscow" name="areaMoscow" value="{srvInfo/AreaId}" />
    <input type="hidden" id="districtMoscow" name="districtMoscow" value="{srvInfo/DistrictId}" />

    <xsl:choose>
        <xsl:when test="/root/srvInfo/IsMobile = '1'">

        </xsl:when>
        <xsl:otherwise>

            <input class="js-choose-input-geo" type="hidden" id="stations" name="stations">
                <xsl:attribute name="value">
                    <xsl:if test="srvInfo/SearchParams/SelectedStations/Element">
                        <xsl:for-each select="srvInfo/SearchParams/SelectedStations/Element">
                            <xsl:value-of select="@id"/>
                            <xsl:if test="position() != last()">,</xsl:if>
                        </xsl:for-each>
                    </xsl:if>
                </xsl:attribute>
            </input>

        </xsl:otherwise>
    </xsl:choose>


</xsl:template>

</xsl:transform>

