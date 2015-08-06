<?xml version='1.0'  encoding="UTF-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:param name="checkStatisticKey" select="/root/srvInfo/Conf/StatisticKey" />
	<xsl:param name="checkSocialKey" select="/root/srvInfo/Conf/SocialKey" />
	<xsl:param name="socialVK" select="''" />
	<xsl:param name="socialFB" select="''" />

	<xsl:param name="stat" select="'no'" />
	<xsl:param name="socialKey" select="'no'" />

	<xsl:decimal-format decimal-separator = '.' grouping-separator = ' ' NaN = ' '/>

	<xsl:output method="html" encoding="utf-8"/>

<xsl:template name="oldSocialLinks">

<xsl:if test="$checkSocialKey = 'yes'"><!-- socials -->

	<div class="social-likes" data-url="http://{dbHeadInfo/ServerFront}{srvInfo/URL}" data-zeroes="yes">
		<div class="facebook">Нравится</div>
		<div class="vkontakte">Одобряю</div>
		<div class="twitter">Твитнуть</div>
	</div>

	<xsl:if test="$socialKey = 'yes' and $stat = 'yes' and $checkStatisticKey = 'yes'"><!-- socials -->
		<script type="text/javascript" src="/js/stat/tracksocial.js"></script>
	</xsl:if><!-- socials track end -->
</xsl:if><!-- socials end -->
</xsl:template>


</xsl:transform>