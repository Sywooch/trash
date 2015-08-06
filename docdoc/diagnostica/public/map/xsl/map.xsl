<?xml version='1.0'  encoding="UTF-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:import href="common.xsl"/>
	
	<xsl:output method="html" encoding="utf-8"/>
	
	
	<xsl:key name="diagnostica" match="/root/dbInfo/DiagnosticList/descendant-or-self::Element" use="@id"/>
	
	
	<xsl:template match="/">
		<link type="text/css" href="/st/css/map/jquery.jscrollpane.css" rel="stylesheet" media="screen"/>

		<link type="text/css" href="/st/css/metro.css" rel="stylesheet" />
		
		<xsl:apply-templates select="root"/>

        <script src="/st/js/plugin/jquery-1.9.1.min.js"></script>
		<script src="/st/js/plugin/jquery.mousewheel.min.js" type="text/javascript"></script>
		<script src='/st/js/plugin/jquery.jscrollpane.js' type='text/javascript'></script>
		<script src="/st/js/map/map.js" type="text/javascript"></script>
		<script>
        	var api;
        	var page = 1;
        	var ready = true;
        	
        	<xsl:choose>
        		<xsl:when test="/root/dbInfo/Pager/Page">
        			var totalPage = <xsl:value-of select="/root/dbInfo/Pager/Page[position() = last()]/@id"/>;
        		</xsl:when>
        		<xsl:otherwise>
        		var totalPage = 0;
        		</xsl:otherwise>
        	</xsl:choose>
        	
        	$(document).ready(function(){
				var pane = $('.scroll-pane').jScrollPane(
					{
						showArrows: true,
						maintainPosition: true,
						hijackInternalLinks: true,
						stickToBottom: false,
						mouseWheelSpeed: 130
					}
				);
				api = pane.data('jsp');
				
				pane.scroll(function(){ 
					if ( api.getPercentScrolledY() == 1 &amp;&amp; page &lt; totalPage &amp;&amp; ready) {
						ready = false;
						getNextPage();
					}
				});
			});
			
			$(".how-rating").hover(
			  function(){
			   $(this).find("div.rating-popup").stop(true,true).fadeIn(200);
			  },
			  function(){
			   $(this).find("div.rating-popup").stop(true,true).fadeOut(200);
			  }
			 );
        </script>
		
		
		<script src="http://api-maps.yandex.ru/2.0/?load=package.standard,package.clusters&amp;mode=debug&amp;lang=ru-RU" type="text/javascript"></script>
		<script src="/st/js/map/ymap.js" language="JavaScript" type="text/javascript" ></script>
		<script type="text/javascript">
			var myMap;
			var myCollection;
			var myBalloonLayout;
			var myBalloonContentBodyLayout;
			var coord;
			<xsl:choose>
	        	<xsl:when test="/root/srvInfo/Id and /root/srvInfo/Id != ''">
	        		var moveReady = false;
	        	</xsl:when>
	        	<xsl:otherwise>
	        		var moveReady = true;
	        	</xsl:otherwise>
	        </xsl:choose>
			
            ymaps.ready(function () { 
            	myMap = new ymaps.Map("YMapsID", {
				        center: [<xsl:value-of select="/root/dbInfo/DCenterList/Element[position() = 1]/Lat"/>, <xsl:value-of select="/root/dbInfo/DCenterList/Element[position() = 1]/Long"/>],
				        <xsl:choose>
				        	<xsl:when test="/root/srvInfo/Id and /root/srvInfo/Id != ''">
				        		zoom: 15,
				        	</xsl:when>
				        	<xsl:otherwise>
				        		zoom: 11,
				        	</xsl:otherwise>
				        </xsl:choose>
				        behaviors: ["default", "scrollZoom"]
				    },
				    myCollection = new ymaps.GeoObjectCollection()
				);
				myMap.controls.add('zoomControl');
			
				<![CDATA[
				myBalloonContentBodyLayout = ymaps.templateLayoutFactory.createClass("<div class=\"b-simple-balloon-layout\">\
					<div class=\"content\">$[properties.body]</div>\
					<div class=\"tail\"></div>\
					</div>");
				]]>
			
				myCollection = new ymaps.GeoObjectCollection({});
				
				var points = [
					<xsl:for-each select="/root/dbInfo/DCenterListAll/Element">
                		{name : <xsl:value-of select="position()"/>, id : <xsl:value-of select="@id"/>, coords : [<xsl:value-of select="Lat"/>, <xsl:value-of select="Long"/>] }
                		<xsl:if test="position() != last()">, </xsl:if>
	                </xsl:for-each>
				];

				setPoints (points, <xsl:value-of select="/root/srvInfo/Step"/>);

	            
	            myMap.events.add('boundschange', function (event) {
	            	page = 1;
	            	var pointlength = points.length;
	            	for ( var i = 0; i &lt; pointlength; i++ ) { 
//	            	points.forEach(function (point) {
	            		myMap.geoObjects.remove(points[i]);
//	            	});
	            	}
	            	
	            	if (moveReady) {
		            	setLeftCollumn(event.get('newBounds'));
		            }
	            	
				});
				
				myMap.events.add('balloonopen', function (e) {
				    var balloon = e.get('balloon');
				    myMap.events.add('click', function (e) {
				        if(e.get('target') === myMap) { 
				            balloon.close();
				            moveReady = true;
				        }
				    });
				});

				
            });
            
	        initItem ();
        </script>
	</xsl:template>
	
	
	
	
	<xsl:template match="root">
	<div id="wrap">
		<div id="header-wrap">
			<div id="header">
				<div class="head-left">
					<div id="logo"><a href="/" class="png"></a></div>
					<div class="b-dropdown tooltip">
                        <div class="b-dropdown_item b-dropdown_item__current">
                            <span class="b-dropdown_item__text">Диагностические центры</span><span class="b-dropdown_item__icon"></span>
                        </div>
                        <ul class="b-dropdown_list">
                            <li class="b-dropdown_item">Диагностические центры</li>
                            <li class="b-dropdown_item"><a href="http://docdoc.ru">Поиск врачей</a></li>
                        </ul>
                    </div>
				</div>
				<div class="head-info">
					<div class="head-info-big">В Москве <span class="head-info-count"><xsl:for-each select="/root/Counter/Center/Digits/Element"><xsl:value-of select="."/></xsl:for-each></span>
					<xsl:call-template name="digitVariant">
						<xsl:with-param name="one"> диагностический центр</xsl:with-param>
						<xsl:with-param name="two"> диагностических центра</xsl:with-param>
						<xsl:with-param name="five"> диагностических центров</xsl:with-param>
						<xsl:with-param name="digit" select="number(/root/Counter/Center/Digits/Element[position() = last()])"/>
					</xsl:call-template>.</div>
					За последнюю неделю <span class="head-info-count"><xsl:for-each select="/root/Counter/Employer/Digits/Element"><xsl:value-of select="."/></xsl:for-each></span> 
					<xsl:call-template name="digitVariant">
						<xsl:with-param name="one"> посетитель </xsl:with-param>
						<xsl:with-param name="two"> посетителея </xsl:with-param>
						<xsl:with-param name="five"> посетителей </xsl:with-param>
						<xsl:with-param name="digit" select="number(/root/Counter/Employer/Digits/Element[position() = last()])"/>
					</xsl:call-template> docdoc.ru
					<xsl:call-template name="digitVariant">
						<xsl:with-param name="one"> нашёл</xsl:with-param>
						<xsl:with-param name="two"> нашли</xsl:with-param>
						<xsl:with-param name="five"> нашли</xsl:with-param>
						<xsl:with-param name="digit" select="number(/root/Counter/Employer/Digits/Element[position() = last()])"/>
					</xsl:call-template>  
					подходящую диагностику.
				</div>
				
				
				<script type="text/javascript">
					var menuItem = new Array();
					<xsl:for-each select="dbInfo/DiagnosticList/Element">
						menuItem['<xsl:value-of select="@id"/>'] = new Array();
						menuItem['<xsl:value-of select="@id"/>'][0] = "<xsl:value-of select="Name"/>";
						var menuSubItem = new Array();
						<xsl:if test="DiagnosticList/Element">
							<xsl:for-each select="DiagnosticList/Element">
								menuSubItem['<xsl:value-of select="position()"/>'] = new Array();
								menuSubItem['<xsl:value-of select="position()"/>'][0] = "<xsl:value-of select="Name"/>";
								menuSubItem['<xsl:value-of select="position()"/>'][1] = <xsl:value-of select="@id"/>;
							</xsl:for-each>
						</xsl:if>
						menuItem['<xsl:value-of select="@id"/>'][1] = menuSubItem; 
					</xsl:for-each>
				</script>
				
				
				<form name="filter" action="" method="get"> 
				<div class="filter-map">
					<div class="head">Вы ищете</div>
					<input name="diagnostic" id="selectDiagnpostic" style="width: 100px" value="{srvInfo/Diagnostica}" type="hidden"/>
					<input name="subDiagnostica" id="selectSubDiagnpostic" style="width: 100px" value="{srvInfo/subDiagnostica}" type="hidden"/>
					<div class="item filter-list" style="z-index:4;" id="leftSlide">
						<div class="head-inp">вид диагностики:</div>
						<div class="inp round" id="diagnostic-type-btn">
							<div class="pict png"></div>
							<div class="inp-txt" id="diagnostic-type-select">
								<xsl:choose>
									<xsl:when test="number(srvInfo/Diagnostica) &gt; 0">
										<xsl:choose>
											<xsl:when test="key('diagnostica',/root/srvInfo/Diagnostica)/ReductionName != ''">
												<xsl:value-of select="key('diagnostica',/root/srvInfo/Diagnostica)/ReductionName"/>
											</xsl:when>
											<xsl:otherwise>
												<xsl:value-of select="key('diagnostica',/root/srvInfo/Diagnostica)/Name"/>
											</xsl:otherwise>
										</xsl:choose>
										<!-- <xsl:value-of select="dbInfo/DiagnosticList/Element[@id= /root/srvInfo/Diagnostica]/Name"/> -->
									</xsl:when>
									<xsl:otherwise>
										Выберите из списка
									</xsl:otherwise>
								</xsl:choose>
							</div>
						</div>
						<div class="filter-list-full-wrap" id="diagnostic-type">
							<div class="filter-list-full">
								<xsl:for-each select="dbInfo/DiagnosticList/Element">
									<div class="item" selId="{@id}">
										<xsl:choose>
											<xsl:when test="key('diagnostica',@id)/ReductionName != ''">
												<xsl:value-of select="key('diagnostica',@id)/ReductionName"/>
											</xsl:when>
											<xsl:otherwise>
												<xsl:value-of select="key('diagnostica',@id)/Name"/>
											</xsl:otherwise>
										</xsl:choose>
									</div>
								</xsl:for-each>
							</div>
						</div>
					</div>
					<div class="item filter-list"  id="rightSlide">
						<div class="head-inp">область диагностики:</div>
						<div sclass="inp round" id="diagnostic-subtype-btn">
							<xsl:attribute name="class">
								<xsl:choose>
									<xsl:when test="dbInfo/DiagnosticList/Element[@id=/root/srvInfo/Diagnostica]/DiagnosticList/Element">
										inp round
									</xsl:when>
									<xsl:otherwise>
										inp round blocked
									</xsl:otherwise>
								</xsl:choose>
							</xsl:attribute>
							<div class="pict png"></div>
							<div class="inp-txt" id="diagnostic-subtype-select">
								<xsl:choose>
									<xsl:when test="dbInfo/DiagnosticList/Element[@id=/root/srvInfo/Diagnostica]/DiagnosticList/Element and /root/srvInfo/subDiagnostica != '0'">
										<xsl:value-of select="dbInfo/DiagnosticList/Element[@id=/root/srvInfo/Diagnostica]/DiagnosticList/Element[@id=/root/srvInfo/subDiagnostica]/Name"/>
									</xsl:when>
									<xsl:when test="dbInfo/DiagnosticList/Element[@id=/root/srvInfo/Diagnostica]/DiagnosticList/Element">
										Выберите из списка
									</xsl:when>
									<xsl:otherwise>
										Нет вариантов
									</xsl:otherwise>
								</xsl:choose></div>
						</div>
						<div class="filter-list-full-wrap" id="diagnostic-subtype">
							<xsl:if test="dbInfo/DiagnosticList/Element[@id=/root/srvInfo/Diagnostica]/DiagnosticList/Element">
							<div class="filter-list-full">
								<xsl:choose>
									<xsl:when test="dbInfo/DiagnosticList/Element[@id=/root/srvInfo/Diagnostica]/DiagnosticList/Element">
										<xsl:for-each select="dbInfo/DiagnosticList/Element[@id=/root/srvInfo/Diagnostica]/DiagnosticList/Element">
											<div class="item" selId="{@id}"><xsl:value-of select="Name"/></div>
										</xsl:for-each>
									</xsl:when>
								</xsl:choose>
							</div>
							</xsl:if>
						</div>
					</div>
					<div class="search_btn_find ui-btn ui-btn_teal" onclick="document.forms['filter'].submit()">
						Найти
					</div>
				</div>
				</form>
			</div>
		</div>

		<div class="shw-head"></div>
		<div id="content" >
			<div id="left-col-list" class="scroll-pane">
			
				<xsl:if test="/root/srvInfo/subDiagnostica != 0 or (/root/srvInfo/Diagnostica != 0 and not(/root/dbInfo/DiagnosticList/Element[@id = /root/srvInfo/Diagnostica]/DiagnosticList))">
				<div class="sort">
					<noindex>сортировать по <a href="javascript:reSort()" class="sort-price {srvInfo/SortRS}">цене</a></noindex>
					<input name="sortRS" id="sortRS" value="{srvInfo/SortRS}" type="hidden"/>
				</div>
				</xsl:if>
				
				<div class="address-list" id="resultSet">
					<xsl:for-each select="dbInfo/DCenterList/Element">
						<xsl:call-template name="resultSetLine"/>
					</xsl:for-each>
				</div>
				
				<xsl:if test="srvInfo/Id != ''">
					<div class="sort" id="showMore">
						<noindex><a href="javascript:showMore()">показать другие центры</a></noindex>
					</div>
				</xsl:if>
			
			</div>
			
			
			<div id="col-right-map">
				<a class="link-back">
					<xsl:attribute name="href">
						<xsl:choose>
							<xsl:when test="/root/srvInfo/subDiagnostica != 0"><xsl:value-of select="/root/dbInfo/DiagnosticList/descendant-or-self::Element[@id = /root/srvInfo/subDiagnostica]/RewriteName"/></xsl:when>
							<xsl:when test="/root/srvInfo/Diagnostica != 0"><xsl:value-of select="/root/dbInfo/DiagnosticList/descendant-or-self::Element[@id = /root/srvInfo/Diagnostica]/RewriteName"/></xsl:when>
							<xsl:otherwise>/kliniki/</xsl:otherwise>
						</xsl:choose>
					</xsl:attribute>
					Вернуться к списку
				</a>
				<div id="YMapsID" style="height:100%;">
				</div>
			</div>
			
			
			
			
		</div>
	</div>
		
	</xsl:template>
	
	
	
	
	<xsl:template name="resultSetLine">
		<xsl:param name="context" select="."/>
		<xsl:param name="pos" select="position()"/>
		
		<div id="item_{$context/@id}" pointId="{$context/@id}"  lat="{$context/Lat}" long="{$context/Long}" pos="{$pos}" >
			<xsl:attribute name="class">
				<xsl:choose>
					<xsl:when test="$context/@id = /root/srvInfo/Id">item act</xsl:when>
					<xsl:otherwise>item</xsl:otherwise>
				</xsl:choose>
			</xsl:attribute>

			<div class="head">
				<span class="marker marker-violet">
					<span><xsl:value-of select="$pos"/></span>
				</span>
				<a href="/kliniki/{$context/Rewrite}" pointId="{$context/@id}" lat="{$context/Lat}" long="{$context/Long}" pos="{$pos}"><xsl:value-of select="$context/Title"/></a>
			</div>
			<div class="address">
				<ul class="metro_list">
					<xsl:for-each select="$context/Metro/Element">
						<li class="metro_item"><span class="metro_link metro_line_{@lineId}"><xsl:value-of select="Name"/></span><xsl:if test="position() != last()">,&#160;</xsl:if></li>
					</xsl:for-each>
				</ul>
				<xsl:value-of select="$context/Address"/>
			</div>
			<table class="price_tbl">
				<tbody>
					<xsl:for-each select="$context/Diagnostics/Element[position() &lt;= 3]">
						<xsl:variable name="id" select="@id"/>
						<tr>
							<td>
								<span class="price_tbl_name">
									<xsl:choose>
										<xsl:when test="/root/dbInfo/DiagnosticList/Element[@id = $id]">
											<xsl:choose>
												<xsl:when test="key('diagnostica',@id)/ReductionName != ''">
													<xsl:value-of select="key('diagnostica',@id)/ReductionName"/>
												</xsl:when>
												<xsl:otherwise>
													<xsl:value-of select="key('diagnostica',@id)/Name"/>
												</xsl:otherwise>
											</xsl:choose>
										</xsl:when>
										<xsl:otherwise>
											<!-- <xsl:value-of select="/root/dbInfo/DiagnosticList/descendant-or-self::Element[@id = $id]/../../Name"/> -->
											<xsl:choose>
												<xsl:when test="/root/dbInfo/DiagnosticList/descendant-or-self::Element[@id = $id]/../../ReductionName != ''">
													<xsl:value-of select="/root/dbInfo/DiagnosticList/descendant-or-self::Element[@id = $id]/../../ReductionName"/>
												</xsl:when>
												<xsl:otherwise>
													<xsl:value-of select="/root/dbInfo/DiagnosticList/descendant-or-self::Element[@id = $id]/../../Name"/>
												</xsl:otherwise>
											</xsl:choose>&#160;<xsl:choose>
												<xsl:when test="key('diagnostica',@id)/ReductionName != ''"><xsl:value-of select="key('diagnostica',@id)/ReductionName"/></xsl:when>
												<xsl:otherwise><xsl:value-of select="key('diagnostica',@id)/Name"/></xsl:otherwise>
											</xsl:choose>
											
										</xsl:otherwise>
									</xsl:choose>
								</span>
							</td>
							<td class="price_tbl_price_wrap">
								<span class="price_tbl_price">
									<xsl:choose>
										<xsl:when test="SpecialPrice != 0">
											<strike><xsl:value-of select="Price"/> р.</strike>
											<xsl:value-of select="SpecialPrice"/> р.
										</xsl:when>
										<xsl:otherwise>
											<xsl:value-of select="Price"/> р.
										</xsl:otherwise>
									</xsl:choose>
								</span>
							</td>
						</tr>
						<xsl:if test="SpecialPrice != 0">
							<tr class="price_tbl_spec">
								<td colspan="2">при записи скажите о docdoc.ru</td>
							</tr>
						</xsl:if>
					</xsl:for-each>
				</tbody>
			</table>
		</div>
	</xsl:template>
	

</xsl:transform>

