<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	   
	<xsl:import href="../../lib/xsl/common.xsl"/> 
	
	<xsl:param name="debug" select="'no'"/>
	
	<xsl:output method="html" encoding="utf-8"/>
	<xsl:decimal-format decimal-separator = '.' grouping-separator = ' ' NaN = ''/>
	
	<xsl:template match="/">	
		<link href="/css/jquery.cleditor.css" type="text/css" rel="stylesheet" media="screen"/>
		<style>	  
			.inputForm	{ color:#353535; width:100%;}
			.inputForm textarea	{ color:#353535; width:100%; resize:vertical; }
		</style>
		
		
		<xsl:apply-templates select="root"/>

		<script type="text/javascript">
			<xsl:choose>
				<xsl:when test="root/dbInfo/Illness/Data/Id">
					$('#modalWin h1').html("<xsl:value-of select="/root/dbInfo/Illness/Data/Name"/> ");
				</xsl:when>		
				<xsl:otherwise>
					$('#modalWin h1').html("Новое заболевание");
				</xsl:otherwise>
			</xsl:choose>
		</script>
	</xsl:template>
	
	
	
	
	<xsl:template match="root">	
		
		<xsl:call-template name="commonData"/>
		
		<div class="clear"/>
		<div id="statusWin" class="warning" style="margin-top: 10px"></div>	  

		<!-- 	DEBUG MODE	-->
		<xsl:if test="$debug = 'yes'">
			<div class="debug">
				<a href="/article/illness.htm?id={srvInfo/Id}&amp;debug=yes" target="_blank">Debug mode</a>
			</div>
		</xsl:if>
	</xsl:template>

	
	
	

	
	<xsl:template name="commonData">
		<xsl:param name="context" select="dbInfo/Illness/Data"/>
		 			
		<div id="editMode" style="position: relative">
			<form name="editForm" id="editForm" method="post">
				
				
				<table width="100%">
					<col width="150"/>
					<col/>
					
					<tr>
						<td>Идентификатор:</td>
						<td>
							<strong><xsl:value-of select="$context/Id"/></strong>
							<input type="hidden" name="id" value="{$context/Id}"/>
						</td>
					</tr>
					<tr>
						<td>Название:</td>
						<td>
							<input name="title" id="title" value="{$context/Name}" style="width: 100%" class="inputForm" maxlength="255"/>
						</td>
					</tr>
					<tr>
						<td>Врач:</td>
						<td>
							<select name="sectorId" id="sectorId" style="width: 200px;">
								<xsl:for-each select="/root/dbInfo/SpecializationDict/Element">
									<option value="{@id}">
										<xsl:if test="@id = $context/SectorId"><xsl:attribute name="selected"/></xsl:if>
										<xsl:value-of select="."/>
									</option>
								</xsl:for-each>
							</select>
						</td>
					</tr>
					<tr>
						<td>О заболевании:</td>
						<td>
							<textarea name="descr" id="descr" class="inputForm"  style="height: 60px;">
								<xsl:value-of select="$context/Description"/>
							</textarea>
						</td>
					</tr>
					<tr>
						<td>Симптомы:</td>
						<td>
							<textarea name="symptom" id="symptom" class="inputForm"  style="height: 60px;">
								<xsl:value-of select="$context/Symptom"/>
							</textarea>
						</td>
					</tr>
					<tr>
						<td>Лечение:</td>
						<td>
							<textarea name="treatment" id="treatment" class="inputForm"  style="height: 60px;">
								<xsl:value-of select="$context/Treatment"/>
							</textarea>
						</td>
					</tr>
					<tr><td colspan="2" align="center">ИЛИ</td></tr>

					<tr>
						<td>Текст статьи:</td>
						<td>
							<textarea name="textArticle" id="textArticle" class="inputFormLong">
								<xsl:value-of select="$context/Body"/>
							</textarea>
						</td>
					</tr>
					<tr><td colspan="2"></td></tr>
					<tr>
						<td>&#160;</td>
						<td>Служебные данные</td>
					</tr>
					<tr>
						<td>Alias:</td>
						<td>
							<input name="alias" id="alias" value="{$context/Alias}" style="width: 100%" class="inputForm" maxlength="255"/>
						</td>
					</tr>
					<tr>
						<td>Доп. текст:</td>
						<td>
							Симптомы &#160;<input name="fullName" id="fullName" value="{$context/FullName}" style="width: 200px" class="inputForm" maxlength="255"/>
						</td>
					</tr>
					<tr>
						<td>SEO заголовок:</td>
						<td>
							<textarea name="metaTitle" id="metaTitle" class="inputForm"  style="height: 40px;">
								<xsl:value-of select="$context/MetaTitle"/>
							</textarea>
						</td>
					</tr>
					<tr>
						<td>Ключевые слова:</td>
						<td>
							<textarea name="metaKeyWd" id="metaKeyWd" class="inputForm"  style="height: 40px;">
								<xsl:value-of select="$context/MetaKeyWd"/>
							</textarea>
						</td>
					</tr>
					<tr>
						<td>SEO описание:</td>
						<td>
							<textarea name="metaDescr" id="metaDescr" class="inputForm"  style="height: 60px;">
								<xsl:value-of select="$context/MetaDescr"/>
							</textarea>
						</td>
					</tr>
					<tr>
						<td>Отключить статью:</td>
						<td>
							<input type="checkbox" name="isDiasble" value="1">
								<xsl:if test="$context/IsDisabled = '1'"><xsl:attribute name="checked"/></xsl:if>
							</input>
						</td>
					</tr>
				</table>
								
			</form>
			<div style="position:relative; margin: 20px 10px 30px 0;">		  
				<div class="form" style="width:100px; float:right; margin-left: 10px" onclick="(modalWinKey === 'close') ? $('#modalWin').hide() : window.location.reload()">ЗАКРЫТЬ</div>
				<div class="form" style="width:100px; float:right;" onclick="saveContent('#editForm', 'illness')">СОХРАНИТЬ</div>
				<xsl:if test="(/root/srvInfo/UserData/Rights/Right = 'ADM' or /root/srvInfo/UserData/Rights/Right = 'ACM') and $context/Id">
					<div class="form" style="width:100px; float:right; margin-right: 10px" onclick="deleteContent('{$context/Id}', 'illness')">УДАЛИТЬ</div>
				</xsl:if>
			</div>
		</div>	 
			  
	</xsl:template>	 

</xsl:transform>

