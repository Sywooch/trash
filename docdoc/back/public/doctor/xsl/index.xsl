<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:import href="../../lib/xsl/modalWindow.xsl"/>
	<xsl:import href="../../lib/xsl/common.xsl"/>
	<xsl:import href="../../lib/xsl/pager.xsl"/>
	<xsl:import href="../../lib/xsl/filter.xsl"/>
	<xsl:import href="../../lib/xsl/sortBy.xsl"/>
	<xsl:import href="../../lib/xsl/statisticNote.xsl"/>

	<xsl:output method="html" encoding="utf-8"/>


	<xsl:template match="/">
		<link type="text/css" href="/css/jquery.autocomplete.css" rel="stylesheet" media="screen"/>
		
		<xsl:apply-templates select="root"/>
		
		<div style="position: absolute; margin: 0; top:0; left:0; z-index: 0;">
			<xsl:call-template name="modalWindow">
				<xsl:with-param name="id" select="'modalWin'"/>
				<xsl:with-param name="title" select="'Врач'"/>
				<xsl:with-param name="width" select="'755'"/>
			</xsl:call-template>
		</div>
		<div style="position: absolute; margin: 0; top:0; left:0; z-index: 0;">
			<xsl:call-template name="modalWindow">
				<xsl:with-param name="id" select="'imgWin'"/>
				<xsl:with-param name="title" select="'Изображение'"/>
				<xsl:with-param name="width" select="'1010'"/>
			</xsl:call-template>
		</div>
		<div style="position: absolute; margin: 0; top:0; left:0; z-index: 0;">
			<xsl:call-template name="modalWindow">
				<xsl:with-param name="id" select="'deleteDoctorWin'"/>
				<xsl:with-param name="title" select="'Выберите другого врача для переноса заявок'"/>
				<xsl:with-param name="width" select="'500'"/>
			</xsl:call-template>
		</div>
		<script src='/lib/js/jquery.autocomplete.min.js' type='text/javascript' language="JavaScript"></script>
		<script src="/doctor/js/doctor.js" type="text/javascript"></script>
		<script>
			var winDeltaY = 150;
			var winDeltaX = 500;
				
			$(document).ready(function(){ 
				$("#shClinic").autocomplete("/service/getClinicList.htm",{
					delay:10,
					minChars:2,
					max:20,
					autoFill:true,
					selectOnly:true,
                    matchContains: true,
					extraParams: { cityId: <xsl:value-of select="/root/srvInfo/City/@id"/>  },
					formatResult: function(row) {
						return row[0];
					},
					formatItem: function(row, i, max) {		
						return row[0];
					}
				}).result(function(event, item) {
					$("#shClinicId").val(item[1]);
					$('#startPage').val("1");
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
				
				$("#shClinic").bind("change", function(e){
					if ( jQuery.trim ($("#shClinic").val()) == '' ) {
						$("#shClinicId").val("0");
					}
					$('#startPage').val("1");
			    });
			    $("#shSector").bind("change", function(e){
					if ( jQuery.trim ($("#shSector").val()) == '' ) {
						$("#shSectorId").val("0");
					}
					$('#startPage').val("1");
			    });
				
			});	
			
			$("div.helper").mouseover( function() {
				$(this).stop(true).delay(300).children().show();
			});
			$("div.helper").mouseleave( function() {
				$(".helpElt").hide();
			});
			
			
			/*	Фильтр	*/
			function clearFilterForm () {
				$("#id").val("");
				
				$("#name").val("");
				$("#shSectorId").val("");
				$("#shSector").val("");
				
				$("#shClinicId").val("");
				$("#shClinic").val("");
				$("#shBranch").attr("checked",false);
				$("#status").val("");
				$("#noClinic").attr("checked",false);
				
				$("#sortBy").val("");
				$("#sortType").val("");
			}
		</script>
	</xsl:template>




	<xsl:template match="root">
		<div id="main">
			<h1>Врачи</h1>
			
<!--	Статистика	-->			
			<xsl:if test="/root/dbInfo/DoctorStat/Element">
				<xsl:call-template name="statisticNote">
					<xsl:with-param name="body">
						<xsl:for-each select="/root/dbInfo/StatusDict/Element">
							<xsl:variable name="id" select="@id"/>
							<div style="margin: 0; height: 18px">
								<div style="margin: 0; float: left; margin-right: 20px"><xsl:value-of select="."/> </div>
								<div style="margin: 0; float: right"><strong><xsl:value-of select="/root/dbInfo/DoctorStat/Element[@status = $id]/."/></strong></div>
							</div>
						</xsl:for-each>
						<div class="m0 wbt" style="padding-top: 2px; height: 18px">
							<div class="m0 mr20" style="float: left;">Всего </div>
							<div class="m0" style="float: right"><strong><xsl:value-of select="sum(/root/dbInfo/DoctorStat/Element/.)"/></strong></div>
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
						    <label>Фамилия:</label>
						    <div>
								<input name="name" id="name" style="width: 130px" maxlength="25"  value="{srvInfo/Name}"/>
						    </div>
					    </div>
					</div>
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
					    <div>
						    <label>Клиника и фил.:</label>
						    <div>
								<input name="shClinic" id="shClinic" style="width: 200px" maxlength="25"  value="{srvInfo/ShClinic}"/>
								<input type="hidden" name="shClinicId" id="shClinicId" value="{srvInfo/ShClinicId}"/>
						    </div>
					    </div>
					</div>
					<div class="inBlockFilter" style="margin-left: 5px">
					    <div>
						    <label>&#160;</label>
						    <div>
								<input type="checkbox" name="shBranch" id="shBranch" value="1">
									<xsl:if test="/root/srvInfo/Branch = '1'">
										<xsl:attribute name="checked"/>
									</xsl:if>
								</input>
						    </div>
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
				<xsl:with-param name="addLine">
					<div class="inBlockFilter" style="margin-left: 10px">
						<div>
							<input type="checkbox" name="noClinic" id="noClinic" value="1">
								<xsl:if test="/root/srvInfo/noClinic = '1'">
									<xsl:attribute name="checked"/>
								</xsl:if>
							</input>
							<label for="noClinic">Без клиники</label>
						</div>
						<div>
							<input type="checkbox" name="kidsReception" id="kidsReception" value="1">
								<xsl:if test="/root/srvInfo/kidsReception = '1'">
									<xsl:attribute name="checked"/>
								</xsl:if>
							</input>
							<label for="kidsReception">Детские врачи</label>
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
					<a href="javascript:editContent('0')">Добавить врача</a>
				</div>
			</div>

				
			<div id="resultSet">
				<xsl:variable name="tdCount" select="15"/>
				<table cellpadding="0" cellspacing="1" width="100%" border="0" class="resultSet">
					<col width="30"/>
					<col width="40"/>
					<col width="50"/>
					<col/>
					<col width="200"/>
					<col/>
					<col width="40"/>
					<col width="30"/>
					<col width="60"/>
					<col width="30"/>
					<col width="30"/>
					<col width="30"/>
					<col width="90"/>
					<col width="50"/>
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
						<th colspan="2">Врач (ФИО)
							<xsl:call-template name="sortBy">
								<xsl:with-param name="sortBy" select="/root/srvInfo/SortBy"/>
								<xsl:with-param name="sortType" select="/root/srvInfo/SortType"/>
								<xsl:with-param name="field" select="'name'"/>
							</xsl:call-template>
						</th>
						<th>Специализация</th>
						<th>Клиника</th>
						<th title="Дополнительный номер">0000</th>
						<th title="Отзывы">Отз.</th>
						<th title="Рейтинг">Рейт.
							<xsl:call-template name="sortBy">
								<xsl:with-param name="sortBy" select="/root/srvInfo/SortBy"/>
								<xsl:with-param name="sortType" select="/root/srvInfo/SortType"/>
								<xsl:with-param name="field" select="'rating'"/>
							</xsl:call-template>
						</th>
						<th title="Поправка рейтинга на основе отзыва">Попр.</th>
						<th title="Комментарий к карточке врача" colspan="2">Ком.</th>
						<th>Дата рег.
							<xsl:call-template name="sortBy">
								<xsl:with-param name="sortBy" select="/root/srvInfo/SortBy"/>
								<xsl:with-param name="sortType" select="/root/srvInfo/SortType"/>
								<xsl:with-param name="field" select="'crDate'"/>
							</xsl:call-template>
						</th>
						<th>Статус</th>
						<th>&#160;</th>
					</tr>
					<xsl:choose>
						<xsl:when test="dbInfo/DoctorList/Element">
							<xsl:for-each select="dbInfo/DoctorList/Element">
								<xsl:variable name="class">
									<xsl:choose>
										<xsl:when test="(position() div 2) - floor(position() div 2) &gt; 0">odd</xsl:when>
										<xsl:otherwise>even</xsl:otherwise>
									</xsl:choose>
								</xsl:variable>
								<tr id="tr_{@id}" class="{$class}" backclass="{$class}" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','{$class}')">

									<td><xsl:value-of select="position()+number(/root/dbInfo/Pager/Page[@id = ../@currentPageId]/@start)-1"/></td>
									<td align="right"><xsl:value-of select="@id"/></td>
									<td align="center">
										<xsl:if test="IMG and IMG != ''">
											IMG
										</xsl:if>
									</td>
									<td class="fio">
										<a href="javascript:editContent('{@id}')">
											<xsl:value-of select="Name"/>
										</a>
									</td>
									<td>
										<xsl:for-each select="SectorList/Sector">
											<xsl:value-of select="."/>
											<xsl:if test="position() != last()">, </xsl:if>	
										</xsl:for-each>
									</td>
									<td>
										<xsl:for-each select="ClinicList/Element">
											<xsl:value-of select="."/>
											<xsl:if test="position() != last()">, </xsl:if>	
										</xsl:for-each>
										
									</td>
									<td align="center">
										<xsl:value-of select="AddNumber"/>
									</td>
									<td align="center">
										<xsl:if test="Opinion and Opinion != '0'">
											<a href="/opinion/index.htm?shDoctorId={@id}&amp;shDoctor={Name}"><xsl:value-of select="Opinion"/></a>
										</xsl:if>
									</td>
									<td align="center">
										<xsl:value-of select="InternalRating"/> / 
										<xsl:choose>
											<xsl:when test="Rating != '0'"><strong class="black"><xsl:value-of select="Rating"/></strong></xsl:when>
											<xsl:when test="TotalRating != '0'"><xsl:value-of select="TotalRating"/></xsl:when>
										</xsl:choose>
									</td>
									<td align="center">
										<xsl:if test="OpinionRatingCalculate != '0'">
											<xsl:choose>
												<xsl:when test="OpinionRating = OpinionRatingCalculate"><xsl:value-of select="OpinionRating"/></xsl:when>
												<xsl:otherwise>
													<span style="font-weight: bold;" >
														<xsl:choose>
															<xsl:when test="number(OpinionRatingCalculate) &gt; 0"><xsl:attribute name="class">green</xsl:attribute></xsl:when>
															<xsl:when test="number(OpinionRatingCalculate) &lt; 0"><xsl:attribute name="class">red</xsl:attribute></xsl:when>
														</xsl:choose>
														<xsl:value-of select="OpinionRatingCalculate"/>
													</span>
												</xsl:otherwise>
											</xsl:choose>
											
										</xsl:if>
									</td>
									<td align="center">
										<xsl:if test="OperatorOpenComment != ''">
											
											<div class="helper">
												<div class="helpElt hd" style="width: 400px; margin:0; text-align: left;">
													<xsl:copy-of select="OperatorOpenComment"/>
												</div>
												<div class="helpMarker"><img src="/img/icon/note.png"/></div>
											</div>
										</xsl:if>
									</td>
									<td align="center">
										<xsl:if test="OperatorComment != ''">
											
											<div class="helper">
												<div class="helpElt hd" style="width: 400px; margin:0; text-align: left;">
													<xsl:copy-of select="OperatorComment"/>
												</div>
												<div class="helpMarker"><img src="/img/icon/note_yellow.png"/></div>
											</div>
										</xsl:if>
									</td>
									<td align="center">
										<xsl:value-of select="CrDate"/>
									</td>
									<td align="center">
										<xsl:variable name="status" select="Status"/>
										<img src="/img/icon/status_{Status}.png" title="{/root/dbInfo/StatusDict/Element[@id = $status]/.}"/>
										<xsl:if test="Clinic/@status != '3'">
											<img src="/img/icon/disable.png" title="Клиника отключена" class="ml5"/>
										</xsl:if>
									</td>
									<td>
										<a href="javascript:editContent('{@id}')">Изменить</a>
										<xsl:if test="ModerationId != ''">
											<a href="#" onclick="moderateContent('{@id}', $(this).closest('tr')); return false;" title="Модерация изменений" style="margin-left:5px;">
												<img src="/img/icon/editor.png"/>
											</a>
										</xsl:if>
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

