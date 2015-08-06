<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	
	<xsl:import href="map.xsl"/>
	
	<xsl:output method="html" encoding="utf-8"/>
	
	
	<xsl:template match="/">
		<xsl:apply-templates select="root"/>
		<script>
			var pointsNew = [
				<xsl:for-each select="/root/dbInfo/DCenterList/Element">
               		{name : <xsl:value-of select="position() +( number(/root/srvInfo/StartPage) - 1 )*number(/root/srvInfo/Step)"/>, id : <xsl:value-of select="@id"/>, coords : [<xsl:value-of select="Lat"/>, <xsl:value-of select="Long"/>] }
               		<xsl:if test="position() != last()">, </xsl:if>
                </xsl:for-each>
			];
            
            var countPoints = <xsl:value-of select="count(/root/dbInfo/DCenterList/Element)"/>;
            <![CDATA[
            
            var strB = '<span class="marker"><span>';
			var strA = '<span class="markerPoint"><span>';

            for ( var i = 0; i < countPoints; i++ ) {
            	//myMap.geoObjects.remove(pointsNew[i].id);
            	myMap.geoObjects.each(function (geoObject) {
		           	if (geoObject.properties.get('id') == pointsNew[i].id) {
				    	myMap.geoObjects.remove(geoObject);
				    }
				});
				var coordinates = pointsNew[i].coords,
	            properties = {
	                name : pointsNew[i].name,
	                id : pointsNew[i].id,  
	                body : 'Идет загрузка данных ...',
	                iconContent:strB + pointsNew[i].name + "</span></span>"
	            },
	            options = {
	            	iconImageHref: '/img/common/null.gif', // картинка иконки
	                iconImageSize: [50, 50], 
	                iconOffset: [-25, -20], 
	                iconImageOffset: [0,-20],
	                maxWidth : 350,
	                balloonCloseButton : false,
	                balloonShadow : false,
	                balloonLayout : myBalloonContentBodyLayout,
	                balloonOffset : [0, -20]
	            },
	            placemark = new ymaps.Placemark(coordinates, properties, options);
		        myMap.geoObjects.add(placemark, pointsNew[i].id);
		        placemark.events.add('click', onClick);
				placemark.events.add('mouseenter', onMouseOver);
	        	placemark.events.add('mouseleave', onMouseOut);	
			}
            ]]>
            
            initItem ();
		</script>
	</xsl:template>
	
	
	
	
	<xsl:template match="root">
		<xsl:for-each select="dbInfo/DCenterList/Element">
			<xsl:call-template name="resultSetLine">
				<xsl:with-param name="pos" select="position()+( number(/root/srvInfo/StartPage) - 1 )*number(/root/srvInfo/Step)"/>
			</xsl:call-template>
		</xsl:for-each>
	</xsl:template>
</xsl:transform>

