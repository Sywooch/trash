<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	
	<xsl:import href="../../lib/xsl/modalWindow.xsl"/>		 
	<xsl:import href="../../lib/xsl/common.xsl"/>

	<xsl:output method="html" version="4.0" indent="yes" encoding="utf-8" omit-xml-declaration="yes"/>
	
	<xsl:template match="/">   
		<div style="position: absolute; margin: 0; top:0; left:0; z-index: 0;">
			<xsl:call-template name="modalWindow">
				<xsl:with-param name="id" select="'modalWin'"/>
				<xsl:with-param name="title" select="'Пользователь'"/>
				<xsl:with-param name="width" select="'650'"/>
			</xsl:call-template>
		</div>	
		<xsl:apply-templates select="root"/>  
		<script type="text/javascript" src="/user/js/user.js"></script>
		<script type="text/javascript">
			var winDeltaY = 150;
			var winDeltaX = 400;	
		</script>
	</xsl:template>
	
	
	
	
	<xsl:template match="root">	
		<div id="main">
			<h1>Список пользователей системы</h1>	

<!--	Фильтр	-->			
			<div id="filter">	  
				<form id="formFilter" name="filter" method="get" action=""> 		
					<table align="center">	 
						<tr>  
							<td valign="top">  
								<label>Фамилия:</label>	
								<div>
									<input name="userName" id="userName" style="width: 250px" maxlength="50" value="{srvInfo/UserName}"/>
								</div>
							</td> 
							<td valign="top">  
								<label>Статус:</label>	
								<div>  
									<select name="status" id="status" style="width: 105px" >
										<option value="">--- все ---</option> 
										<option value="enable"><xsl:if test="srvInfo/Status = 'enable'"><xsl:attribute name="selected"/></xsl:if>активный</option> 
										<option value="disable"><xsl:if test="srvInfo/Status = 'disable'"><xsl:attribute name="selected"/></xsl:if>заблокирован</option>
									</select>
								</div>
							</td>  
							<td>   
								<div style="margin: 2px 10px 0px 0;">	  
									<div class="form" style="width:100px; float:right;" onclick="document.forms['filter'].submit()">ПОИСК</div>
								</div>
							</td>
						</tr>
					</table>
					
					
				</form>
			</div>	 
			
			
	
<!--	Action List	-->
			<div id="actionList">  
				<a href="javascript:editContent('','edit')">Добавить пользователя</a>
			</div>		
<!--	resultSet	-->
			<div id="resultSet">	
				<xsl:variable name="tdCount" select="10"/> 
				<table cellpadding="0" cellspacing="1" width="100%" border="0" class="resultSet">	
					<col width="50"/> 
					<col width="100"/> 
					<col width="300"/>
					<col width="200"/>
					<col width="200"/>
					<col width="80"/>
					<tr>  
						<th>Id</th>
						<th>Login</th>
						<th>Фамилия Имя</th>
						<th>E-mail</th> 
						<th>Телефон</th> 
						<th>Skype</th>
						<th>Права</th>
						<th>Статус</th>
						<th>Действия</th>
					</tr>
					<xsl:choose>
						<xsl:when test="dbInfo/UserList/Element"> 
							<xsl:for-each select="dbInfo/UserList/Element">   
								<xsl:variable name="class">
									<xsl:choose>
										<xsl:when test="(position() div 2) - floor(position() div 2) &gt; 0">odd</xsl:when>
										<xsl:otherwise>even</xsl:otherwise>
									</xsl:choose>
								</xsl:variable>	 
								<tr id="tr_{@id}" class="{$class}" backclass="{$class}" onmouseover="if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')" onmouseout="if (!$(this).hasClass('trSelected')) $(this).attr('class','{$class}')">
								
									<td><xsl:value-of select="@id"/></td> 
									<td><xsl:value-of select="Login"/></td> 
									<td>  
										<a href="javascript:editContent('{@id}')"><xsl:value-of select="LastName"/>&#160;<xsl:value-of select="FirstName"/></a>
									</td>  
									<td>
										<a href="mailto:{Email}"><xsl:value-of select="Email"/></a>
									</td> 
									<td>
										<xsl:value-of select="Phone"/>
									</td>
									<td>
										<xsl:value-of select="Skype"/>
									</td>
									<td>
										<xsl:for-each select="Rights/Right"> 
											<xsl:variable name="id" select="@id"/>
											<span title="{/root/dbInfo/RightList/Element[@id = $id]/.}"><xsl:value-of select="/root/dbInfo/RightList/Element[@id = $id]/@code"/></span>
											<!--<xsl:value-of select="@id"/>-->
											<xsl:if test="position() != last()">,&#160;</xsl:if>
										</xsl:for-each>
									</td>
									<td align="right">
										<xsl:choose>	
											<xsl:when test="Status = 'enable'"><span class="green">активен</span></xsl:when>
											<xsl:otherwise><span class="red">заблокирован</span></xsl:otherwise>
										</xsl:choose>
									</td>
									<td>
										<a href="javascript:editContent('{@id}')">edit</a>
									</td>
								</tr>
							</xsl:for-each>
						</xsl:when>
						<xsl:otherwise>	
							<tr>
								<td colspan="{$tdCount}" align="center">
									<div class="error" style="margin: 20px">Данных не найдено</div>
								</td>
							</tr>
						</xsl:otherwise>
					</xsl:choose>
				</table>
			</div>	
			   
		</div>
	</xsl:template>
</xsl:transform>

