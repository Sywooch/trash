<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:output method="html" encoding="utf-8"/>
	
	<xsl:template match="/" >
		<xsl:apply-templates select="root"/>
	</xsl:template>


	<xsl:template match="root">
		<form name="loginForm" id="loginForm" method="post" action="/auth/login.htm">
				<input name="formName" type="hidden" value="loginForm" />
				<div align="center">
					<div class="wb" style="width:300px; height: 70px; margin: 100px 0 0 0; padding:20px;">
						<table align="center" border="0">
							<tr>
								<td>Логин:</td>
								<td><input name="login" class="form" type="text" value="{srvInfo/Login}"/></td>
								<td></td>
							</tr>
							<tr>
								<td>Пароль:</td>
								<td><input name="passwd" class="form" type="Password" value=""/></td>
								<td>
									<input type="submit" class="submit" value="ок" />
									<!-- <div class="form" style="width: 30px;" onclick="document.forms['loginForm'].submit()">ок</div> -->
								</td>
							</tr>
							<tr>
								<td colspan="3">
									<div id="ErrorForm" class="error" style="margin:  10px 0 10px 0;">
										<xsl:choose>
											<xsl:when test="srvInfo/Errors"><xsl:value-of select="srvInfo/Errors"/></xsl:when>
											<xsl:otherwise>&#160;</xsl:otherwise>
										</xsl:choose>
									</div>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</form>
	</xsl:template>
</xsl:transform>

