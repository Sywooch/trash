<?xml version='1.0'  encoding="UTF-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

    <xsl:template name="cycle">
        <xsl:param name="times"/>
        <xsl:param name="counter" select="1"/>
        <xsl:param name="content"/>

		<!-- <xsl:value-of select="$times"/> -->

        <xsl:if test="number($times) &gt; 0">

            <xsl:copy-of select="$content"/>

            <xsl:if test="$counter &lt; $times">
                <xsl:call-template name="cycle">
                    <xsl:with-param name="times" select="$times"/>
                    <xsl:with-param name="counter" select="$counter+1"/>
                    <xsl:with-param name="content" select="$content"/>
                </xsl:call-template>
            </xsl:if>
        </xsl:if>
    </xsl:template>
</xsl:transform>