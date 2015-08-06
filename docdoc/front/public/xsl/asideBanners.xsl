<?xml version='1.0'  encoding="UTF-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

<xsl:output method="html" encoding="utf-8"/>

<xsl:template match="root">

    <xsl:call-template name="asideBanners" />
</xsl:template>


<xsl:template name="asideBanners">

    <ul class="throughout_banners">
        <li class="throughout_item">
            <a class="throughout_link" href="/library">
                Медицинская библиотека
            </a>
            <p class="throughout_text">
                Полезные статьи о заболеваниях, современных методах лечения и диагностиках.
            </p>
        </li>

        <li class="throughout_item">
            <a class="throughout_link" href="{/root/dbHeadInfo/City/Diagnostica}">
                Диагностические центры
            </a>
            <p class="throughout_text">
                Вам нужно провести диагностику или обследование? Специализированный портал поможет подобрать диагностический центр рядом с домом.
            </p>
        </li>

        <li class="throughout_item">
            <a class="throughout_link" href="/illness">
                Справочник заболеваний
            </a>
            <p class="throughout_text">
                Медицинский справочник болезней от А до Я.
            </p>
        </li>
    </ul>

</xsl:template>

</xsl:transform>

