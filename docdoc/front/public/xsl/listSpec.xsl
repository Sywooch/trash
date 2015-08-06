<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:output method="html" encoding="utf-8"/>

	<xsl:template name="listSpec">

		<xsl:if test="count(/root/dbInfo/RelatedSpecList/Element) &gt; 0">
			<h3>Связанные специальности</h3>
			<ul class="related_list">
				<xsl:for-each select="/root/dbInfo/RelatedSpecList/Element">
					<li class="related_item">
						<a href="/doctor/{./Rewrite_name}" title="{Name}" class="related_link">
							<xsl:attribute name="href">/doctor/<xsl:value-of select="./Rewrite_name"/></xsl:attribute>
							<xsl:value-of select="./Name"/>
						</a>
					</li>
				</xsl:for-each>
			</ul>
		</xsl:if>
		<xsl:if test="count(/root/dbInfo/SectorList/Element) &gt; 0">
			<h3>Все специальности</h3>
			<ul class="related_list">
				<xsl:for-each select="/root/dbInfo/SectorList/Element">
					<li class="related_item">
						<a href="/doctor/{./RewriteName}" title="{Name}" class="related_link">
							<xsl:choose>
								<xsl:when test="/root/dbInfo/IsLandingPage = '1'">
									<xsl:attribute name="href">/landing/<xsl:value-of select="./RewriteName"/>
									</xsl:attribute>
								</xsl:when>
								<xsl:otherwise>
									<xsl:attribute name="href">/doctor/<xsl:value-of select="./RewriteName"/>
									</xsl:attribute>
								</xsl:otherwise>
							</xsl:choose>
							<xsl:choose>
								<xsl:when
										test="number(/root/srvInfo/SearchParams/SelectedSpeciality/Id) = number(./@id)">
									<xsl:value-of select="./Name"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="./Name"/>
								</xsl:otherwise>
							</xsl:choose>
						</a>
					</li>
				</xsl:for-each>
			</ul>
		</xsl:if>
	</xsl:template>

</xsl:stylesheet>