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
			<div>
				<xsl:call-template name="editMode"/>
			</div>
		</div>
	</xsl:template>

	<xsl:template name="editMode"> 	  
		<div id="editMode" class="wb">
			
			<xsl:variable name="context" select="dbInfo/Clinic"/>
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
					<td>Клиника:</td>
					<td>
						<xsl:value-of select="$context/Title"/>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div style="margin:0">
							<div style="float:left; width: 160px; height: 218px; margin:0">
								<xsl:if test="dbInfo/Clinic/FullLogoPath">
									<img class="logo" src="{dbInfo/Clinic/FullLogoPath}?param={srvInfo/Random}"/>
								</xsl:if>
							</div>
						</div>
					</td>
				</tr>
			</table>

			<form name="loadImage" id="loadImage" method="post" enctype="multipart/form-data">
					<div style="height: 90px; padding: 0 5px 0 5px" >
						<div class="null" style="width: 98%; height: 1px; border-bottom: 1px solid #aaa; margin: 0 2px 5px 2px"/>
						
						<div style="float:left; padding: 4px 60px 10px 0">Загрузить изображение</div>
						<div style="float:left; margin-left: 10px" id="file-uploader"></div>
						<script type="text/javascript">
							function createUploader(clinicId){
								var uploader = new qq.FileUploader({
									element: document.getElementById('file-uploader'),
									action: '/clinic/service/imageUpload.php?id='+clinicId,
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
											  url: "/clinic/chImage.htm",
											  async: false,
											  data: "id="+clinicId,
											  success: function(html){
												$("#imgWin .modWinContent").html(html);
												$('.fileName').val(responseJSON['file']);
												$('.logo').attr('src', responseJSON['filePath']);
											  }
											});
										} else {
											$('.qq-upload-list').html("<span class=\"red\">Случилась какая-то шняга.</span>");
										}
										]]>
									},
					                debug: false
					            });
					        }
					        createUploader(<xsl:value-of select="srvInfo/Id"/>);
					    </script>
					    <div class="clear" style="height: 20px"/>
					   </div>
			</form>	
		</div>	
		 
		<div style="position:relative; margin: 20px 0px 60px 0;">
			<div class="form" style="width:100px; float:right; margin-left: 10px" onclick="$('#imgWin').hide(); editContent('{dbInfo/Clinic/@id}')">ЗАКРЫТЬ</div>
			<div class="form" style="width:100px; float:right;" onclick="saveShablon('{dbInfo/Clinic/@id}'); $('#imgWin').hide(); editContent('{dbInfo/Clinic/@id}')">СОХРАНИТЬ</div>
			<input type='hidden' class='fileName' value='' />
		</div>
	</xsl:template>

</xsl:transform>

