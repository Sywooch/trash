<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:output method="html" encoding="utf-8"/>


	<xsl:template match="/">
		<style>
			.phoneList	{padding: 10px; margin: 0; font-size: 16px; display: none;}
			.phoneList span {display: inline-block; margin-left: 10px; margin-bottom: 10px;}
			.queueList {padding: 10px; text-align: center; font-size: 16px;}
			.queueList span {margin-left:10px;}
		</style>
		<script>
			$(".queueSelector").click(function(){
				$(".queueSelector").addClass("link");
				$(this).removeClass("link");
				$(".phoneList").show(200);
			});

			$(".phoneList .link").click(function(){
				var queue = $(".queueSelector").not(".link").attr("data-id");
				var num = $(this).data("sip");
				setQueueNum(queue, num);
			});
		</script>
		<xsl:apply-templates select="root"/>
	</xsl:template>




	<xsl:template match="root">
		<div class="queueList">
			<xsl:for-each select="dbInfo/QueueList/Element">
				<span class="link queueSelector" data-id="{Id}"><xsl:value-of select="Name"/></span>
			</xsl:for-each>
		</div>
		<div class="phoneList">
			<xsl:for-each select="srvInfo/QueueDict/Element">
				<xsl:choose>
					<xsl:when test=". = /root/dbInfo/Queue/Element/@sip"><span><xsl:value-of select="."/></span></xsl:when>
					<xsl:when test=". = /root/srvInfo/UserData/SIP"><span class="link" style="color:red"><xsl:value-of select="."/></span></xsl:when>
					<xsl:otherwise><span class="link" data-sip="{.}"><xsl:value-of select="."/></span></xsl:otherwise>
				</xsl:choose>
				<xsl:if test="position() != last()">,&#160;</xsl:if>
				<xsl:if test="position() = 14"><br/></xsl:if>
			</xsl:for-each>
		</div>
		<img src="/img/common/clBtBig.gif" width="20" height="20"  alt="закрыть" style="position: absolute; cursor: pointer; right: 5px; top: 5px;" title="закрыть" onclick="clousePopup();" border="0"/>
	</xsl:template>

</xsl:transform>

