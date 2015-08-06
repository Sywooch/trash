<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:decimal-format decimal-separator = '.' grouping-separator = ' ' NaN = ' '/>

    <xsl:key name="sector" match="/root/dbInfo/SectorList/Element" use="@id"/>

    <xsl:output method="html" encoding="utf-8"/>

    <xsl:template match="root">
        <xsl:call-template name="doctorsBest" />
    </xsl:template>




    <xsl:template name="doctorsBest">

        <!-- -->


    </xsl:template>

</xsl:stylesheet>