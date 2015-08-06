<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
               xmlns:Xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:output method="html" encoding="utf-8"/>

    <xsl:template name="pageUrlOld">
        <xsl:param name="Id" />
        /<xsl:choose>
            <xsl:when test="/root/srvInfo/SearchParams/SelectedSpeciality/Alias and /root/srvInfo/SearchParams/SelectedSpeciality/Alias != ''">doctor/<xsl:value-of select="/root/srvInfo/SearchParams/SelectedSpeciality/Alias"/>/<xsl:if test="/root/srvInfo/SearchParams/SelectedStations/Element">stations/<xsl:for-each select="/root/srvInfo/SearchParams/SelectedStations/Element"><xsl:value-of select="Id"/><xsl:if test="position()!= last()">,</xsl:if></xsl:for-each>/</xsl:if></xsl:when>
            <xsl:otherwise>search/</xsl:otherwise>
        </xsl:choose>page/<xsl:value-of select="$Id"/>
    </xsl:template>
    
    
    
    <xsl:template name="pageUrl">
       <xsl:param name="Id" />
       
       <xsl:value-of select="/root/srvInfo/URL4Pager"/>/page/<xsl:value-of select="$Id"/>
    </xsl:template>
    
    
    
    

    <xsl:template name="doctorsPager">
        <xsl:param name="pagerData"  />

        <xsl:variable name="baseUrl">/doctor<xsl:if test="/root/srvInfo/SearchParams/SelectedSpeciality/Alias">/<xsl:value-of select="/root/srvInfo/SearchParams/SelectedSpeciality/Alias"/>
        </xsl:if>
            <xsl:if test="/root/srvInfo/SearchParams/SelectedStations">/stations/<xsl:for-each select="/root/srvInfo/SearchParams/SelectedStations/Element">
                <xsl:value-of select="@id"/>
                <xsl:if test="position() != last()">,</xsl:if>
            </xsl:for-each>
            </xsl:if>
            <xsl:if test="/root/srvInfo/SearchParams/Area">/area/<xsl:value-of select="/root/srvInfo/SearchParams/Area/Alias"/></xsl:if>
            <xsl:if test="/root/srvInfo/SearchParams/District">/<xsl:value-of select="/root/srvInfo/SearchParams/District/Alias"/></xsl:if>
        </xsl:variable>

        <xsl:if test="count($pagerData/Pages/Element) &gt; 1">

        <ul class="pager">
            <!--
            <li class="pager_item pager_showall">
                <a href="#" class="pager_item_link">??? показать всех на одной странице</a>
            </li>
            -->


            <xsl:if test="number($pagerData/Params/CurrentPage) != 1">
            <li class="pager_item">
                <a href="{$baseUrl}/{root/srvInfo/SearchParams/SelectedSpeciality/Alias}" class="pager_item_link pager_item_nav">
                    <xsl:attribute name="href">
                        <xsl:call-template name="pageUrl">
                            <xsl:with-param name="Id" select="number($pagerData/Params/CurrentPage) -1" />
                        </xsl:call-template>
                    </xsl:attribute>
                    ←
                </a>
            </li>
            </xsl:if>

            <xsl:for-each select="$pagerData/Pages/Element[position() &gt; (number($pagerData/Params/CurrentPage) -5)  and  position() &lt; (number($pagerData/Params/CurrentPage) + 5)]">

                <li class="pager_item">
                    <!--
                    <xsl:if test="Id = $pagerData/Params/CurrentPage">
                        <xsl:attribute name="class">
                            pager_item s-current
                        </xsl:attribute>
                    </xsl:if>
                    -->

                    <a class="pager_item_link">
                        <xsl:if test="Id = $pagerData/Params/CurrentPage">
                            <xsl:attribute name="class">
                                pager_item_link s-current
                            </xsl:attribute>
                        </xsl:if>

                        <xsl:attribute name="href">
                            <xsl:call-template name="pageUrl">
                                <xsl:with-param name="Id" select="Id" />
                            </xsl:call-template>
                        </xsl:attribute>
                        <xsl:value-of select="Id"/>
                    </a>
                </li>
            </xsl:for-each>

            <xsl:if test="number($pagerData/Params/CurrentPage) &lt; count($pagerData/Pages/Element)">
                <li class="pager_item">
                    <a href="{$baseUrl}/{root/srvInfo/SearchParams/SelectedSpeciality/Alias}" class="pager_item_link pager_item_nav">
                        <xsl:attribute name="href">
                            <xsl:call-template name="pageUrl">
                                <xsl:with-param name="Id">
                                    <xsl:value-of select="number($pagerData/Params/CurrentPage) + 1"/>
                                    <!--<xsl:choose>
                                        <xsl:when test="(number($pagerData/Params/CurrentPage) + 1) &lt; count($pagerData/Pages/Element)">
                                            <xsl:value-of select="number($pagerData/Params/CurrentPage) + 1"/>
                                        </xsl:when>
                                        <xsl:otherwise>
                                            <xsl:value-of select="number($pagerData/Params/CurrentPage)"/>
                                        </xsl:otherwise>
                                    </xsl:choose>-->
                                </xsl:with-param>
                            </xsl:call-template>
                        </xsl:attribute>
                        →
                    </a>
            </li>

            </xsl:if>
        </ul>

        </xsl:if>
    </xsl:template>


</xsl:transform>