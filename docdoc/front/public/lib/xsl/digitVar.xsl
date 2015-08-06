<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template name="digitVariant">
        <xsl:param name="one" />
        <xsl:param name="two" />
        <xsl:param name="five" />
        <xsl:param name="digit" />

        <xsl:variable name="last_digit">
            <xsl:value-of select="$digit mod 10"/>
        </xsl:variable>
        <xsl:variable name="last_two_digits">
            <xsl:value-of select="$digit mod 100"/>
        </xsl:variable>
        <xsl:choose>
            <xsl:when test="$last_digit = 1 and $last_two_digits != 11">
                <xsl:copy-of select="$one" />
            </xsl:when>
            <xsl:when
                    test="
                $last_digit = 2 and $last_two_digits != 12
                or
                $last_digit = 3 and $last_two_digits != 13
                or
                $last_digit = 4 and $last_two_digits != 14
                "
                    >
                <xsl:copy-of select="$two" />
            </xsl:when>
            <xsl:otherwise>
                <xsl:copy-of select="$five" />
            </xsl:otherwise>
        </xsl:choose>


    </xsl:template>

</xsl:stylesheet>