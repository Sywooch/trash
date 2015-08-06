<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">


    <xsl:output method="html" encoding="utf-8"/>


    <xsl:template match="/root">

        <xsl:call-template name="errorPage"/>

    </xsl:template>

<xsl:template name="errorPage">
<main class="l-main l-wrapper" role="main">
    <h1>404. Такой страницы нет.</h1>
    <p>
        Вы можете <a href="/">вернуться на главную страницу.</a>
    </p>
</main>
</xsl:template>


</xsl:transform>
