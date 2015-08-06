<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:output method="html" encoding="utf-8"/>
	
	<xsl:template name="pager">
		<xsl:param name="context" select="/root/dbInfo/Pager"/>
		<xsl:param name="formName" select="'filter'"/>
		
		<xsl:if test="count($context/Page) &gt; 1">
		
			<style type="text/css">
				#slider { height: 30px; overflow:hidden; padding: 0 0 10px; float:right; }
				#slider .viewport { float: left; width: 270px; height: 20px; overflow: hidden; position: relative; }
				#slider .buttons {  display: block; margin: 0px 10px 0 0; float: left; width: 80px; height: 20px; overflow: hidden; position: relative; line-height: 20px;}
				
				#slider .next { margin: 0px 0 0 10px;  text-align:right; }
				#slider span.ar{ color:#333; font-weight:bold; font-size:16px; line-height: 15px;}
				#slider .disable { visibility: hidden; }
				#slider .overview { list-style: none; position: absolute; padding: 0; margin: 0; width: 100px; left: 0 top: 0; }
				#slider .overview li{ float: left; margin: 0 10px 0 0; padding: 0 10px 0 0; height: 20px; line-height: 20px; border-right: 1px solid #dcdcdc; width:70px; text-align:center; cursor:pointer; text-decoration:underline; color: #004080;}
				#slider .overview li:hover{text-decoration:none}
				#slider #lastBlockWithNumber{border-right:none}
				#slider .overview li.current{text-decoration:none; cursor:auto; color:#333; font-weight:bold;}
				.cBlock{margin:0;padding:0;}
				#ui-datepicker-div{margin-top:-20px;}
			</style>
			<div class="cBlock"></div>
			<div id="slider">
				<table cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td><a class="buttons prev" href="#">Назад</a></td>
						<td><span class="ar">[</span></td>
						<td style="padding:15px 0 0 10px;">
							<div class="viewport">
								<ul class="overview">
									<xsl:for-each select="$context/Page">
										<li>
											<xsl:if test="position() = 1">
												<xsl:attribute name="id">firstBlockWithNumber</xsl:attribute>
											</xsl:if>
											<xsl:if test="position() = last()">
												<xsl:attribute name="id">lastBlockWithNumber</xsl:attribute>
											</xsl:if>
											<xsl:choose>
												<xsl:when test="../@currentPageId = @id">
													<xsl:attribute name="class">current</xsl:attribute>
												</xsl:when>
												<xsl:otherwise>
													<xsl:attribute name="class">gopage</xsl:attribute>
													<xsl:attribute name="onclick">$("#startPage").val('<xsl:value-of select="@id"/>'); document.forms['<xsl:value-of select="$formName"/>'].submit() </xsl:attribute>
												</xsl:otherwise>
											</xsl:choose>
											<xsl:value-of select="@start"/> - <xsl:value-of select="@end"/>
										</li>
									</xsl:for-each>
								</ul>
							</div>
						</td>
						<td><span class="ar">]</span></td>
						<td align="right"><a class="buttons next" href="#">Вперед</a></td>
						<td width="70" align="center">Всего:</td>
						<td><b><xsl:value-of select="$context/@total" /></b></td>
					</tr>
				</table>
			</div>
			<div class="cBlock"></div>
			<script type="text/javascript" src="/lib/js/jquery.tinycarousel.min.js"></script>
			
			<xsl:variable name="StartPage">
				<xsl:choose>
					<xsl:when test="/root/srvInfo/StartPage and /root/srvInfo/StartPage != ''">
						<xsl:value-of select="/root/srvInfo/StartPage" />
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="'1'" />
					</xsl:otherwise>
				</xsl:choose>
			</xsl:variable>
			
			<script type="text/javascript">
				$(document).ready(function(){
					$('#slider').tinycarousel({
						animation:true,
						duration:100,
						start:<xsl:value-of select="$StartPage" />
					});	
				});
			</script>	

		</xsl:if>
	</xsl:template>
	
</xsl:transform>

