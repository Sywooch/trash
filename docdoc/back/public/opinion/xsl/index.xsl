<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:import href="../../lib/xsl/modalWindow.xsl"/>
	<xsl:import href="../../lib/xsl/common.xsl"/>
	<xsl:import href="../../lib/xsl/pager.xsl"/>
	<xsl:import href="../../lib/xsl/filter.xsl"/>
	<xsl:import href="../../lib/xsl/sortBy.xsl"/>
	<xsl:import href="../../lib/xsl/statisticNote.xsl"/>

	<xsl:output method="html" encoding="utf-8"/>

	<xsl:key name="logDict" match="/root/dbInfo/LogDict/Element" use="@id"/>

	<xsl:template match="/">
		<link type="text/css" href="/css/jquery-ui-1.7.2.custom.css" rel="stylesheet" />
		<link type="text/css" href="/css/jquery.autocomplete.css" rel="stylesheet" media="screen"/>
		<style>	  
			.downOpinion	{ color:#bf0000;  }
			.upOpinion		{ color:#008400;  }
		</style>
		
		<xsl:apply-templates select="root"/>
		
		<div style="position: absolute; margin: 0; top:0; left:0; z-index: 0;">
			<xsl:call-template name="modalWindow">
				<xsl:with-param name="id" select="'modalWin'"/>
				<xsl:with-param name="title" select="'Отзыв'"/>
				<xsl:with-param name="width" select="'700'"/>
			</xsl:call-template>
		</div>
		<script src="/lib/js/jquery-ui-1.7.2.custom.min.js" type="text/javascript" ></script>
		<script src='/lib/js/jquery.autocomplete.min.js' type='text/javascript' language="JavaScript"></script>
		<script src="/lib/js/ui.datepicker-ru.js" type="text/javascript" ></script>
		<script src="js/opinion.js" type="text/javascript"></script>
		<script>
			var winDeltaY = 150;
			var winDeltaX = 500;	
			
			$(document).ready(function() {
				$(function(){
					$.datepicker.setDefaults($.extend($.datepicker.regional["ru"]));
					$("#crDateShFrom").datepicker( {
						changeMonth : true,
						changeYear: true,
						duration : "fast",
						maxDate : "+1y",
	
						showButtonPanel: true
					});
					$("#crDateShTill").datepicker( {
						changeMonth : true,
						changeYear: true,
						duration : "fast",
						maxDate : "+1y",
	
						showButtonPanel: true
					});
				});
				
				$("#shSector").autocomplete("/doctor/service/getSectorList.htm",{
					delay:10,
					minChars:2,
					max:20,
					autoFill:true,
					selectOnly:true,
					formatResult: function(row) {
						return row[0];
					},
					formatItem: function(row, i, max) {		
						return row[0];
					}
				}).result(function(event, item) {
					$("#shSectorId").val(item[1]);
					$('#startPage').val("1");
				});
				
				$("#shDoctor").autocomplete("/opinion/service/getDoctorList.htm",{
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
					$("#shDoctorId").val(item[1]);
				});
			});
			
			$("div.helper").mouseover( function() {
				$(this).stop(true).delay(300).children().show();
			});
			$("div.helper").mouseleave( function() {
				$(".helpEltR").hide();
			});
			
			
			/*	Фильтр	*/
			function clearFilterForm () {
				$("#id").val("");
				
				$("#shDoctor").val("");
				$("#shDoctorId").val("");
				
				$("#crDateShFrom").val("");
				$("#crDateShTill").val("");
				
				$("#shSector").val("");
				$("#shSectorId").val("");
				$("#shAllow").val("");
				$("#shOrigin").val("");
				$("#shAuthor").val("");
				
				$("#shSector").val("");
				$("#shSectorId").val("");
				$("#ratingColor").val("");
				
				$("#sortBy").val("");
				$("#sortType").val("");
			}
		
		</script>
	</xsl:template>




	<xsl:template match="root">
		<div id="main">
			<h1>Отзывы</h1>
			
<!--	Статистика	-->			
			<xsl:if test="/root/dbInfo/OpinionStat/Element">
				<xsl:call-template name="statisticNote">
					<xsl:with-param name="body">
						<div class="m0 wbt" style="padding-top: 2px; height: 18px">
							<div class="m0 mr20" style="float: left;">Опубликовано</div>
							<div class="m0" style="float: right"><strong><xsl:value-of select="sum(/root/dbInfo/OpinionStat/Element[@status = 'publish']/.)"/></strong></div>
						</div>
						
						<div class="m0 mt5" style="padding-top: 2px; height: 18px">
							<div class="m0 mr20" style="float: left;">Оригинальные</div>
							<div class="m0" style="float: right"><strong><xsl:value-of select="sum(/root/dbInfo/OpinionStat/Element[@status = 'original']/.)"/></strong></div>
						</div>
						<div class="m0" style="padding-top: 2px; height: 18px">
							<div class="m0 mr20" style="float: left;">Редакторские</div>
							<div class="m0" style="float: right"><strong><xsl:value-of select="sum(/root/dbInfo/OpinionStat/Element[@status = 'editor']/.)"/></strong></div>
						</div>
						
						<div class="m0 mt5" style="padding-top: 2px; height: 18px">
							<div class="m0 mr20" style="float: left;">С сайта</div>
							<div class="m0" style="float: right"><strong><xsl:value-of select="sum(/root/dbInfo/OpinionStat/Element[@status = 'guest']/.)"/></strong></div>
						</div>
						<div class="m0" style="padding-top: 2px; height: 18px">
							<div class="m0 mr20" style="float: left;">Контент</div>
							<div class="m0" style="float: right"><strong><xsl:value-of select="sum(/root/dbInfo/OpinionStat/Element[@status = 'content']/.)"/></strong></div>
						</div>
						
					</xsl:with-param>
				</xsl:call-template>
			</xsl:if>			

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
							<label>Врач:</label>
							<div>
							    <input name="shDoctor" id="shDoctor" value="{srvInfo/ShDoctor}" style="width:200px" maxlength="100"/>
								<input type="hidden" name="shDoctorId" id="shDoctorId" value="{srvInfo/ShDoctorId}"/>
							</div>

						</div>
					</div>
					<div class="inBlockFilter">
						<div>
							<label>Дата создания записи:</label>
							<div>
							    c:
							    <input name="crDateShFrom" id="crDateShFrom" style="width:70px" maxlength="12" value="{srvInfo/CrDateShFrom}"/>
							    &#160;по:
							    <input name="crDateShTill" id="crDateShTill" style="width:70px" maxlength="12" value="{srvInfo/CrDateShTill}"/>
							</div>

						</div>
					</div>
					<div class="inBlockFilter" style="margin-left: 10px">
					    <div>
						    <label>Происхождение:</label>
						    <div>
							<select name="shAuthor" id="shAuthor" style="width: 140px">
								<option value="">--- Любой ---</option>
								<option value="oper" style="background:url('/img/icon/receptionist.png') no-repeat; padding-left: 20px">
								    <xsl:if test="/root/srvInfo/ShAuthor = 'oper'">
										<xsl:attribute name="selected"/>
								    </xsl:if>
								    Оператор
								</option>
								<option value="cont" style="background:url('/img/icon/business-contact.png') no-repeat; padding-left: 20px">
								    <xsl:if test="/root/srvInfo/ShAuthor = 'cont'">
										<xsl:attribute name="selected"/>
								    </xsl:if>
								    Контент менеджер
								</option>
								<option value="gues" style="background:url('/img/icon/earth.png') no-repeat; padding-left: 20px">
								    <xsl:if test="/root/srvInfo/ShAuthor = 'gues'">
										<xsl:attribute name="selected"/>
								    </xsl:if>
								    С сайта
								</option>
							</select>
						</div>
					    </div>
					</div>
					<div class="inBlockFilter" style="margin-left: 10px">
						<label>Тип: </label>
						<div>
							<select name="shOrigin" id="shOrigin" style="width: 110px">
								<option value="">--- Любой ---</option>
								<option value="original" style="background:url('/img/icon/woman.png') no-repeat; padding-left: 20px">
								    <xsl:if test="/root/srvInfo/ShOrigin = 'original'">
										<xsl:attribute name="selected"/>
								    </xsl:if>
								    Оригинальный
								</option>
								<option value="editor" style="background:url('/img/icon/editor.png') no-repeat; padding-left: 20px">
								    <xsl:if test="/root/srvInfo/ShOrigin = 'editor'">
										<xsl:attribute name="selected"/>
								    </xsl:if>
								    Редакторский
								</option>
								<option value="combine" style="background:url('/img/icon/yin-yang.png') no-repeat; padding-left: 20px">
								    <xsl:if test="/root/srvInfo/ShOrigin = 'combine'">
										<xsl:attribute name="selected"/>
								    </xsl:if>
								    Смешанный
								</option>
							</select>
						</div>
					</div>
					
					<div class="inBlockFilter" style="margin-left: 10px">
						<label>Статус: </label>
						<div>
							<select name="shAllow" id="shAllow" style="width: 120px">
								<option value="">--- Любой ---</option>
								<option value="1" style="background:url('/img/icon/check_ok.png') no-repeat; padding-left: 20px">
								    <xsl:if test="/root/srvInfo/ShAllow = '1'">
										<xsl:attribute name="selected"/>
								    </xsl:if>
								    Опубликован
								</option>
								<option value="0" style="background:url('/img/icon/check_no.png') no-repeat; padding-left: 20px">
								    <xsl:if test="/root/srvInfo/ShAllow = '0'">
										<xsl:attribute name="selected"/>
								    </xsl:if>
								    Не опубликован
								</option>
								<option value="2" style="background:url('/img/icon/disable.png') no-repeat; padding-left: 20px">
								    <xsl:if test="/root/srvInfo/ShAllow = '2'">
										<xsl:attribute name="selected"/>
								    </xsl:if>
								    Заблокирован
								</option>
							</select>
						</div>
					</div>

				</xsl:with-param>

				<xsl:with-param name="addLine">
					<div class="inBlockFilter" style="margin-left: 10px">
					    <div>
						    <label>Специализация:</label>
						    <div>
								<input name="shSector" id="shSector" style="width: 200px" maxlength="25"  value="{srvInfo/ShSector}"/>
								<input type="hidden" name="shSectorId" id="shSectorId" value="{srvInfo/ShSectorId}"/>
						    </div>
					    </div>
					</div>
					
					<div class="inBlockFilter" style="margin-left: 10px">
						<label>Оценка: </label>
						<div>
							<select name="ratingColor" id="ratingColor" style="width: 120px">
								<option value="">--- Любой ---</option>
								<xsl:for-each select="dbInfo/RatingColorDict/Element">
									<option value="{@id}">
									    <xsl:if test="/root/srvInfo/RatingColor = @id">
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
				<div class="total" style="float:left">
					Всего: <strong><xsl:value-of select="dbInfo/Pager/@total"/></strong> 
				</div>
				<div class="actionList">  
					<a href="javascript:editContent('0')">Добавить отзыв</a>
				</div>
			</div>
				
			<div id="resultSet">
				<xsl:variable name="tdCount" select="16"/>
				<table cellpadding="0" cellspacing="1" width="100%" border="0" class="resultSet">
					<col width="30"/>
					<col width="30"/>
					<col width="30"/>
					<col width="200"/>
					<col width="200"/>
					<col width="110"/>
					<col width="50"/>
					<col width="30"/>
					<col width="30"/>
					<col width="30"/>
					<col width="80"/>
					<col width="80"/>
					<col width="30"/>
					<col width="30"/>
					<col width="30"/>
					<col width="80"/>
					

					<tr>
						<th rowspan="2">#</th>
						<th rowspan="2">Id
							<xsl:call-template name="sortBy">
								<xsl:with-param name="sortBy" select="/root/srvInfo/SortBy"/>
								<xsl:with-param name="sortType" select="/root/srvInfo/SortType"/>
								<xsl:with-param name="field" select="'id'"/>
							</xsl:call-template>
						</th>
						<th rowspan="2">Отзыв</th>
						
						<th rowspan="2">Врач
							<xsl:call-template name="sortBy">
								<xsl:with-param name="sortBy" select="/root/srvInfo/SortBy"/>
								<xsl:with-param name="sortType" select="/root/srvInfo/SortType"/>
								<xsl:with-param name="field" select="'doctor'"/>
							</xsl:call-template>
						</th>
						<th rowspan="2">Клиент
							<xsl:call-template name="sortBy">
								<xsl:with-param name="sortBy" select="/root/srvInfo/SortBy"/>
								<xsl:with-param name="sortType" select="/root/srvInfo/SortType"/>
								<xsl:with-param name="field" select="'name'"/>
							</xsl:call-template>
						</th>
						<th rowspan="2">Телефон</th>
						<th rowspan="2" title="Заявка">Заяв.</th>
						<th colspan="3">Рейтинг</th>
						<th colspan="2">Дата</th>
						<th rowspan="2" title="Происхождение">Пр.&#160;</th>
						<th rowspan="2" title="Тип отзыва">Тип</th>
						<th rowspan="2">Статус</th>
						<th rowspan="2">&#160;</th>
					</tr>
					<tr>
						<th title="Врач">Врач.</th>
						<th title="Внимание">Вним.</th>
						<th title="Цена / качество">Цен./Кач.</th>
						<th title="Создан">Создан
							<xsl:call-template name="sortBy">
								<xsl:with-param name="sortBy" select="/root/srvInfo/SortBy"/>
								<xsl:with-param name="sortType" select="/root/srvInfo/SortType"/>
								<xsl:with-param name="field" select="'crDate'"/>
							</xsl:call-template>
						</th>
						<th title="Опубликован">Публик.
							<xsl:call-template name="sortBy">
								<xsl:with-param name="sortBy" select="/root/srvInfo/SortBy"/>
								<xsl:with-param name="sortType" select="/root/srvInfo/SortType"/>
								<xsl:with-param name="field" select="'pubDate'"/>
							</xsl:call-template>
						</th>
					</tr>
					<xsl:choose>
						<xsl:when test="dbInfo/OpinionList/Element">
							<xsl:for-each select="dbInfo/OpinionList/Element">
								<xsl:variable name="class">
									<xsl:choose>
										<xsl:when test="(position() div 2) - floor(position() div 2) &gt; 0">odd</xsl:when>
										<xsl:otherwise>even</xsl:otherwise>
									</xsl:choose>
								</xsl:variable>
								
								<xsl:variable name="ratingColor">
									<xsl:choose>
										<xsl:when test="RatingColor != ''">
											<xsl:choose>
												<xsl:when test="RatingColor = '-1'">downOpinion</xsl:when>
												<xsl:when test="RatingColor = '1'">upOpinion</xsl:when>
											</xsl:choose>
										</xsl:when>
										<xsl:otherwise>
											<xsl:choose>
												<xsl:when test="RatingColor/@recomend = '-1'">downOpinion</xsl:when>
												<xsl:when test="RatingColor/@recomend = '1'">upOpinion</xsl:when>
											</xsl:choose>
										</xsl:otherwise>
									</xsl:choose>
									
								</xsl:variable>
								<tr id="tr_{@id}" class="{$class}" backclass="{$class}" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','{$class}')">

									<td><xsl:value-of select="position()+number(/root/dbInfo/Pager/Page[@id = ../@currentPageId]/@start)-1"/></td>
									<td align="right">
										<a href="javascript:editContent('{@id}')">
											<xsl:value-of select="@id"/>
										</a>
									</td>
									<td align="center">
										<xsl:if test="Note != ''">
											<div class="helper">
												<div class="helpEltR hd" style="width: 400px; margin:0 0 0 30px; text-align: left;">
													<xsl:copy-of select="Note"/>
												</div>
												<div class="helpMarker"><img src="/img/icon/note.png" onclick="editContent('{@id}')"/></div>
											</div>
										</xsl:if>
									</td>
									<td>
										<xsl:value-of select="Doctor"/>
									</td>
									<td>
										<xsl:value-of select="Client"/>
									</td>
									<td nowrap="">
										<xsl:value-of select="Phone"/>
									</td>
									<td>
										<xsl:choose>
											<xsl:when test="RequestId != 0 and RequestId  != ''">
												<span class="black"><xsl:value-of select="RequestId"/></span>
											</xsl:when>
											<xsl:when test="RequestList and RequestList/Request and count(RequestList/Request) = 1">
												<em><xsl:value-of select="RequestList/Request"/></em>
											</xsl:when>
											<xsl:when test="RequestList and RequestList/Request and count(RequestList/Request) &gt; 1">
												<em><xsl:value-of select="RequestList/Request[position() = 1]"/> ...</em>
											</xsl:when>
										</xsl:choose>
										
									</td>
									<td align="center">
										<span class="{$ratingColor}">
											<xsl:value-of select="RatingQlf"/>
										</span>
									</td>
									<td align="center">
										<span class="{$ratingColor}">
											<xsl:value-of select="RatingAtt"/>
										</span>
									</td>
									<td align="center">
										<span class="{$ratingColor}">
											<xsl:value-of select="RatingRoom"/>
										</span>
									</td>
									<td align="right">
										<xsl:value-of select="CrDate"/>
									</td>
									<td align="right">
										<xsl:if test="Allow = '1'">
											<xsl:value-of select="PubDate"/>
										</xsl:if>
									</td>
									
									<td align="center">
										<xsl:choose>
											<xsl:when test="Author = 'gues'"><img src="/img/icon/earth.png" title="С сайта"/></xsl:when>
											<xsl:when test="Author = 'cont'"><img src="/img/icon/business-contact.png" title="Контент"/></xsl:when>
											<xsl:when test="Author = 'oper'"><img src="/img/icon/receptionist.png" title="Оператор"/></xsl:when>
											<xsl:otherwise></xsl:otherwise>
										</xsl:choose>
									</td>
									<td align="center">
										<xsl:choose>
											<xsl:when test="Origin = 'original'"><img src="/img/icon/woman.png" title="Оригинальный"/></xsl:when>
											<xsl:when test="Origin = 'combine'"><img src="/img/icon/yin-yang.png" title="Смешанный"/></xsl:when>
											<xsl:when test="Origin = 'editor'"><img src="/img/icon/editor.png" title="Редакторский"/></xsl:when>
											<xsl:otherwise></xsl:otherwise>
										</xsl:choose>
									</td>
									<td align="center">
										<xsl:choose>
											<xsl:when test="Allow = '2'"><img src="/img/icon/disable.png" title="Заблокирован"/></xsl:when>
											<xsl:when test="Allow = '1'"><img src="/img/icon/check_ok.png" title="Опубликован"/></xsl:when>
											<xsl:when test="Allow = '0'"><img src="/img/icon/check_no.png" title="Не опубликован"/></xsl:when>
											<xsl:otherwise></xsl:otherwise>
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

