<?xml version='1.0'  encoding="UTF-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:output method="html" encoding="utf-8"/>

	<xsl:template match="root">
		<xsl:choose>
			<xsl:when test="dbInfo/Step = 'step2'">
				<xsl:choose>
					<xsl:when test="dbInfo/Mode = 'doctor'">
						<xsl:call-template name="registrationOldStep2Doctor"/>
					</xsl:when>
					<xsl:when test="dbInfo/Mode = 'clinic'">
						<xsl:call-template name="registrationOldStep2Clinic"/>
					</xsl:when>
				</xsl:choose>
			</xsl:when>
			<xsl:when test="dbInfo/Step = 'proceed'">
				<xsl:call-template name="registrationOldThanks"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:call-template name="registrationOldStep1"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template name="registrationOldStep1">
		<main class="l-main l-wrapper" role="main">
			<div class="client_reg">
				<div class="client_reg_main">
					<form class="client_reg_form i-doctor_4" action="/service/register.php" method="post">
						<h1>
							Регистрация врачей и клиник
						</h1>
						<p class="client_req_form_row">
							<input class="dd_input" type="text" name="name" placeholder="Имя и фамилия" />
						</p>
						<p class="client_req_form_row">
							<input class="dd_input js-mask-phone" type="text" placeholder="Контактный номер телефона" name="phone" />
						</p>
						<p class="client_req_form_row">
							<span class="l-b">
								<label class="label_radio">
									<input type="radio" class="input_radio" name="mode" value="doctor" checked="" />
									Я представляю свои интересы
								</label>
							</span>
							<span class="l-b">
								<label class="label_radio">
									<input type="radio" class="input_radio" name="mode" value="clinic" />
									Я представляю интересы клиники
								</label>
							</span>
						</p>
						<p class="t-center">
							<input class="ui-btn ui-btn_green" type="submit" value="Далее" />
						</p>
					</form>
				</div>
				<div class="client_reg_description">
					<p>
						Вы врач и хотите разместить анкету на DocDoc.ru?
						Или Вы представитель клиники и хотите с нами сотрудничать?
					</p>
					<p>
						Вам нужно просто заполнить все поля анкеты и нажать кнопку «Далее». Регистрация не займет много времени.
					</p>
					<p>
						Если у Вас возникли вопросы, связанные с регистрацией, свяжитесь с нами:
					</p>
					<p class="client_reg_contact">
						тел.:
						<xsl:choose>
							<xsl:when test="/root/srvInfo/IsMobile = '1'">
								<a class="">
									<xsl:attribute name="href">
										tel:<xsl:value-of select="dbHeadInfo/Phone/Numerically" />
									</xsl:attribute>
									<xsl:value-of select="dbHeadInfo/Phone/Short"/>
								</a>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="dbHeadInfo/Phone/Office"/>
							</xsl:otherwise>
						</xsl:choose>
					</p>
					<p class="client_reg_contact">
						email: <a class="" href="mailto:{dbHeadInfo/Emails/PublicEmail}">
                        <xsl:value-of select="dbHeadInfo/Emails/PublicEmail"/>
								</a>
					</p>
				</div>
			</div>
		</main>
	</xsl:template>

	<xsl:template name="registrationOldStep2Doctor">
		<main class="l-main l-wrapper" role="main">
			<div class="client_reg">
				<div class="client_reg_main">
					<form class="client_reg_form i-doctor_4" action="/service/registerStep2.php" method="post">
						<h1>
							Регистрация врачей и клиник (шаг 2)
						</h1>
						<p class="client_req_form_row">
							<input class="dd_input" type="text" name="clinic" placeholder="Название клиники, в которой Вы принимаете" />
							<input type="hidden" name="mode" value="doctor" />
						</p>
						<p class="client_req_form_row">
							<label>
								<input class="ui-checkbox" type="checkbox" name="isPrivatDoctor" />
								Я веду прием в домашних условиях
							</label>
						</p>
						<p class="client_req_form_row">
							<input class="dd_input" type="text" placeholder="Ваш контактный email адрес" name="email" />
						</p>
						<p class="client_req_form_row">
							<label class="label_relative">
								<input class="ui-checkbox" type="checkbox" name="agreed" />
								Я принимаю условия
								<a href="/download/docdoc_reg_offer.pdf" target="_blank">
									Договора оферты
								</a>
							</label>
						</p>
						<p class="t-center">
							<input class="ui-btn ui-btn_green" type="submit" value="Далее" />
						</p>
					</form>
				</div>
				<div class="client_reg_description">
					<p>
						Если у Вас возникли вопросы, связанные с регистрацией, свяжитесь с нами:
					</p>
					<p class="client_reg_contact">
						тел.:
						<xsl:choose>
							<xsl:when test="/root/srvInfo/IsMobile = '1'">
								<a class="">
									<xsl:attribute name="href">
										tel:<xsl:value-of select="dbHeadInfo/Phone/Numerically" />
									</xsl:attribute>
									<xsl:value-of select="dbHeadInfo/Phone/Short"/>
								</a>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="dbHeadInfo/Phone/Office"/>
							</xsl:otherwise>
						</xsl:choose>
					</p>
					<p class="client_reg_contact">
						email: <a class="" href="mailto:{dbHeadInfo/Emails/PublicEmail}">
                        <xsl:value-of select="dbHeadInfo/Emails/PublicEmail"/>
					</a>
					</p>
				</div>
			</div>
		</main>
	</xsl:template>

	<xsl:template name="registrationOldStep2Clinic">
		<main class="l-main l-wrapper" role="main">
			<div class="client_reg">
				<div class="client_reg_main">
					<form class="client_reg_form i-doctor_4" action="/service/registerStep2.php" method="post">
						<h1>
							Регистрация врачей и клиник (шаг 2)
						</h1>
						<p class="client_req_form_row">
							<input class="dd_input" type="text" name="clinic" placeholder="Название клиники" />
							<input type="hidden" name="mode" value="clinic" />
						</p>
						<p class="client_req_form_row">
							<input class="dd_input" type="text" placeholder="Ваш контактный email адрес" name="email" />
						</p>
						<p class="t-center">
							<input class="ui-btn ui-btn_green" type="submit" value="Далее" />
						</p>
					</form>
				</div>
				<div class="client_reg_description">
					<p>
						Если у Вас возникли вопросы, связанные с регистрацией, свяжитесь с нами:
					</p>
					<p class="client_reg_contact">
						тел.:
						<xsl:choose>
							<xsl:when test="/root/srvInfo/IsMobile = '1'">
								<a class="">
									<xsl:attribute name="href">
										tel:<xsl:value-of select="dbHeadInfo/Phone/Numerically" />
									</xsl:attribute>
									<xsl:value-of select="dbHeadInfo/Phone/Short"/>
								</a>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="dbHeadInfo/Phone/Office"/>
							</xsl:otherwise>
						</xsl:choose>
					</p>
					<p class="client_reg_contact">
						email: <a class="" href="mailto:{dbHeadInfo/Emails/PublicEmail}">
                        <xsl:value-of select="dbHeadInfo/Emails/PublicEmail"/>
					</a>
					</p>
				</div>
			</div>
		</main>
	</xsl:template>

	<xsl:template name="registrationOldThanks">
		<main class="l-main l-wrapper" role="main">
			<div class="client_reg">
				<div class="client_reg_thanks">
					<h1>
						Благодарим вас за обращение!
					</h1>
					<p>
						Ваша заявка на регистрацию отправлена. Наши специалисты свяжутся с Вами в ближайшее время и зарегистрируют Вас.
					</p>
					<p>
						Если у Вас возникли вопросы, связанные с регистрацией, свяжитесь с нами:
					</p>
					<p class="client_reg_contact">
						тел.:
						<xsl:choose>
							<xsl:when test="/root/srvInfo/IsMobile = '1'">
								<a class="">
									<xsl:attribute name="href">
										tel:<xsl:value-of select="dbHeadInfo/Phone/Numerically" />
									</xsl:attribute>
									<xsl:value-of select="dbHeadInfo/Phone/Short"/>
								</a>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="dbHeadInfo/Phone/Office"/>
							</xsl:otherwise>
						</xsl:choose>
					</p>
					<p class="client_reg_contact">
						email:
						<a class="" href="mailto:{dbHeadInfo/Emails/PublicEmail}">
                            <xsl:value-of select="dbHeadInfo/Emails/PublicEmail"/>
						</a>
					</p>
				</div>
			</div>
		</main>
	</xsl:template>

</xsl:transform>

