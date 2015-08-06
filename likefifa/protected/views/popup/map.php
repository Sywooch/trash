<div class="popup-app_head">Выберите удобные станции метро или ветки целиком<div class="popup-close png"></div></div>
<div class="popup-metro_cont">
	<script type="text/javascript" src="<?php echo Yii::app()->homeUrl; ?>js/metro.js?<?php echo RELEASE_MEDIA; ?>"></script>
	<div class="change-metro">
		<div class="metro-lines_wrap">
			<div class="head">
				Линии метро
			</div>
			<div id="metro_lines">
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
			</div>
			<div id="select-stations">
				<strong>Выбранные станции</strong> (<span id="stations-select-count"></span>)
				<div>
				</div>
			</div>
			<a href="#" id="metro-clear-act">очистить все выбранные</a>
			<div id="map-submit" class="button button-blue">
				<span>Применить</span>
			</div>
		</div>
		<div id="map_moscow">
			<div style="background:url('<?php echo Yii::app()->homeUrl; ?>i/metro.gif?<?php echo RELEASE_MEDIA; ?>') no-repeat; width:669px; height:714px;"></div>
			<div id="map_stations">
				<!-- Замоскворецкая линия -->
				<div style="left: 170px; top: 57px; width: 62px;" title="Речной вокзал" class="line_zm title"></div>
				<div style="top: 57px; left: 160px;" title="Речной вокзал" class="line_zm" data-idline="124"></div>
				<div style="top: 90px; left: 172px; width: 65px;" title="Водный стадион" class="line_zm title"></div>
				<div style="top: 88px; left: 160px;" title="Водный стадион" class="line_zm" data-idline="30"></div>
				<div style="top: 113px; left: 171px; width: 49px;" title="Войковская" class="line_zm title"></div>
				<div style="left: 160px; top: 113px;" title="Войковская" class="line_zm" data-idline="31"></div>
				<div style="left: 171px; top: 137px; width: 26px;" title="Сокол" class="line_zm title"></div>
				<div style="left: 160px; top: 137px;" title="Сокол" class="line_zm" data-idline="136"></div>
				<div style="left: 179px; top: 160px; width: 40px;" title="Аэропорт" class="line_zm title"></div>
				<div style="left: 169px; top: 168px;" title="Аэропорт" class="line_zm" data-idline="10"></div>
				<div style="left: 199px; top: 179px; width: 34px;" title="Динамо" class="line_zm title"></div>
				<div style="left: 191px; top: 185px;" title="Динамо" class="line_zm" data-idline="38"></div>
				<div style="left: 160px; top: 212px; width: 50px;" title="Белорусская" class="line_zm title"></div>
				<div style="left: 208px; top: 205px;" title="Белорусская" class="line_zm" data-idline="16"></div>
				<div style="left: 202px; top: 249px; width: 47px;" title="Маяковская" class="line_zm title"></div>
				<div style="left: 245px; top: 244px;" title="Маяковская" class="line_zm" data-idline="80"></div>
				<div style="left: 231px; top: 269px; width: 38px;" title="Тверская" class="line_zm title"></div>
				<div style="left: 266px; top: 264px;" title="Тверская" class="line_zm" data-idline="146"></div>
				<div style="left: 342px; top: 334px; width: 49px;" title="Театральная" class="line_zm title"></div>
				<div style="left: 330px; top: 340px;" title="Театральная" class="line_zm" data-idline="147"></div>
				<div style="left: 395px; top: 399px; width: 59px;" title="Новокузнецкая" class="line_zm title"></div>
				<div style="left: 383px; top: 403px;" title="Новокузнецкая" class="line_zm" data-idline="91"></div>
				<div style="left: 436px; top: 440px; width: 48px;" title="Павелецкая" class="line_zm title"></div>
				<div style="left: 426px; top: 447px;" title="Павелецкая" class="line_zm" data-idline="101"></div>
				<div style="left: 445px; top: 481px; width: 60px;" title="Автозаводская" class="line_zm title"></div>
				<div style="left: 432px; top: 481px;" title="Автозаводская" class="line_zm" data-idline="2"></div>
				<div style="left: 445px; top: 507px; width: 57px;" title="Коломенская" class="line_zm title"></div>
				<div style="left: 433px; top: 507px;" title="Коломенская" class="line_zm" data-idline="56"></div>
				<div style="left: 446px; top: 534px; width: 44px;" title="Каширская" class="line_zm title"></div>
				<div style="left: 433px; top: 534px;" title="Каширская" class="line_zm" data-idline="48"></div>
				<div style="left: 449px; top: 568px; width: 71px;" title="Кантемировская" class="line_zm title"></div>
				<div style="left: 441px; top: 574px;" title="Кантемировская" class="line_zm" data-idline="46"></div>
				<div style="left: 468px; top: 586px; width: 45px;" title="Царицыно" class="line_zm title"></div>
				<div style="left: 458px; top: 592px;" title="Царицыно" class="line_zm" data-idline="167"></div>
				<div style="left: 485px; top: 604px; width: 35px;" title="Орехово" class="line_zm title"></div>
				<div style="left: 476px; top: 611px;" title="Орехово" class="line_zm" data-idline="98"></div>
				<div style="left: 433px; top: 636px; width: 61px;" title="Домодедовская" class="line_zm title"></div>
				<div style="left: 492px; top: 630px;" title="Домодедовская" class="line_zm" data-idline="41"></div>
				<div style="left: 438px; top: 657px; width: 77px;" title="Красногвардейская" class="line_zm title"></div>
				<div style="left: 515px; top: 650px;" title="Красногвардейская" class="line_zm" data-idline="60"></div>
				<div style="left: 467px; top: 672px; width: 63px;" title="Алма-Атинская" class="line_zm title"></div>
				<div style="left: 530px; top: 667px;" title="Алма-Атинская" class="line_zm" data-idline="187"></div>
				<!-- Замоскворецкая линия -->

				<!-- Серпуховская линия -->
				<div class="line_serpyx title" title="Алтуфьево" style="left: 253px; top: 4px; width: 46px;"></div>
				<div class="line_serpyx" data-idline="6" title="Алтуфьево" style="left: 242px; top: 4px;"></div>
				<div class="line_serpyx title" title="Бибирево" style="left: 253px; top: 17px; width: 41px;"></div>
				<div class="line_serpyx" data-idline="19" title="Бибирево" style="left: 242px; top: 17px;"></div>
				<div class="line_serpyx title" title="Отрадное" style="left: 253px; top: 30px; width: 41px;"></div>
				<div class="line_serpyx" data-idline="99" title="Отрадное" style="left: 241px; top: 30px;"></div>
				<div class="line_serpyx title" title="Владыкино" style="left: 253px; top: 45px; width: 49px;"></div>
				<div class="line_serpyx" data-idline="29" title="Владыкино" style="left: 241px; top: 45px;"></div>
				<div class="line_serpyx title" title="Петровско-Разумовская" style="left: 253px; top: 59px; width: 97px;"></div>
				<div class="line_serpyx" data-idline="108" title="Петровско-Разумовская" style="left: 241px; top: 59px;"></div>
				<div class="line_serpyx title" title="Тимирязевская" style="left: 253px; top: 75px; width: 62px;"></div>
				<div class="line_serpyx" data-idline="150" title="Тимирязевская" style="left: 241px; top: 75px;"></div>
				<div class="line_serpyx title" title="Дмитровская" style="left: 254px; top: 115px; width: 53px;"></div>
				<div class="line_serpyx" data-idline="39" title="Дмитровская" style="left: 242px; top: 115px;"></div>
				<div class="line_serpyx title" title="Савеловская" style="left: 254px; top: 136px; width: 51px;"></div>
				<div class="line_serpyx" data-idline="128" title="Савеловская" style="left: 242px; top: 136px;"></div>
				<div class="line_serpyx title" title="Менделеевская" style="left: 259px; top: 157px; width: 63px;"></div>
				<div class="line_serpyx" data-idline="83" title="Менделеевская" style="left: 258px; top: 167px;"></div>
				<div class="line_serpyx title" title="Цветной бульвар" style="left: 256px; top: 220px; width: 37px; height: 16px;"></div>
				<div class="line_serpyx" data-idline="168" title="Цветной бульвар" style="left: 293px; top: 219px;"></div>
				<div class="line_serpyx title" title="Чеховская" style="left: 295px; top: 270px; width: 40px;"></div>
				<div class="line_serpyx" data-idline="171" title="Чеховская" style="left: 284px; top: 265px;"></div>
				<div class="line_serpyx title" title="Боровицкая" style="left: 285px; top: 386px; width: 55px;"></div>
				<div class="line_serpyx" data-idline="21" title="Боровицкая" style="left: 282px; top: 375px;"></div>
				<div class="line_serpyx title" title="Полянка" style="left: 346px; top: 445px; width: 36px;"></div>
				<div class="line_serpyx" data-idline="115" title="Полянка" style="left: 334px; top: 445px;"></div>
				<div class="line_serpyx title" title="Серпуховская" style="left: 347px; top: 480px; width: 62px;"></div>
				<div class="line_serpyx" data-idline="132" title="Серпуховская" style="left: 334px; top: 480px;"></div>
				<div class="line_serpyx title" title="Тульская" style="left: 346px; top: 500px; width: 40px;"></div>
				<div class="line_serpyx" data-idline="154" title="Тульская" style="left: 334px; top: 500px;"></div>
				<div class="line_serpyx title" title="Нагатинская" style="left: 346px; top: 519px; width: 56px;"></div>
				<div class="line_serpyx" data-idline="87" title="Нагатинская" style="left: 334px; top: 519px;"></div>
				<div class="line_serpyx title" title="Нагорная" style="left: 346px; top: 537px; width: 42px;"></div>
				<div class="line_serpyx" data-idline="88" title="Нагорная" style="left: 334px; top: 537px;"></div>
				<div class="line_serpyx title" title="Нахимовский проспект" style="left: 347px; top: 557px; width: 52px; height: 19px;"></div>
				<div class="line_serpyx" data-idline="89" title="Нахимовский проспект" style="left: 335px; top: 557px;"></div>
				<div class="line_serpyx title" title="Севастопольская" style="left: 347px; top: 598px; width: 73px;"></div>
				<div class="line_serpyx" data-idline="130" title="Севастопольская" style="left: 335px; top: 598px;"></div>
				<div class="line_serpyx title" title="Чертановская" style="left: 347px; top: 620px; width: 59px;"></div>
				<div class="line_serpyx" data-idline="170" title="Чертановская" style="left: 335px; top: 620px;"></div>
				<div class="line_serpyx title" title="Южная" style="left: 339px; top: 644px; width: 30px;"></div>
				<div class="line_serpyx" data-idline="180" title="Южная" style="left: 331px; top: 636px;"></div>
				<div class="line_serpyx title" title="Пражская" style="left: 325px; top: 658px; width: 43px;"></div>
				<div class="line_serpyx" data-idline="116" title="Пражская" style="left: 315px; top: 651px;"></div>
				<div class="line_serpyx title" title="Улица Академика Янгеля" style="left: 310px; top: 672px; width: 99px;"></div>
				<div class="line_serpyx" data-idline="162" title="Улица Академика Янгеля" style="left: 302px; top: 665px;"></div>
				<div class="line_serpyx title" title="Аннино" style="left: 259px; top: 662px; width: 33px;"></div>
				<div class="line_serpyx" data-idline="7" title="Аннино" style="left: 271px; top: 671px;"></div>
				<div class="line_serpyx title" title="Бульвар Дм.Донского" style="left: 179px; top: 683px; width: 74px; height: 16px;"></div>
				<div class="line_serpyx" data-idline="24" title="Бульвар Дм.Донского" style="left: 169px; top: 672px;"></div>
				<!-- Серпуховская линия -->

				<!-- Калужско-рижская линия -->
				<div class="line_kaluga title" title="Медведково" style="left: 411px; top: 4px; width: 52px;"></div>
				<div class="line_kaluga" data-idline="81" title="Медведково" style="left: 399px; top: 4px;"></div>
				<div class="line_kaluga title" title="Бабушкинская" style="left: 410px; top: 18px; width: 60px;"></div>
				<div class="line_kaluga" data-idline="11" title="Бабушкинская" style="left: 399px; top: 18px;"></div>
				<div class="line_kaluga title" title="Свиблово" style="left: 410px; top: 32px; width: 42px;"></div>
				<div class="line_kaluga" data-idline="129" title="Свиблово" style="left: 398px; top: 32px;"></div>
				<div class="line_kaluga title" title="Ботанический сад" style="left: 410px; top: 47px; width: 71px;"></div>
				<div class="line_kaluga" data-idline="22" title="Ботанический сад" style="left: 398px; top: 47px;"></div>
				<div class="line_kaluga title" title="ВДНХ" style="left: 411px; top: 97px; width: 22px;"></div>
				<div class="line_kaluga" data-idline="27" title="ВДНХ" style="left: 399px; top: 97px;"></div>
				<div class="line_kaluga title" title="Алексеевская" style="left: 411px; top: 116px; width: 60px;"></div>
				<div class="line_kaluga" data-idline="5" title="Алексеевская" style="left: 399px; top: 116px;"></div>
				<div class="line_kaluga title" title="Рижская" style="left: 411px; top: 136px; width: 38px;"></div>
				<div class="line_kaluga" data-idline="125" title="Рижская" style="left: 399px; top: 136px;"></div>
				<div class="line_kaluga title" title="Проспект мира" style="left: 393px; top: 168px; width: 63px;"></div>
				<div class="line_kaluga" data-idline="121" title="Проспект мира" style="left: 384px; top: 161px;"></div>
				<div class="line_kaluga title" title="Сухаревская" style="left: 360px; top: 210px; width: 56px;"></div>
				<div class="line_kaluga" data-idline="142" title="Сухаревская" style="left: 347px; top: 210px;"></div>
				<div class="line_kaluga title" title="Тургеневская" style="left: 321px; top: 260px; width: 52px;"></div>
				<div class="line_kaluga" data-idline="155" title="Тургеневская" style="left: 374px; top: 260px;"></div>
				<div class="line_kaluga title" title="Китай-город" style="left: 357px; top: 317px; width: 49px;"></div>
				<div class="line_kaluga" data-idline="54" title="Китай-город" style="left: 406px; top: 323px;"></div>
				<div class="line_kaluga title" title="Третьяковская" style="left: 316px; top: 420px; width: 54px;"></div>
				<div class="line_kaluga" data-idline="151" title="Третьяковская" style="left: 368px; top: 412px;"></div>
				<div class="line_kaluga title" title="Октябрьская" style="left: 280px; top: 451px; width: 50px;"></div>
				<div class="line_kaluga" data-idline="96" title="Октябрьская" style="left: 272px; top: 444px;"></div>
				<div class="line_kaluga title" title="Шаболовская" style="left: 244px; top: 489px; width: 57px;"></div>
				<div class="line_kaluga" data-idline="174" title="Шаболовская" style="left: 237px; top: 482px;"></div>
				<div class="line_kaluga title" title="Ленинский проспект" style="left: 230px; top: 504px; width: 82px;"></div>
				<div class="line_kaluga" data-idline="74" title="Ленинский проспект" style="left: 221px; top: 497px;"></div>
				<div class="line_kaluga title" title="Академическая" style="left: 213px; top: 519px; width: 66px;"></div>
				<div class="line_kaluga" data-idline="3" title="Академическая" style="left: 205px; top: 513px;"></div>
				<div class="line_kaluga title" title="Профсоюзная" style="left: 208px; top: 533px; width: 60px;"></div>
				<div class="line_kaluga" data-idline="122" title="Профсоюзная" style="left: 196px; top: 533px;"></div>
				<div class="line_kaluga title" title="Новые черемушки" style="left: 207px; top: 547px; width: 73px;"></div>
				<div class="line_kaluga" data-idline="94" title="Новые черемушки" style="left: 196px; top: 547px;"></div>
				<div class="line_kaluga title" title="Калужская" style="left: 208px; top: 563px; width: 48px;"></div>
				<div class="line_kaluga" data-idline="45" title="Калужская" style="left: 196px; top: 563px;"></div>
				<div class="line_kaluga title" title="Беляево" style="left: 208px; top: 578px; width: 42px;"></div>
				<div class="line_kaluga" data-idline="18" title="Беляево" style="left: 196px; top: 578px;"></div>
				<div class="line_kaluga title" title="Коньково" style="left: 208px; top: 593px; width: 43px;"></div>
				<div class="line_kaluga" data-idline="59" title="Коньково" style="left: 196px; top: 593px;"></div>
				<div class="line_kaluga title" title="Теплый стан" style="left: 208px; top: 607px; width: 52px;"></div>
				<div class="line_kaluga" data-idline="149" title="Теплый стан" style="left: 196px; top: 607px;"></div>
				<div class="line_kaluga title" title="Ясенево" style="left: 208px; top: 622px; width: 34px;"></div>
				<div class="line_kaluga" data-idline="181" title="Ясенево" style="left: 196px; top: 622px;"></div>
				<div class="line_kaluga title" title="Новоясеневская" style="left: 208px; top: 638px; width: 71px;"></div>
				<div class="line_kaluga" data-idline="93" title="Новоясеневская" style="left: 196px; top: 638px;"></div>
				<!-- Калужско-рижская линия -->

				<!-- Сокольническая линия -->
				<div class="line_sokolniki title" title="Улица Подбельского" style="left: 488px; top: 70px; width: 67px;"></div>
				<div class="line_sokolniki" data-idline="159" title="Улица Подбельского" style="left: 476px; top: 70px;"></div>
				<div class="line_sokolniki title" title="Черкизовская" style="left: 488px; top: 97px; width: 60px;"></div>
				<div class="line_sokolniki" data-idline="169" title="Черкизовская" style="left: 476px; top: 97px;"></div>
				<div class="line_sokolniki title" title="Преображенская площадь" style="left: 488px; top: 123px; width: 68px; height: 20px;"></div>
				<div class="line_sokolniki" data-idline="117" title="Преображенская площадь" style="left: 476px; top: 123px;"></div>
				<div class="line_sokolniki title" title="Сокольники" style="left: 488px; top: 158px; width: 50px;"></div>
				<div class="line_sokolniki" data-idline="137" title="Сокольники" style="left: 476px; top: 158px;"></div>
				<div class="line_sokolniki title" title="Красносельская" style="left: 473px; top: 195px; width: 70px;"></div>
				<div class="line_sokolniki" data-idline="62" title="Красносельская" style="left: 465px; top: 188px;"></div>
				<div class="line_sokolniki title" title="Комсомольская" style="left: 455px; top: 212px; width: 62px;"></div>
				<div class="line_sokolniki" data-idline="58" title="Комсомольская" style="left: 445px; top: 205px;"></div>
				<div class="line_sokolniki title" title="Красные ворота" style="left: 425px; top: 242px; width: 34px; height: 18px;"></div>
				<div class="line_sokolniki" data-idline="63" title="Красные ворота" style="left: 418px; top: 234px;"></div>
				<div class="line_sokolniki title" title="Чистые пруды" style="left: 400px; top: 268px; width: 29px; height: 17px;"></div>
				<div class="line_sokolniki" data-idline="172" title="Чистые пруды" style="left: 390px; top: 260px;"></div>
				<div class="line_sokolniki title" title="Лубянка" style="left: 361px; top: 302px; width: 33px;"></div>
				<div class="line_sokolniki" data-idline="75" title="Лубянка" style="left: 358px; top: 292px;"></div>
				<div class="line_sokolniki title" title="Охотный ряд" style="left: 286px; top: 319px; width: 33px; height: 17px;"></div>
				<div class="line_sokolniki" data-idline="100" title="Охотный ряд" style="left: 321px; top: 329px;"></div>
				<div class="line_sokolniki title" title="Библиотека им.Ленина" style="left: 301px; top: 368px; width: 44px; height: 16px;"></div>
				<div class="line_sokolniki" data-idline="20" title="Библиотека им.Ленина" style="left: 290px; top: 360px;"></div>
				<div class="line_sokolniki title" title="Кропоткинская" style="left: 256px; top: 411px; width: 59px;"></div>
				<div class="line_sokolniki" data-idline="65" title="Кропоткинская" style="left: 249px; top: 403px;"></div>
				<div class="line_sokolniki title" title="Парк культуры" style="left: 242px; top: 425px; width: 37px; height: 16px;"></div>
				<div class="line_sokolniki" data-idline="104" title="Парк культуры" style="left: 232px; top: 418px;"></div>
				<div class="line_sokolniki title" title="Фрунзенская" style="left: 145px; top: 441px; width: 58px;"></div>
				<div class="line_sokolniki" data-idline="166" title="Фрунзенская" style="left: 201px; top: 447px;"></div>
				<div class="line_sokolniki title" title="Спортивная" style="left: 133px; top: 470px; width: 47px;"></div>
				<div class="line_sokolniki" data-idline="138" title="Спортивная" style="left:151px; top: 459px;"></div>
				<div class="line_sokolniki title" title="Воробьевы горы" style="left: 111px; top: 487px; width: 64px;"></div>
				<div class="line_sokolniki" data-idline="35" title="Воробьевы горы" style="left: 102px; top: 480px;"></div>
				<div class="line_sokolniki title" title="Университет" style="left: 89px; top: 508px; width: 52px;"></div>
				<div class="line_sokolniki" data-idline="163" title="Университет" style="left: 80px; top: 501px;"></div>
				<div class="line_sokolniki title" title="Пр-т Вернадского" style="left: 68px; top: 530px; width: 87px;"></div>
				<div class="line_sokolniki" data-idline="119" title="Пр-т Вернадского" style="left: 59px; top: 523px;"></div>
				<div class="line_sokolniki title" title="Юго-западная" style="left: 44px; top: 552px; width: 60px;"></div>
				<div class="line_sokolniki" data-idline="179" title="Юго-западная" style="left: 35px; top: 543px;"></div>
				<!-- Сокольническая линия -->

				<!-- Люблинская линия -->
				<div class="line_lyblino title" title="Марьина роща" style="left: 335px; top: 122px; width: 57px;"></div>
				<div class="line_lyblino" data-idline="78" title="Марьина роща" style="left: 323px; top: 122px;"></div>
				<div class="line_lyblino title" title="Достоевская" style="left: 335px; top: 141px; width: 50px;"></div>
				<div class="line_lyblino" data-idline="42" title="Достоевская" style="left: 323px; top: 141px;"></div>
				<div class="line_lyblino title" title="Трубная" style="left: 315px; top: 229px; width: 33px;"></div>
				<div class="line_lyblino" data-idline="153" title="Трубная" style="left: 304px; top: 229px;"></div>
				<div class="line_lyblino title" title="Сретенский бульвар" style="left: 364px; top: 231px; width: 48px; height: 17px;"></div>
				<div class="line_lyblino" data-idline="139" title="Сретенский бульвар" style="left: 382px; top: 246px;"></div>
				<div class="line_lyblino title" title="Чкаловская" style="left: 423px; top: 308px; width: 46px;"></div>
				<div class="line_lyblino" data-idline="173" title="Чкаловская" style="left: 462px; top: 299px;"></div>
				<div class="line_lyblino title" title="Римская" style="left: 531px; top: 356px; width: 39px;"></div>
				<div class="line_lyblino" data-idline="126" title="Римская" style="left: 519px; top: 356px;"></div>
				<div class="line_lyblino title" title="Крестьянская застава" style="left: 537px; top: 402px; width: 54px; height: 16px;"></div>
				<div class="line_lyblino" data-idline="64" title="Крестьянская застава" style="left: 525px; top: 414px;"></div>
				<div class="line_lyblino title" title="Дубровка" style="left: 537px; top: 464px; width: 39px;"></div>
				<div class="line_lyblino" data-idline="43" title="Дубровка" style="left: 525px; top: 464px;"></div>
				<div class="line_lyblino title" title="Кожуховская" style="left: 537px; top: 484px; width: 51px;"></div>
				<div class="line_lyblino" data-idline="55" title="Кожуховская" style="left: 525px; top: 484px;"></div>
				<div class="line_lyblino title" title="Печатники" style="left: 537px; top: 503px; width: 44px;"></div>
				<div class="line_lyblino" data-idline="109" title="Печатники" style="left: 525px; top: 503px;"></div>
				<div class="line_lyblino title" title="Волжская" style="left: 537px; top: 524px; width: 42px;"></div>
				<div class="line_lyblino" data-idline="33" title="Волжская" style="left: 525px; top: 524px;"></div>
				<div class="line_lyblino title" title="Люблино" style="left: 537px; top: 543px; width: 38px;"></div>
				<div class="line_lyblino" data-idline="76" title="Люблино" style="left: 525px; top: 543px;"></div>
				<div class="line_lyblino title" title="Братиславская" style="left: 537px; top: 563px; width: 57px;"></div>
				<div class="line_lyblino" data-idline="23" title="Братиславская" style="left: 525px; top: 563px;"></div>
				<div class="line_lyblino title" title="Марьино" style="left: 537px; top: 583px; width: 38px;"></div>
				<div class="line_lyblino" data-idline="79" title="Марьино" style="left: 525px; top: 583px;"></div>
				<div class="line_lyblino title" title="Борисово" style="left: 537px; top: 602px; width: 42px;"></div>
				<div class="line_lyblino" data-idline="182" title="Борисово" style="left: 525px; top: 602px;"></div>
				<div class="line_lyblino title" title="Шипиловская" style="left: 537px; top: 621px; width: 58px;"></div>
				<div class="line_lyblino" data-idline="184" title="Шипиловская" style="left: 525px; top: 621px;"></div>
				<div class="line_lyblino title" title="Зябликово" style="left: 537px; top: 640px; width: 47px;"></div>
				<div class="line_lyblino" data-idline="183" title="Зябликово" style="left: 525px; top: 641px;"></div>
				<!-- Люблинская линия -->

				<!-- Калининская линия -->
				<div class="line_kalinin title" title="Новокосино" style="left: 605px; top: 272px; width: 52px;"></div>
				<div class="line_kalinin" data-idline="188" title="Новокосино" style="left: 597px; top: 263px;"></div>
				<div class="line_kalinin title" title="Новогиреево" style="left: 592px; top: 287px; width: 52px;"></div>
				<div class="line_kalinin" data-idline="90" title="Новогиреево" style="left: 583px; top: 280px;"></div>
				<div class="line_kalinin title" title="Перово" style="left: 577px; top: 300px; width: 30px;"></div>
				<div class="line_kalinin" data-idline="107" title="Перово" style="left: 569px; top: 294px;"></div>
				<div class="line_kalinin title" title="Шоссе энтузиастов" style="left: 564px; top: 315px; width: 76px;"></div>
				<div class="line_kalinin" data-idline="175" title="Шоссе энтузиастов" style="left: 556px; top: 309px;"></div>
				<div class="line_kalinin title" title="Авиамоторная" style="left: 549px; top: 329px; width: 58px;"></div>
				<div class="line_kalinin" data-idline="1" title="Авиамоторная" style="left: 541px; top: 322px;"></div>
				<div class="line_kalinin title" title="Площадь Ильича" style="left: 532px; top: 344px; width: 72px;"></div>
				<div class="line_kalinin" data-idline="112" title="Площадь Ильича" style="left: 520px; top: 342px;"></div>
				<div class="line_kalinin title" title="Марксистская" style="left: 496px; top: 380px; width: 60px;"></div>
				<div class="line_kalinin" data-idline="77" title="Марксистская" style="left: 488px; top: 374px;"></div>
				<div class="line_kalinin title" title="Третьяковская" style="left: 376px; top: 431px;"></div>
				<div class="line_kalinin" data-idline="152" title="Третьяковская" style="left: 376px; top: 420px;"></div>
				<div class="line_kalinin title" style="left: 81px; top: 423px;" title="Парк победы"></div>
				<div class="line_kalinin" style="left: 81px; top: 412px;" title="Парк победы" data-idline="194"></div>
				<div class="line_kalinin title" style="left: 48px; top: 353px; width: 32px; height: 16px;" title="Деловой центр"></div>
				<div class="line_kalinin" style="left: 81px; top: 351px;" title="Деловой центр" data-idline="193"></div>
				<!-- Калининская линия -->

				<!-- Каховская линия -->
				<div class="line_kahov title" title="Каширская" style="left: 454px; top: 545px;"></div>
				<div class="line_kahov" data-idline="49" title="Каширская" style="left: 444px; top: 544px;"></div>
				<div class="line_kahov title" title="Варшавская" style="left: 355px; top: 578px; width: 52px;"></div>
				<div class="line_kahov" data-idline="28" title="Варшавская" style="left: 375px; top: 587px;"></div>
				<div class="line_kahov title" title="Каховская" style="left: 292px; top: 578px; width: 45px;"></div>
				<div class="line_kahov" data-idline="47" title="Каховская" style="left: 326px; top: 587px;"></div>
				<!-- Каховская линия -->

				<!-- Таганско-краснопресненская линия -->
				<div class="line_taganka title" title="Планерная" style="left: 89px; top: 107px; width: 47px;"></div>
				<div class="line_taganka" data-idline="111" title="Планерная" style="left: 78px; top: 107px;"></div>
				<div class="line_taganka title" title="Сходненская" style="left: 89px; top: 139px; width: 60px;"></div>
				<div class="line_taganka" data-idline="143" title="Сходненская" style="left: 78px; top: 139px;"></div>
				<div class="line_taganka title" title="Тушинская" style="left: 90px; top: 159px; width: 48px;"></div>
				<div class="line_taganka" data-idline="156" title="Тушинская" style="left: 78px; top: 159px;"></div>
				<div class="line_taganka title" title="Щукинская" style="left: 90px; top: 180px; width: 50px;"></div>
				<div class="line_taganka" data-idline="177" title="Щукинская" style="left: 78px; top: 180px;"></div>
				<div class="line_taganka title" title="Октябрьское поле" style="left: 90px; top: 200px; width: 76px;"></div>
				<div class="line_taganka" data-idline="97" title="Октябрьское поле" style="left: 78px; top: 200px;"></div>
				<div class="line_taganka title" title="Полежаевская" style="left: 90px; top: 219px; width: 60px;"></div>
				<div class="line_taganka" data-idline="114" title="Полежаевская" style="left: 78px; top: 219px;"></div>
				<div class="line_taganka title" title="Беговая" style="left: 98px; top: 244px; width: 36px;"></div>
				<div class="line_taganka" data-idline="15" title="Беговая" style="left: 93px; top: 249px;"></div>
				<div class="line_taganka title" title="Улица 1905 года" style="left: 124px; top: 262px; width: 39px; height: 16px;"></div>
				<div class="line_taganka" data-idline="157" title="Улица 1905 года" style="left: 139px; top: 277px;"></div>
				<div class="line_taganka title" title="Барикадная" style="left: 194px; top: 288px; width: 52px;"></div>
				<div class="line_taganka" data-idline="13" title="Барикадная" style="left: 189px; top: 278px;"></div>
				<div class="line_taganka title" title="Пушкинская" style="left: 256px; top: 290px; width: 50px;"></div>
				<div class="line_taganka" data-idline="123" title="Пушкинская" style="left: 276px; top: 278px;"></div>
				<div class="line_taganka title" title="Кузнецкий мост" style="left: 310px; top: 289px; width: 41px; height: 9px;"></div>
				<div class="line_taganka" data-idline="67" title="Кузнецкий мост" style="left: 344px; top: 278px;"></div>
				<div class="line_taganka title" title="Китай-город" style="left: 401px; top: 309px;"></div>
				<div class="line_taganka" data-idline="53" title="Китай-город" style="left: 401px; top: 309px;"></div>
				<div class="line_taganka title" title="Таганская" style="left: 432px; top: 373px; width: 38px;"></div>
				<div class="line_taganka" data-idline="145" title="Таганская" style="left: 470px; top: 380px;"></div>
				<div class="line_taganka title" title="Пролетарская" style="left: 459px; top: 426px; width: 55px;"></div>
				<div class="line_taganka" data-idline="118" title="Пролетарская" style="left: 515px; top: 424px;"></div>
				<div class="line_taganka title" title="Волгоградский проспект" style="left: 539px; top: 429px; width: 96px;"></div>
				<div class="line_taganka" data-idline="32" title="Волгоградский проспект" style="left: 542px; top: 437px;"></div>
				<div class="line_taganka title" title="Текстильщики" style="left: 579px; top: 444px; width: 58px;"></div>
				<div class="line_taganka" data-idline="148" title="Текстильщики" style="left: 571px; top: 451px;"></div>
				<div class="line_taganka title" title="Кузьминки" style="left: 593px; top: 458px; width: 48px;"></div>
				<div class="line_taganka" data-idline="68" title="Кузьминки" style="left: 585px; top: 465px;"></div>
				<div class="line_taganka title" title="Рязанский проспект" style="left: 607px; top: 500px; width: 41px; height: 15px;"></div>
				<div class="line_taganka" data-idline="127" title="Рязанский проспект" style="left: 595px; top: 499px;"></div>
				<div class="line_taganka title" title="Выхино" style="left: 606px; top: 525px; width: 32px;"></div>
				<div class="line_taganka" data-idline="37" title="Выхино" style="left: 594px; top: 525px;"></div>
				<div class="line_taganka title" style="left: 606px; top: 547px; width: 63px; height: 16px;" title="Лермонтовский проспект"></div>
				<div class="line_taganka" style="left: 594px; top: 545px;" title="Лермонтовский проспект" data-idline="189"></div>
				<div class="line_taganka title" style="left: 606px; top: 569px; width: 42px;" title="Жулебино"></div>
				<div class="line_taganka" style="left: 594px; top: 569px;" title="Жулебино" data-idline="190"></div>
				<!-- Таганско-краснопресненская линия -->

				<!-- Арбатско-покровская линия -->
				<div class="line_arbat title" title="Щелковская" style="left: 571px; top: 104px; width: 50px;"></div>
				<div class="line_arbat" data-idline="176" title="Щелковская" style="left: 559px; top: 104px;"></div>
				<div class="line_arbat title" title="Первомайская" style="left: 571px; top: 128px; width: 60px;"></div>
				<div class="line_arbat" data-idline="106" title="Первомайская" style="left: 560px; top: 128px;"></div>
				<div class="line_arbat title" title="Измайловская" style="left: 572px; top: 153px; width: 60px;"></div>
				<div class="line_arbat" data-idline="44" title="Измайловская" style="left: 560px; top: 153px;"></div>
				<div class="line_arbat title" title="Партизанская" style="left: 572px; top: 179px; width: 60px;"></div>
				<div class="line_arbat" data-idline="105" title="Партизанская" style="left: 560px; top: 179px;"></div>
				<div class="line_arbat title" title="Семеновская" style="left: 555px; top: 215px; width: 56px;"></div>
				<div class="line_arbat" data-idline="131" title="Семеновская" style="left: 548px; top: 208px;"></div>
				<div class="line_arbat title" title="Электрозаводская" style="left: 531px; top: 238px; width: 74px;"></div>
				<div class="line_arbat" data-idline="178" title="Электрозаводская" style="left: 523px; top: 230px;"></div>
				<div class="line_arbat title" title="Бауманская" style="left: 507px; top: 263px; width: 52px;"></div>
				<div class="line_arbat" data-idline="14" title="Бауманская" style="left: 499px; top: 255px;"></div>
				<div class="line_arbat title" title="Курская" style="left: 485px; top: 280px; width: 33px;"></div>
				<div class="line_arbat" data-idline="71" title="Курская" style="left: 470px; top: 283px;"></div>
				<div class="line_arbat title" title="Площадь революции" style="left: 350px; top: 359px; width: 56px;"></div>
				<div class="line_arbat" data-idline="113" title="Площадь революции" style="left: 342px; top: 350px;"></div>
				<div class="line_arbat title" title="Арбатская" style="left: 217px; top: 355px; width: 41px; height: 7px;"></div>
				<div class="line_arbat" data-idline="8" title="Арбатская" style="left: 255px; top: 362px;"></div>
				<div class="line_arbat title" title="Смоленская" style="left: 195px; top: 372px; width: 43px;"></div>
				<div class="line_arbat" data-idline="134" title="Смоленская" style="left: 237px; top: 376px;"></div>
				<div class="line_arbat title" title="Киевская" style="left: 151px; top: 388px; width: 37px; height: 7px;"></div>
				<div class="line_arbat" data-idline="50" title="Киевская" style="left: 170px; top: 399px;"></div>
				<div class="line_arbat title" title="Парк победы" style="left: 95px; top: 412px; width: 33px; height: 19px;"></div>
				<div class="line_arbat" data-idline="103" title="Парк победы" style="left: 94px; top: 398px;"></div>
				<div class="line_arbat title" title="Славянский бульвар" style="left: 31px; top: 411px; width: 45px; height: 16px;"></div>
				<div class="line_arbat" data-idline="133" title="Славянский бульвар" style="left: 48px; top: 399px;"></div>
				<div class="line_arbat title" title="Кунцевская" style="left: 17px; top: 233px; width: 50px;"></div>
				<div class="line_arbat" data-idline="69" title="Кунцевская" style="left: 4px; top: 233px;"></div>
				<div class="line_arbat title" title="Молодежная" style="left: 14px; top: 211px; width: 52px;"></div>
				<div class="line_arbat" data-idline="85" title="Молодежная" style="left: 4px; top: 211px;"></div>
				<div class="line_arbat title" title="Крылатское" style="left: 15px; top: 188px; width: 48px;"></div>
				<div class="line_arbat" data-idline="66" title="Крылатское" style="left: 4px; top: 188px;"></div>
				<div class="line_arbat title" title="Строгино" style="left: 16px; top: 166px; width: 40px;"></div>
				<div class="line_arbat" data-idline="140" title="Строгино" style="left: 4px; top: 166px;"></div>
				<div class="line_arbat title" title="Мякинино" style="left: 17px; top: 145px; width: 40px;"></div>
				<div class="line_arbat" data-idline="86" title="Мякинино" style="left: 4px; top: 144px;"></div>
				<div class="line_arbat title" title="Волоколамская" style="left: 15px; top: 124px; width: 61px;"></div>
				<div class="line_arbat" data-idline="34" title="Волоколамская" style="left: 4px; top: 124px;"></div>
				<div class="line_arbat title" title="Митино" style="left: 17px; top: 103px; width: 30px;"></div>
				<div class="line_arbat" data-idline="84" title="Митино" style="left: 4px; top: 103px;"></div>
				<div class="line_arbat title" title="Пятницкое шоссе" style="width: 44px; top: 75px; left: 16px; height: 20px;"></div>
				<div class="line_arbat" data-idline="186" title="Пятницкое шоссе" style="top: 76px; left: 4px;"></div>
				<!-- Арбатско-покровская линия -->

				<!-- Филевская линия -->
				<div class="line_fili title" title="Кунцевская" style="left: 27px; top: 245px;"></div>
				<div class="line_fili" data-idline="70" title="Кунцевская" style="left: 17px; top: 246px;"></div>
				<div class="line_fili title" title="Пионерская" style="left: 28px; top: 265px; width: 50px;"></div>
				<div class="line_fili" data-idline="110" title="Пионерская" style="left: 17px; top: 265px;"></div>
				<div class="line_fili title" title="Филевский парк" style="left: 28px; top: 282px; width: 68px;"></div>
				<div class="line_fili" data-idline="164" title="Филевский парк" style="left: 17px; top: 282px;"></div>
				<div class="line_fili title" title="Багратионовская" style="left: 27px; top: 300px; width: 76px;"></div>
				<div class="line_fili" data-idline="12" title="Багратионовская" style="left: 17px; top: 300px;"></div>
				<div class="line_fili title" title="Фили" style="left: 28px; top: 316px; width: 20px;"></div>
				<div class="line_fili" data-idline="165" title="Фили" style="left: 17px; top: 316px;"></div>
				<div class="line_fili title" title="Кутузовская" style="left: 29px; top: 333px; width: 49px;"></div>
				<div class="line_fili" data-idline="73" title="Кутузовская" style="left: 16px; top: 333px;"></div>
				<div class="line_fili title" title="Студенческая" style="left: 30px; top: 386px; width: 53px;"></div>
				<div class="line_fili" data-idline="141" title="Студенческая" style="left: 48px; top: 374px;"></div>
				<div class="line_fili title" title="Международная" style="left: 109px; top: 318px; width: 65px;"></div>
				<div class="line_fili" data-idline="82" title="Международная" style="left: 97px; top: 318px;"></div>
				<div class="line_fili title" title="Выставочная" style="left: 108px; top: 351px; width: 56px;"></div>
				<div class="line_fili" data-idline="36" title="Выставочная" style="left: 95px; top: 351px;"></div>
				<div class="line_fili title" title="Киевская" style="left: 171px; top: 375px;"></div>
				<div class="line_fili" data-idline="51" title="Киевская" style="left: 171px; top: 375px;"></div>
				<div class="line_fili title" title="Смоленская" style="left: 197px; top: 364px; width: 47px; height: 7px;"></div>
				<div class="line_fili" data-idline="135" title="Смоленская" style="left: 192px; top: 356px;"></div>
				<div class="line_fili title" title="Арбатская" style="left: 219px; top: 349px; width: 41px; height: 5px;"></div>
				<div class="line_fili" data-idline="9" title="Арбатская" style="left: 252px; top: 337px;"></div>
				<div class="line_fili title" title="Александровский сад" style="left: 214px; top: 322px; width: 69px; height: 16px"></div>
				<div class="line_fili" data-idline="4" title="Александровский сад" style="left: 281px; top: 337px;"></div>
				<!-- Филевская линия -->

				<!-- Бутовская линия -->
				<div class="line_butovo title" title="Улица Старокачаловская" style="left: 97px; top: 654px; width: 64px;"></div>
				<div class="line_butovo" data-idline="161" title="Улица Старокачаловская" style="left: 159px; top: 662px;"></div>
				<div class="line_butovo title" title="Улица Скобелевская" style="left: 89px; top: 684px; width: 49px;"></div>
				<div class="line_butovo" data-idline="160" title="Улица Скобелевская" style="left: 108px; top: 673px;"></div>
				<div class="line_butovo title" title="Бульвар адм.Ушакова" style="left: 39px; top: 662px; width: 57px;"></div>
				<div class="line_butovo" data-idline="25" title="Бульвар адм.Ушакова" style="left: 66px; top: 673px;"></div>
				<div class="line_butovo title" title="Улица Горчакова" style="left: 29px; top: 683px; width: 36px;"></div>
				<div class="line_butovo" data-idline="158" title="Улица Горчакова" style="left: 41px; top: 673px; z-index: 1;"></div>
				<div class="line_butovo title" title="Бунинская аллея" style="left: 0px; top: 662px; width: 37px;"></div>
				<div class="line_butovo" data-idline="26" title="Бунинская аллея" style="left: 14px; top: 673px;"></div>
				<div class="line_butovo title" style="left: 176px; top: 659px; width: 49px;" title="Лесопарковая"></div>
				<div class="line_butovo" style="left: 168px; top: 653px;" title="Лесопарковая" data-idline="191"></div>
				<div class="line_butovo title" style="left: 121px; top: 638px; width: 61px;" title="Битцевский парк"></div>
				<div class="line_butovo" style="left: 181px; top: 638px;" title="Битцевский парк" data-idline="192"></div>
				<!-- Бутовская линия -->

				<!-- Кольцевая линия -->
				<div class="line_ring title" title="Краснопресненская" style="left: 185px; top: 301px; width: 78px;"></div>
				<div class="line_ring" data-idline="61" title="Краснопресненская" style="left: 176px; top: 291px;"></div>
				<div class="line_ring title" title="Белорусская" style="left: 209px; top: 220px;"></div>
				<div class="line_ring" data-idline="17" title="Белорусская" style="left: 209px; top: 220px;"></div>
				<div class="line_ring title" title="Новослободская" style="left: 259px; top: 191px; width: 64px;"></div>
				<div class="line_ring" data-idline="92" title="Новослободская" style="left: 257px; top: 181px;"></div>
				<div class="line_ring title" title="Проспект мира" style="left: 384px; top: 176px;"></div>
				<div class="line_ring" data-idline="120" title="Проспект мира" style="left: 384px; top: 176px;"></div>
				<div class="line_ring title" title="Комсомольская" style="left: 445px; top: 220px;"></div>
				<div class="line_ring" data-idline="57" title="Комсомольская" style="left: 445px; top: 220px;"></div>
				<div class="line_ring title" title="Курская" style="left: 485px; top: 280px; width: 38px;"></div>
				<div class="line_ring" data-idline="72" title="Курская" style="left: 478px; top: 291px;"></div>
				<div class="line_ring title" title="Таганская" style="left: 432px; top: 373px; width: 38px;"></div>
				<div class="line_ring" data-idline="144" title="Таганская" style="left: 471px; top: 368px;"></div>
				<div class="line_ring title" title="Павелецкая" style="left: 436px; top: 440px; width: 51px;"></div>
				<div class="line_ring" data-idline="102" title="Павелецкая" style="left: 426px; top: 432px;"></div>
				<div class="line_ring title" title="Добрынинская" style="left: 267px; top: 476px; width: 57px;"></div>
				<div class="line_ring" data-idline="40" title="Добрынинская" style="left: 325px; top: 469px;"></div>
				<div class="line_ring title" title="Октябрьская" style="left: 280px; top: 451px; width: 50px;"></div>
				<div class="line_ring" data-idline="95" title="Октябрьская" style="left: 271px; top: 459px;"></div>
				<div class="line_ring title" title="Парк культуры" style="left: 242px; top: 425px; width: 37px; height: 16px;"></div>
				<div class="line_ring" data-idline="185" title="Парк культуры" style="left: 232px; top: 436px;"></div>
				<div class="line_ring title" title="Киевская" style="left: 151px; top: 388px; width: 37px; height: 7px;"></div>
				<div class="line_ring" data-idline="52" title="Киевская" style="left: 192px; top: 387px;"></div>
				<!-- Кольцевая линия -->

			</div>

		</div>
	</div>
</div>