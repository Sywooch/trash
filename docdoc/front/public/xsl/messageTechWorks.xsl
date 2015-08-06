<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">


    <xsl:output method="html" encoding="utf-8"/>


    <xsl:template match="/root">

        <xsl:call-template name="messageTechWorks"/>

    </xsl:template>

    <xsl:template name="messageTechWorks">
        <!-- <div class="back-to-main">
            <i></i>
            <a href="">Вернуться на главную</a>
        </div> -->
        <div class="registration round shadow div-thanks">
            <div class="thx">
                <div class="doctor"></div>
                <h1>Технические работы</h1>
                <div class="wait">
                    <!-- phone number -->
                    <!-- request ID -->
                    <p>
                        Извините, ведутся технические работы, сайт будет доступен через несколько минут.
                    </p>
                </div>
            </div>
            <div class="clear"></div>
        </div>
    </xsl:template>


</xsl:transform>
