<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:output method="html" encoding="utf-8"/>

	<xsl:template name="status">
		<xsl:param name="id"/>
		<xsl:param name="name"/>
		<xsl:param name="style" select="''"/>
		<xsl:param name="withName" select="'yes'"/>
		
		
		<span style="display: inline-block; line-height: 18px; margin: 0px; padding: 0px 0 0 20px; text-align: left; background: url(/img/icon/req_status_16_{$id}.png) no-repeat 0 0px;  min-height: 16px; {$style}">
			<xsl:if test="$withName = 'yes'">
				<xsl:value-of select="$name"/>
			</xsl:if>
		</span>
	</xsl:template>

	<xsl:template name="multi-select">
		<xsl:param name="context" select="Element"/>
		<xsl:param name="param" />
		<xsl:param name="selectedItems" select="ElementId" />
		<xsl:param name="withoutItem" />

		<dl class="dropdown">
			<dt>
				<a>
					<span class="hidden">
						<xsl:if test="$selectedItems">
							<xsl:attribute name="style">display:none;</xsl:attribute>
						</xsl:if>
						Выбрать..
					</span>
					<p class="multiSel">
						<xsl:if test="$selectedItems = 0 and $withoutItem">
							<span title="{$withoutItem},"><xsl:value-of select="$withoutItem"/>,</span>
						</xsl:if>
						<xsl:for-each select="$selectedItems">
							<xsl:variable name="id" select="."/>
							<xsl:for-each select="$context">
								<xsl:choose>
									<xsl:when test="Name">
										<xsl:if test="Id=$id">
											<span title="{Name},"><xsl:value-of select="Name"/>,</span>
										</xsl:if>
									</xsl:when>
									<xsl:otherwise>
										<xsl:if test="@id=$id">
											<span title="{.},"><xsl:value-of select="."/>,</span>
										</xsl:if>
									</xsl:otherwise>
								</xsl:choose>
							</xsl:for-each>
						</xsl:for-each>
					</p>
				</a>
			</dt>
			<dd>
				<div class="multiSelect">
					<ul>
						<li class="multiAllSelect">
							<xsl:choose>
								<xsl:when test="count($selectedItems) = count($context)">
									<xsl:attribute name="class">multiAllSelect clear</xsl:attribute>
									Удалить все
								</xsl:when>
								<xsl:otherwise>
									Выбрать все
								</xsl:otherwise>
							</xsl:choose>
						</li>
						<xsl:if test="$withoutItem">
							<li class="noItem">
								<input type="checkbox" class="multiCheckbox noItem" name="{$param}" value="0">
									<xsl:if test="$selectedItems = 0">
										<xsl:attribute name="checked"/>
									</xsl:if>
								</input>
								<span>
									<xsl:if test="$selectedItems = 0">
										<xsl:attribute name="class">act</xsl:attribute>
									</xsl:if>
									<xsl:value-of select="$withoutItem"/>
								</span>
							</li>
						</xsl:if>
						<xsl:for-each select="$context">
							<li>
								<xsl:choose>
									<xsl:when test="Name">
										<input type="checkbox" class="multiCheckbox" name="{$param}" value="{Id}">
											<xsl:if test="$selectedItems = Id">
												<xsl:attribute name="checked"/>
											</xsl:if>
										</input>
										<span>
											<xsl:if test="$selectedItems = Id">
												<xsl:attribute name="class">act</xsl:attribute>
											</xsl:if>
											<xsl:value-of select="Name"/>
										</span>
									</xsl:when>
									<xsl:otherwise>
										<xsl:if test="not(@display) or @display='yes'">
											<input type="checkbox" class="multiCheckbox" name="{$param}" value="{@id}">
												<xsl:if test="$selectedItems = @id">
													<xsl:attribute name="checked"/>
												</xsl:if>
											</input>
											<span>
												<xsl:if test="$selectedItems = @id">
													<xsl:attribute name="class">act</xsl:attribute>
												</xsl:if>
												<xsl:value-of select="."/>
											</span>
										</xsl:if>
									</xsl:otherwise>
								</xsl:choose>
							</li>
						</xsl:for-each>
					</ul>
				</div>
			</dd>
		</dl>
	</xsl:template>

</xsl:transform>

