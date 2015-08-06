<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output method="html" encoding="utf-8"/>
	
	
	<xsl:template name="sortBy">
		<xsl:param name="sortBy" select="'id'"/>
		<xsl:param name="sortType" select="'desc'"/>
		<xsl:param name="field" select="'id'"/>
		
		<style>
			.sortBlock	{background: url("/img/common/sortUpDown.png") no-repeat 0 0; width: 11px; height: 11px; display: inline-block}
			.sortUp {background-position: 0px -22px }
			.sortDown {background-position: 0px -11px }
			.sortNornal {background-position: 0px 0px }
		</style>
		<a href="javascript:setSort('{$field}')">
			<span>
				<xsl:attribute name="class"> 
				<xsl:choose>
					<xsl:when test="$sortBy = $field and $sortType='asc'">sortBlock sortDown</xsl:when>
					<xsl:when test="$sortBy = $field and $sortType='desc'">sortBlock sortUp</xsl:when>
					<xsl:otherwise>sortBlock sortNornal</xsl:otherwise>
				</xsl:choose>  
				<!-- <xsl:choose>
					<xsl:when test="$sortBy = $field and $sortType='asc'"><img src="/img/common/sortAsc.gif" width="14" height="14" alt="" border="0"/></xsl:when>
					<xsl:when test="$sortBy = $field and $sortType='desc'"><img src="/img/common/sortDesc.gif" width="14" height="14" alt="" border="0"/></xsl:when>
					<xsl:otherwise><img src="/img/common/sort.gif" width="14" height="14" alt="сортировка" title="сортировка" align="absmiddle" border="0"/></xsl:otherwise>
				</xsl:choose> -->
				</xsl:attribute>
			</span>
		</a>
	</xsl:template>
	
	
	
	<xsl:template name="sortInit">
		<xsl:param name="form" select="'filter'"/>
		<xsl:param name="sortBy" select="'id'"/>
		<xsl:param name="sortType" select="'desc'"/>
		
		
		<script type="text/javascript" language="JavaScript">
			function setSort (field, formName) {
				formName = formName || '<xsl:value-of select="$form"/>';
				
				<![CDATA[
				if ($('#sortBy').val() == field ) {
					if ($('#sortType').val() == 'asc') {$('#sortType').val('desc');}
					else if ($('#sortType').val() == 'desc') {$('#sortType').val('asc');}
					else {$('#sortType').val('desc');}
				} else {
					$('#sortBy').val(field); 
					$('#sortType').val('desc');
				}
				if (document.forms[formName].filter) {
					document.forms[formName].filter.value=1;
				}
				document.forms[formName].submit();
				]]>
			}
		</script>
		<input name="sortBy" id="sortBy" type="hidden" value="{$sortBy}"/>
		<input name="sortType" id="sortType" type="hidden" value="{$sortType}"/>
	</xsl:template>

</xsl:transform>

