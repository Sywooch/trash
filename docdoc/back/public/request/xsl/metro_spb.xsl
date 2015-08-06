<?xml version='1.0'  encoding="utf-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:output method="html" encoding="utf-8"/>

	
	<xsl:template name="metroScemSpb">
		<div class="mapOverlay" id="mapOverlay" sonclick="closeMapPopup(); return false">
		<div id="mapPopupIn">
			<a href="#" id="closePopup" onclick="closeMapPopup(); return false" title="Закрыть" class="closePopup"></a>
			<div id="mapPopupContent">
		
		<div style="width: 920px;">
			<div style="position: absolute; right: 0; margin: 0 20px 0 0">
				<img src="/img/common/clBtBig.gif" width="20" height="20"  alt="закрыть" title="закрыть"  onclick="closeMapPopup()" style="cursor: pointer" border="0"/>
			</div>



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
                        <div></div>
                    </div>
                    <a id="metro-clear-act" href="#" style="display: none;">очистить все выбранные</a>
                    <div style="margin-top: 20px">
                        <!-- <span class="form" style="padding: 3px 10px 2px 10px; margin: 0 0 0px 0px;" onclick="clousePopup();">ОТМЕНИТЬ</span> -->
                        <div class="form" style="width: 120px; padding: 3px 15px 2px 15px; margin: 0 0 0px 0px;" onclick="setMetroFilter($stationsSelectName);closeMapPopup()">ВЫБРАТЬ</div>
                    </div>
                </div>

                <div id="map_spb" class="metro-map">

                    <div class="map-image">

                        <div style="background:url(/img/common/metro-spb.png) no-repeat; width:726px; height:578px;"></div>

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
                        <div style="left: 124px; top: 347px; width: 124px;" title="Технологический ин-т" class="line_kv title" data-idline="198"></div>
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
                        <div style="left: 507px; top: 295px; width: 156px;" title="Пл. Александра Невского-1" class="line_nv title" data-idline="227"></div>
                        <div style="left: 505px; top: 351px; width: 61px;" title="Елизаровская" class="line_nv title" data-idline="228"></div>
                        <div style="left: 505px; top: 374px; width: 69px;" title="Ломоносовская" class="line_nv title" data-idline="229"></div>
                        <div style="left: 505px; top: 398px; width: 61px;" title="Пролетарская" class="line_nv title" data-idline="230"></div>
                        <div style="left: 505px; top: 422px; width: 40px;" title="Обухово" class="line_nv title" data-idline="231"></div>
                        <div style="left: 505px; top: 445px; width: 42px;" title="Рыбацкое" class="line_nv title" data-idline="232"></div>

                        <!-- Правобережная линия -->
                        <div style="left: 208px; top: 331px; width: 45px;" title="Спасская" class="line_pr title" data-idline="233"></div>
                        <div style="left: 388px; top: 309px; width: 60px;" title="Достоевская" class="line_pr title" data-idline="234"></div>
                        <div style="left: 414px; top: 330px; width: 45px; height: 21px;" title="Лиговский проспект" class="line_pr title" data-idline="235"></div>
                        <div style="left: 507px; top: 309px; width: 156px;" title="Пл. Александра Невского-2" class="line_pr title" data-idline="236"></div>
                        <div style="left: 519px; top: 329px; width: 72px;" title="Новочеркасская" class="line_pr title" data-idline="237"></div>
                        <div style="left: 623px; top: 351px; width: 47px;" title="Ладожская" class="line_pr title" data-idline="238"></div>
                        <div style="left: 623px; top: 374px; width: 101px;" title="Проспект Большевиков" class="line_pr title" data-idline="239"></div>
                        <div style="left: 623px; top: 398px; width: 67px;" title="Улица Дыбенко" class="line_pr title" data-idline="240"></div>

                        <!-- Фрунзенская линия -->
                        <div style="left: 150px; top: 110px; width: 65px; height: 21px;" title="Комендантский пр-т" class="line_fr title" data-idline="241"></div>
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



		</div>
		
		</div>
		</div>
		</div>
	</xsl:template>
</xsl:transform>

