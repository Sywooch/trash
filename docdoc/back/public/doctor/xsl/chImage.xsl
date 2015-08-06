<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	   
	<xsl:import href="../../lib/xsl/common.xsl"/> 
	
	<xsl:output method="html" encoding="utf-8"/>
	
	
	<xsl:template match="/">		 
		<link rel="stylesheet" type="text/css" href="/css/fileuploader.css"/>
		<link rel="stylesheet" type="text/css" href="/css/jquery.Jcrop.css" media="screen"/>
		
		<script type="text/javascript" src="/lib/js/fileuploader.js"></script>
		<script type='text/javascript' src='/lib/js/jquery.ajaxQueue.js'></script>
		<script type='text/javascript' src="/lib/js/jquery.Jcrop.min.js"></script>
		<script type='text/javascript' src="/doctor/js/cropLib.js"></script>
		
		<script type="text/javascript">
			var jcrop_api;

			var shablon = function(crop,width,height){
				this.c = crop;
				this.w = width;
				this.h = height;
			}


			function initJcrop(elt) {
				$(elt).Jcrop({
					onSelect: updateCoords,
					setSelect: [ 0, 0, 160, 218 ],
					aspectRatio: 0.7339,
					minSize: [ 160, 218 ],
					onRelease: releaseCheck
			       },function(){ jcrop_api = this; } );
			}

			function releaseCheck() {
				jcrop_api.setOptions({ allowSelect: true });
			};

			function updateCoords(c){$('#x').val(c.x);$('#y').val(c.y);$('#w').val(c.w);$('#h').val(c.h);};


			function checkCoords()
			{
				if (parseInt($('#w').val())) return true;
				return false;
			};

		
			$(document).ready(function() {
				myShablon = new shablon( '0.73', '160', '218');
				
				initJcrop( $("#srcImage2") );	
			});

			
		</script>
		
		
		<xsl:apply-templates select="root"/>  
	</xsl:template>
	
	
	
	
	<xsl:template match="root">
		<div>			
			<div style="float:left; width: 550px" class="wb">
				<xsl:call-template name="imageArea"/>
			</div>
			<div style="float:right; width: 400px">
				<xsl:call-template name="editMode"/>
			</div>
		</div>	
	</xsl:template>
	
	
	
	<xsl:template name="editMode"> 	  
		<div id="editMode" class="wb">
			
			<xsl:variable name="context" select="dbInfo/Doctor"/>
			<table>
				<col width="120"/>
				<col/>
				
				<tr>
					<td>Идентификатор:</td>
					<td>
						<strong><xsl:value-of select="$context/@id"/></strong>
					</td>
				</tr>
				<tr>
					<td>Имя:</td>
					<td>
						<xsl:value-of select="$context/Name"/>
					</td>
				</tr>
				<tr>
					<td>Специальность:</td>
					<td>
						<xsl:for-each select="$context/SectorList/Sector">
							<xsl:value-of select="."/>
							<xsl:if test="position() != last()">, </xsl:if>
						</xsl:for-each>
					</td>
				</tr>
				<tr>
					<td>Клиника:</td>
					<td>
						<xsl:value-of select="$context/Clinic"/>
					</td>
				</tr>
				<xsl:if test="srvInfo/IsFile and srvInfo/IsFile='yes'">
					<tr>
						<td colspan="2">
							<div style="margin:0">
								<div style="float:left; width: 160px; height: 218px; margin:0">
									<img src="/img/doctorsNew/{$context/@id}_med.jpg?param={srvInfo/Random}"/>
								</div>
								<div style="float:left; width: 110px; height: 218px; margin: 0 0 0 10px">
									<img src="/img/doctorsNew/{$context/@id}_small.jpg?param={srvInfo/Random}"/>
								</div>
                                <div style="float:left; width: 73px; height: 100px; margin: 0 0 0 10px">
                                    <img src="/img/doctors/1x1/{$context/@id}.jpg?param={srvInfo/Random}"/>
                                </div>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<div style="margin:0">
								<div style="float:left; width: 160px; height: 218px; margin:0">
									<img src="/img/doctorsNew/{$context/@id}.160x218.jpg?param={srvInfo/Random}"/>
								</div>
								<div style="float:left; width: 110px; height: 218px; margin: 0 0 0 10px">
									<img src="/img/doctorsNew/{$context/@id}.110x150.jpg?param={srvInfo/Random}"/>
								</div>
								<div style="float:left; width: 73px; height: 100; margin: 0 0 0 10px">
									<img src="/img/doctorsNew/{$context/@id}.73x100.jpg?param={srvInfo/Random}"/>
								</div>
							</div>
						</td>
					</tr>
				</xsl:if>
			</table>
							
			<form name="loadImage" id="loadImage" method="post" enctype="multipart/form-data">
					<div style="height: 90px; padding: 0 5px 0 5px" >
						<div class="null" style="width: 98%; height: 1px; border-bottom: 1px solid #aaa; margin: 0 2px 5px 2px"/>
						<table>
							<col width="200"/>
							<col/>

							<tr>
								<td>Расположить метку справа:</td>
								<td>
									<input name="prMarkPos" type="checkbox" value="right" onchange="( $(this).attr('checked') ) ? $('#markPos').val('right') : $('#markPos').val('left')"/>
								</td>
							</tr>
						</table>
						
						<div style="float:left; padding: 4px 60px 10px 0">Загрузить изображение</div>
						<div style="float:left; margin-left: 10px" id="file-uploader"></div>
					    <script type="text/javascript">       
					        function createUploader(doctorId){    
					            var uploader = new qq.FileUploader({
					                element: document.getElementById('file-uploader'),
					                action: '/doctor/service/imageUpload.php?id='+doctorId,
									allowedExtensions:  ['jpg','gif','png','tif'],
									sizeLimit: 50000000, 
									multiple : false,
									onError: function (id, fileName, errorReason) {
										alert(errorReason);
									},
									onComplete: function(id, fileName, responseJSON){
										<![CDATA[
										if (responseJSON['success']) {
											$.ajax({
											  type: "get",
											  url: "/doctor/chImage.htm",
											  async: false,
											  data: "id="+doctorId,
											  success: function(html){
												$("#imgWin .modWinContent").html(html);
											  }
											});
										} else {
											$('.qq-upload-list').html("<span class=\"red\">Случилась какая-то шняга.</span>");
										}
										]]>
									},
					                debug: true
					            });           
					        }
					        createUploader(<xsl:value-of select="srvInfo/Id"/>);     
					    </script>
					    <div class="clear" style="height: 20px"/>
					   </div>
			</form>	
		</div>	
		 
		<div style="position:relative; margin: 20px 0px 30px 0;">		  
			<div class="form" style="width:100px; float:right; margin-left: 10px" onclick="$('#imgWin').hide(); editContent('{dbInfo/Doctor/@id}')">ЗАКРЫТЬ</div>
			<div class="form" style="width:100px; float:right;" onclick="saveShablon('{dbInfo/Doctor/@id}')">СОХРАНИТЬ</div>
		</div>
	</xsl:template>
	
	
	
	
	<xsl:template name="imageArea">	  
		<div>
			<xsl:choose>
				<xsl:when test="srvInfo/IsFile and srvInfo/IsFile='yes'">
					<style>
						#imgContainer div {margin:0}
					</style>
							
					<form name="cropForm" id="cropForm" method="post" onsubmit="return checkCoords();">
						<input type="hidden" name="id" id="id" value="{dbInfo/Doctor/@id}"/>   
						<input type="hidden" id="x" name="x" />
						<input type="hidden" id="y" name="y" />
						<input type="hidden" id="w" name="w" />
						<input type="hidden" id="h" name="h" />
						
						<input type="hidden" id="sharp" name="sharp" />
						<input type="hidden" id="markPos" name="markPos" />
					
						<div id="imgContainer">
							<img id="srcImage2" src="/img/doctorsFull/{dbInfo/Doctor/@id}.jpg?param={srvInfo/Random}"/>
							
						</div>
					</form>
					<div>Исходное изображение</div>
				</xsl:when>
				<xsl:otherwise>
					<div style="padding: 47px 0 47px 0; text-align: center;">
						Изображение не загружено
					</div>
				</xsl:otherwise>
			</xsl:choose>
		</div>
	</xsl:template>
	
	
	
	
</xsl:transform>

