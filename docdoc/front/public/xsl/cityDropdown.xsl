<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:output method="html" encoding="utf-8"/>

    <xsl:template name="cityDropdown">


        <xsl:param name="context" select="dbHeadInfo/CityList"/>

        <div id="ChangeCityBlock" class="b-dropdown tooltip">
            <div class="b-dropdown_item b-dropdown_item__current">
                <span id="CurrentCityName" class="b-dropdown_item__text">
                    <xsl:value-of select="$context/Element[@selected = '1']" />
                </span>
                <span class="b-dropdown_item__icon">
                </span>
            </div>
            <ul class="b-dropdown_list">
                <xsl:for-each select="$context/Element">
                    <xsl:choose>

                    <xsl:when test="@selected = '1'">
                        <li class="b-dropdown_item s-current" data-cityid="{@id}">
                            <xsl:value-of select="."/>
                        </li>
                    </xsl:when>
                    <xsl:otherwise>
                        <li class="b-dropdown_item" data-cityid="{@id}">
                            <xsl:value-of select="."/>
                        </li>
                    </xsl:otherwise>
                    </xsl:choose>

                </xsl:for-each>
            </ul>

            <form class="b-dropdown_form" name="cityselector" method="get" action="/service/changeCity.php" >
                <input class="b-dropdown_input" name="cityid" type="hidden">
                    <xsl:attribute name="value"/>
                </input>
            </form>

        </div>
    </xsl:template>

</xsl:transform>