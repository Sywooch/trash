<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	
	<xsl:import href="map.xsl"/>
	
	<xsl:output method="html" encoding="utf-8"/>
	
	
	<xsl:template match="/">
		<xsl:apply-templates select="root"/>
		<script>
			<xsl:choose>
        		<xsl:when test="/root/dbInfo/Pager/Page">
        			var totalPage = <xsl:value-of select="/root/dbInfo/Pager/Page[position() = last()]/@id"/>;
        		</xsl:when>
        		<xsl:otherwise>
        		var totalPage = 0;
        		</xsl:otherwise>
        	</xsl:choose>
	
			var points = [
				<xsl:for-each select="/root/dbInfo/DCenterListAll/Element">
               		{name : <xsl:value-of select="position()"/>, id : <xsl:value-of select="@id"/>, coords : [<xsl:value-of select="Lat"/>, <xsl:value-of select="Long"/>] }
               		<xsl:if test="position() != last()">, </xsl:if>
                </xsl:for-each>
			];
            
            myMap.geoObjects.each(function (geoObject) {
				myMap.geoObjects.remove(geoObject);
			});
						
			setPoints (points, <xsl:value-of select="/root/srvInfo/Step"/>);	
			initItem ();
		</script>
	</xsl:template>
	
	
	
	
	<xsl:template match="root">
		<xsl:for-each select="dbInfo/DCenterList/Element">
			<xsl:call-template name="resultSetLine">
				<xsl:with-param name="pos" select="position()"/>
			</xsl:call-template>
		</xsl:for-each>
	</xsl:template>
</xsl:transform>

