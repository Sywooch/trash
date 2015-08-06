<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">


    <xsl:output method="html" encoding="utf-8"/>


    <xsl:template name="hotBanners">

        <xsl:param name="bannersId" select="'search'" />

        <xsl:choose>

        <!-- help -->
        <xsl:when test="$bannersId = 'help'">
        <div class="box round" style="margin-top: 8px;">
            <i></i>
            <a href="/library">Медицинская<br />библиотека</a>
            Полезные статьи о заболеваниях, современных методах лечения и диагностиках.
        </div>
        <div class="box round">
            <i style="background-position: 0 -37px;"></i>
            <a href="{/root/dbHeadInfo/City/Diagnostica}">Диагностические<br />центры</a>
            Вам нужно провести диагностику или обследование? Специализированный портал поможет подобрать диагностический центр рядом с домом.
        </div>
        </xsl:when>
        <!-- help end -->




        <!-- library -->
        <xsl:when test="$bannersId = 'library'">
        <div class="box round" style="margin-top: 8px;">
            <i style="background-position: 0 -77px;"></i>
            <a href="/page/help">Что такое<br />docdoc.ru</a>
            Мы собрали для вас массу полезных статей о заболеваниях, современных методах лечения и диагностики
        </div>
        <div class="box round">
            <i style="background-position: 0 -37px;"></i>
            <a href="{/root/dbHeadInfo/City/Diagnostica}">Диагностические<br />центры</a>
            Вам нужно провести диагностику или обследование? Специализированный портал поможет подобрать диагностический центр рядом с домом.
        </div>
        <div class="box round">
            <i style="background-position: 0 -261px;"></i>
            <a href="/illness">Справочник заболеваний</a>
            Медицинский справочник болезней от А до Я.
        </div>
        </xsl:when>
        <!-- library end -->




        <!-- illness -->
        <xsl:when test="$bannersId = 'illness'">
        <div class="box round" style="margin-top: 8px;">
            <i style="background-position: 0 -77px;"></i>
            <a href="/page/help">Что такое<br />docdoc.ru</a>
            Мы собрали для вас массу полезных статей о заболеваниях, современных методах лечения и диагностики
        </div>
        <div class="box round">
            <i style="background-position: 0 -37px;"></i>
            <a href="{/root/dbHeadInfo/City/Diagnostica}">Диагностические<br />центры</a>
            Вам нужно провести диагностику или обследование? Специализированный портал поможет подобрать диагностический центр рядом с домом.
        </div>
        </xsl:when>
        <!-- illness end -->



        <!-- search -->
        <xsl:when test="$bannersId = 'search'">
        <div class="box round">
            <i></i>
            <a href="/library">Медицинская<br />библиотека</a>
            Полезные статьи о заболеваниях, современных методах лечения и диагностиках.
        </div>
        <div class="box round">
            <i style="background-position: 0 -37px;"></i>
            <a href="{/root/dbHeadInfo/City/Diagnostica}">Диагностические<br />центры</a>
            Вам нужно провести диагностику или обследование? Специализированный портал поможет подобрать диагностический центр рядом с домом.
        </div>
        <div class="box round">
            <i style="background-position: 0 -261px;"></i>
            <a href="/illness">Справочник заболеваний</a>
            Медицинский справочник болезней от А до Я.
        </div>
        </xsl:when>
        <!-- search end -->

        </xsl:choose>

    </xsl:template>
    
    
    
    
    
    
    <xsl:template name="rightHotBanner">
    	<xsl:param name="pos" select="'1'"/>
    	<xsl:param name="type" select="'library'"/>
    	
    	<xsl:variable name="style">
    		<xsl:choose>
    			<xsl:when test="$type = 'library'"></xsl:when>
    			<xsl:when test="$type = 'article'"></xsl:when>
    			<xsl:when test="$type = 'diagnostica'">background-position: 0 -37px;</xsl:when>
    			<xsl:when test="$type = 'illness'">background-position: 0 -261px;;</xsl:when>
    			<xsl:when test="$type = 'docdoc'">background-position: 0 -77px;;</xsl:when>
    			<xsl:otherwise></xsl:otherwise>
    		</xsl:choose>
    	</xsl:variable>
    	
    	<div class="box round">
    		<xsl:if test="$pos =  '1'"><xsl:attribute name="style">margin-top: 8px;</xsl:attribute></xsl:if>
            <i style="{$style}"></i>
            
            <xsl:choose>
    			<xsl:when test="$type = 'illness'">
    				<a href="/illness">Справочник заболеваний</a>
            		Медицинский справочник болезней от А до Я.
    			</xsl:when>
    			<xsl:when test="$type = 'diagnostica'">
    				<a href="{/root/dbHeadInfo/City/Diagnostica}">Диагностические<br />центры</a>
		            Вам нужно провести диагностику или обследование? Специализированный портал поможет подобрать диагностический центр рядом с домом.
    			</xsl:when>
    			<xsl:when test="$type = 'library'">
    				<a href="/library">Медицинская<br />библиотека</a>
            		Полезные статьи о заболеваниях, современных методах лечения и диагностиках.
    			</xsl:when>
    			<xsl:when test="$type = 'docdoc'">
    				<a href="/page/help">Что такое<br />docdoc.ru</a>
            		Мы собрали для вас массу полезных статей о заболеваниях, современных методах лечения и диагностики.
    			</xsl:when>
    			<xsl:when test="$type = 'article'">
    				<a href="/library">Справочник<br />пациента</a>
            		Полезные статьи о заболеваниях, современных методах лечения и диагностиках.
    			</xsl:when>
    			<xsl:otherwise></xsl:otherwise>
    		</xsl:choose>
        </div>
    </xsl:template>


</xsl:transform>