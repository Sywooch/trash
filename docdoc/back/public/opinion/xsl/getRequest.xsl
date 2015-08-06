<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	   
	<xsl:param name="debug" select="'no'"/>
	
	<xsl:output method="html" encoding="utf-8"/>
	
	
	<xsl:template match="/">	
		<xsl:apply-templates select="root"/>
	</xsl:template>
	
	
	
	
	<xsl:template match="root">
		<div style="margin:0; width: 400px; padding: 5px 10px 5px 10px" class="wb">
		<div style="margin: 0">Запрос № <xsl:value-of select="srvInfo/Id"/></div>
		<xsl:if test="dbInfo/Request">
			Клиент:  <xsl:value-of select="dbInfo/Request/Client"/> <xsl:value-of select="dbInfo/Request/ClientPhone"/>
			<br/>
			Врач: <xsl:value-of select="dbInfo/Request/Doctor"/>
			<br/>
			Дата создания:  <xsl:value-of select="dbInfo/Request/CrDate"/>
			<xsl:if test="dbInfo/Request/RecordList">
				<div id="audioResultset" class="scroll-pane-audio">
					<table width="100%" border="0">
						<xsl:for-each select="dbInfo/Request/RecordList/Element">
							<tr>
								<td>
									<div style="margin:0">
										<div style="margin:0; width:200px; overflow:hidden; float:left; ">
											<audio controls="true" style="background:#fff;" id="audio-{Record_id}" playbackRate="1.0">
												<source src="{Filename}" type="audio/mpeg" />
											</audio>
										</div>
										<div style="float:left;">
											<button type="button" class="audio-rate-button" data-record="audio-{Record_id}">x1</button>
										</div>
										<xsl:if test="CrDateTime != ''">
											<span style="margin: 0 5px 0 5px"><xsl:value-of select="CrDateTime"/></span>
										</xsl:if>
										<xsl:if test="IsOpinion = 'yes'">
											<div  style="float:left; width: 16px; height:16px; background: url('/img/icon/ok.png') no-repeat; margin: 2px 0 0 5px;" title="Отзыв"/>
										</xsl:if>
									</div>
									<div class="clear"/>
								</td>
							</tr>
						</xsl:for-each>
					</table>
				</div>
			</xsl:if>
			<div style="margin: 5px 0 0 0; text-align: right">
				<!--
				<span class="link" onclick="$('#requestId').val('{srvInfo/Id}')">Связать с отзывом</span>
				<span class="delimiter">|</span>
				-->
				<span class="link" onclick="$('#requestDetail').html('')">Скрыть</span>
			</div>
		</xsl:if>
		</div>
	</xsl:template>

 
</xsl:transform>

