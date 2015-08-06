<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:output method="html" encoding="utf-8"/>


	<xsl:template match="/">
		<xsl:apply-templates select="root"/>
	</xsl:template>




	<xsl:template match="root">
		<div style="width: 400px;">
			<table>
				<col width="120px"/>
				<tr>
					<td>Врач:</td>
					<td>
						<span style="font-size: 14px"><xsl:value-of select="dbInfo/Doctor/Name"/></span>
					</td>
				</tr>
				<tr>
					<td>Специальность:</td>
					<td>
						<xsl:for-each select="dbInfo/Doctor/SectorList/Sector ">
							<xsl:choose>
								<xsl:when test="@id = /root/srvInfo/Sector">
									<span style="font-weight: bold"><xsl:value-of select="."/></span>
								</xsl:when>
								<xsl:otherwise>
									<span><xsl:value-of select="."/></span>
								</xsl:otherwise>
							</xsl:choose>
							
							<xsl:if test="position() != last()">, </xsl:if>
						</xsl:for-each>
					</td>
				</tr>
				<tr>
					<td>Клиника:</td>
					<td><xsl:value-of select="dbInfo/Doctor/Clinic"/></td>
				</tr>
				<tr>
					<td>Телефоны:</td>
					<td>
						<xsl:for-each select="dbInfo/Doctor/PhoneList/Element">
							<div style="margin: 2px 0 2px 0">
								<span style="width: 100px; margin: 0 10px 0 0"><xsl:value-of select="Label"/>:</span>
								<span class="link"><xsl:value-of select="PhoneFormat"/></span>	
							</div>
						</xsl:for-each>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						Ваш комментарий:
						<textarea name="transferComment" id="transferComment" style="color:#353535; width:395px; height: 100px; resize:vertical; margin-top: 4px; padding: 2px">Переведен. </textarea>
					</td>
				</tr>
			</table> 
			
			
			<div style="position:relative; margin: 20px 10px 30px 0;">		  
				<div class="form" style="width:100px; float:right; margin-left: 10px" onclick="clousePopup()">ЗАКРЫТЬ</div>
				<div class="form" style="width:100px; float:right;" onclick="alert('ok')">ПЕРЕВЕСТИ</div>
			</div>
		</div>
	</xsl:template>

</xsl:transform>

