<?xml version='1.0'  encoding="UTF-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

    
    <xsl:decimal-format decimal-separator = '.' grouping-separator = ' ' NaN = ' '/>

    <xsl:output method="html" encoding="utf-8"/>

    
   <xsl:template match="/">
		<xsl:apply-templates select="root"/>
   </xsl:template>
   
   
   
   
	<xsl:template match="root">
		<xsl:choose>
			<xsl:when test="dbHeadInfo/City/@id = 2">
				<xsl:call-template name="spbMap"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:call-template name="mskMap"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	
	
	<xsl:template name="mskMap">
		<script type="text/javascript" src="/js/metro.js"></script>

		<div class="change-metro">
			<div id="metro_lines">
				<div class="head">
					Выберите удобные станции метро<br/> или ветки целиком
				</div>
				<i></i><a href="" id="line_kalinin">Калининская</a>
				<i style="background-position: 0 -9px"></i><a href="" id="line_lyblino">Люблинская</a>
				<i style="background-position: 0 -18px"></i><a href="" id="line_sokolniki">Сокольническая</a>
				<i style="background-position: 0 -27px"></i><a href="" id="line_kahov">Каховская</a>
				<i style="background-position: 0 -36px"></i><a href="" id="line_serpyx">Серпуховско-тимирязевская</a>
				<i style="background-position: 0 -45px"></i><a href="" id="line_taganka">Таганско-краснопресненская</a>
				<i style="background-position: 0 -54px"></i><a href="" id="line_kaluga">Калужско-рижская</a>
				<i style="background-position: 0 -63px"></i><a href="" id="line_ring">Кольцевая</a>
				<i style="background-position: 0 -72px"></i><a href="" id="line_arbat">Арбатско-покровская</a>
				<i style="background-position: 0 -81px"></i><a href="" id="line_zm">Замоскворецкая</a>
				<i style="background-position: 0 -90px"></i><a href="" id="line_fili">Филевская</a>
				<i style="background-position: 0 -99px"></i><a href="" id="line_butovo">Бутовская линия легкого метро</a>
				<div id="select-stations">
					<strong>Выбранные станции</strong> (<span id="stations-select-count"></span>)
					<div>
					</div>
				</div>
				<a href="#" id="metro-clear-act">очистить все выбранные</a>
				<div id="map-submit" class="button but-metro">
					<span>Применить</span>
				</div>
			</div>
			<div id="map_moscow" class="metro-map">
				<div style="background:url(/i/map_moscow.gif) no-repeat; width:605px; height:714px;"></div>
				<div id="map_stations">
					<!-- Замоскворецкая линия -->
		            <div style="left: 120px; top: 9px; width: 65px;" title="Речной вокзал" class="line_zm title"></div>
					<div style="left: 184px; top: 9px;" title="Речной вокзал" class="line_zm" data-idline="124"></div>
		            <div style="left: 113px; top: 19px; width: 72px;" title="Водный стадион" class="line_zm title"></div>
					<div style="left: 184px; top: 19px;" title="Водный стадион" class="line_zm" data-idline="30"></div>
					<div style="left: 133px; top: 29px; width: 52px;" title="Войковская" class="line_zm title"></div>
					<div style="left: 184px; top: 29px;" title="Войковская" class="line_zm" data-idline="31"></div>
					<div style="left: 158px; top: 39px; width: 26px;" title="Сокол" class="line_zm title"></div>
					<div style="left: 184px; top: 39px;" title="Сокол" class="line_zm" data-idline="136"></div>
					<div style="left: 144px; top: 49px; width: 40px;" title="Аэропорт" class="line_zm title"></div>
					<div style="left: 184px; top: 49px;" title="Аэропорт" class="line_zm" data-idline="10"></div>
					<div style="left: 150px; top: 59px; width: 34px;" title="Динамо" class="line_zm title"></div>
					<div style="left: 184px; top: 59px;" title="Динамо" class="line_zm" data-idline="38"></div>
					<div style="left: 160px; top: 188px; width: 58px;" title="Белорусская" class="line_zm title"></div>
					<div style="left: 218px; top: 188px;" title="Белорусская" class="line_zm" data-idline="16"></div>
					<div style="left: 234px; top: 214px; width: 52px;" title="Маяковская" class="line_zm title"></div>
					<div style="left: 222px; top: 214px;" title="Маяковская" class="line_zm" data-idline="80"></div>
					<div style="left: 235px; top: 240px; width: 38px;" title="Тверская" class="line_zm title"></div>
					<div style="left: 222px; top: 242px;" title="Тверская" class="line_zm" data-idline="146"></div>
					<div style="left: 303px; top: 313px; width: 54px;" title="Театральная" class="line_zm title"></div>
					<div style="left: 291px; top: 314px;" title="Театральная" class="line_zm" data-idline="147"></div>
					<div style="left: 339px; top: 343px; width: 67px;" title="Новокузнецкая" class="line_zm title"></div>
					<div style="left: 326px; top: 348px;" title="Новокузнецкая" class="line_zm" data-idline="91"></div>
					<div style="left: 377px; top: 400px;" title="Павелецкая" class="line_zm" data-idline="101"></div>
					<div style="left: 394px; top: 440px; width: 67px;" title="Автозаводская" class="line_zm title"></div>
					<div style="left: 383px; top: 440px;" title="Автозаводская" class="line_zm" data-idline="2"></div>
					<div style="left: 394px; top: 450px; width: 57px;" title="Коломенская" class="line_zm title"></div>
					<div style="left: 383px; top: 450px;" title="Коломенская" class="line_zm" data-idline="56"></div>
					<div style="left: 383px; top: 462px;" title="Каширская" class="line_zm" data-idline="48"></div>
					<div style="left: 312px; top: 574px; width: 71px;" title="Кантемировская" class="line_zm title"></div>
					<div style="left: 383px; top: 574px;" title="Кантемировская" class="line_zm" data-idline="46"></div>
					<div style="left: 338px; top: 584px; width: 45px;" title="Царицыно" class="line_zm title"></div>
					<div style="left: 383px; top: 584px;" title="Царицыно" class="line_zm" data-idline="167"></div>
					<div style="left: 346px; top: 594px; width: 37px;" title="Орехово" class="line_zm title"></div>
					<div style="left: 383px; top: 594px;" title="Орехово" class="line_zm" data-idline="98"></div>
					<div style="left: 316px; top: 604px; width: 67px;" title="Домодедовская" class="line_zm title"></div>
					<div style="left: 383px; top: 604px;" title="Домодедовская" class="line_zm" data-idline="41"></div>
					<div style="left: 296px; top: 620px; width: 86px;" title="Красногвардейская" class="line_zm title"></div>
					<div style="left: 383px; top: 620px;" title="Красногвардейская" class="line_zm" data-idline="60"></div>
		            <div style="left: 393px; top: 638px; width: 68px;" title="Алма-Атинская" class="line_zm title"></div>
		            <div style="left: 383px; top: 638px;" title="Алма-Атинская" class="line_zm" data-idline="252"></div>
					<!-- Замоскворецкая линия -->
		
					<!-- Серпуховская линия -->
					<div style="left: 320px; top: 9px; width: 46px;" title="Алтуфьево" class="line_serpyx title"></div>
					<div style="left: 309px; top: 9px;" title="Алтуфьево" class="line_serpyx" data-idline="6"></div>
					<div style="left: 320px; top: 19px; width: 41px;" title="Бибирево" class="line_serpyx title"></div>
					<div style="left: 309px; top: 19px;" title="Бибирево" class="line_serpyx" data-idline="19"></div>
					<div style="left: 320px; top: 29px; width: 41px;" title="Отрадное" class="line_serpyx title"></div>
					<div style="left: 309px; top: 29px;" title="Отрадное" class="line_serpyx" data-idline="99"></div>
					<div style="left: 320px; top: 39px; width: 49px;" title="Владыкино" class="line_serpyx title"></div>
					<div style="left: 309px; top: 39px;" title="Владыкино" class="line_serpyx" data-idline="29"></div>
					<div style="left: 294px; top: 79px; width: 58px; height: 16px;" title="Петровско-Разумовская" class="line_serpyx title"></div>
					<div style="left: 283px; top: 79px;" title="Петровско-Разумовская" class="line_serpyx" data-idline="108"></div>
					<div style="left: 192px; top: 118px; width: 66px;" title="Тимирязевская" class="line_serpyx title"></div>
					<div style="left: 258px; top: 118px;" title="Тимирязевская" class="line_serpyx" data-idline="150"></div>
					<div style="left: 199px; top: 128px; width: 60px;" title="Дмитровская" class="line_serpyx title"></div>
					<div style="left: 258px; top: 128px;" title="Дмитровская" class="line_serpyx" data-idline="39"></div>
					<div style="left: 202px; top: 138px; width: 56px;" title="Савеловская" class="line_serpyx title"></div>
					<div style="left: 258px; top: 138px;" title="Савеловская" class="line_serpyx" data-idline="128"></div>
					<div style="left: 190px; top: 161px; width: 66px;" title="Менделеевская" class="line_serpyx title"></div>
					<div style="left: 258px; top: 161px;" title="Менделеевская" class="line_serpyx" data-idline="83"></div>
					<div style="left: 240px; top: 192px; width: 37px; height: 16px;" title="Цветной бульвар" class="line_serpyx title"></div>
					<div style="left: 278px; top: 192px;" title="Цветной бульвар" class="line_serpyx" data-idline="168"></div>
					<div style="left: 240px; top: 252px; width: 50px;" title="Чеховская" class="line_serpyx title"></div>
					<div style="left: 228px; top: 252px;" title="Чеховская" class="line_serpyx" data-idline="171"></div>
					<div style="left: 246px; top: 368px; width: 55px;" title="Боровицкая" class="line_serpyx title"></div>
					<div style="left: 234px; top: 368px;" title="Боровицкая" class="line_serpyx" data-idline="21"></div>
					<div style="left: 294px; top: 418px; width: 36px;" title="Полянка" class="line_serpyx title"></div>
					<div style="left: 283px; top: 418px;" title="Полянка" class="line_serpyx" data-idline="115"></div>
					<div style="left: 296px; top: 448px; width: 62px;" title="Серпуховская" class="line_serpyx title"></div>
					<div style="left: 283px; top: 448px;" title="Серпуховская" class="line_serpyx" data-idline="132"></div>
					<div style="left: 243px; top: 497px; width: 40px;" title="Тульская" class="line_serpyx title"></div>
					<div style="left: 283px; top: 497px;" title="Тульская" class="line_serpyx" data-idline="154"></div>
					<div style="left: 228px; top: 507px; width: 56px;" title="Нагатинская" class="line_serpyx title"></div>
					<div style="left: 283px; top: 507px;" title="Нагатинская" class="line_serpyx" data-idline="87"></div>
					<div style="left: 242px; top: 517px; width: 42px;" title="Нагорная" class="line_serpyx title"></div>
					<div style="left: 283px; top: 517px;" title="Нагорная" class="line_serpyx" data-idline="88"></div>
					<div style="left: 206px; top: 527px; width: 77px;" title="Нахимовский проспект" class="line_serpyx title"></div>
					<div style="left: 283px; top: 527px;" title="Нахимовский проспект" class="line_serpyx" data-idline="89"></div>
					<div style="left: 208px; top: 543px; width: 73px;" title="Севастопольская" class="line_serpyx title"></div>
					<div style="left: 283px; top: 543px;" title="Севастопольская" class="line_serpyx" data-idline="130"></div>
					<div style="left: 224px; top: 574px; width: 59px;" title="Чертановская" class="line_serpyx title"></div>
					<div style="left: 283px; top: 574px;" title="Чертановская" class="line_serpyx" data-idline="170"></div>
					<div style="left: 253px; top: 584px; width: 30px;" title="Южная" class="line_serpyx title"></div>
					<div style="left: 283px; top: 584px;" title="Южная" class="line_serpyx" data-idline="180"></div>
					<div style="left: 240px; top: 594px; width: 43px;" title="Пражская" class="line_serpyx title"></div>
					<div style="left: 283px; top: 594px;" title="Пражская" class="line_serpyx" data-idline="116"></div>
					<div style="left: 228px; top: 604px; width: 56px;" title="Ул. Ак.Янгеля" class="line_serpyx title"></div>
					<div style="left: 283px; top: 604px;" title="Ул. Ак.Янгеля" class="line_serpyx" data-idline="162"></div>
					<div style="left: 250px; top: 614px; width: 33px;" title="Аннино" class="line_serpyx title" ></div>
					<div style="left: 283px; top: 614px;" title="Аннино" class="line_serpyx" data-idline="7"></div>
					<div style="left: 295px; top: 640px; width: 55px; height: 16px;" title="Бульвар Дм.Донского" class="line_serpyx title"></div>
					<div style="left: 283px; top: 646px;" title="Бульвар Дм.Донского" class="line_serpyx" data-idline="24"></div>
					<!-- Серпуховская линия -->
		
					<!-- Калужско-рижская линия -->
					<div style="left: 394px; top: 9px; width: 52px;" title="Медведково" class="line_kaluga title"></div>
					<div style="left: 383px; top: 9px;" title="Медведково" class="line_kaluga" data-idline="81"></div>
					<div style="left: 394px; top: 19px; width: 61px;" title="Бабушкинская" class="line_kaluga title"></div>
					<div style="left: 383px; top: 19px;" title="Бабушкинская" class="line_kaluga" data-idline="11"></div>
					<div style="left: 394px; top: 29px; width: 42px;" title="Свиблово" class="line_kaluga title"></div>
					<div style="left: 383px; top: 29px;" title="Свиблово" class="line_kaluga" data-idline="129"></div>
					<div style="left: 394px; top: 39px; width: 76px;" title="Ботанический сад" class="line_kaluga title"></div>
					<div style="left: 383px; top: 39px;" title="Ботанический сад" class="line_kaluga" data-idline="22"></div>
					<div style="left: 394px; top: 49px; width: 22px;" title="ВДНХ" class="line_kaluga title"></div>
					<div style="left: 383px; top: 49px;" title="ВДНХ" class="line_kaluga" data-idline="27"></div>
					<div style="left: 394px; top: 59px; width: 60px;" title="Алексеевская" class="line_kaluga title"></div>
					<div style="left: 383px; top: 59px;" title="Алексеевская" class="line_kaluga" data-idline="5"></div>
					<div style="left: 394px; top: 70px; width: 38px;" title="Рижская" class="line_kaluga title"></div>
					<div style="left: 383px; top: 70px;" title="Рижская" class="line_kaluga" data-idline="125"></div>
					<div style="left: 358px; top: 183px; width: 63px;" title="Проспект мира" class="line_kaluga title"></div>
					<div style="left: 346px; top: 183px;" title="Проспект мира" class="line_kaluga" data-idline="121"></div>
					<div style="left: 290px; top: 214px; width: 56px;" title="Сухаревская" class="line_kaluga title"></div>
					<div style="left: 346px; top: 214px;" title="Сухаревская" class="line_kaluga" data-idline="142"></div>
					<div style="left: 359px; top: 256px; width: 58px;" title="Тургеневская" class="line_kaluga title"></div>
					<div style="left: 346px; top: 252px;" title="Тургеневская" class="line_kaluga" data-idline="155"></div>
					<div style="left: 347px; top: 298px;" title="Китай-город" class="line_kaluga" data-idline="54"></div>
					<div style="left: 315px; top: 348px;" title="Третьяковская" class="line_kaluga" data-idline="151"></div>
					<div style="left: 234px; top: 428px;" title="Октябрьская" class="line_kaluga" data-idline="96"></div>
					<div style="left: 126px; top: 514px; width: 58px;" title="Шаболовская" class="line_kaluga title"></div>
					<div style="left: 184px; top: 514px;" title="Шаболовская" class="line_kaluga" data-idline="174"></div>
					<div style="left: 122px; top: 524px; width: 62px;" title="Ленинский проспект" class="line_kaluga title"></div>
					<div style="left: 184px; top: 524px;" title="Ленинский проспект" class="line_kaluga" data-idline="74"></div>
					<div style="left: 118px; top: 534px; width: 66px;" title="Академическая" class="line_kaluga title"></div>
					<div style="left: 184px; top: 534px;" title="Академическая" class="line_kaluga" data-idline="3"></div>
					<div style="left: 124px; top: 544px; width: 60px;" title="Профсоюзная" class="line_kaluga title"></div>
					<div style="left: 184px; top: 544px;" title="Профсоюзная" class="line_kaluga" data-idline="122"></div>
					<div style="left: 111px; top: 554px; width: 73px;" title="Новые черемушки" class="line_kaluga title"></div>
					<div style="left: 184px; top: 554px;" title="Новые черемушки" class="line_kaluga" data-idline="94"></div>
					<div style="left: 136px; top: 564px; width: 48px;" title="Калужская" class="line_kaluga title"></div>
					<div style="left: 184px; top: 564px;" title="Калужская" class="line_kaluga" data-idline="45"></div>
					<div style="left: 142px; top: 574px; width: 42px;" title="Беляево" class="line_kaluga title"></div>
					<div style="left: 184px; top: 574px;" title="Беляево" class="line_kaluga" data-idline="18"></div>
					<div style="left: 142px; top: 584px; width: 43px;" title="Коньково" class="line_kaluga title"></div>
					<div style="left: 184px; top: 584px;" title="Коньково" class="line_kaluga" data-idline="59"></div>
					<div style="left: 132px; top: 594px; width: 52px;" title="Теплый стан" class="line_kaluga title"></div>
					<div style="left: 184px; top: 594px;" title="Теплый стан" class="line_kaluga" data-idline="149"></div>
					<div style="left: 150px; top: 604px; width: 34px;" title="Ясенево" class="line_kaluga title"></div>
					<div style="left: 184px; top: 604px;" title="Ясенево" class="line_kaluga" data-idline="181"></div>
					<div style="left: 112px; top: 613px; width: 71px;" title="Новоясеневская" class="line_kaluga title"></div>
					<div style="left: 184px; top: 616px;" title="Новоясеневская" class="line_kaluga" data-idline="93"></div>
					<!-- Калужско-рижская линия -->
		
					<!-- Сокольническая линия -->
					<div style="left: 494px; top: 9px; width: 90px;" title="Улица Подбельского" class="line_sokolniki title"></div>
					<div style="left: 483px; top: 9px;" title="Улица Подбельского" class="line_sokolniki" data-idline="159"></div>
					<div style="left: 494px; top: 19px; width: 60px;" title="Черкизовская" class="line_sokolniki title"></div>
					<div style="left: 483px; top: 19px;" title="Черкизовская" class="line_sokolniki" data-idline="169"></div>
					<div style="left: 494px; top: 29px; width: 84px;" title="Преображенская пл." class="line_sokolniki title"></div>
					<div style="left: 483px; top: 29px;" title="Преображенская пл." class="line_sokolniki" data-idline="117"></div>
					<div style="left: 494px; top: 39px; width: 50px;" title="Сокольники" class="line_sokolniki title"></div>
					<div style="left: 483px; top: 39px;" title="Сокольники" class="line_sokolniki" data-idline="137"></div>
					<div style="left: 494px; top: 49px; width: 70px;" title="Красносельская" class="line_sokolniki title"></div>
					<div style="left: 483px; top: 49px;" title="Красносельская" class="line_sokolniki" data-idline="62"></div>
					<div style="left: 379px; top: 208px;" title="Комсомольская" class="line_sokolniki" data-idline="58"></div>
					<div style="left: 375px; top: 222px; width: 70px;" title="Красные ворота" class="line_sokolniki title"></div>
					<div style="left: 365px; top: 222px;" title="Красные ворота" class="line_sokolniki" data-idline="63"></div>
					<div style="left: 356px; top: 236px; width: 60px;" title="Чистые пруды" class="line_sokolniki title"></div>
					<div style="left: 346px; top: 240px;" title="Чистые пруды" class="line_sokolniki" data-idline="172"></div>
					<div style="left: 280px; top: 271px; width: 37px;" title="Лубянка" class="line_sokolniki title"></div>
					<div style="left: 318px; top: 269px;" title="Лубянка" class="line_sokolniki" data-idline="75"></div>
					<div style="left: 294px; top: 304px; width: 55px;" title="Охотный ряд" class="line_sokolniki title"></div>
					<div style="left: 282px; top: 305px;" title="Охотный ряд" class="line_sokolniki" data-idline="100"></div>
					<div style="left: 240px; top: 358px; width: 96px;" title="Библиотека им.Ленина" class="line_sokolniki title"></div>
					<div style="left: 228px; top: 358px;" title="Библиотека им.Ленина" class="line_sokolniki" data-idline="20"></div>
					<div style="left: 214px; top: 385px; width: 66px;" title="Кропоткинская" class="line_sokolniki title"></div>
					<div style="left: 202px; top: 385px;" title="Кропоткинская" class="line_sokolniki" data-idline="65"></div>
					<div style="left: 189px; top: 398px;" title="Парк культуры" class="line_sokolniki" data-idline="104"></div>
					<div style="left: 22px; top: 554px; width: 58px;" title="Фрунзенская" class="line_sokolniki title"></div>
					<div style="left: 80px; top: 554px;" title="Фрунзенская" class="line_sokolniki" data-idline="166"></div>
					<div style="left: 28px; top: 564px; width: 52px;" title="Спортивная" class="line_sokolniki title"></div>
					<div style="left: 80px; top: 564px;" title="Спортивная" class="line_sokolniki" data-idline="138"></div>
					<div style="left: 10px; top: 574px; width: 70px;" title="Воробьевы горы" class="line_sokolniki title"></div>
					<div style="left: 80px; top: 574px;" title="Воробьевы горы" class="line_sokolniki" data-idline="35"></div>
					<div style="left: 28px; top: 584px; width: 52px;" title="Университет" class="line_sokolniki title"></div>
					<div style="left: 80px; top: 584px;" title="Университет" class="line_sokolniki" data-idline="163"></div>
					<div style="left: 7px; top: 594px; width: 72px;" title="Пр-т Вернадского" class="line_sokolniki title"></div>
					<div style="left: 80px; top: 594px;" title="Пр-т Вернадского" class="line_sokolniki" data-idline="119"></div>
					<div style="left: 18px; top: 609px; width: 60px;" title="Юго-западная" class="line_sokolniki title"></div>
					<div style="left: 80px; top: 609px;" title="Юго-западная" class="line_sokolniki" data-idline="179"></div>
					<!-- Сокольническая линия -->
		
					<!-- Люблинская линия -->
					<div style="left: 321px; top: 138px; width: 60px;" title="Марьина роща" class="line_lyblino title"></div>
					<div style="left: 309px; top: 138px;" title="Марьина роща" class="line_lyblino" data-idline="78"></div>
					<div style="left: 321px; top: 160px; width: 58px;" title="Достоевская" class="line_lyblino title"></div>
					<div style="left: 309px; top: 160px;" title="Достоевская" class="line_lyblino" data-idline="42"></div>
					<div style="left: 303px; top: 192px; width: 35px;" title="Трубная" class="line_lyblino title"></div>
					<div style="left: 290px; top: 192px;" title="Трубная" class="line_lyblino" data-idline="153"></div>
					<div style="left: 370px; top: 246px; width: 82px;" title="Сретенский бульвар" class="line_lyblino title"></div>
					<div style="left: 356px; top: 246px;" title="Сретенский бульвар" class="line_lyblino" data-idline="139"></div>
					<div style="left: 430px; top: 278px; width: 51px;" title="Чкаловская" class="line_lyblino title"></div>
					<div style="left: 416px; top: 278px;" title="Чкаловская" class="line_lyblino" data-idline="173"></div>
					<div style="left: 462px; top: 323px; width: 39px;" title="Римская" class="line_lyblino title"></div>
					<div style="left: 449px; top: 323px;" title="Римская" class="line_lyblino" data-idline="126"></div>
					<div style="left: 462px; top: 388px; width: 97px;" title="Крестьянская застава" class="line_lyblino title"></div>
					<div style="left: 449px; top: 388px;" title="Крестьянская застава" class="line_lyblino" data-idline="64"></div>
					<div style="left: 412px; top: 524px; width: 42px;" title="Дубровка" class="line_lyblino title"></div>
					<div style="left: 400px; top: 524px;" title="Дубровка" class="line_lyblino" data-idline="43"></div>
					<div style="left: 412px; top: 534px; width: 58px;" title="Кожуховская" class="line_lyblino title"></div>
					<div style="left: 400px; top: 534px;" title="Кожуховская" class="line_lyblino" data-idline="55"></div>
					<div style="left: 412px; top: 544px; width: 44px;" title="Печатники" class="line_lyblino title"></div>
					<div style="left: 400px; top: 544px;" title="Печатники" class="line_lyblino" data-idline="109"></div>
					<div style="left: 412px; top: 554px; width: 42px;" title="Волжская" class="line_lyblino title"></div>
					<div style="left: 400px; top: 554px;" title="Волжская" class="line_lyblino" data-idline="33"></div>
					<div style="left: 412px; top: 564px; width: 38px;" title="Люблино" class="line_lyblino title"></div>
					<div style="left: 400px; top: 564px;" title="Люблино" class="line_lyblino" data-idline="76"></div>
					<div style="left: 412px; top: 574px; width: 64px;" title="Братиславская" class="line_lyblino title"></div>
					<div style="left: 400px; top: 574px;" title="Братиславская" class="line_lyblino" data-idline="23"></div>
					<div style="left: 412px; top: 584px; width: 38px;" title="Марьино" class="line_lyblino title"></div>
					<div style="left: 400px; top: 584px;" title="Марьино" class="line_lyblino" data-idline="79"></div>
					<div style="left: 412px; top: 594px; width: 42px;" title="Борисово" class="line_lyblino title"></div>
					<div style="left: 400px; top: 594px;" title="Борисово" class="line_lyblino" data-idline="182"></div>
					<div style="left: 412px; top: 604px; width: 58px;" title="Шипиловская" class="line_lyblino title"></div>
					<div style="left: 400px; top: 604px;" title="Шипиловская" class="line_lyblino" data-idline="183"></div>
					<div style="left: 408px; top: 620px; width: 47px;" title="Зябликово" class="line_lyblino title"></div>
					<div style="left: 395px; top: 620px;" title="Зябликово" class="line_lyblino" data-idline="184"></div>
					<!-- Люблинская линия -->
		
					<!-- Калининская линия -->
		            <div style="left: 514px; top: 218px; width: 52px;" title="Новокосино" class="line_kalinin title"></div>
		            <div style="left: 502px; top: 218px;" title="Новокосино" class="line_kalinin" data-idline="251"></div>
					<div style="left: 514px; top: 228px; width: 52px;" title="Новогиреево" class="line_kalinin title"></div>
					<div style="left: 502px; top: 228px;" title="Новогиреево" class="line_kalinin" data-idline="90"></div>
					<div style="left: 514px; top: 238px; width: 30px;" title="Перово" class="line_kalinin title"></div>
					<div style="left: 502px; top: 238px;" title="Перово" class="line_kalinin" data-idline="107"></div>
					<div style="left: 514px; top: 248px; width: 80px;" title="Шоссе энтузиастов" class="line_kalinin title"></div>
					<div style="left: 502px; top: 248px;" title="Шоссе энтузиастов" class="line_kalinin" data-idline="175"></div>
					<div style="left: 514px; top: 258px; width: 60px;" title="Авиамоторная" class="line_kalinin title"></div>
					<div style="left: 502px; top: 258px;" title="Авиамоторная" class="line_kalinin" data-idline="1"></div>
					<div style="left: 461px; top: 311px; width: 72px;" title="Площадь Ильича" class="line_kalinin title"></div>
					<div style="left: 449px; top: 311px;" title="Площадь Ильича" class="line_kalinin" data-idline="112"></div>
					<div style="left: 425px; top: 346px; width: 60px;" title="Марксистская" class="line_kalinin title"></div>
					<div style="left: 411px; top: 346px;" title="Марксистская" class="line_kalinin" data-idline="77"></div>
					<div style="left: 313px; top: 336px; width: 62px;" title="Третьяковская" class="line_kalinin title"></div>
					<div style="left: 315px; top: 348px;" title="Третьяковская" class="line_kalinin" data-idline="152"></div>
		
					<!-- Калининская линия -->
		
					<!-- Каховская линия -->
					<div style="left: 396px; top: 462px; width: 48px;" title="Каширская" class="line_kahov title"></div>
					<div style="left: 383px; top: 462px;" title="Каширская" class="line_kahov" data-idline="49"></div>
					<div style="left: 304px; top: 486px; width: 56px;" title="Варшавская" class="line_kahov title"></div>
					<div style="left: 358px; top: 486px;" title="Варшавская" class="line_kahov" data-idline="28"></div>
					<div style="left: 308px; top: 543px; width: 50px;" title="Каховская" class="line_kahov title"></div>
					<div style="left: 295px; top: 543px;" title="Каховская" class="line_kahov" data-idline="47"></div>
					<!-- Каховская линия -->
		
					<!-- Таганско-краснопресненская линия -->
					<div style="left: 122px; top: 88px; width: 47px;" title="Планерная" class="line_taganka title"></div>
					<div style="left: 169px; top: 88px;" title="Планерная" class="line_taganka" data-idline="111"></div>
					<div style="left: 110px; top: 98px; width: 60px;" title="Сходненская" class="line_taganka title"></div>
					<div style="left: 169px; top: 98px;" title="Сходненская" class="line_taganka" data-idline="143"></div>
					<div style="left: 122px; top: 108px; width: 48px;" title="Тушинская" class="line_taganka title"></div>
					<div style="left: 169px; top: 108px;" title="Тушинская" class="line_taganka" data-idline="156"></div>
					<div style="left: 120px; top: 118px; width: 50px;" title="Щукинская" class="line_taganka title"></div>
					<div style="left: 169px; top: 118px;" title="Щукинская" class="line_taganka" data-idline="177"></div>
					<div style="left: 94px; top: 128px; width: 76px;" title="Октябрьское поле" class="line_taganka title"></div>
					<div style="left: 169px; top: 128px;" title="Октябрьское поле" class="line_taganka" data-idline="97"></div>
					<div style="left: 108px; top: 138px; width: 60px;" title="Полежаевская" class="line_taganka title"></div>
					<div style="left: 169px; top: 138px;" title="Полежаевская" class="line_taganka" data-idline="114"></div>
					<div style="left: 134px; top: 148px; width: 36px;" title="Беговая" class="line_taganka title"></div>
					<div style="left: 169px; top: 148px;" title="Беговая" class="line_taganka" data-idline="15"></div>
					<div style="left: 100px; top: 158px; width: 70px;" title="Улица 1905 года" class="line_taganka title"></div>
					<div style="left: 169px; top: 158px;" title="Улица 1905 года" class="line_taganka" data-idline="157"></div>
					<div style="left: 110px; top: 209px; width: 60px;" title="Барикадная" class="line_taganka title"></div>
					<div style="left: 172px; top: 209px;" title="Барикадная" class="line_taganka" data-idline="13"></div>
					<div style="left: 160px; top: 252px; width: 56px;" title="Пушкинская" class="line_taganka title"></div>
					<div style="left: 216px; top: 252px;" title="Пушкинская" class="line_taganka" data-idline="123"></div>
					<div style="left: 240px; top: 261px; width: 70px;" title="Кузнецкий мост" class="line_taganka title"></div>
					<div style="left: 310px; top: 261px;" title="Кузнецкий мост" class="line_taganka" data-idline="67"></div>
					<div style="left: 360px; top: 298px; width: 52px;" title="Китай-город" class="line_taganka title"></div>
					<div style="left: 347px; top: 298px;" title="Китай-город" class="line_taganka" data-idline="53"></div>
					<div style="left: 407px; top: 358px;" title="Таганская" class="line_taganka" data-idline="145"></div>
					<div style="left: 462px; top: 400px; width: 60px;" title="Пролетарская" class="line_taganka title"></div>
					<div style="left: 449px; top: 400px;" title="Пролетарская" class="line_taganka" data-idline="118"></div>
					<div style="left: 494px; top: 574px; width: 82px;" title="Волгоградский проспект" class="line_taganka title"></div>
					<div style="left: 483px; top: 574px;" title="Волгоградский проспект" class="line_taganka" data-idline="32"></div>
					<div style="left: 494px; top: 584px; width: 60px;" title="Текстильщики" class="line_taganka title"></div>
					<div style="left: 483px; top: 584px;" title="Текстильщики" class="line_taganka" data-idline="148"></div>
					<div style="left: 494px; top: 594px; width: 48px;" title="Кузьминки" class="line_taganka title"></div>
					<div style="left: 483px; top: 594px;" title="Кузьминки" class="line_taganka" data-idline="68"></div>
					<div style="left: 494px; top: 604px; width: 90px;" title="Рязанский проспект" class="line_taganka title"></div>
					<div style="left: 483px; top: 604px;" title="Рязанский проспект" class="line_taganka" data-idline="127"></div>
					<div style="left: 494px; top: 614px; width: 32px;" title="Выхино" class="line_taganka title"></div>
					<div style="left: 483px; top: 614px;" title="Выхино" class="line_taganka" data-idline="37"></div>
					<!-- Таганско-краснопресненская линия -->
		
					<!-- Арбатско-покровская линия -->
					<div style="left: 514px; top: 118px; width: 50px;" title="Щелковская" class="line_arbat title"></div>
					<div style="left: 502px; top: 118px;" title="Щелковская" class="line_arbat" data-idline="176"></div>
					<div style="left: 514px; top: 128px; width: 60px;" title="Первомайская" class="line_arbat title"></div>
					<div style="left: 502px; top: 128px;" title="Первомайская" class="line_arbat" data-idline="106"></div>
					<div style="left: 514px; top: 138px; width: 60px;" title="Измайловская" class="line_arbat title"></div>
					<div style="left: 502px; top: 138px;" title="Измайловская" class="line_arbat" data-idline="44"></div>
					<div style="left: 514px; top: 148px; width: 60px;" title="Партизанская" class="line_arbat title"></div>
					<div style="left: 502px; top: 148px;" title="Партизанская" class="line_arbat" data-idline="105"></div>
					<div style="left: 514px; top: 158px; width: 56px;" title="Семеновская" class="line_arbat title"></div>
					<div style="left: 502px; top: 158px;" title="Семеновская" class="line_arbat" data-idline="131"></div>
					<div style="left: 514px; top: 168px; width: 80px;" title="Электрозаводская" class="line_arbat title"></div>
					<div style="left: 502px; top: 168px;" title="Электрозаводская" class="line_arbat" data-idline="178"></div>
					<div style="left: 514px; top: 178px; width: 52px;" title="Бауманская" class="line_arbat title"></div>
					<div style="left: 502px; top: 178px;" title="Бауманская" class="line_arbat" data-idline="14"></div>
					<div style="left: 414px; top: 266px;" title="Курская" class="line_arbat" data-idline="71"></div>
					<div style="left: 314px; top: 323px; width: 90px;" title="Площадь революции" class="line_arbat title"></div>
					<div style="left: 300px; top: 323px;" title="Площадь революции" class="line_arbat" data-idline="113"></div>
					<div style="left: 176px; top: 368px; width: 46px;" title="Арбатская" class="line_arbat title"></div>
					<div style="left: 222px; top: 368px;" title="Арбатская" class="line_arbat" data-idline="8"></div>
					<div style="left: 114px; top: 341px; width: 52px;" title="Смоленская" class="line_arbat title"></div>
					<div style="left: 166px; top: 341px;" title="Смоленская" class="line_arbat" data-idline="134"></div>
					<div style="left: 151px; top: 326px;" title="Киевская" class="line_arbat" data-idline="50"></div>
					<div style="left: 44px; top: 325px; width: 54px;" title="Парк победы" class="line_arbat title"></div>
					<div style="left: 98px; top: 325px;" title="Парк победы" class="line_arbat" data-idline="103"></div>
					<div style="left: 5px; top: 276px; width: 52px; height: 16px; " title="Славянский бульвар" class="line_arbat title"></div>
					<div style="left: 56px; top: 282px;" title="Славянский бульвар" class="line_arbat" data-idline="133"></div>
					<div style="left: 10px; top: 211px;" title="Кунцевская" class="line_arbat" data-idline="69"></div>
					<div style="left: 22px; top: 186px; width: 52px;" title="Молодежная" class="line_arbat title"></div>
					<div style="left: 10px; top: 186px;" title="Молодежная" class="line_arbat" data-idline="85"></div>
					<div style="left: 22px; top: 176px; width: 48px;" title="Крылатское" class="line_arbat title"></div>
					<div style="left: 10px; top: 176px;" title="Крылатское" class="line_arbat" data-idline="66"></div>
					<div style="left: 22px; top: 166px; width: 40px;" title="Строгино" class="line_arbat title"></div>
					<div style="left: 10px; top: 166px;" title="Строгино" class="line_arbat" data-idline="140"></div>
					<div style="left: 22px; top: 156px; width: 40px;" title="Мякинино" class="line_arbat title"></div>
					<div style="left: 10px; top: 156px;" title="Мякинино" class="line_arbat" data-idline="86"></div>
					<div style="left: 22px; top: 146px; width: 68px;" title="Волоколамская" class="line_arbat title"></div>
					<div style="left: 10px; top: 146px;" title="Волоколамская" class="line_arbat" data-idline="34"></div>
					<div style="left: 22px; top: 136px; width: 30px;" title="Митино" class="line_arbat title"></div>
					<div style="left: 10px; top: 136px;" title="Митино" class="line_arbat" data-idline="84"></div>
		            <div style="left: 22px; top: 126px; width: 50px;" title="Пятницкое шоссе" class="line_arbat title"></div>
		            <div style="left: 10px; top: 126px;" title="Пятницкое шоссе" class="line_arbat" data-idline="253"></div>
					<!-- Арбатско-покровская линия -->
		
					<!-- Филевская линия -->
					<div style="left: 24px; top: 211px; width: 50px;" title="Кунцевская" class="line_fili title"></div>
					<div style="left: 10px; top: 211px;" title="Кунцевская" class="line_fili" data-idline="70"></div>
					<div style="left: 35px; top: 226px; width: 50px;" title="Пионерская" class="line_fili title"></div>
					<div style="left: 25px; top: 226px;" title="Пионерская" class="line_fili" data-idline="110"></div>
					<div style="left: 44px; top: 236px; width: 68px;" title="Филевский парк" class="line_fili title"></div>
					<div style="left: 35px; top: 236px;" title="Филевский парк" class="line_fili" data-idline="164"></div>
					<div style="left: 56px; top: 246px; width: 76px;" title="Багратионовская" class="line_fili title"></div>
					<div style="left: 45px; top: 246px;" title="Багратионовская" class="line_fili" data-idline="12"></div>
					<div style="left: 66px; top: 256px; width: 20px;" title="Фили" class="line_fili title"></div>
					<div style="left: 55px; top: 256px;" title="Фили" class="line_fili" data-idline="165"></div>
					<div style="left: 74px; top: 266px; width: 60px;" title="Кутузовская" class="line_fili title"></div>
					<div style="left: 65px; top: 266px;" title="Кутузовская" class="line_fili" data-idline="73"></div>
					<div style="left: 84px; top: 276px; width: 60px;" title="Студенческая" class="line_fili title"></div>
					<div style="left: 75px; top: 276px;" title="Студенческая" class="line_fili" data-idline="141"></div>
					<div style="left: 133px; top: 287px; width: 70px;" title="Международная" class="line_fili title"></div>
					<div style="left: 121px; top: 287px;" title="Международная" class="line_fili" data-idline="82"></div>
					<div style="left: 133px; top: 297px; width: 56px;" title="Выставочная" class="line_fili title"></div>
					<div style="left: 121px; top: 297px;" title="Выставочная" class="line_fili" data-idline="36"></div>
					<div style="left: 151px; top: 326px;" title="Киевская" class="line_fili" data-idline="51"></div>
					<div style="left: 196px; top: 326px; width: 50px;" title="Смоленская" class="line_fili title"></div>
					<div style="left: 185px; top: 327px;" title="Смоленская" class="line_fili" data-idline="135"></div>
					<div style="left: 212px; top: 342px; width: 44px;" title="Арбатская" class="line_fili title"></div>
					<div style="left: 200px; top: 342px;" title="Арбатская" class="line_fili" data-idline="9"></div>
					<div style="left: 120px; top: 358px; width: 94px;" title="Александровский сад" class="line_fili title"></div>
					<div style="left: 216px; top: 358px;" title="Александровский сад" class="line_fili" data-idline="4"></div>
					<!-- Филевская линия -->
		
					<!-- Бутовская линия -->
					<div style="left: 296px; top: 658px; width: 110px;" title="Улица Старокачаловская" class="line_butovo title"></div>
					<div style="left: 283px; top: 658px;" title="Улица Старокачаловская" class="line_butovo" data-idline="161"></div>
					<div style="left: 296px; top: 674px; width: 86px;" title="Улица Скобелевская" class="line_butovo title"></div>
					<div style="left: 283px; top: 674px;" title="Улица Скобелевская" class="line_butovo" data-idline="160"></div>
					<div style="left: 296px; top: 684px; width: 96px;" title="Бульвар адм.Ушакова" class="line_butovo title"></div>
					<div style="left: 283px; top: 684px;" title="Бульвар адм.Ушакова" class="line_butovo" data-idline="25"></div>
					<div style="left: 296px; top: 694px; width: 76px;" title="Улица Горчакова" class="line_butovo title"></div>
					<div style="left: 283px; top: 694px;" title="Улица Горчакова" class="line_butovo" data-idline="158"></div>
					<div style="left: 296px; top: 704px; width: 73px;" title="Бунинская аллея" class="line_butovo title"></div>
					<div style="left: 283px; top: 704px;" title="Бунинская аллея" class="line_butovo" data-idline="26"></div>
					<!-- Бутовская линия -->
		
					<!-- Кольцевая линия -->
					<div style="left: 93px; top: 219px; width: 86px;" title="Краснопресненская" class="line_ring title"></div>
					<div style="left: 180px; top: 217px;" title="Краснопресненская" class="line_ring" data-idline="61"></div>
					<div style="left: 160px; top: 188px; width: 57px;" title="Белорусская" class="line_ring title"></div>
					<div style="left: 218px; top: 188px;" title="Белорусская" class="line_ring" data-idline="17"></div>
					<div style="left: 183px; top: 173px; width: 74px;" title="Новослободская" class="line_ring title"></div>
					<div style="left: 258px; top: 173px;" title="Новослободская" class="line_ring" data-idline="92"></div>
		<!-- 		<div style="left: 392px; top: 183px; width: 69px;" title="Проспект мира" class="line_ring title"></div>
					<div style="left: 359px; top: 183px;" title="Проспект мира" class="line_ring" data-idline="120"></div>
		-->			<div style="left: 392px; top: 208px; width: 70px;" title="Комсомольская" class="line_ring title"></div>
					<div style="left: 379px; top: 208px;" title="Комсомольская" class="line_ring" data-idline="57"></div>
					<div style="left: 426px; top: 266px; width: 38px;" title="Курская" class="line_ring title"></div>
					<div style="left: 414px; top: 266px;" title="Курская" class="line_ring" data-idline="72"></div>
					<div style="left: 420px; top: 358px; width: 46px;" title="Таганская" class="line_ring title"></div>
					<div style="left: 407px; top: 358px;" title="Таганская" class="line_ring" data-idline="144"></div>
					<div style="left: 390px; top: 400px; width: 51px;" title="Павелецкая" class="line_ring title"></div>
					<div style="left: 377px; top: 400px;" title="Павелецкая" class="line_ring" data-idline="102"></div>
					<div style="left: 295px; top: 436px; width: 64px;" title="Добрынинская" class="line_ring title"></div>
					<div style="left: 283px; top: 436px;" title="Добрынинская" class="line_ring" data-idline="40"></div>
					<div style="left: 176px; top: 428px; width: 57px;" title="Октябрьская" class="line_ring title" ></div>
					<div style="left: 234px; top: 428px;" title="Октябрьская" class="line_ring" data-idline="95"></div>
					<div style="left: 125px; top: 398px; width: 62px;" title="Парк культуры" class="line_ring title"></div>
					<div style="left: 189px; top: 398px;" title="Парк культуры" class="line_ring" data-idline="185"></div>
					<div style="left: 148px; top: 316px; width: 42px;" title="Киевская" class="line_ring title"></div>
					<div style="left: 151px; top: 326px;" title="Киевская" class="line_ring" data-idline="52"></div>
					<!-- Кольцевая линия -->
		
				</div>
		
			</div>
		
		    <div class="clear"></div>
		</div>

	</xsl:template>
	
	
	
	
	<xsl:template name="spbMap">
	
		<script type="text/javascript" src="/js/metro_spb.js"></script>
		
		<div class="change-metro">
		<div id="metro_lines">
		    <div class="head">
		        Выберите удобные станции метро<br/> или ветки целиком
		    </div>
		    <i style="background-position: 0 -18px"></i><a href="" id="line_kv">Кировско-выборгская</a>
		    <i style="background-position: 0 -90px"></i><a href="" id="line_mp">Московско-петроградская</a>
		    <i style="background-position: 0 -81px"></i><a href="" id="line_nv">Невско-василеостровская</a>
		    <i style="background-position: 0 -54px"></i><a href="" id="line_pr">Правобережная</a>
		    <i style="background-position: 0 -45px"></i><a href="" id="line_fr">Фрунзенская</a>
		    <div id="select-stations">
		        <strong>Выбранные станции</strong> (<span id="stations-select-count"></span>)
		        <div>
		        </div>
		    </div>
		    <a href="#" id="metro-clear-act">очистить все выбранные</a>
		    <div id="map-submit" class="button but-metro">
		        <span>Применить</span>
		    </div>
		</div>
		
		<div id="map_spb" class="metro-map">
		
		    <div class="map-image">
		
		        <div style="background:url(/images/metro-spb.png) no-repeat; width:726px; height:578px;"></div>
		
		    </div>
		
		    <div id="map_stations">
		        <!-- Кировско-выборгская линия -->
		        <div style="left: 386px; top: 19px; width: 48px;" title="Девяткино" class="line_kv title" data-idline="186"></div>
		        <div style="left: 386px; top: 43px; width: 98px;" title="Гражданский проспект" class="line_kv title" data-idline="187"></div>
		        <div style="left: 386px; top: 67px; width: 68px;" title="Академическая" class="line_kv title" data-idline="188"></div>
		        <div style="left: 386px; top: 90px; width: 77px;" title="Политехническая" class="line_kv title" data-idline="189"></div>
		        <div style="left: 386px; top: 114px; width: 88px;" title="Площадь Мужества" class="line_kv title" data-idline="190"></div>
		        <div style="left: 386px; top: 138px; width: 33px;" title="Лесная" class="line_kv title" data-idline="191"></div>
		        <div style="left: 386px; top: 161px; width: 50px;" title="Выборгская" class="line_kv title" data-idline="192"></div>
		        <div style="left: 386px; top: 185px; width: 75px;" title="Площадь Ленина" class="line_kv title" data-idline="193"></div>
		        <div style="left: 386px; top: 209px; width: 66px;" title="Чернышевская" class="line_kv title" data-idline="194"></div>
		        <div style="left: 389px; top: 232px; width: 98px;" title="Площадь Восстания" class="line_kv title" data-idline="195"></div>
		        <div style="left: 389px; top: 295px; width: 70px;" title="Владимирская" class="line_kv title" data-idline="196"></div>
		        <div style="left: 336px; top: 347px; width: 56px;" title="Пушкинская" class="line_kv title" data-idline="197"></div>
		        <div style="left: 124px; top: 347px; width: 124px;" title="Технологический институт" class="line_kv title" data-idline="198"></div>
		        <div style="left: 176px; top: 364px; width: 50px;" title="Балтийская" class="line_kv title" data-idline="199"></div>
		        <div style="left: 159px; top: 388px; width: 44px;" title="Нарвская" class="line_kv title" data-idline="200"></div>
		        <div style="left: 105px; top: 412px; width: 76px;" title="Кировский завод" class="line_kv title" data-idline="201"></div>
		        <div style="left: 122px; top: 435px; width: 34px;" title="Автово" class="line_kv title" data-idline="202"></div>
		        <div style="left: 44px; top: 459px; width: 88px;" title="Ленинский проспект" class="line_kv title" data-idline="203"></div>
		        <div style="left: 18px; top: 483px; width: 89px;" title="Проспект Ветеранов" class="line_kv title" data-idline="204"></div>
		
		        <!-- Московско-петроградская линия -->
		        <div style="left: 268px; top: 43px; width: 32px;" title="Парнас" class="line_mp title" data-idline="205"></div>
		        <div style="left: 268px; top: 63px; width: 62px; height: 21px;" title="Проспект Просвещения" class="line_mp title" data-idline="206"></div>
		        <div style="left: 268px; top: 90px; width: 31px;" title="Озерки" class="line_mp title" data-idline="207"></div>
		        <div style="left: 268px; top: 114px; width: 41px;" title="Удельная" class="line_mp title" data-idline="208"></div>
		        <div style="left: 268px; top: 138px; width: 52px;" title="Пионерская" class="line_mp title" data-idline="209"></div>
		        <div style="left: 268px; top: 161px; width: 59px;" title="Черная речка" class="line_mp title" data-idline="210"></div>
		        <div style="left: 268px; top: 185px; width: 64px;" title="Петроградская" class="line_mp title" data-idline="211"></div>
		        <div style="left: 268px; top: 209px; width: 55px;" title="Горьковская" class="line_mp title" data-idline="212"></div>
		        <div style="left: 271px; top: 232px; width: 85px;" title="Невский проспект" class="line_mp title" data-idline="213"></div>
		        <div style="left: 271px; top: 295px; width: 44px; height: 20px;" title="Сенная площадь" class="line_mp title" data-idline="214"></div>
		        <div style="left: 268px; top: 398px; width: 57px;" title="Фрунзенская" class="line_mp title" data-idline="216"></div>
		        <div style="left: 268px; top: 422px; width: 86px;" title="Московские ворота" class="line_mp title" data-idline="217"></div>
		        <div style="left: 268px; top: 445px; width: 55px;" title="Электросила" class="line_mp title" data-idline="218"></div>
		        <div style="left: 268px; top: 469px; width: 59px;" title="Парк Победы" class="line_mp title" data-idline="219"></div>
		        <div style="left: 268px; top: 493px; width: 52px;" title="Московская" class="line_mp title" data-idline="220"></div>
		        <div style="left: 268px; top: 517px; width: 41px;" title="Звездная" class="line_mp title" data-idline="221"></div>
		        <div style="left: 268px; top: 540px; width: 37px;" title="Купчино" class="line_mp title" data-idline="222"></div>
		
		        <!-- Невско-василеостровская линия -->
		        <div style="left: 19px; top: 240px; width: 54px;" title="Приморская" class="line_nv title" data-idline="223"></div>
		        <div style="left: 45px; top: 267px; width: 82px;" title="Василеостровская" class="line_nv title" data-idline="224"></div>
		        <div style="left: 271px; top: 246px; width: 71px;" title="Гстиный двор" class="line_nv title" data-idline="225"></div>
		        <div style="left: 389px; top: 246px; width: 55px;" title="Маяковская" class="line_nv title" data-idline="226"></div>
		        <div style="left: 507px; top: 295px; width: 156px;" title="Площадь Александра Невского-1" class="line_nv title" data-idline="227"></div>
		        <div style="left: 505px; top: 351px; width: 61px;" title="Елизаровская" class="line_nv title" data-idline="228"></div>
		        <div style="left: 505px; top: 374px; width: 69px;" title="Ломоносовская" class="line_nv title" data-idline="229"></div>
		        <div style="left: 505px; top: 398px; width: 61px;" title="Пролетарская" class="line_nv title" data-idline="230"></div>
		        <div style="left: 505px; top: 422px; width: 40px;" title="Обухово" class="line_nv title" data-idline="231"></div>
		        <div style="left: 505px; top: 445px; width: 42px;" title="Рыбацкое" class="line_nv title" data-idline="232"></div>
		
		        <!-- Правобережная линия -->
		        <div style="left: 208px; top: 331px; width: 45px;" title="Спасская" class="line_pr title" data-idline="233"></div>
		        <div style="left: 388px; top: 309px; width: 60px;" title="Достоевская" class="line_pr title" data-idline="234"></div>
		        <div style="left: 414px; top: 330px; width: 45px; height: 21px;" title="Лиговский проспект" class="line_pr title" data-idline="235"></div>
		        <div style="left: 507px; top: 309px; width: 156px;" title="Площадь Александра Невского-2" class="line_pr title" data-idline="236"></div>
		        <div style="left: 519px; top: 329px; width: 72px;" title="Новочеркасская" class="line_pr title" data-idline="237"></div>
		        <div style="left: 623px; top: 351px; width: 47px;" title="Ладожская" class="line_pr title" data-idline="238"></div>
		        <div style="left: 623px; top: 374px; width: 101px;" title="Проспект Большевиков" class="line_pr title" data-idline="239"></div>
		        <div style="left: 623px; top: 398px; width: 67px;" title="Улица Дыбенко" class="line_pr title" data-idline="240"></div>
		
		        <!-- Фрунзенская линия -->
		        <div style="left: 150px; top: 110px; width: 65px; height: 21px;" title="Комендантский проспект" class="line_fr title" data-idline="241"></div>
		        <div style="left: 150px; top: 138px; width: 73px;" title="Старая Деревня" class="line_fr title" data-idline="242"></div>
		        <div style="left: 150px; top: 161px; width: 86px;" title="Крестовский остров" class="line_fr title" data-idline="243"></div>
		        <div style="left: 150px; top: 185px; width: 51px;" title="Чкаловская" class="line_fr title" data-idline="244"></div>
		        <div style="left: 150px; top: 209px; width: 52px;" title="Спортивная" class="line_fr title" data-idline="245"></div>
		        <div style="left: 150px; top: 232px; width: 71px;" title="Адмиралтейская" class="line_fr title" data-idline="246"></div>
		        <div style="left: 211px; top: 273px; width: 42px;" title="Садовая" class="line_fr title" data-idline="247"></div>
		        <div style="left: 336px; top: 370px; width: 77px;" title="Звенигородская" class="line_fr title" data-idline="248"></div>
		        <div style="left: 371px; top: 398px; width: 73px;" title="Обводный канал" class="line_fr title" data-idline="249"></div>
		        <div style="left: 386px; top: 445px; width: 52px;" title="Волковская" class="line_fr title" data-idline="250"></div>
		    </div>
		
		
		</div>
		
		<div class="clear"></div>
		        
		
		</div>	
	</xsl:template>
	
</xsl:transform>
