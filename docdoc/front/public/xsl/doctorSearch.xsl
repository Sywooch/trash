<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:import href="requestForm.xsl"/>
	<xsl:import href="hotBanners.xsl"/>
	<xsl:import href="specList.xsl"/>
	<xsl:import href="thisIllness.xsl"/>
	<xsl:import href="doctorSearchTop.xsl"/>
	<xsl:import href="doctorSearchFilter.xsl"/>
	<xsl:import href="doctorsList.xsl"/>
	<xsl:import href="doctorsPager.xsl"/>
	<xsl:import href="listSpecIllness.xsl"/>
	<xsl:import href="listSpecUnderspec.xsl"/>
	<xsl:import href="listSpecDiag.xsl"/>
	<xsl:import href="listSpec.xsl"/>
	<xsl:import href="geoLinks.xsl"/>
	<xsl:import href="asideBanners.xsl"/>
	<xsl:import href="listSpecByStation.xsl"/>


	<xsl:decimal-format decimal-separator='.' grouping-separator=' ' NaN=' '/>

	<xsl:output method="html" encoding="utf-8"/>


	<xsl:template match="/root">


		<xsl:call-template name="doctorList"/>

	</xsl:template>

	<xsl:template name="doctorList">
		<xsl:param name="doctorList" select="/root/dbInfo/DoctorList"/>
		<xsl:if test="/root/dbHeadInfo/IsAbTest = 1">
			<script>
				if (typeof ga !== 'undefined') {
					ga('set', 'dimension4', 'With telefone');
				} else {
					$(document).on('gaCreated', function () {
						ga('set', 'dimension4', 'With telefone' );
					});
				}
			</script>
		</xsl:if>
		<xsl:if test="/root/dbHeadInfo/IsAbTest = 2">
			<script>
				if (typeof ga !== 'undefined') {
				ga('set', 'dimension4', 'Without telefone');
				} else {
				$(document).on('gaCreated', function () {
				ga('set', 'dimension4', 'Without telefone' );
				});
				}
			</script>
		</xsl:if>

		<main class="l-main l-wrapper" role="main">

			<div class="">

				<xsl:choose>
					<!-- give doctor-cards full screen width -->
					<xsl:when test="/root/srvInfo/IsMobile = '1'">

					</xsl:when>
					<xsl:otherwise>
						<xsl:attribute name="class">has-aside</xsl:attribute>
					</xsl:otherwise>
				</xsl:choose>

				<xsl:choose>
					<xsl:when test="/root/dbInfo/IsContextAdv = 1">
						<xsl:call-template name="doctorSearchTop">
							<xsl:with-param name="doctorSearchType" select="'context'"/>
						</xsl:call-template>
					</xsl:when>
					<xsl:when test="/root/dbInfo/IsLandingPage = 1">
						<xsl:call-template name="doctorSearchTop">
							<xsl:with-param name="doctorSearchType" select="'landing'"/>
						</xsl:call-template>
					</xsl:when>
					<xsl:otherwise>
						<xsl:call-template name="doctorSearchTop">
							<xsl:with-param name="doctorSearchType" select="'usual'"/>
						</xsl:call-template>
					</xsl:otherwise>
				</xsl:choose>

				<xsl:call-template name="doctorSearchFilter"/>

				<section class="doctor_list">
					<xsl:for-each select="$doctorList/Element">
						<xsl:call-template name="doctorShortCard">
							<xsl:with-param name="context" select="."/>
						</xsl:call-template>
					</xsl:for-each>
				</section>

				<xsl:if test="count(dbInfo/BestDoctorList/Element) &gt; 0">

					<div class="b-notification">
						<p class="i-notification">
							По вашему запросу врачей-<xsl:value-of
								select="/root/srvInfo/SearchParams/SelectedSpeciality/InGenitivePluralLC"/> мало,
							поэтому мы предлагаем вам ознакомиться и выбрать врача-<xsl:value-of
								select="/root/srvInfo/SearchParams/SelectedSpeciality/InGenitiveLC"/> из лучших
							клиник
							<xsl:value-of select="/root/dbHeadInfo/City/NameInGenitive"/> и записаться к нему на
							приём.
						</p>
						<span class="notification_title">
							Лучшие врачи-<xsl:value-of
								select="/root/srvInfo/SearchParams/SelectedSpeciality/InPluralLC"/>
						</span>
					</div>

					<xsl:for-each select="dbInfo/BestDoctorList/Element">
						<xsl:call-template name="doctorShortCard">
							<xsl:with-param name="context" select="."/>
						</xsl:call-template>
					</xsl:for-each>

				</xsl:if>
			</div>
			<!-- has-aside -->

			<xsl:choose>
				<xsl:when test="/root/srvInfo/IsMobile = '1'">
					<!-- do not load aside for mobile devices -->

				</xsl:when>
				<xsl:otherwise>
					<aside class="l-aside">
						<xsl:call-template name="listSpecUnderspec"/>
						<xsl:call-template name="listSpecDiag"/>
						<xsl:call-template name="listSpec"/>
						<xsl:call-template name="geoLinks"/>
						<xsl:call-template name="listSpecIllness"/>

						<xsl:if test="not(/root/srvInfo/IsLandingPage)">
							<xsl:call-template name="asideBanners"/>
						</xsl:if>
					</aside>
				</xsl:otherwise>
			</xsl:choose>


			<xsl:choose>
				<xsl:when test="/root/srvInfo/IsLandingPage">
					<div class="link_all_doctors">
						<a href="/doctor/{/root/srvInfo/SearchParams/SelectedSpeciality/Alias}">
							посмотреть всех
							<xsl:value-of select="/root/srvInfo/SearchParams/SelectedSpeciality/InGenitivePluralLC"/> на
							DocDoc.ru
						</a>
					</div>
				</xsl:when>
				<xsl:otherwise>
					<xsl:call-template name="doctorsPager">
						<xsl:with-param name="pagerData" select="/root/dbInfo/Pager"/>
					</xsl:call-template>
				</xsl:otherwise>
			</xsl:choose>

			<xsl:value-of select="/root/dbHeadInfo/SEO/Texts/Element[Position='2']/Text"/>

			<xsl:if test="/root/srvInfo/IsMobile = '0'">
				<xsl:call-template name="listSpecByStation"/>
			</xsl:if>

		</main>

		<div class="js-popup popup" data-popup-id="schedule-popup" id="schedule-popup" data-popupWidth="735">
		</div>

	</xsl:template>


</xsl:transform>
