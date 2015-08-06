<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:import href="../../lib/xsl/common.xsl"/>

	<xsl:output method="html" encoding="utf-8"/>


	<xsl:template match="/">
		<script src="/adminservice/js/index.js"></script>
		<xsl:apply-templates select="root"/>
	</xsl:template>




	<xsl:template match="root">
		<div id="main">
			<h1>Сервисы</h1>

			<ul style="margin: 0 0 10px 20px">
				<li>
					<span class="link" onclick="sendMailFromQuery()">
						Отправить письма из очереди
					</span>
					<span class="delimiter">|</span>
					<span id="mailSendStatistic">Писем в очереди: <xsl:value-of select="dbInfo/MailCount"/></span>
					<span class="ml5" id="mailSendStatus"></span>
					<span class="delimiter">|</span>
					<a href="/logview/emailList.htm" class="link ml10">Посмотреть очередь</a>
					
					<span class="delimiter">|</span>
					<span class="link" onclick="clearMailQuery()">
						Очистить очередь e-mail рассылки
					</span>
					<span id="mailClearStatus"></span>
				</li>
				
				
				
				<li class="mt20">
					<span id="smsQuery">
						<span id="smsStarted">
							<xsl:if test="dbInfo/SMSQuery = 'stop'"><xsl:attribute name="class">hd</xsl:attribute></xsl:if>
							Конфигурация. SMS очередь <strong class="green">запущена</strong> / <span class="link" id="smsQuery" onclick="startStopSMSQuery('stop')">остановить</span>
						</span>
						<span id="smsStoped">
							<xsl:if test="dbInfo/SMSQuery = 'start'"><xsl:attribute name="class">hd</xsl:attribute></xsl:if>
							Конфигурация. SMS очередь <strong class="red">остановлена</strong> / <span class="link" id="smsQuery" onclick="startStopSMSQuery('start')">запустить</span>
						</span>
					</span>
					<span class="delimiter">|</span>
					<a href="/logview/smsList.htm" class="link ml10">Посмотреть очередь</a>
					<span class="delimiter">|</span>
					<span class="link ml10" onclick="checkSMSstsuses()">Запустить проверку статусов (разово)</span>
					<span class="delimiter">|</span>
					<span class="ml10">Баланс гейта(<xsl:value-of select="dbInfo/SMSBalance/@id"/>): <strong><xsl:value-of select="format-number(dbInfo/SMSBalance,'#.00')"/></strong></span>
				</li>
				
				
				
				<li class="mt20">
					<span id="asteriskQueue">
						Очередь Asterisk: 
						<xsl:choose>
							<xsl:when test="dbInfo/Queue/Element">
								<xsl:for-each select="dbInfo/Queue/Element">
									<span class="link"><strong onclick="clearAsteriskNumber('{@sip}')" title="сбросить очередь"><xsl:value-of select="@sip"/></strong></span>
									&#160;(<xsl:value-of select="User"/>)
									<span class="delimiter">|</span>
								</xsl:for-each>
							</xsl:when>
							<xsl:otherwise>
								<em>в очереди никого нет</em>
							</xsl:otherwise>
						</xsl:choose>
					</span>
				</li>
				
				<li class="mt20">
					Процессы пло расписанию
					<!-- span class="link ml10" onclick="clearCrone('all')">Сбросить все блокировки</span -->
				</li>
			</ul>
			
			<div style="margin-left: 20px">
			<span id="crone">
					<table class="seviceCron">
						<thead><tr>
							<th>Крон процессы:</th>
							<th><small>разрешение</small><br/>пользователя</th>
							<th><small>разрешение</small><br/>системы</th>
							<th>Кол-во неудачных<br/>попыток</th>
						</tr>
						</thead>
						<tbody>
							<xsl:for-each select="dbInfo/CroneList/Element">
								<tr>
									<td>
										<strong><xsl:value-of select="."/></strong>
									</td>
									<td align="center" class="isAvailabeGlobal isAvailableGlobal_{@isAvailableGlobal}" cronename="{.}" title="Изменить блокировку">
										 <xsl:value-of select="@isAvailableGlobal"/>
									</td>
									<td align="center" class="isAvailable isAvailable_{@isAvailable}" cronename="{.}" title="Изменить блокировку">
										<xsl:value-of select="@isAvailable"/>
									</td>
									<td align="center">
										<xsl:value-of select="@countFailTry"/>
									</td>
								</tr>
							</xsl:for-each>
						</tbody>
					</table>
				</span>
			</div>

		</div>
	</xsl:template>

</xsl:transform>

