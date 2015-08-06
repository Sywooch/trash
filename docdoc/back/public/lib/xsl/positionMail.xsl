<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output method="html" encoding="utf-8"/>
	
	<xsl:template name="positionContent">
		<xsl:param name="disableContent" select="."/>
		
		<div id="positionContent">
			<div align="center" style="margin: 5px 0 0 0">Схема контента</div>
			<table cellpadding="0" cellspacing="2" border="0">
				<tr>
					<td rel="1" id="posContTd_1_1">1-1</td>
					<td rel="2" id="posContTd_2_1">2-1</td>
					<td rel="3" id="posContTd_3_1">3-1</td>
					<td rel="4" id="posContTd_4_1">4-1</td>
				</tr>
				
			</table>
			
		</div>
		<style type="text/css">
			#positionContentDiv{}
			
			#positionContent{font-size:18px;}
			#positionContent table{border:1px solid #353535;}
			#positionContent td{width:87px;height:49px;text-align:center;background:#c4c4c4;cursor:pointer;}
			#positionContent td.posContTd_disable{background:#e0e0e0;cursor:auto;color:#9b9b9b;}
		</style>
		<script type="text/javascript">
			$("#posContTd_"+$("#positionContentDiv_str").text()+"_"+$("#positionContentDiv_stolb").text()).css({'background':'#9b9b9b','font-weight':'bold','color':'#fff'});
			$("#positionContent td").click(
				function(){
					if($(this).attr('class') == 'posContTd_disable'){
						alert('Эта позиция заблокирована!');
					}else{
						$("#positionContent td").css({'background':'#c4c4c4','font-weight':'normal','color':'#353535'});
						$("#positionContent td.posContTd_disable").css({'background':'#e0e0e0','color':'#9b9b9b'});
						$(this).css({'background':'#9b9b9b','font-weight':'bold','color':'#fff'});
						$("#positionContentDiv_str").html($(this).attr('id').split('_')[1]);
						$("#positionContentDiv_stolb").html($(this).attr('id').split('_')[2]);
						$("#sortOrderInput").val($(this).attr('rel'));
						//////////////////////
					}
				}
			);
		</script>
		
	</xsl:template>
	
</xsl:transform>

