<?xml version='1.0'  encoding="UTF-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

    
    <xsl:decimal-format decimal-separator = '.' grouping-separator = ' ' NaN = ' '/>

    <xsl:output method="html" encoding="utf-8"/>

    
   <xsl:template match="/">
		<xsl:apply-templates select="root"/>
   </xsl:template>
   
   
   
   
	<xsl:template match="root">
		<div id="specs">
			<div class="head">Выберите специальность врача</div>
			<xsl:variable name="maxItemsPerColumn" select="round((count(/root/dbInfo/SectorList/Element) + 3) div 3)"/>
			<table>
				<tr>
					<td>
						<xsl:for-each select="/root/dbInfo/SectorList/Element[position() &lt;= $maxItemsPerColumn]">
							<xsl:call-template name="item">
								<xsl:with-param name="context" select="."/>
							</xsl:call-template>
						</xsl:for-each>
					</td>
					<td>
						<xsl:for-each select="/root/dbInfo/SectorList/Element[position() &gt; $maxItemsPerColumn and position() &lt;= $maxItemsPerColumn*2]">
							<xsl:call-template name="item">
								<xsl:with-param name="context" select="."/>
							</xsl:call-template>
						</xsl:for-each>
					</td>
					<td>
						<xsl:for-each select="/root/dbInfo/SectorList/Element[position() &gt; $maxItemsPerColumn*2 ]">
							<xsl:call-template name="item">
								<xsl:with-param name="context" select="."/>
							</xsl:call-template>
						</xsl:for-each>
						
						<xsl:call-template name="emptyLine">
							<xsl:with-param name="pos" select="count(/root/dbInfo/SectorList/Element)"/>
							<xsl:with-param name="max" select="($maxItemsPerColumn*3 - 1)"/>
						</xsl:call-template>
						
						<p>
							<input id="filter-sector-all" class="filter-sector" type="radio" value="" name="filter_sector" data-title="по всем специальностям"/>
							<label for="filter-sector-all">по всем специальностям</label>
						</p>
					</td>
				</tr>
						
							
							
			</table>
			<div id="sector-submit" class="button but-specs">
				<span>Применить</span>	
			</div>
		</div>
	</xsl:template>
	
	
	
	
	<xsl:template name="item">
		<xsl:param name="context" select="."/>
		
		<p>
			<input class="filter-sector" id="filter-sector-{$context/Id}" data-title="{$context/Name}" type="radio" name="filter_sector" value="{$context/Id}" />
			<label for="filter-sector-{$context/Id}">
				<xsl:value-of select="$context/Name"/>
			</label>
		</p>
	</xsl:template>
	
	
	
	<xsl:template name="emptyLine">
		<xsl:param name="pos" select="1"/>
		<xsl:param name="max" select="1"/>
		
		<xsl:if test="number($pos) &lt; number($max)">
			<p><br/></p>
			<xsl:call-template name="emptyLine">
				<xsl:with-param name="pos" select="number($pos)+ 1"/>
				<xsl:with-param name="max" select="$max"/>
			</xsl:call-template>
		</xsl:if>
	</xsl:template>
	
</xsl:transform>
