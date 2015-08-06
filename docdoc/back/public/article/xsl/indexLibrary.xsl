<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:import href="../../lib/xsl/modalWindow.xsl"/>
	<xsl:import href="../../lib/xsl/common.xsl"/>
	<xsl:import href="../../lib/xsl/pager.xsl"/>
	<xsl:import href="../../lib/xsl/filter.xsl"/>
	<xsl:import href="../../lib/xsl/sortBy.xsl"/>

	<xsl:output method="html" encoding="utf-8"/>


	<xsl:template match="/">
		
		<xsl:apply-templates select="root"/>
		
		<script src="/article/js/index.js"></script>
		
		<script src="/lib/js/jquery.cleditor.js" type="text/javascript"></script>
		<div style="position: absolute; margin: 0; top:0; left:0; z-index: 0;">
			<xsl:call-template name="modalWindow">
				<xsl:with-param name="id" select="'modalWin'"/>
				<xsl:with-param name="title" select="'Статья'"/>
				<xsl:with-param name="width" select="'1000'"/>
			</xsl:call-template>
		</div>
		<script>
			var winDeltaY = 150;
			var winDeltaX = 500;
				
			$(document).ready(function(){ 
			});	
			
			/*	Фильтр	*/
			function clearFilterForm () {
				$("#id").val("");
				$("#name").val("");
				$("#shSection").val("");
				$("#status").val("");
				
				$("#sortBy").val("");
				$("#sortType").val("");
			}
		</script>
	</xsl:template>




	<xsl:template match="root">
		<div id="main">
			<h1>Справочник пациента (статьи)</h1>
			

<!--	Фильтр	-->

			<xsl:call-template name="filter">
				<xsl:with-param name="formId" select="'filter'"/>
				<xsl:with-param name="startPage" select="dbInfo/Pager/@currentPageId"/>
				<xsl:with-param name="generalLine">
				
					<xsl:call-template name="sortInit">
						<xsl:with-param name="form" select="'filter'"/>
						<xsl:with-param name="sortBy" select="/root/srvInfo/SortBy"/>
						<xsl:with-param name="sortType" select="/root/srvInfo/SortType"/>
					</xsl:call-template>
					
					<div class="inBlockFilter">
						<div>
						    <label>ID:</label>
						    <div>
								<input name="id" id="id" style="width: 40px" maxlength="5"  value="{srvInfo/Id}"/>
						    </div>
					    </div>
					</div>
					<div class="inBlockFilter">
						<div>
						    <label>Название:</label>
						    <div>
								<input name="name" id="name" style="width: 130px" maxlength="25"  value="{srvInfo/Name}"/>
						    </div>
					    </div>
					</div>
					<div class="inBlockFilter" style="margin-left: 10px">
					    <div>
						    <label>Специализация:</label>
						    <div>
						    	<select name="shSection" id="shSection" style="width: 150px">
									<option value="">--- Любой ---</option>
									<option value="-1">
										<xsl:if test="/root/srvInfo/ShSectionId = '-1'">
											<xsl:attribute name="selected"/>
									    </xsl:if>
										--- Не установлена ---
									</option>
									<xsl:for-each select="dbInfo/Specialization4ArticleDict/Element">
										<option value="{@id}">
										    <xsl:if test="Id = /root/srvInfo/ShSectionId">
												<xsl:attribute name="selected"/>
										    </xsl:if>
										    <xsl:value-of select="Name"/>
										</option>
									</xsl:for-each>
								</select>
						    </div>
					    </div>
					</div>
					<div class="inBlockFilter" style="margin-left: 10px">
						<label>Статус: </label>
						<div>
							<select name="status" id="status" style="width: 150px">
								<option value="">--- Любой ---</option>
								<xsl:for-each select="dbInfo/StatusDict/Element">
									<option value="{@id}">
									    <xsl:if test="@id = /root/srvInfo/Status">
											<xsl:attribute name="selected"/>
									    </xsl:if>
									    <xsl:value-of select="."/>
									</option>
								</xsl:for-each>
							</select>
						</div>
					</div>

				</xsl:with-param>
				<xsl:with-param name="clearFunction">
					clearFilterForm()
				</xsl:with-param>
			</xsl:call-template>





			<div class="m0">
				<div class="total">
					Всего: <strong><xsl:value-of select="dbInfo/Pager/@total"/></strong> 
				</div>
				<div class="actionList">  
					<a href="javascript:editContent('0','library')">Добавить статью</a>
				</div>
			</div>

				
			<div id="resultSet">
				<xsl:variable name="tdCount" select="6"/>
				<table cellpadding="0" cellspacing="1" width="100%" border="0" class="resultSet">
					<col width="30"/>
					<col width="50"/>
					<col/>
					<col width="100"/>
					<col width="50"/>
					

					<tr>
						<th>#</th>
						<th>Id
							<xsl:call-template name="sortBy">
								<xsl:with-param name="sortBy" select="/root/srvInfo/SortBy"/>
								<xsl:with-param name="sortType" select="/root/srvInfo/SortType"/>
								<xsl:with-param name="field" select="'id'"/>
							</xsl:call-template>
						</th>
						<th>Название
							<xsl:call-template name="sortBy">
								<xsl:with-param name="sortBy" select="/root/srvInfo/SortBy"/>
								<xsl:with-param name="sortType" select="/root/srvInfo/SortType"/>
								<xsl:with-param name="field" select="'title'"/>
							</xsl:call-template>
						</th>
						<th>Специальности</th>
						<th>Статус</th>
						<th>&#160;</th>
					</tr>
					<xsl:choose>
						<xsl:when test="dbInfo/ArticleList/Element">
							<xsl:for-each select="dbInfo/ArticleList/Element">
								<xsl:variable name="class">
									<xsl:choose>
										<xsl:when test="(position() div 2) - floor(position() div 2) &gt; 0">odd</xsl:when>
										<xsl:otherwise>even</xsl:otherwise>
									</xsl:choose>
								</xsl:variable>
								<tr id="tr_{@id}" class="{$class}" backclass="{$class}" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','{$class}')">

									<td><xsl:value-of select="position()+number(/root/dbInfo/Pager/Page[@id = ../@currentPageId]/@start)-1"/></td>
									<td align="right"><xsl:value-of select="@id"/></td>
									<td>
										<a href="javascript:editContent('{@id}','library')">
											<xsl:value-of select="Name"/>
										</a>
									</td>
									<td>
										<xsl:value-of select="SectionName"/>
									</td>
									<td align="center">
										<xsl:choose>
											<xsl:when test="Status = '1'">Блокировка</xsl:when>
											<xsl:when test="Status = '0'">Показывается</xsl:when>
										</xsl:choose>
									</td>
									<td>
										<a href="javascript:editContent('{@id}')">Изменить</a>
									</td>

								</tr>
							</xsl:for-each>
						</xsl:when>
						<xsl:otherwise>
							<tr>
								<td colspan="{$tdCount}" align="center">
									<div class="error" style="margin: 20px">Данных не найдено</div>
								</td>
							</tr>
						</xsl:otherwise>
					</xsl:choose>
				</table>
			</div>
			<xsl:call-template name="pager">
				<xsl:with-param name="context" select="dbInfo/Pager"/>
			</xsl:call-template>

		</div>
	</xsl:template>

</xsl:transform>

