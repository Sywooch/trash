<?xml version='1.0'  encoding="UTF-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

<xsl:output method="html" encoding="utf-8"/>

<xsl:template name="geoLinks">

	<xsl:if test="count(/root/dbInfo/NearestStations) > 0">
		<div>
			<xsl:choose>
				<xsl:when test="/root/dbInfo/DoctorList">
					<p><b>Врачи рядом с метро</b></p>
					<ul>
						<xsl:for-each select="/root/dbInfo/NearestStations/Element">
							<li><a href="/doctor/{/root/dbInfo/SpecialityAlias}/{./RewriteName}"><xsl:value-of select="./Name"/></a></li>
						</xsl:for-each>
					</ul>
					<p>в районе</p>
					<ul>
						<xsl:for-each select="/root/dbInfo/NearestDistricts/Element">
							<li>
								<xsl:choose>
									<xsl:when test="./Area != ''">
										<a href="/doctor/{/root/dbInfo/SpecialityAlias}/area/{./Area}/{./RewriteName}"><xsl:value-of select="./DistrictName"/></a>
									</xsl:when>
									<xsl:when test="./Area = ''">
										<a href="/doctor/{/root/dbInfo/SpecialityAlias}/district/{./RewriteName}"><xsl:value-of select="./DistrictName"/></a>
									</xsl:when>
								</xsl:choose>
							</li>
						</xsl:for-each>
					</ul>
				</xsl:when>
				<xsl:when test="/root/dbInfo/ClinicList">
					<p><b>Клиники рядом с метро</b></p>
					<ul>
						<xsl:for-each select="/root/dbInfo/NearestStations/Element">
							<li><a href="/clinic/station/{./RewriteName}"><xsl:value-of select="./Name"/></a></li>
						</xsl:for-each>
					</ul>
					<p>в районе</p>
					<ul>
						<xsl:for-each select="/root/dbInfo/NearestDistricts/Element">
							<li>
								<xsl:choose>
									<xsl:when test="./Area != ''">
										<a href="/clinic/area/{./Area}/{./RewriteName}"><xsl:value-of select="./DistrictName"/></a>
									</xsl:when>
									<xsl:when test="./Area = ''">
										<a href="/clinic/district/{./RewriteName}"><xsl:value-of select="./DistrictName"/></a>
									</xsl:when>
								</xsl:choose>
							</li>
						</xsl:for-each>
					</ul>
				</xsl:when>
			</xsl:choose>
			<br/>
		</div>
	</xsl:if>

</xsl:template>

</xsl:transform>

