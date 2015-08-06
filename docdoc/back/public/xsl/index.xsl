<?xml version='1.0'  encoding="UTF-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:output method="html" encoding="utf-8"/>
	
	<xsl:template match="/">
		<xsl:apply-templates select="root"/>
	</xsl:template>
	
	
	
	
	<xsl:template match="root">
		<div>
				
				<xsl:call-template name="vidget">
					<xsl:with-param name="id" select="'doctorStat'"/>
					<xsl:with-param name="title" select="'Врачи'"/>
					<xsl:with-param name="body">
						<xsl:for-each select="/root/dbInfo/StatusDict[@mode = 'doctorDict']/Element[not(@display) or @display != 'no']">
							<xsl:variable name="id" select="@id"/>
							<div class="line">
								<div style="margin: 0; float: left; margin-right: 20px"><xsl:value-of select="."/> </div>
								<div style="margin: 0; float: right">
									<strong>
										<xsl:value-of select="/root/dbInfo/DoctorStat/Element[@status = $id]/."/>
									</strong>
								</div>
							</div>
						</xsl:for-each>
					</xsl:with-param>
				</xsl:call-template>
				
				
				<xsl:call-template name="vidget">
					<xsl:with-param name="id" select="'clinicStat'"/>
					<xsl:with-param name="title" select="'Клиники'"/>
					<xsl:with-param name="body">
						<xsl:for-each select="/root/dbInfo/StatusDict[@mode = 'clinicDict']/Element[not(@display) or @display != 'no']">
							<xsl:variable name="id" select="@id"/>
							<div class="line">
								<div style="margin: 0; float: left; margin-right: 20px"><xsl:value-of select="."/> </div>
								<div style="margin: 0; float: right">
									<strong>
										<xsl:value-of select="/root/dbInfo/ClinicStat[@type ='clinic']/Element[@status = $id]/."/>
									</strong>
								</div>
							</div>
						</xsl:for-each>
					</xsl:with-param>
				</xsl:call-template>
				
				
				<xsl:call-template name="vidget">
					<xsl:with-param name="id" select="'clinicStat'"/>
					<xsl:with-param name="title" select="'Диагностические центры'"/>
					<xsl:with-param name="body">
						<xsl:for-each select="/root/dbInfo/StatusDict[@mode = 'clinicDict']/Element[not(@display) or @display != 'no']">
							<xsl:variable name="id" select="@id"/>
							<div class="line">
								<div style="margin: 0; float: left; margin-right: 20px"><xsl:value-of select="."/> </div>
								<div style="margin: 0; float: right">
									<strong>
										<xsl:value-of select="/root/dbInfo/ClinicStat[@type ='center']/Element[@status = $id]/."/>
									</strong>
								</div>
							</div>
						</xsl:for-each>
					</xsl:with-param>
				</xsl:call-template>
				
				
				
				
				<xsl:call-template name="vidget">
					<xsl:with-param name="id" select="'opinionStat'"/>
					<xsl:with-param name="title" select="'Отзывы'"/>
					<xsl:with-param name="body">
						<xsl:for-each select="/root/dbInfo/OpinionStat/Element">
							<div class="line">
								<div style="margin: 0; float: left; margin-right: 20px">
									<xsl:choose>
										<xsl:when test="@status = 'publish'">Опубликовано</xsl:when>
										<xsl:when test="@status = 'original'">Оригинальные</xsl:when>
										<xsl:when test="@status = 'editor'">Редакторские</xsl:when>
										<xsl:when test="@status = 'guest'">С сайта</xsl:when>
										<xsl:when test="@status = 'content'">Контент</xsl:when>
									</xsl:choose>
								</div>
								<div style="margin: 0; float: right">
									<strong>
										<xsl:value-of select="."/>
									</strong>
								</div>
							</div>
						</xsl:for-each>
						
					</xsl:with-param>
				</xsl:call-template>
				
				
				
				
				 
				<div class="vidget">
					<div class="vdtitle wb">
						Релиз
						<xsl:value-of select="/root/srvInfo/Version"/>
					</div>
					<div class ="vdbody personal personal">
						<xsl:attribute name="style">
							background-image: url('/img/release_logo/<xsl:value-of select="/root/srvInfo/VersionImage"/>');
						</xsl:attribute>
						<div>

							<div style="margin: 0 200px 10px 0; float: left; width:400px;">
								<p><a target="_blank">
									<xsl:attribute name="href">
										https://docdoc.atlassian.net/issues/?jql=project%20%3D%20DD%20AND%20fixVersion%20%3D%20<xsl:value-of select="/root/srvInfo/version"/>%20ORDER%20BY%20priority%20DESC%2C%20key%20DESC
									</xsl:attribute>

									Release notes
								</a></p>
								<p>
									<a href="/2.0/version/history">История версий</a>
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
	</xsl:template>

	
	<xsl:template name="vidget">
		<xsl:param name="id"/>
		<xsl:param name="title" select="''"/>
		<xsl:param name="body" select="''"/>


		<div class="vidget">
			<div class="vdtitle wb"><xsl:value-of select="$title"/></div>
			<div class ="vdbody" id="{$id}">
				<xsl:copy-of select="$body"/>	
			</div>
		</div>

			
	</xsl:template>
</xsl:transform>

