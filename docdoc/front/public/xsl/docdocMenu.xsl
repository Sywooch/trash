<?xml version='1.0'  encoding="UTF-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

    <xsl:output method="html" encoding="utf-8"/>

    <xsl:template match="root">

        <xsl:call-template name="docdocMenu" />
    </xsl:template>


<xsl:template name="docdocMenu">

<ul class="menu_list">

    <li class="menu_item">
        <a href="#about" class="menu_link s-current">
            О DocDoc
        </a>
        <ul class="menu_sublist">
            <li class="menu_subitem">
                <a href="#ourmission" class="menu_sublink">
                    Наша миссия
                </a>
            </li>
            <li class="menu_subitem">
                <a href="#ourstory" class="menu_sublink">
                    Наша история
                </a>
            </li>
        </ul>
    </li><!--
    <li class="menu_item">
        <a href="#" class="menu_link">
            Контакты
        </a>
    </li>
    <li class="menu_item">
        <a href="#" class="menu_link">
            Новости
        </a>
    </li>
    <li class="menu_item">
        <a href="#" class="menu_link">
            Мы в СМИ
        </a>
    </li>-->

</ul>



</xsl:template>

</xsl:transform>

