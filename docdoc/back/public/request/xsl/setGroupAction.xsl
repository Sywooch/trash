<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:output method="html" encoding="utf-8"/>


	<xsl:template match="/">
		<xsl:apply-templates select="root"/>
	</xsl:template>




	<xsl:template match="root">
		<div style="padding: 10px">
			Установить отмеченным заявкам следующий статус:
			<div class="mt5">
				<select name="setStatus" id="setStatus" style="width: 300px">
					<xsl:for-each select="dbInfo/StatusDict/Element">
						<option value="{@id}" style="background:url('/img/icon/req_status_16_{@id}.png') no-repeat 2px 3px; padding: 4px 0 0 25px; margin: 2px 0 2px 0px; height: 18px">
						    <xsl:value-of select="."/>
						</option>
					</xsl:for-each>
				</select>
			</div>

			<xsl:if test="dbInfo/TypeView != 'partners'">
				<div id="rejectionDiv" style="display:none;">
					<input type="checkbox" name="isReject" id="isReject" style="margin-right:5px;vertical-align: middle;"/>
					<label for="isReject" style="padding-right: 35px;">Отказ</label>

					<select name="rejectReasonId" disabled="disabled" style="margin-right:5px;">
						<option value="7">Тест</option>
						<option value="4">Дубль</option>
						<option value="24">В услуге больше не нуждается</option>
					</select>
				</div>
			</xsl:if>

			<div class="form {dbInfo/TypeView}" style="width:120px; float: right; padding: 3px 10px 2px 10px; margin: 0;" onclick="setStatus($('#setStatus').val(), '{dbInfo/TypeView}')">УСТАНОВИТЬ</div>
			<img src="/img/common/clBtBig.gif" width="20" height="20"  alt="закрыть" style="position: absolute; cursor: pointer; right: 5px; top: 5px;" title="закрыть" onclick="clousePopup();" border="0"/>
		</div>
	</xsl:template>

</xsl:transform>

