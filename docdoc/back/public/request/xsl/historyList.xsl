<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:import href="../../lib/xsl/common.xsl"/>
	
	<xsl:output method="html" encoding="utf-8"/>


	<xsl:key name="action" match="/root/dbInfo/ActionDict/Element" use="@id"/>

	<xsl:template match="/">
		<xsl:apply-templates select="root"/>
	</xsl:template>




	<xsl:template match="root">
		<table width="100%" cellpadding="4" cellspacing="1" style="background-color:#fff">
					<col width="60"/>
					<col width="30"/>
					<col width="30"/>
					<col />
					<col swidth="150"/>
					
					<tr>
						<th colspan="2">Время</th>
						<th>Тип</th>
						<th>Текст</th>
						<th>Пользователь</th>
					</tr>
					
					<xsl:for-each select="dbInfo/CommentList/Element">
						<xsl:variable name="class">
							<xsl:choose>
								<xsl:when test="(position() div 2) - floor(position() div 2) &gt; 0">odd</xsl:when>
								<xsl:otherwise>even</xsl:otherwise>
							</xsl:choose>
						</xsl:variable>
						
						<tr id="tr_cell_{@id}" class="{$class}" backclass="{$class}" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','{$class}')">
							<td><xsl:value-of select="CrDate"/></td>
							<td><xsl:value-of select="CrTime"/></td>
							<td align="center">
								<xsl:call-template name="statusState">
									<xsl:with-param name="class" select="concat('req_history ', 'i-st1-', Type) "/>
								</xsl:call-template>
								<!--   <img src="/img/icon/req_comment_type_{Type}.png" title="{key('action', Type)/.}"/> -->
							</td>
							<td>
								<xsl:value-of select="Text"/>
							</td>
							<td>
								<xsl:value-of select="Owner"/>
							</td>
						</tr>
					</xsl:for-each>
				</table>
	</xsl:template>

</xsl:transform>

