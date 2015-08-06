<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:import href="../lib/xsl/common.xsl" />

    <xsl:output method="html" encoding="utf-8"/>
        




    <xsl:template name="doctorSearchFilter">
 		<div class="list_header">
        <div class="h1 mvm">
        <xsl:if test="not(/root/srvInfo/IsLandingPage)">
	        <xsl:attribute name="class">h1 mvm doctor_list_title</xsl:attribute>
        </xsl:if>
        <xsl:choose>
	        <xsl:when test="/root/srvInfo/IsLandingPage">
				Самые востребованные <span class="t-orange t-fs-xl">врачи-<xsl:value-of select="/root/srvInfo/SearchParams/SelectedSpeciality/InPluralLC"/></span> портала DocDoc
	        </xsl:when>
            <xsl:when test="(count(/root/dbInfo/DoctorList/Element) &gt; 0 and string-length(srvInfo/SearchParams/SelectedSpeciality) &gt; 0) or (count(/root/dbInfo/DoctorList/Element) &gt; 0 and string-length(srvInfo/SearchParams/SelectedStations/Element) &gt; 0) or (count(/root/dbInfo/DoctorList/Element) &gt; 0 and /root/srvInfo/SearchParams/SearchWord != '')">
                <xsl:call-template name="digitVariant">
                <xsl:with-param name="digit" select="dbInfo/DoctorCount"/>
                <xsl:with-param name="one" select="'Найден'"/>
                <xsl:with-param name="two" select="'Найдено'"/>
                <xsl:with-param name="five" select="'Найдено'"/>
                </xsl:call-template>&#160;<span class="t-orange t-fs-xl"><xsl:value-of select="/root/dbInfo/DoctorCount"/></span>&#160;<xsl:call-template name="digitVariant">
                <xsl:with-param name="digit" select="dbInfo/DoctorCount"/>
                <xsl:with-param name="one" select="'врач'"/>
                <xsl:with-param name="two" select="'врача'"/>
                <xsl:with-param name="five" select="'врачей'"/>
            </xsl:call-template>
            </xsl:when>
            <xsl:when test="string-length(srvInfo/SearchParams/SelectedSpeciality) &lt; 1 and string-length(srvInfo/SearchParams/SelectedStations/Element) &lt; 1 and /root/srvInfo/SearchParams/SearchWord != '' and count(/root/dbInfo/DoctorList/Element) &lt; 1 ">
                Найдено 0 врачей

                К сожалению, по Вашему запросу врачей не найдено. <a href="/request">Заполните заявку</a> и мы подберем Вам врача в ближайшее время.

                <span class="keys" style="display:none" title="/contextSearch/keywords/{/root/srvInfo/SearchParams/SearchWord}"/>

            </xsl:when>
            <xsl:when test="count(/root/dbInfo/DoctorList/Element) &gt; 1">
                Все врачи
            </xsl:when>
            <xsl:otherwise>

            </xsl:otherwise>
        </xsl:choose>
        </div>

        <!-- NEW -->
        <xsl:if test="(not(/root/srvInfo/SearchParams/SearchWord) or /root/srvInfo/SearchParams/SearchWord = '') and /root/dbInfo/DoctorList/Element and not(/root/srvInfo/IsLandingPage)">

        <div style="clear: both;">
        <ul class="filter_list">
            <li class="filter_item">Сортировка по</li>
            <li class="filter_item filter_sort">
                <xsl:call-template name="linkSort">
                    <xsl:with-param name="url" select="urls/OrderRating"/>
                    <xsl:with-param name="order" select="'rating'"/>
                    <xsl:with-param name="title" select="'Рейтингу'"/>
                </xsl:call-template>
            </li>
            <li class="filter_item filter_sort">
                <xsl:call-template name="linkSort">
                    <xsl:with-param name="url" select="urls/OrderExperience"/>
                    <xsl:with-param name="order" select="'experience'"/>
                    <xsl:with-param name="title" select="'Стажу'"/>
                </xsl:call-template>
            </li>
            <li class="filter_item filter_sort">
                <xsl:call-template name="linkSort">
                    <xsl:with-param name="url" select="urls/OrderPrice"/>
                    <xsl:with-param name="order" select="'price'"/>
                    <xsl:with-param name="title" select="'Стоимости'"/>
                </xsl:call-template>
            </li>

            <li class="filter_item filter_item_checkbox">

                <xsl:if test="dbInfo/Params/KidsReception != 'none'">
                    <a class="link-departure" href="{urls/KidsReception}" style="margin-right:10px;">
                        <label class="filter_label_checkbox">
                            <input class="filter_input_checkbox" type="checkbox" name="kidsReception">
                                <xsl:if test="dbInfo/Params/KidsReception = '1'">
                                    <xsl:attribute name="checked">checked</xsl:attribute>
                                </xsl:if>
                            </input>
                            Детский врач
                        </label>
                    </a>
                </xsl:if>

                <a class="link-departure" href="{urls/Departure}">
                    <label class="filter_label_checkbox">
                        <input class="filter_input_checkbox" type="checkbox" name="departure">
                            <xsl:if test="dbInfo/Params/Departure = '1'">
                                <xsl:attribute name="checked">checked</xsl:attribute>
                            </xsl:if>
                        </input>
                        Выезд на дом
                    </label>
                </a>

                <xsl:if test="/root/srvInfo/Conf/AllowOnlineBooking = '1'">
                    <a class="link-departure" href="{urls/Base}?filter[]=booking" style="margin-right:10px; padding-left:28px;">
                        <xsl:attribute name="href">
                            <xsl:choose>
                                <xsl:when test="dbInfo/Params/Filter/Booking = '1'">
                                    <xsl:value-of select="urls/Base"/>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:value-of select="concat(urls/Base, '?filter[]=booking')"/>
                                </xsl:otherwise>
                            </xsl:choose>

                        </xsl:attribute>

                        <label class="filter_label_checkbox">
                            <input class="filter_input_checkbox" type="checkbox">
                                <xsl:if test="dbInfo/Params/Filter/Booking = '1'">
                                    <xsl:attribute name="checked">checked</xsl:attribute>
                                </xsl:if>
                            </input>
                            Онлайн-запись
                        </label>
                    </a>
                </xsl:if>
            </li>

		</ul><!-- filter list -->
        </div>


        </xsl:if>
		</div>


    </xsl:template>


    <xsl:template name="linkSort">
        <xsl:param name="url" />
        <xsl:param name="order" />
        <xsl:param name="title" />
        <xsl:param name="class" select="'filter_label'" />

        <xsl:variable name="cClass">
            <xsl:choose>
                <xsl:when test="dbInfo/Params/Sort/Direction='asc' and dbInfo/Params/Sort/Type=$order">filter_label s-active i-asc</xsl:when>
                <xsl:when test="dbInfo/Params/Sort/Direction='desc' and dbInfo/Params/Sort/Type=$order">filter_label s-active i-dsc</xsl:when>
                <xsl:otherwise><xsl:value-of select="$class"/></xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <a href="{$url}" class="{$cClass}" rel="nofollow">
            <xsl:value-of select="$title"/>
        </a>
    </xsl:template>

</xsl:stylesheet>
