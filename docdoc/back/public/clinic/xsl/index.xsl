<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:import href="../../lib/xsl/modalWindow.xsl"/>
	<xsl:import href="../../lib/xsl/common.xsl"/>
	<xsl:import href="../../lib/xsl/pager.xsl"/>
	<xsl:import href="../../lib/xsl/filter.xsl"/>
	<xsl:import href="../../lib/xsl/statisticNote.xsl"/>
	

	<xsl:output method="html" encoding="utf-8"/>

	<xsl:key name="logDict" match="/root/dbInfo/LogDict/Element" use="@id"/>

	<xsl:template match="/">
		<link type="text/css" href="/css/jquery.autocomplete.css" rel="stylesheet" media="screen"/>
		
		<xsl:apply-templates select="root"/>
		
		<div style="position: absolute; margin: 0; top:0; left:0; z-index: 0;">
			<xsl:call-template name="modalWindow">
				<xsl:with-param name="id" select="'modalWin'"/>
				<xsl:with-param name="title" select="'Клиника'"/>
				<xsl:with-param name="width" select="'800'"/>
			</xsl:call-template>
		</div>
		<div style="position: absolute; margin: 0; top:0; left:0; z-index: 0;">
			<xsl:call-template name="modalWindow">
				<xsl:with-param name="id" select="'imgWin'"/>
				<xsl:with-param name="title" select="'Изображение'"/>
				<xsl:with-param name="width" select="'500'"/>
			</xsl:call-template>
		</div>
		<div style="position: absolute; margin: 0; top:0; left:0; z-index: 0;">
			<xsl:call-template name="modalWindow">
				<xsl:with-param name="id" select="'moderationWin'"/>
				<xsl:with-param name="title" select="'Клиника'"/>
				<xsl:with-param name="width" select="'800'"/>
			</xsl:call-template>
		</div>
		<script src="/lib/js/jquery.autocomplete.min.js" type="text/javascript" language="JavaScript"></script>
		<script src="/clinic/js/clinic.js" type="text/javascript" language="JavaScript"></script>
		<script>
			var winDeltaY = 150;
			var winDeltaX = 500;	
			
			$("#shMetro").autocomplete("/clinic/service/getMetroList.htm",{
					delay:10,
					minChars:1,
					max:20,
					autoFill:true,
					multiple: false,
					selectOnly:true,
					extraParams: { cityId: <xsl:value-of select="/root/srvInfo/City/@id"/>  },
					formatResult: function(row) {
						return row[0];
					},
					formatItem: function(row, i, max) {		
						return row[0];
					}
				}).result(function(event, item) {
				});
		</script>
	</xsl:template>




	<xsl:template match="root">
		<div id="main">
			<h1>Клиники</h1>
			
<!--	Статистика	-->			
			<xsl:if test="/root/dbInfo/ClinicStat/Element">
				<xsl:call-template name="statisticNote">
					<xsl:with-param name="body">
						<xsl:for-each select="/root/dbInfo/StatusDict/Element">
							<xsl:variable name="id" select="@id"/>
							<div style="margin: 0; height: 18px">
								<div style="margin: 0; float: left; margin-right: 20px"><xsl:value-of select="."/> </div>
								<div style="margin: 0; float: right"><strong><xsl:value-of select="/root/dbInfo/ClinicStat[@type = /root/srvInfo/Type]/Element[@status = $id]/."/></strong></div>
							</div>
						</xsl:for-each>
						<div class="m0 wbt" style="padding-top: 2px; height: 18px">
							<div class="m0 mr20" style="float: left;">Всего </div>
							<div class="m0" style="float: right"><strong><xsl:value-of select="sum(/root/dbInfo/ClinicStat[@type = /root/srvInfo/Type]/Element/.)"/></strong></div>
						</div>
					</xsl:with-param>
				</xsl:call-template>
			</xsl:if>
			

