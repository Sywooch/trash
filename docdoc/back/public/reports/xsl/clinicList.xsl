<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:import href="../../lib/xsl/common.xsl"/>

	<xsl:output method="html" encoding="utf-8"/>


	<xsl:template match="/">
		<xsl:apply-templates select="root"/>
	</xsl:template>




	<xsl:template match="root">
		<!-- <xsl:copy-of select="dbInfo/SearchClinicList"/> -->
		<xsl:choose>
			<xsl:when test="dbInfo/SearchClinicList/Element">
				<table cellpading="1" cellspacing="1" width="100%" id="clinicList">
					<col/>
					<col width="20px"/>
					
					<xsl:for-each select="dbInfo/SearchClinicList/Element">
						<xsl:variable name="class">
							<xsl:choose>
								<xsl:when test="(position() div 2) - floor(position() div 2) &gt; 0">odd</xsl:when>
								<xsl:otherwise>even</xsl:otherwise>
							</xsl:choose>
						</xsl:variable>
						<tr id="searchClinic{Id}" clinicId="{Id}" class="{$class}" backclass="{$class}" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','{$class}')">
							<td>
								<a href="javascript:selectClinic({Id},'{Name}')"><xsl:value-of select="Name"/></a>
							</td>
							<td>
								<span class="i-status arrow-right" style="cursor: pointer; margin-left: 5px; margin-top: 2px" onclick="selectClinic({Id},'{Name}')"/>
							</td>
						</tr>
					</xsl:for-each>
				</table>
			</xsl:when>
			<xsl:otherwise>
				<div class="em">Клиник не найдено</div>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

</xsl:transform>

