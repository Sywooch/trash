<?php
/**
 * @var string $host
 */
?>
<!doctype html>
<html>
<head>
	<title>Виджеты ДокДок</title>
	<meta http-equiv="Content-Type" content="charset=UTF-8">
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,800,300,700&subset=latin,cyrillic-ext'
		  rel='stylesheet' type='text/css'>
	<style>
		body {
			font-family: 'Open Sans', sans-serif;
			font-size: 14px;
		}

		a {
			text-decoration: none;
			border-bottom: 1px solid #b2ccf0;
			color: #008ace;
		}

		a:hover {
			color: #cc0000;
			border-bottom-color: #f0b2b2;
		}

		h2 {
			margin-top: 30px;
			border-top: 1px solid #ccc;
			padding: 15px 15px 0 15px;
		}

		h3 {
			background: #f5f5ea;
			padding: 20px 15px;
			margin-top: 50px;;
		}

		h2 a, h3 a {
			font-size: 14px;
			float: right;
		}

		.widget-container {
			max-width: 1000px;
		}
	</style>
</head>
<body>

<h1 id="top">Виджеты ДокДок</h1>

<ul id="menu">
	<li>
		<a href="#ClinicList_title">
			Список клиник
		</a>
		<ul>
			<li>
				<a href="#ClinicList_knigamedika_title">
					шаблон: ClinicList, тема: knigamedika
				</a>
			</li>
			<li>
				<a href="#ClinicList_medinfa_title">
					шаблон: ClinicList, тема: medinfa
				</a>
			</li>
			<li>
				<a href="#ClinicList_sidemedical_title">
					шаблон: ClinicList, тема: sidemedical
				</a>
			</li>
			<li>
				<a href="#ClinicList_uzilab_title">
					шаблон: ClinicList, тема: uzilab
				</a>
			</li>
			<li>
				<a href="#ClinicList_uzilab2_title">
					шаблон: ClinicList, тема: uzilab2
				</a>
			</li>
			<li>
				<a href="#ClinicList_ClinicList_700_msk_title">
					шаблон: ClinicList_700, город: Москва
				</a>
			</li>
			<li>
				<a href="#ClinicList_ClinicList_700_msk_params_title">
					шаблон: ClinicList_700, город: Москва, специальность: Стоматология, метро: Киевская
				</a>
			</li>
			<li>
				<a href="#ClinicList_ClinicList_700_spb_title">
					шаблон: ClinicList_700, город: Санкт-Петербург
				</a>
			</li>
			<li>
				<a href="#ClinicList_ClinicList_700_spb_params_title">
					шаблон: ClinicList_700, город: Санкт-Петербург, специальность: Стоматология, метро: Владимирская
				</a>
			</li>
			<li>
				<a href="#ClinicList_ClinicList_700_ekb_title">
					шаблон: ClinicList_700, город: Екатеринбург
				</a>
			</li>
			<li>
				<a href="#ClinicList_ClinicList_700_ekb_params_title">
					шаблон: ClinicList_700, город: Екатеринбург, специальность: Венерология, район: Кировский
				</a>
			</li>
			<li>
				<a href="#ClinicList_ClinicList_700_nsk_title">
					шаблон: ClinicList_700, город: Новосибирск
				</a>
			</li>
			<li>
				<a href="#ClinicList_ClinicList_700_perm_title">
					шаблон: ClinicList_700, город: Пермь
				</a>
			</li>
			<li>
				<a href="#ClinicList_ClinicList_700_nn_title">
					шаблон: ClinicList_700, город: Нижний Новгород
				</a>
			</li>
			<li>
				<a href="#ClinicList_ClinicList_700_kazan_title">
					шаблон: ClinicList_700, город: Казань
				</a>
			</li>
			<li>
				<a href="#ClinicList_ClinicList_700_samara_title">
					шаблон: ClinicList_700, город: Самара
				</a>
			</li>
			<li>
				<a href="#ClinicList_Knigamedika_title">
					шаблон: Knigamedika
				</a>
			</li>
		</ul>
	</li>
	<li>
		<a href="#DoctorList_title">
			Список врачей
		</a>
		<ul>
			<li>
				<a href="#DoctorList_DoctorList_700_msk_title">
					шаблон: DoctorList_700, город: Москва
				</a>
			</li>
			<li>
				<a href="#DoctorList_DoctorList_700_msk_params_title">
					шаблон: DoctorList_700, город: Москва, специальность: Стоматология, метро: Киевская
				</a>
			</li>
			<li>
				<a href="#DoctorList_DoctorList_700_spb_title">
					шаблон: DoctorList_700, город: Санкт-Петербург
				</a>
			</li>
			<li>
				<a href="#DoctorList_DoctorList_700_spb_params_title">
					шаблон: DoctorList_700, город: Санкт-Петербург, специальность: Стоматология, метро: Владимирская
				</a>
			</li>
			<li>
				<a href="#DoctorList_DoctorList_700_ekb_title">
					шаблон: DoctorList_700, город: Екатеринбург
				</a>
			</li>
			<li>
				<a href="#DoctorList_DoctorList_700_ekb_params_title">
					шаблон: DoctorList_700, город: Екатеринбург, специальность: Венерология, район: Кировский
				</a>
			</li>
			<li>
				<a href="#DoctorList_DoctorList_700_nsk_title">
					шаблон: DoctorList_700, город: Новосибирск
				</a>
			</li>
			<li>
				<a href="#DoctorList_DoctorList_700_perm_title">
					шаблон: DoctorList_700, город: Пермь
				</a>
			</li>
			<li>
				<a href="#DoctorList_DoctorList_700_nn_title">
					шаблон: DoctorList_700, город: Нижний Новгород
				</a>
			</li>
			<li>
				<a href="#DoctorList_DoctorList_700_kazan_title">
					шаблон: DoctorList_700, город: Казань
				</a>
			</li>
			<li>
				<a href="#DoctorList_DoctorList_700_samara_title">
					шаблон: DoctorList_700, город: Самара
				</a>
			</li>
			<li>
				<a href="#DoctorList_DoctorList_240_title">
					шаблон: DoctorList_240
				</a>
			</li>
			<li>
				<a href="#DoctorList_DoctorList_240_mamaru_title">
					шаблон: DoctorList_240, тема: mamaru
				</a>
			</li>
		</ul>
	</li>
	<li>
		<a href="#Request_title">
			Заявки
		</a>
		<ul>
			<li>
				<a href="#Request_728x90_title">
					шаблон: Request_728x90
				</a>
			</li>
			<li>
				<a href="#Request_medinfa_title">
					шаблон: Request, тема: medinfa
				</a>
			</li>
			<li>
				<a href="#Request_medvopros_title">
					шаблон: Request, тема: medvopros
				</a>
			</li>
			<li>
				<a href="#Request_sidemedical_title">
					шаблон: Request, тема: sidemedical
				</a>
			</li>
			<li>
				<a href="#Request_theme1_title">
					шаблон: Request, тема: theme1
				</a>
			</li>
			<li>
				<a href="#Request_theme2_title">
					шаблон: Request, тема: theme2
				</a>
			</li>
			<li>
				<a href="#Request_theme3_title">
					шаблон: Request, тема: theme3
				</a>
			</li>
			<li>
				<a href="#Request_theme4_title">
					шаблон: Request, тема: theme4
				</a>
			</li>
			<li>
				<a href="#Request_vlanamed_510_title">
					шаблон: Request, тема: vlanamed_510
				</a>
			</li>
		</ul>
	</li>
	<li>
		<a href="#Search_title">
			Поиск
		</a>
		<ul>
			<li>
				<a href="#Search_medinfa_title">
					шаблон: Search, тема: medinfa
				</a>
			</li>
			<li>
				<a href="#Search_medinfa_clinic_title">
					шаблон: Search, тема: medinfa, тип поиска: clinic
				</a>
			</li>
			<li>
				<a href="#Search_medvopros_title">
					шаблон: Search, тема: medvopros
				</a>
			</li>
			<li>
				<a href="#Search_sidemedical_title">
					шаблон: Search, тема: sidemedical
				</a>
			</li>
			<li>
				<a href="#Search_theme1_title">
					шаблон: Search, тема: theme1
				</a>
			</li>
			<li>
				<a href="#Search_theme2_title">
					шаблон: Search, тема: theme2
				</a>
			</li>
			<li>
				<a href="#Search_theme3_title">
					шаблон: Search, тема: theme3
				</a>
			</li>
			<li>
				<a href="#Search_theme4_title">
					шаблон: Search, тема: theme4
				</a>
			</li>
			<li>
				<a href="#Search_vlanamed_510_title">
					шаблон: Search, тема: vlanamed_510
				</a>
			</li>
			<li>
				<a href="#SearchClinic_240x400_title">
					шаблон: SearchClinic_240x400
				</a>
			</li>
			<li>
				<a href="#SearchDoctor_240x400_title">
					шаблон: SearchDoctor_240x400
				</a>
			</li>
			<li>
				<a href="#SearchDoctor_vertical_mosglavmed_180x180_title">
					шаблон: SearchDoctor_vertical, тема: mosglavmed_180x180
				</a>
			</li>
			<li>
				<a href="#SearchDoctor_vertical_psihomed_225x225_title">
					шаблон: SearchDoctor_vertical, тема: psihomed_225x225
				</a>
			</li>
			<li>
				<a href="#SearchDoctor_vertical_vlanamed_200x400_title">
					шаблон: SearchDoctor_vertical, тема: vlanamed_200x400
				</a>
			</li>
			<li>
				<a href="#SearchDoctor_vertical_vlanamed_225x400_title">
					шаблон: SearchDoctor_vertical, тема: vlanamed_225x400
				</a>
			</li>
			<li>
				<a href="#SearchClinic_vertical_vlanamed_200x400_title">
					шаблон: SearchClinic_vertical, тема: vlanamed_200x400
				</a>
			</li>
			<li>
				<a href="#SearchClinic_vertical_vlanamed_225x400_title">
					шаблон: SearchClinic_vertical, тема: vlanamed_225x400
				</a>
			</li>`
		</ul>
	</li>
</ul>

<h2 id="ClinicList_title">Список клиник <a href="#top">наверх</a></h2>

<h3 id="ClinicList_knigamedika_title">
	шаблон: ClinicList, тема: knigamedika
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="ClinicList_knigamedika"></div>

<h3 id="ClinicList_medinfa_title">
	шаблон: ClinicList, тема: medinfa
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="ClinicList_medinfa"></div>

<h3 id="ClinicList_sidemedical_title">
	шаблон: ClinicList, тема: sidemedical
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="ClinicList_sidemedical"></div>

<h3 id="ClinicList_uzilab_title">
	шаблон: ClinicList, тема: uzilab
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="ClinicList_uzilab"></div>

<h3 id="ClinicList_uzilab2_title">
	шаблон: ClinicList, тема: uzilab2
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="ClinicList_uzilab2"></div>

<h3 id="ClinicList_ClinicList_700_msk_title">
	шаблон: ClinicList_700, город: Москва
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="ClinicList_700_msk"></div>

<h3 id="ClinicList_ClinicList_700_msk_params_title">
	шаблон: ClinicList_700, город: Москва, специальность: Стоматология, метро: Киевская
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="ClinicList_700_msk_params"></div>

<h3 id="ClinicList_ClinicList_700_spb_title">
	шаблон: ClinicList_700, город: Санкт-Петербург
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="ClinicList_700_spb"></div>

<h3 id="ClinicList_ClinicList_700_spb_params_title">
	шаблон: ClinicList_700, город: Санкт-Петербург, специальность: Стоматология, метро: Владимирская
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="ClinicList_700_spb_params"></div>

<h3 id="ClinicList_ClinicList_700_ekb_title">
	шаблон: ClinicList_700, город: Екатеринбург
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="ClinicList_700_ekb"></div>

<h3 id="ClinicList_ClinicList_700_ekb_params_title">
	шаблон: ClinicList_700, город: Екатеринбург, специальность: Венерология, район: Кировский
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="ClinicList_700_ekb_params"></div>

<h3 id="ClinicList_ClinicList_700_nsk_title">
	шаблон: ClinicList_700, город: Новосибирск
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="ClinicList_700_nsk"></div>

<h3 id="ClinicList_ClinicList_700_perm_title">
	шаблон: ClinicList_700, город: Пермь
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="ClinicList_700_perm"></div>

<h3 id="ClinicList_ClinicList_700_nn_title">
	шаблон: ClinicList_700, город: Нижний Новгород
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="ClinicList_700_nn"></div>

<h3 id="ClinicList_ClinicList_700_kazan_title">
	шаблон: ClinicList_700, город: Казань
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="ClinicList_700_kazan"></div>

<h3 id="ClinicList_ClinicList_700_samara_title">
	шаблон: ClinicList_700, город: Самара
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="ClinicList_700_samara"></div>

<h3 id="ClinicList_Knigamedika_title">
	шаблон: Knigamedika
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="ClinicList_Knigamedika"></div>


<h2 id="DoctorList_title">Список врачей <a href="#top">наверх</a></h2>

<h3 id="DoctorList_DoctorList_700_msk_title">
	шаблон: DoctorList_700, город: Москва
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="DoctorList_700_msk"></div>

<h3 id="DoctorList_DoctorList_700_msk_params_title">
	шаблон: DoctorList_700, город: Москва, специальность: Стоматология, метро: Киевская
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="DoctorList_700_msk_params"></div>

<h3 id="DoctorList_DoctorList_700_spb_title">
	шаблон: DoctorList_700, город: Санкт-Петербург
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="DoctorList_700_spb"></div>

<h3 id="DoctorList_DoctorList_700_spb_params_title">
	шаблон: DoctorList_700, город: Санкт-Петербург, специальность: Стоматология, метро: Владимирская
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="DoctorList_700_spb_params"></div>

<h3 id="DoctorList_DoctorList_700_ekb_title">
	шаблон: DoctorList_700, город: Екатеринбург
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="DoctorList_700_ekb"></div>

<h3 id="DoctorList_DoctorList_700_ekb_params_title">
	шаблон: DoctorList_700, город: Екатеринбург, специальность: Венерология, район: Кировский
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="DoctorList_700_ekb_params"></div>

<h3 id="DoctorList_DoctorList_700_nsk_title">
	шаблон: DoctorList_700, город: Новосибирск
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="DoctorList_700_nsk"></div>

<h3 id="DoctorList_DoctorList_700_perm_title">
	шаблон: DoctorList_700, город: Пермь
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="DoctorList_700_perm"></div>

<h3 id="DoctorList_DoctorList_700_nn_title">
	шаблон: DoctorList_700, город: Нижний Новгород
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="DoctorList_700_nn"></div>

<h3 id="DoctorList_DoctorList_700_kazan_title">
	шаблон: DoctorList_700, город: Казань
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="DoctorList_700_kazan"></div>

<h3 id="DoctorList_DoctorList_700_samara_title">
	шаблон: DoctorList_700, город: Самара
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="DoctorList_700_samara"></div>

<h3 id="DoctorList_DoctorList_240_title">
	шаблон: DoctorList_240
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="DoctorList_240"></div>

<h3 id="DoctorList_DoctorList_240_mamaru_title">
	шаблон: DoctorList_240, тема: mamaru
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="DoctorList_240_mamaru"></div>


<h2 id="Request_title">Заявки <a href="#top">наверх</a></h2>

<h3 id="Request_728x90_title">
	шаблон: Request_728x90
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="Request_728x90"></div>

<h3 id="Request_medinfa_title">
	шаблон: Request, тема: medinfa
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="Request_medinfa"></div>

<h3 id="Request_medvopros_title">
	шаблон: Request, тема: medvopros
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="Request_medvopros"></div>

<h3 id="Request_sidemedical_title">
	шаблон: Request, тема: sidemedical
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="Request_sidemedical"></div>

<h3 id="Request_theme1_title">
	шаблон: Request, тема: theme1
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="Request_theme1"></div>

<h3 id="Request_theme2_title">
	шаблон: Request, тема: theme2
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="Request_theme2"></div>

<h3 id="Request_theme3_title">
	шаблон: Request, тема: theme3
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="Request_theme3"></div>

<h3 id="Request_theme4_title">
	шаблон: Request, тема: theme4
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="Request_theme4"></div>

<h3 id="Request_vlanamed_510_title">
	шаблон: Request, тема: vlanamed_510
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="Request_vlanamed_510"></div>


<h2 id="Search_title">Поиск <a href="#top">наверх</a></h2>

<h3 id="Search_medinfa_title">
	шаблон: Search, тема: medinfa
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="Search_medinfa"></div>

<h3 id="Search_medinfa_clinic_title">
	шаблон: Search, тема: medinfa, тип поиска: clinic
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="Search_medinfa_clinic"></div>

<h3 id="Search_medvopros_title">
	шаблон: Search, тема: medvopros
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="Search_medvopros"></div>

<h3 id="Search_sidemedical_title">
	шаблон: Search, тема: sidemedical
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="Search_sidemedical"></div>

<h3 id="Search_theme1_title">
	шаблон: Search, тема: theme1
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="Search_theme1"></div>

<h3 id="Search_theme2_title">
	шаблон: Search, тема: theme2
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="Search_theme2"></div>

<h3 id="Search_theme3_title">
	шаблон: Search, тема: theme3
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="Search_theme3"></div>

<h3 id="Search_theme4_title">
	шаблон: Search, тема: theme4
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="Search_theme4"></div>

<h3 id="Search_vlanamed_510_title">
	шаблон: Search, тема: vlanamed_510
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="Search_vlanamed_510"></div>

<h3 id="SearchClinic_240x400_title">
	шаблон: SearchClinic_240x400
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="SearchClinic_240x400"></div>

<h3 id="SearchDoctor_240x400_title">
	шаблон: SearchDoctor_240x400
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="SearchDoctor_240x400"></div>

<h3 id="SearchDoctor_vertical_mosglavmed_180x180_title">
	шаблон: SearchDoctor_vertical, тема: mosglavmed_180x180
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="SearchDoctor_vertical_mosglavmed_180x180"></div>

<h3 id="SearchDoctor_vertical_psihomed_225x225_title">
	шаблон: SearchDoctor_vertical, тема: psihomed_225x225
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="SearchDoctor_vertical_psihomed_225x225"></div>

<h3 id="SearchDoctor_vertical_vlanamed_200x400_title">
	шаблон: SearchDoctor_vertical, тема: vlanamed_200x400
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="SearchDoctor_vertical_vlanamed_200x400"></div>

<h3 id="SearchDoctor_vertical_vlanamed_225x400_title">
	шаблон: SearchDoctor_vertical, тема: vlanamed_225x400
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="SearchDoctor_vertical_vlanamed_225x400"></div>

<h3 id="SearchClinic_vertical_vlanamed_200x400_title">
	шаблон: SearchClinic_vertical, тема: vlanamed_200x400
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="SearchClinic_vertical_vlanamed_200x400"></div>

<h3 id="SearchClinic_vertical_vlanamed_225x400_title">
	шаблон: SearchClinic_vertical, тема: vlanamed_225x400
	<a href="#top">наверх</a>
</h3>

<div class="widget-container" id="SearchClinic_vertical_vlanamed_225x400"></div>


<script type="text/javascript">
	(function (w, d) {
		var proto = d.location.protocol === "https:" ? "https:" : "http:";
		w.DdFeedAsyncInit = function () {
			DdWidget({
				pid: '1',
				id: "ClinicList_knigamedika",
				container: 'ClinicList_knigamedika',
				action: 'LoadWidget',
				widget: 'ClinicList',
				template: 'ClinicList',
				theme: 'ClinicList/knigamedika'
			});
			DdWidget({
				pid: '1',
				id: "ClinicList_sidemedical",
				container: 'ClinicList_sidemedical',
				action: 'LoadWidget',
				widget: 'ClinicList',
				template: 'ClinicList',
				theme: 'ClinicList/sidemedical'
			});
			DdWidget({
				pid: '1',
				id: "ClinicList_medinfa",
				container: 'ClinicList_medinfa',
				action: 'LoadWidget',
				widget: 'ClinicList',
				template: 'ClinicList',
				theme: 'ClinicList/medinfa'
			});
			DdWidget({
				pid: '1',
				id: "ClinicList_uzilab",
				container: 'ClinicList_uzilab',
				action: 'LoadWidget',
				widget: 'ClinicList',
				template: 'ClinicList',
				theme: 'ClinicList/uzilab'
			});
			DdWidget({
				pid: '1',
				id: "ClinicList_uzilab2",
				container: 'ClinicList_uzilab2',
				action: 'LoadWidget',
				widget: 'ClinicList',
				template: 'ClinicList',
				theme: 'ClinicList/uzilab2'
			});
			DdWidget({
				pid: '1',
				id: "ClinicList_700_msk",
				container: 'ClinicList_700_msk',
				action: 'LoadWidget',
				widget: 'ClinicList',
				template: 'ClinicList_700'
			});
			DdWidget({
				pid: '1',
				id: "ClinicList_700_msk_params",
				container: 'ClinicList_700_msk_params',
				action: 'LoadWidget',
				widget: 'ClinicList',
				template: 'ClinicList_700',
				spec: 'stomatologiya',
				station: 'kievskaya'
			});
			DdWidget({
				pid: '1',
				id: "ClinicList_700_spb",
				container: 'ClinicList_700_spb',
				action: 'LoadWidget',
				widget: 'ClinicList',
				template: 'ClinicList_700',
				city: 'spb'
			});
			DdWidget({
				pid: '1',
				id: "ClinicList_700_spb_params",
				container: 'ClinicList_700_spb_params',
				action: 'LoadWidget',
				widget: 'ClinicList',
				template: 'ClinicList_700',
				city: 'spb',
				spec: 'stomatologiya',
				station: 'vladimirskaya'
			});
			DdWidget({
				pid: '1',
				id: "ClinicList_700_ekb",
				container: 'ClinicList_700_ekb',
				action: 'LoadWidget',
				widget: 'ClinicList',
				template: 'ClinicList_700',
				city: 'ekb'
			});
			DdWidget({
				pid: '1',
				id: "ClinicList_700_ekb_params",
				container: 'ClinicList_700_ekb_params',
				action: 'LoadWidget',
				widget: 'ClinicList',
				template: 'ClinicList_700',
				city: 'ekb',
				spec: 'venerologiya',
				district: 'kirovskij'
			});
			DdWidget({
				pid: '1',
				id: "ClinicList_700_nsk",
				container: 'ClinicList_700_nsk',
				action: 'LoadWidget',
				widget: 'ClinicList',
				template: 'ClinicList_700',
				city: 'nsk'
			});
			DdWidget({
				pid: '1',
				id: "ClinicList_700_perm",
				container: 'ClinicList_700_perm',
				action: 'LoadWidget',
				widget: 'ClinicList',
				template: 'ClinicList_700',
				city: 'perm'
			});
			DdWidget({
				pid: '1',
				id: "ClinicList_700_nn",
				container: 'ClinicList_700_nn',
				action: 'LoadWidget',
				widget: 'ClinicList',
				template: 'ClinicList_700',
				city: 'nn'
			});
			DdWidget({
				pid: '1',
				id: "ClinicList_700_kazan",
				container: 'ClinicList_700_kazan',
				action: 'LoadWidget',
				widget: 'ClinicList',
				template: 'ClinicList_700',
				city: 'kazan'
			});
			DdWidget({
				pid: '1',
				id: "ClinicList_700_samara",
				container: 'ClinicList_700_samara',
				action: 'LoadWidget',
				widget: 'ClinicList',
				template: 'ClinicList_700',
				city: 'samara'
			});
			DdWidget({
				pid: '1',
				id: "ClinicList_Knigamedika",
				container: 'ClinicList_Knigamedika',
				action: 'LoadWidget',
				widget: 'ClinicList',
				template: 'Knigamedika'
			});
			DdWidget({
				pid: '1',
				id: "DoctorList_700_msk",
				container: 'DoctorList_700_msk',
				action: 'LoadWidget',
				widget: 'DoctorList',
				template: 'DoctorList_700'
			});
			DdWidget({
				pid: '1',
				id: "DoctorList_700_msk_params",
				container: 'DoctorList_700_msk_params',
				action: 'LoadWidget',
				widget: 'DoctorList',
				template: 'DoctorList_700',
				sector: 'stomatolog',
				station: 'kievskaya'
			});
			DdWidget({
				pid: '1',
				id: "DoctorList_700_spb",
				container: 'DoctorList_700_spb',
				action: 'LoadWidget',
				widget: 'DoctorList',
				template: 'DoctorList_700',
				city: 'spb'
			});
			DdWidget({
				pid: '1',
				id: "DoctorList_700_spb_params",
				container: 'DoctorList_700_spb_params',
				action: 'LoadWidget',
				widget: 'DoctorList',
				template: 'DoctorList_700',
				city: 'spb',
				sector: 'stomatolog',
				station: 'vladimirskaya'
			});
			DdWidget({
				pid: '1',
				id: "DoctorList_700_ekb",
				container: 'DoctorList_700_ekb',
				action: 'LoadWidget',
				widget: 'DoctorList',
				template: 'DoctorList_700',
				city: 'ekb'
			});
			DdWidget({
				pid: '1',
				id: "DoctorList_700_ekb_params",
				container: 'DoctorList_700_ekb_params',
				action: 'LoadWidget',
				widget: 'DoctorList',
				template: 'DoctorList_700',
				city: 'ekb',
				sector: 'venerolog',
				district: 'kirovskij'
			});
			DdWidget({
				pid: '1',
				id: "DoctorList_700_nsk",
				container: 'DoctorList_700_nsk',
				action: 'LoadWidget',
				widget: 'DoctorList',
				template: 'DoctorList_700',
				city: 'nsk'
			});
			DdWidget({
				pid: '1',
				id: "DoctorList_700_perm",
				container: 'DoctorList_700_perm',
				action: 'LoadWidget',
				widget: 'DoctorList',
				template: 'DoctorList_700',
				city: 'perm'
			});
			DdWidget({
				pid: '1',
				id: "DoctorList_700_nn",
				container: 'DoctorList_700_nn',
				action: 'LoadWidget',
				widget: 'DoctorList',
				template: 'DoctorList_700',
				city: 'nn'
			});
			DdWidget({
				pid: '1',
				id: "DoctorList_700_kazan",
				container: 'DoctorList_700_kazan',
				action: 'LoadWidget',
				widget: 'DoctorList',
				template: 'DoctorList_700',
				city: 'kazan'
			});
			DdWidget({
				pid: '1',
				id: "DoctorList_700_samara",
				container: 'DoctorList_700_samara',
				action: 'LoadWidget',
				widget: 'DoctorList',
				template: 'DoctorList_700',
				city: 'samara'
			});
			DdWidget({
				pid: '1',
				id: "DoctorList_240",
				container: 'DoctorList_240',
				action: 'LoadWidget',
				widget: 'DoctorList',
				template: 'DoctorList_240'
			});
			DdWidget({
				pid: '1',
				id: "DoctorList_240_mamaru",
				container: 'DoctorList_240_mamaru',
				action: 'LoadWidget',
				widget: 'DoctorList',
				template: 'DoctorList_240',
				theme: 'DoctorList/mamaru'
			});

			DdWidget({
				pid: '1',
				id: "Request_728x90",
				container: 'Request_728x90',
				action: 'LoadWidget',
				widget: 'Request',
				template: 'Request_728x90'
			});
			DdWidget({
				pid: '1',
				id: "Request_medinfa",
				container: 'Request_medinfa',
				action: 'LoadWidget',
				widget: 'Request',
				template: 'Request',
				theme: 'rs/medinfa'
			});
			DdWidget({
				pid: '1',
				id: "Request_medvopros",
				container: 'Request_medvopros',
				action: 'LoadWidget',
				widget: 'Request',
				template: 'Request',
				theme: 'rs/medvopros'
			});
			DdWidget({
				pid: '1',
				id: "Request_sidemedical",
				container: 'Request_sidemedical',
				action: 'LoadWidget',
				widget: 'Request',
				template: 'Request',
				theme: 'rs/sidemedical'
			});
			DdWidget({
				pid: '1',
				id: "Request_theme1",
				container: 'Request_theme1',
				action: 'LoadWidget',
				widget: 'Request',
				template: 'Request',
				theme: 'rs/theme1'
			});
			DdWidget({
				pid: '1',
				id: "Request_theme2",
				container: 'Request_theme2',
				action: 'LoadWidget',
				widget: 'Request',
				template: 'Request',
				theme: 'rs/theme2'
			});
			DdWidget({
				pid: '1',
				id: "Request_theme3",
				container: 'Request_theme3',
				action: 'LoadWidget',
				widget: 'Request',
				template: 'Request',
				theme: 'rs/theme3'
			});
			DdWidget({
				pid: '1',
				id: "Request_theme4",
				container: 'Request_theme4',
				action: 'LoadWidget',
				widget: 'Request',
				template: 'Request',
				theme: 'rs/theme4'
			});
			DdWidget({
				pid: '1',
				id: "Request_vlanamed_510",
				container: 'Request_vlanamed_510',
				action: 'LoadWidget',
				widget: 'Request',
				template: 'Request',
				theme: 'rs/vlanamed_510'
			});
			DdWidget({
				pid: '1',
				id: "Search_medinfa",
				container: 'Search_medinfa',
				action: 'LoadWidget',
				widget: 'Search',
				template: 'Search',
				theme: 'rs/medinfa'
			});
			DdWidget({
				pid: '1',
				id: "Search_medinfa_clinic",
				container: 'Search_medinfa_clinic',
				action: 'LoadWidget',
				widget: 'Search',
				template: 'Search',
				theme: 'rs/medinfa',
				searchType: 'clinic'
			});
			DdWidget({
				pid: '1',
				id: "Search_medvopros",
				container: 'Search_medvopros',
				action: 'LoadWidget',
				widget: 'Search',
				template: 'Search',
				theme: 'rs/medvopros'
			});
			DdWidget({
				pid: '1',
				id: "Search_sidemedical",
				container: 'Search_sidemedical',
				action: 'LoadWidget',
				widget: 'Search',
				template: 'Search',
				theme: 'rs/sidemedical'
			});
			DdWidget({
				pid: '1',
				id: "Search_theme1",
				container: 'Search_theme1',
				action: 'LoadWidget',
				widget: 'Search',
				template: 'Search',
				theme: 'rs/theme1'
			});
			DdWidget({
				pid: '1',
				id: "Search_theme2",
				container: 'Search_theme2',
				action: 'LoadWidget',
				widget: 'Search',
				template: 'Search',
				theme: 'rs/theme2'
			});
			DdWidget({
				pid: '1',
				id: "Search_theme3",
				container: 'Search_theme3',
				action: 'LoadWidget',
				widget: 'Search',
				template: 'Search',
				theme: 'rs/theme3'
			});
			DdWidget({
				pid: '1',
				id: "Search_theme4",
				container: 'Search_theme4',
				action: 'LoadWidget',
				widget: 'Search',
				template: 'Search',
				theme: 'rs/theme4'
			});
			DdWidget({
				pid: '1',
				id: "Search_vlanamed_510",
				container: 'Search_vlanamed_510',
				action: 'LoadWidget',
				widget: 'Search',
				template: 'Search',
				theme: 'rs/vlanamed_510'
			});
			DdWidget({
				pid: '1',
				id: "SearchClinic_240x400",
				container: 'SearchClinic_240x400',
				action: 'LoadWidget',
				widget: 'Search',
				template: 'SearchClinic_240x400'
			});
			DdWidget({
				pid: '1',
				id: "SearchDoctor_240x400",
				container: 'SearchDoctor_240x400',
				action: 'LoadWidget',
				widget: 'Search',
				template: 'SearchDoctor_240x400'
			});
			DdWidget({
				pid: '1',
				id: "SearchDoctor_vertical_mosglavmed_180x180",
				container: 'SearchDoctor_vertical_mosglavmed_180x180',
				action: 'LoadWidget',
				widget: 'Search',
				template: 'SearchDoctor_vertical',
				theme: 'Search/mosglavmed_180x180'
			});
			DdWidget({
				pid: '1',
				id: "SearchDoctor_vertical_psihomed_225x225",
				container: 'SearchDoctor_vertical_psihomed_225x225',
				action: 'LoadWidget',
				widget: 'Search',
				template: 'SearchDoctor_vertical',
				theme: 'Search/psihomed_225x225'
			});
			DdWidget({
				pid: '1',
				id: "SearchDoctor_vertical_vlanamed_200x400",
				container: 'SearchDoctor_vertical_vlanamed_200x400',
				action: 'LoadWidget',
				widget: 'Search',
				template: 'SearchDoctor_vertical',
				theme: 'Search/vlanamed_200x400'
			});
			DdWidget({
				pid: '1',
				id: "SearchDoctor_vertical_vlanamed_225x400",
				container: 'SearchDoctor_vertical_vlanamed_225x400',
				action: 'LoadWidget',
				widget: 'Search',
				template: 'SearchDoctor_vertical',
				theme: 'Search/vlanamed_225x400'
			});
			DdWidget({
				pid: '1',
				id: "SearchClinic_vertical_vlanamed_200x400",
				container: 'SearchClinic_vertical_vlanamed_200x400',
				action: 'LoadWidget',
				widget: 'Search',
				template: 'SearchClinic_vertical',
				theme: 'Search/vlanamed_200x400'
			});
			DdWidget({
				pid: '1',
				id: "SearchClinic_vertical_vlanamed_225x400",
				container: 'SearchClinic_vertical_vlanamed_225x400',
				action: 'LoadWidget',
				widget: 'Search',
				template: 'SearchClinic_vertical',
				theme: 'Search/vlanamed_225x400'
			});
		};
		(function (d) {
			var js, id = 'dd-feed', ref = d.getElementsByTagName('script')[0];
			if (d.getElementById(id)) {
				return;
			}
			js = d.createElement('script');
			js.id = id;
			js.async = true;
			js.src = proto + '//<?php echo $host; ?>/widget/js';
			ref.parentNode.insertBefore(js, ref);
		}(d));
	}(window, window.document));
</script>

</body>