<!--	Фильтр	-->

			<xsl:call-template name="filter">
				<xsl:with-param name="formId" select="'filterForm'"/>
				<xsl:with-param name="startPage" select="dbInfo/Pager/@currentPageId"/>
				<xsl:with-param name="generalLine">
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
								<input name="title" id="title" style="width: 130px" maxlength="25"  value="{srvInfo/Title}"/>
						    </div>
					    </div>
					</div>
					<div class="inBlockFilter">
						<div>
						    <label>Alias:</label>
						    <div>
								<input name="alias" id="alias" style="width: 130px" maxlength="25"  value="{srvInfo/Alias}"/>
						    </div>
					    </div>
					</div>
					<!--
					<div class="inBlockFilter" style="margin-left: 10px">
					    <div>
						    <label>Телефон:</label>
						    <div>
								<input name="shPhone" id="shPhone" style="width: 130px" maxlength="25"  value="{srvInfo/Phone}" disabled=""/>
						    </div>
					    </div>
					</div>
					-->
					<!-- <div class="inBlockFilter" style="margin-left: 10px">
					    <div>
						    <label>Филиалы:</label>
						    <div>
								<input type="checkbox" name="shBranch" id="shBranch" value="1">
									<xsl:if test="/root/srvInfo/Branch = '1'">
										<xsl:attribute name="checked"/>
									</xsl:if>
								</input>
						    </div>
					    </div>
					</div> -->
					<div class="inBlockFilter" style="margin-left: 10px">
					    <div>
						    <label>Метро:</label>
						    <div>
								<input name="shMetro" id="shMetro" value="{srvInfo/shMetro}" style="width: 150px"   type="Text" maxlength="150"/>
						    </div>
					    </div>
					</div>
					
					<div class="inBlockFilter" style="margin-left: 10px">
						<label>Тип: </label>
						<div>
							<select name="type" id="type" style="width: 150px">
								<option value="">--- Любой ---</option>
								<option value="clinic" style="background:url('/img/icon/hospital16.png') no-repeat; padding-left: 20px">
								    <xsl:if test="/root/srvInfo/Type = 'clinic'">
										<xsl:attribute name="selected"/>
								    </xsl:if>
								    Клиника
								</option>
								<option value="center" style="background:url('/img/icon/dc16.png') no-repeat; padding-left: 20px">
								    <xsl:if test="/root/srvInfo/Type = 'center'">
										<xsl:attribute name="selected"/>
								    </xsl:if>
								    Диагн.центр
								</option>
								<option value="privatDoctor" style="background:url('/img/icon/privatDoctor.png') no-repeat; padding-left: 20px">
								    <xsl:if test="/root/srvInfo/Type = 'privatDoctor'">
										<xsl:attribute name="selected"/>
								    </xsl:if>
								    Частный врач
								</option>
								
							</select>
						</div>
					</div>
					
					<div class="inBlockFilter" style="margin-left: 10px">
						<label>Статус: </label>
						<div>
							<select name="status" id="status" style="width: 150px">
								<option value="">--- Любой ---</option>
								<xsl:for-each select="dbInfo/StatusDict/Element">
									<option value="{@id}" style="background:url('/img/icon/status_{@id}.png') no-repeat; padding-left: 20px">
									    <xsl:if test="@id = /root/srvInfo/Status">
											<xsl:attribute name="selected"/>
									    </xsl:if>
									    <xsl:value-of select="."/>
									</option>
								</xsl:for-each>
							</select>
						</div>
					</div>

					<div class="inBlockFilter" style="margin-left: 10px">
						<label for="shModeration">Сверка изменения: </label>
						<div>
							<input type="checkbox" name="shModeration" id="shModeration" value="1">
								<xsl:if test="/root/srvInfo/Moderation = '1'">
									<xsl:attribute name="checked"/>
								</xsl:if>
							</input>
						</div>
					</div>

				</xsl:with-param>

				
			</xsl:call-template>




			<xsl:if test="srvInfo/CreateClinic = '1'">
				<div class="m0">
					<div class="total">
						Всего: <strong><xsl:value-of select="dbInfo/Pager/@total"/></strong> (без учета филиалов)
					</div>
					<div class="actionList">
						<a href="javascript:editContent('0')">Добавить клинику</a>
					</div>
				</div>
			</xsl:if>
			
			

			
				
			<div id="resultSet">
				<xsl:variable name="tdCount" select="13"/>
				<table cellpadding="0" cellspacing="1" width="100%" border="0" class="resultSet">
					<col width="30"/>
					<col width="30"/>
					<col/>
					<col width="30"/>
					<col width="200"/>
					<col width="120"/>
					<col width="30"/>
					<col width="30"/>
					<col width="30"/>
					<col width="30"/>
					<col width="90"/>
					<col width="30"/>
					<col width="50"/>
					

					<tr>
						<th>#</th>
						<th>Id</th>
						<th colspan="2">Клиника</th>
						<th>Контактное лицо</th>
						<th>Телефон</th>
						<th>URL</th>
						<th title="Внутренний рейтинг клиники">Рт.</th>
						<th title="Филиалы">Фил.</th>
						<th title="Врачи">Вр.</th>
						<th>Дата рег.</th>
						<th>Статус</th>
						<th>&#160;</th>
					</tr>
					
					<xsl:choose>
						<xsl:when test="dbInfo/ClinicList/Element">
							<xsl:for-each select="dbInfo/ClinicList/Element">
								<xsl:variable name="id" select="@id"/>
								<xsl:call-template name="line">
									<xsl:with-param name="context" select="."/>
									<xsl:with-param name="position" select="position()"/>
								</xsl:call-template>
								<xsl:for-each select="ClinicList/Element">
									<xsl:call-template name="line">
										<xsl:with-param name="context" select="."/>
										<xsl:with-param name="position" select="position()"/>
									</xsl:call-template>
								</xsl:for-each>
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
				<xsl:with-param name="formName" select="'filterForm'"/>
			</xsl:call-template>

		</div>
	</xsl:template>
	
	
	
	<xsl:template name="line">
		<xsl:param name="context" select="."/>
		<xsl:param name="position" select="1"/>
		
		<xsl:variable name="class">
			<xsl:choose>
				<xsl:when test="($position div 2) - floor($position div 2) &gt; 0">odd</xsl:when>
				<xsl:otherwise>even</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<tr id="tr_{$context/@id}" class="{$class}" backclass="{$class}" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','{$class}')">

			<td>
				<xsl:if test="$context/ParentId = 0">
					<xsl:value-of select="$position+number(/root/dbInfo/Pager/Page[@id = ../@currentPageId]/@start)-1"/>
				</xsl:if>
			</td>
			<td align="right"><xsl:value-of select="$context/@id"/></td>
			<td class="clinic_name">
				<xsl:if test="$context/ParentId != 0">
					<xsl:attribute name="style">padding-left: 20px</xsl:attribute>
				</xsl:if>
				<a>
					<xsl:attribute name="href">
						<xsl:choose>
							<xsl:when test="$context/ParentId != '0'">
								javascript:editContent('<xsl:value-of select="$context/@id"/>','<xsl:value-of select="$context/ParentId"/>')
							</xsl:when>
							<xsl:otherwise>
								javascript:editContent('<xsl:value-of select="$context/@id"/>')</xsl:otherwise>
						</xsl:choose>
					</xsl:attribute>
					<xsl:choose>
						<xsl:when test="$context/ShortName != ''"><xsl:value-of select="$context/ShortName"/></xsl:when>
						<xsl:otherwise><xsl:value-of select="$context/Title"/></xsl:otherwise>
					</xsl:choose>
					
				</a>
				<xsl:choose>
					<xsl:when test="$context/Age = 'child'">
						<img align="right" src="/img/icon/girl.png" title="Детская клиника"/>
					</xsl:when>
					<xsl:when test="$context/Age = 'adult'">
						<img align="right" src="/img/icon/adult_clinic.png" title="Для взрослых"/>
					</xsl:when>
					<!-- <xsl:otherwise>
						<img src="/img/icon/family.png" title="Многопрофильная"/>
					</xsl:otherwise> -->
				</xsl:choose>
			</td>
			<td align="center">
				
				<xsl:if test="IsClinic = 'yes'">
					<img src="/img/icon/hospital16.png" title="Клиника"/>
				</xsl:if>
				<xsl:if test="IsDiagnostic = 'yes'">
					<img src="/img/icon/dc16.png" title="Диагностический центр"/>
				</xsl:if>
				<xsl:if test="IsPrivatDoctor = 'yes'">
					<img src="/img/icon/privatDoctor.png" title="Частный врач"/>
				</xsl:if>
			</td>
			<td>
				<xsl:value-of select="$context/ContactName"/>
			</td>
			<td>
				<xsl:choose>
					<xsl:when test="$context/Phone != ''"><xsl:value-of select="$context/Phone"/></xsl:when>
					<xsl:when test="$context/PhoneList/Element/PhoneFormat"><xsl:value-of select="$context/PhoneList/Element/PhoneFormat"/></xsl:when>
					<xsl:when test="$context/AsteriskPhone != ''">Ast:<xsl:value-of select="$context/AsteriskPhone/@digit"/></xsl:when>
				</xsl:choose>
			</td>
			<td align="center">
				<xsl:if test="$context/URL != ''">
					<a href="http://{$context/URL}" target="_blank">url</a>
				</xsl:if>
				
			</td>
			<td align="center">
				<xsl:value-of select="$context/TotalRating"/>
			</td>
			<td align="center">
				<xsl:choose>
					<xsl:when test="$context/ParentId != 0"></xsl:when>
					<xsl:when test="count($context/ClinicBranchList/Element) &gt; 0">
						<span class="link" onclick="editContent('{$context/@id}', '0', '3')">
							<xsl:value-of select="count($context/ClinicBranchList/Element)"/>
						</span>
					</xsl:when>
					<xsl:otherwise>0</xsl:otherwise>
				</xsl:choose>
				
			</td>
			<td align="center">
				<xsl:if test="$context/DoctorCount != ''">
					<xsl:choose>
						<xsl:when test="$context/DoctorCount = '0'">0</xsl:when>
						<xsl:otherwise>
							<a href="/doctor/index.htm?shClinicId={$context/@id}&amp;shClinic={$context/Title}"><xsl:value-of select="$context/DoctorCount"/></a>
						</xsl:otherwise>
					</xsl:choose>
					
				</xsl:if>
				
			</td>
			<td align="center">
				<xsl:value-of select="$context/CrDate"/>
			</td>
			<td align="center">
				<xsl:variable name="status" select="$context/Status"/>
				<xsl:choose>
					<xsl:when test="$context/Status = '1'">
						<img src="/img/icon/status_1.png" title="{/root/dbInfo/StatusDict/Element[@id = $status]/.}"/>
					</xsl:when>
					<xsl:when test="$context/Status = '2'">
						<img src="/img/icon/status_2.png" title="{/root/dbInfo/StatusDict/Element[@id = $status]/.}"/>
					</xsl:when>
					<xsl:when test="$context/Status = '3'">
						<img src="/img/icon/status_3.png" title="{/root/dbInfo/StatusDict/Element[@id = $status]/.}"/>
					</xsl:when>
					<xsl:when test="$context/Status = '4'">
						<img src="/img/icon/status_4.png" title="{/root/dbInfo/StatusDict/Element[@id = $status]/.}"/>
					</xsl:when>
					<xsl:when test="$context/Status = '5'">
						<img src="/img/icon/status_5.png" title="{/root/dbInfo/StatusDict/Element[@id = $status]/.}"/>
					</xsl:when>
					<xsl:otherwise>
						<img src="/img/common/null.gif" title="..."/>
					</xsl:otherwise>
				</xsl:choose>
				<!--   <xsl:value-of select="/root/dbInfo/StatusDict/Element[@id = $status]/."/> -->
			</td>
			<td>
				<a href="javascript:editContent('{$context/@id}')">Изменить</a>
				<xsl:if test="$context/ModerationId != ''">
					<a href="#" id="ClinicModerationLink{$context/@id}" onclick="moderateClinicContent('{$context/@id}', $(this).closest('tr')); return false;" title="Модерация изменений" style="margin-left:5px;">
						<img src="/img/icon/editor.png"/>
					</a>
				</xsl:if>
			</td>

		</tr>
	</xsl:template>

</xsl:transform>

