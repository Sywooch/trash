<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:output method="html" encoding="utf-8"/>


	<xsl:template match="/">
		<xsl:apply-templates select="root"/>
	</xsl:template>




	<xsl:template match="root">
		<div style="width: 300px;">
			Напишите причину отказа:
			<textarea name="rejectReason" id="rejectReason" style="color:#353535; width:295px; height: 100px; resize:vertical;">Отказ. </textarea>
			<div style="position:relative; margin: 20px 10px 30px 0;">		  
				<div class="form" style="width:100px; float:right; margin-left: 10px" onclick="clousePopup()">ЗАКРЫТЬ</div>
				<div class="form" style="width:100px; float:right;" onclick="rejectRequest()">СОХРАНИТЬ</div>
			</div>
		</div>
	</xsl:template>

</xsl:transform>

