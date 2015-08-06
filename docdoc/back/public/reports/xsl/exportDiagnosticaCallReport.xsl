<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:output method="html" encoding="windows-1251"/>


	<xsl:template match="/">
		<xsl:apply-templates select="root"/>
	</xsl:template>




<xsl:template match="root1">&#160;;Итого;;<xsl:for-each select="dbInfo/DayList/Day"><xsl:value-of select="substring(.,1,5)"/>;;;;;</xsl:for-each>
Клиники;20;30;<xsl:for-each select="dbInfo/DayList/Day">В;У;Д;20;;</xsl:for-each>
<xsl:for-each select="dbInfo/ClinicList/Element">
<xsl:variable name="id" select="@id"/>
&#160;<xsl:value-of select="SoftName"/>;<xsl:value-of select="sum(/root/dbInfo/TotalData/Day/Clinic[@id=$id]/ValidDataII/@uniq)"/>;<xsl:value-of select="sum(/root/dbInfo/TotalData/Day/Clinic[@id=$id]/ValidData/@uniq)"/>;<xsl:for-each select="/root/dbInfo/DayList/Day"><xsl:variable name="day" select="."/><xsl:value-of select="/root/dbInfo/TotalData/Day[@day = $day]/Clinic[@id=$id]/Data/@total"/>;<xsl:value-of select="/root/dbInfo/TotalData/Day[@day = $day]/Clinic[@id=$id]/Data/@uniq"/>;<xsl:value-of select="number(/root/dbInfo/TotalData/Day[@day = $day]/Clinic[@id=$id]/Data/@total) -  number(/root/dbInfo/TotalData/Day[@day = $day]/Clinic[@id=$id]/Data/@uniq)"/>;<xsl:value-of select="/root/dbInfo/TotalData/Day[@day = $day]/Clinic[@id=$id]/ValidData/@uniq"/>;&#160;;</xsl:for-each>
</xsl:for-each>
&#160;;&#160;;&#160;;<xsl:for-each select="/root/dbInfo/DayList/Day"><xsl:variable name="day" select="."/><xsl:value-of select="sum(/root/dbInfo/TotalData/Day[@day = $day]/Clinic/Data/@total)"/>;<xsl:value-of select="sum(/root/dbInfo/TotalData/Day[@day = $day]/Clinic/Data/@uniq)"/>;<xsl:value-of select="sum(/root/dbInfo/TotalData/Day[@day = $day]/Clinic/Data/@total) -  sum(/root/dbInfo/TotalData/Day[@day = $day]/Clinic/Data/@uniq)"/>;<xsl:value-of select="sum(/root/dbInfo/TotalData/Day[@day = $day]/Clinic/ValidData/@uniq)"/>;&#160;;</xsl:for-each>
</xsl:template>



<xsl:template match="root">
Даты;;Всего;<xsl:for-each select="dbInfo/ClinicList/Element"><xsl:choose><xsl:when test="ShortName != ''"><xsl:value-of select="ShortName"/></xsl:when><xsl:otherwise><xsl:value-of select="@id"/></xsl:otherwise></xsl:choose>;</xsl:for-each>
20c;;<xsl:value-of select="sum(/root/dbInfo/TotalData/Day/Clinic/ValidData/@uniq)"/>;<xsl:for-each select="dbInfo/ClinicList/Element"><xsl:variable name="id" select="@id"/><xsl:value-of select="sum(/root/dbInfo/TotalData/Day/Clinic[@id=$id]/ValidData/@uniq)"/>;</xsl:for-each>
30c;;<xsl:value-of select="sum(/root/dbInfo/TotalData/Day/Clinic/ValidDataII/@uniq)"/>;<xsl:for-each select="dbInfo/ClinicList/Element"><xsl:variable name="id" select="@id"/><xsl:value-of select="sum(/root/dbInfo/TotalData/Day/Clinic[@id=$id]/ValidData/@uniq)"/>;</xsl:for-each>


<xsl:for-each select="dbInfo/DayList/Day"><xsl:variable name="day" select="."/>
&#160;<xsl:value-of select="substring(.,1,5)"/>;В;<xsl:value-of select="sum(/root/dbInfo/TotalData/Day[@day = $day]/Clinic/Data/@total)"/>;<xsl:for-each select="/root/dbInfo/ClinicList/Element"><xsl:variable name="id" select="@id"/><xsl:value-of select="/root/dbInfo/TotalData/Day[@day = $day]/Clinic[@id=$id]/Data/@total"/>;</xsl:for-each>
&#160;;У;<xsl:value-of select="sum(/root/dbInfo/TotalData/Day[@day = $day]/Clinic/Data/@uniq)"/>;<xsl:for-each select="/root/dbInfo/ClinicList/Element"><xsl:variable name="id" select="@id"/><xsl:value-of select="/root/dbInfo/TotalData/Day[@day = $day]/Clinic[@id=$id]/Data/@uniq"/>;</xsl:for-each>
&#160;;Д;<xsl:value-of select="sum(/root/dbInfo/TotalData/Day[@day = $day]/Clinic/Data/@total) -  sum(/root/dbInfo/TotalData/Day[@day = $day]/Clinic/Data/@uniq)"/>;<xsl:for-each select="/root/dbInfo/ClinicList/Element"><xsl:variable name="id" select="@id"/><xsl:value-of select="number(/root/dbInfo/TotalData/Day[@day = $day]/Clinic[@id=$id]/Data/@total) -  number(/root/dbInfo/TotalData/Day[@day = $day]/Clinic[@id=$id]/Data/@uniq)"/>;</xsl:for-each>
&#160;;20;<xsl:value-of select="sum(/root/dbInfo/TotalData/Day[@day = $day]/Clinic/ValidData/@uniq)"/>;<xsl:for-each select="/root/dbInfo/ClinicList/Element"><xsl:variable name="id" select="@id"/><xsl:value-of select="/root/dbInfo/TotalData/Day[@day = $day]/Clinic[@id=$id]/ValidData/@uniq"/>;</xsl:for-each>
</xsl:for-each>
</xsl:template>



</xsl:transform>

