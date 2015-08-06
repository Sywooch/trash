<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:output method="html" encoding="utf-8"/>

	<xsl:template name="listSpecByStation">

		<xsl:if test="/root/dbInfo/SpecialitiesByStation">
			<div class="spec_list_by_station">
				<h3>Доктора: <xsl:value-of select="srvInfo/SearchParams/SelectedStations/Element/Name" /></h3>

				<ul class="columns_5">
					<xsl:for-each select="/root/dbInfo/SpecialitiesByStation/Group">
						<li class="column">
							<ul class="column_group">
								<xsl:for-each select="Element">
									<li class="spec_list_item">
										<a href="/doctor/{Alias}/{/root/srvInfo/SearchParams/SelectedStations/Element/Alias}">
											<xsl:value-of select="InPlural" />
										</a>
									</li>
								</xsl:for-each>
							</ul>
						</li>
					</xsl:for-each>
				</ul>
			</div>
		</xsl:if>

	</xsl:template>

</xsl:stylesheet>