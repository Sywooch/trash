 <div class="js-popup popup" data-popup-id="js-popup-geo">
    <div class="js-tabs">
        <ul class="js-tabs-controls">
            <li class="js-tabs-control s-active">Карта метро</li>
            <li class="js-tabs-control" data-stat="tabListStations">Список станций</li>
            <li class="js-tabs-control" data-stat="tabListRegions">Список районов</li>
        </ul>
        <div class="js-tabs-tab">
            <form action="" id="extended_search_form_act" class="sf_form zf zf-inited" onsubmit="return false">
                <div class="">
                    <div class="">
                        <div class="popup_geo_controls"><div class="popup_geo_title h2">Выберите станции метро</div></div>
                        <input class="ex_location_map_trigger_metro s-hidden" type="checkbox" name="location_trigger" value="location_metro" checked rel="moscow">
                        <div id="metro" class="metro_section_map ex_location_type" style="display: block;">
                            <div class="metro_top_controls">
                                <ul class="als_metro_circle_triggers">
                                    <li class="metro_top_controls_item"><input class="ui-autocomplete-input metro_filter" placeholder="Поиск по названию"></li>
                                    <li class="metro_top_controls_item">
                                        <span class="als_metro_select_inside i-metroctrl_circleinner l-ib">
                                            <span class="pseudo">Выделить станции внутри кольца</span>
                                        </span>
                                    </li>
                                    <li class="metro_top_controls_item">
                                        <span class="als_metro_select_circle i-metroctrl_circle l-ib">
                                            <span class="pseudo">Выделить кольцевые станции</span>
                                        </span>
                                    </li>
                                    <li class="metro_top_controls_item">
                                        <span class="als_metro_deselect i-metroctrl_remove" style="display: none;">
                                            <span class="pseudo">удалить выбранные станции</span>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                            <div class="metrobox ex_location_type opened" style="display: block;">
                                <div class="als_metro">
                                    <div style="width: 900px; height: 961px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="js-tabs-tab">
            <noindex>
                <ul class="stations_list columns_4 metro_list_stations">
                    <li class="column">
                        <ul class="stations_list_group">
                            <li data-station-id="1" class="stations_list_item js-stationselect js-geoselect">Авиамоторная</li>
                            <li data-station-id="2" class="stations_list_item js-stationselect js-geoselect">Автозаводская</li>
                            <li data-station-id="3" class="stations_list_item js-stationselect js-geoselect">Академическая</li>
                            <li data-station-id="4" class="stations_list_item js-stationselect js-geoselect">Александровский сад</li>
                            <li data-station-id="5" class="stations_list_item js-stationselect js-geoselect">Алексеевская</li>
                            <li data-station-id="252" class="stations_list_item js-stationselect js-geoselect">Алма-Атинская</li>
                            <li data-station-id="6" class="stations_list_item js-stationselect js-geoselect">Алтуфьево</li>
                            <li data-station-id="7" class="stations_list_item js-stationselect js-geoselect">Аннино</li>
                            <li data-station-id="8" class="stations_list_item js-stationselect js-geoselect">Арбатская</li>
                            <li data-station-id="9" class="stations_list_item js-stationselect js-geoselect">Арбатская</li>
                            <li data-station-id="10" class="stations_list_item js-stationselect js-geoselect">Аэропорт</li>
                        </ul>
                        <ul class="stations_list_group">
                            <li data-station-id="11" class="stations_list_item js-stationselect js-geoselect">Бабушкинская</li>
                            <li data-station-id="12" class="stations_list_item js-stationselect js-geoselect">Багратионовская</li>
                            <li data-station-id="13" class="stations_list_item js-stationselect js-geoselect">Баррикадная</li>
                            <li data-station-id="14" class="stations_list_item js-stationselect js-geoselect">Бауманская</li>
                            <li data-station-id="15" class="stations_list_item js-stationselect js-geoselect">Беговая</li>
                            <li data-station-id="16" class="stations_list_item js-stationselect js-geoselect">Белорусская</li>
                            <li data-station-id="17" class="stations_list_item js-stationselect js-geoselect s-hidden">Белорусская</li>
                            <li data-station-id="18" class="stations_list_item js-stationselect js-geoselect">Беляево</li>
                            <li data-station-id="19" class="stations_list_item js-stationselect js-geoselect">Бибирево</li>
                            <li data-station-id="20" class="stations_list_item js-stationselect js-geoselect">Библиотека им. Ленина</li>
                            <li data-station-id="182" class="stations_list_item js-stationselect js-geoselect">Борисово</li>
                            <li data-station-id="21" class="stations_list_item js-stationselect js-geoselect">Боровицкая</li>
                            <li data-station-id="22" class="stations_list_item js-stationselect js-geoselect">Ботанический сад</li>
                            <li data-station-id="23" class="stations_list_item js-stationselect js-geoselect">Братиславская</li>
                            <li data-station-id="25" class="stations_list_item js-stationselect js-geoselect">Бульвар Адм. Ушакова</li>
                            <li data-station-id="24" class="stations_list_item js-stationselect js-geoselect">Бульвар Дм. Донского</li>
							<li data-station-id="159" class="stations_list_item js-stationselect js-geoselect">Бульвар Рокоссовского</li>
                            <li data-station-id="26" class="stations_list_item js-stationselect js-geoselect">Бунинская аллея</li>
                        </ul>
                        <ul class="stations_list_group">
                            <li data-station-id="28" class="stations_list_item js-stationselect js-geoselect">Варшавская</li>
                            <li data-station-id="27" class="stations_list_item js-stationselect js-geoselect">ВДНХ</li>
                            <li data-station-id="29" class="stations_list_item js-stationselect js-geoselect">Владыкино</li>
                            <li data-station-id="30" class="stations_list_item js-stationselect js-geoselect">Водный стадион</li>
                            <li data-station-id="31" class="stations_list_item js-stationselect js-geoselect">Войковская</li>
                            <li data-station-id="32" class="stations_list_item js-stationselect js-geoselect">Волгоградский пр-т</li>
                            <li data-station-id="33" class="stations_list_item js-stationselect js-geoselect">Волжская</li>
                            <li data-station-id="34" class="stations_list_item js-stationselect js-geoselect">Волоколамская</li>
                            <li data-station-id="35" class="stations_list_item js-stationselect js-geoselect">Воробьевы горы</li>
                            <li data-station-id="36" class="stations_list_item js-stationselect js-geoselect">Выставочная</li>
                            <li data-station-id="37" class="stations_list_item js-stationselect js-geoselect">Выхино</li>
                        </ul>
                        <ul class="stations_list_group">
                            <li data-station-id="38" class="stations_list_item js-stationselect js-geoselect">Динамо</li>
                            <li data-station-id="39" class="stations_list_item js-stationselect js-geoselect">Дмитровская</li>
                            <li data-station-id="40" class="stations_list_item js-stationselect js-geoselect">Добрынинская</li>
                            <li data-station-id="41" class="stations_list_item js-stationselect js-geoselect">Домодедовская</li>
                            <li data-station-id="42" class="stations_list_item js-stationselect js-geoselect">Достоевская</li>
                            <li data-station-id="43" class="stations_list_item js-stationselect js-geoselect">Дубровка</li>
                        </ul>
                        <ul class="stations_list_group">
                            <li data-station-id="183" class="stations_list_item js-stationselect js-geoselect">Зябликово</li>
                        </ul>
                        <ul class="stations_list_group">
                            <li data-station-id="44" class="stations_list_item js-stationselect js-geoselect">Измайловская</li>
                        </ul>
                    </li>
                    <li class="column">
                        <ul class="stations_list_group">
                            <li data-station-id="45" class="stations_list_item js-stationselect js-geoselect">Калужская</li>
                            <li data-station-id="46" class="stations_list_item js-stationselect js-geoselect">Кантемировская</li>
                            <li data-station-id="47" class="stations_list_item js-stationselect js-geoselect">Каховская</li>
                            <li data-station-id="48" class="stations_list_item js-stationselect js-geoselect">Каширская</li>
                            <li data-station-id="49" class="stations_list_item js-stationselect js-geoselect s-hidden">Каширская</li>
                            <li data-station-id="50" class="stations_list_item js-stationselect js-geoselect">Киевская</li>
                            <li data-station-id="51" class="stations_list_item js-stationselect js-geoselect s-hidden">Киевская</li>
                            <li data-station-id="52" class="stations_list_item js-stationselect js-geoselect s-hidden">Киевская</li>
                            <li data-station-id="53" class="stations_list_item js-stationselect js-geoselect">Китай-город</li>
                            <li data-station-id="54" class="stations_list_item js-stationselect js-geoselect s-hidden">Китай-город</li>
                            <li data-station-id="55" class="stations_list_item js-stationselect js-geoselect">Кожуховская</li>
                            <li data-station-id="56" class="stations_list_item js-stationselect js-geoselect">Коломенская</li>
                            <li data-station-id="57" class="stations_list_item js-stationselect js-geoselect">Комсомольская</li>
                            <li data-station-id="58" class="stations_list_item js-stationselect js-geoselect s-hidden">Комсомольская</li>
                            <li data-station-id="59" class="stations_list_item js-stationselect js-geoselect">Коньково</li>
                            <li data-station-id="60" class="stations_list_item js-stationselect js-geoselect">Красногвардейская</li>
                            <li data-station-id="61" class="stations_list_item js-stationselect js-geoselect">Краснопресненская</li>
                            <li data-station-id="62" class="stations_list_item js-stationselect js-geoselect">Красносельская</li>
                            <li data-station-id="63" class="stations_list_item js-stationselect js-geoselect">Красные Ворота</li>
                            <li data-station-id="64" class="stations_list_item js-stationselect js-geoselect">Крестьянская застава</li>
                            <li data-station-id="65" class="stations_list_item js-stationselect js-geoselect">Кропоткинская</li>
                            <li data-station-id="66" class="stations_list_item js-stationselect js-geoselect">Крылатское</li>
                            <li data-station-id="67" class="stations_list_item js-stationselect js-geoselect">Кузнецкий мост</li>
                            <li data-station-id="68" class="stations_list_item js-stationselect js-geoselect">Кузьминки</li>
                            <li data-station-id="69" class="stations_list_item js-stationselect js-geoselect">Кунцевская</li>
                            <li data-station-id="70" class="stations_list_item js-stationselect js-geoselect s-hidden">Кунцевская</li>
                            <li data-station-id="71" class="stations_list_item js-stationselect js-geoselect">Курская</li>
                            <li data-station-id="72" class="stations_list_item js-stationselect js-geoselect s-hidden">Курская</li>
                            <li data-station-id="73" class="stations_list_item js-stationselect js-geoselect">Кутузовская</li>
                        </ul>
                        <ul class="stations_list_group">
                            <li data-station-id="74" class="stations_list_item js-stationselect js-geoselect">Ленинский проспект</li>
                            <li data-station-id="75" class="stations_list_item js-stationselect js-geoselect">Лубянка</li>
                            <li data-station-id="76" class="stations_list_item js-stationselect js-geoselect">Люблино</li>
                        </ul>
                        <ul class="stations_list_group">
                            <li data-station-id="77" class="stations_list_item js-stationselect js-geoselect">Марксистская</li>
                            <li data-station-id="78" class="stations_list_item js-stationselect js-geoselect">Марьина Роща</li>
                            <li data-station-id="79" class="stations_list_item js-stationselect js-geoselect">Марьино</li>
                            <li data-station-id="80" class="stations_list_item js-stationselect js-geoselect">Маяковская</li>
                            <li data-station-id="81" class="stations_list_item js-stationselect js-geoselect">Медведково</li>
                            <li data-station-id="82" class="stations_list_item js-stationselect js-geoselect">Международная</li>
                            <li data-station-id="83" class="stations_list_item js-stationselect js-geoselect">Менделеевская</li>
                            <li data-station-id="84" class="stations_list_item js-stationselect js-geoselect">Митино</li>
                            <li data-station-id="85" class="stations_list_item js-stationselect js-geoselect">Молодежная</li>
                            <li data-station-id="86" class="stations_list_item js-stationselect js-geoselect">Мякинино</li>
                        </ul>
                        <ul class="stations_list_group">
                            <li data-station-id="87" class="stations_list_item js-stationselect js-geoselect">Нагатинская</li>
                            <li data-station-id="88" class="stations_list_item js-stationselect js-geoselect">Нагорная</li>
                            <li data-station-id="89" class="stations_list_item js-stationselect js-geoselect">Нахимовский проспект</li>
                            <li data-station-id="90" class="stations_list_item js-stationselect js-geoselect">Новогиреево</li>
                            <li data-station-id="251" class="stations_list_item js-stationselect js-geoselect">Новокосино</li>
                            <li data-station-id="91" class="stations_list_item js-stationselect js-geoselect">Новокузнецкая</li>
                            <li data-station-id="92" class="stations_list_item js-stationselect js-geoselect">Новослободская</li>
                            <li data-station-id="93" class="stations_list_item js-stationselect js-geoselect">Новоясеневская</li>
                            <li data-station-id="94" class="stations_list_item js-stationselect js-geoselect">Новые Черемушки</li>
                        </ul>
                    </li>
                    <li class="column">
                        <ul class="stations_list_group">
                            <li data-station-id="95" class="stations_list_item js-stationselect js-geoselect">Октябрьская</li>
                            <li data-station-id="96" class="stations_list_item js-stationselect js-geoselect s-hidden">Октябрьская</li>
                            <li data-station-id="97" class="stations_list_item js-stationselect js-geoselect">Октябрьское поле</li>
                            <li data-station-id="98" class="stations_list_item js-stationselect js-geoselect">Орехово</li>
                            <li data-station-id="99" class="stations_list_item js-stationselect js-geoselect">Отрадное</li>
                            <li data-station-id="100" class="stations_list_item js-stationselect js-geoselect">Охотный ряд</li>
                        </ul>
                        <ul class="stations_list_group">
                            <li data-station-id="101" class="stations_list_item js-stationselect js-geoselect">Павелецкая</li>
                            <li data-station-id="102" class="stations_list_item js-stationselect js-geoselect s-hidden">Павелецкая</li>
                            <li data-station-id="104" class="stations_list_item js-stationselect js-geoselect">Парк культуры</li>
                            <li data-station-id="185" class="stations_list_item js-stationselect js-geoselect s-hidden">Парк культуры</li>
                            <li data-station-id="103" class="stations_list_item js-stationselect js-geoselect">Парк Победы</li>
                            <li data-station-id="105" class="stations_list_item js-stationselect js-geoselect">Партизанская</li>
                            <li data-station-id="106" class="stations_list_item js-stationselect js-geoselect">Первомайская</li>
                            <li data-station-id="107" class="stations_list_item js-stationselect js-geoselect">Перово</li>
                            <li data-station-id="108" class="stations_list_item js-stationselect js-geoselect">Петровско-Разумовская</li>
                            <li data-station-id="109" class="stations_list_item js-stationselect js-geoselect">Печатники</li>
                            <li data-station-id="110" class="stations_list_item js-stationselect js-geoselect">Пионерская</li>
                            <li data-station-id="111" class="stations_list_item js-stationselect js-geoselect">Планерная</li>
                            <li data-station-id="112" class="stations_list_item js-stationselect js-geoselect">Площадь Ильича</li>
                            <li data-station-id="113" class="stations_list_item js-stationselect js-geoselect">Площадь Революции</li>
                            <li data-station-id="114" class="stations_list_item js-stationselect js-geoselect">Полежаевская</li>
                            <li data-station-id="115" class="stations_list_item js-stationselect js-geoselect">Полянка</li>
                            <li data-station-id="116" class="stations_list_item js-stationselect js-geoselect">Пражская</li>
                            <li data-station-id="117" class="stations_list_item js-stationselect js-geoselect">Преображенская пл.</li>
                            <li data-station-id="118" class="stations_list_item js-stationselect js-geoselect">Пролетарская</li>
                            <li data-station-id="119" class="stations_list_item js-stationselect js-geoselect">Проспект Вернадского</li>
                            <li data-station-id="120" class="stations_list_item js-stationselect js-geoselect">Проспект мира</li>
                            <li data-station-id="121" class="stations_list_item js-stationselect js-geoselect s-hidden">Проспект мира</li>
                            <li data-station-id="122" class="stations_list_item js-stationselect js-geoselect">Профсоюзная</li>
                            <li data-station-id="123" class="stations_list_item js-stationselect js-geoselect">Пушкинская</li>
                            <li data-station-id="253" class="stations_list_item js-stationselect js-geoselect">Пятницкое шоссе</li>
                        </ul>
                        <ul class="stations_list_group">
                            <li data-station-id="124" class="stations_list_item js-stationselect js-geoselect">Речной вокзал</li>
                            <li data-station-id="125" class="stations_list_item js-stationselect js-geoselect">Рижская</li>
                            <li data-station-id="126" class="stations_list_item js-stationselect js-geoselect">Римская</li>
                            <li data-station-id="127" class="stations_list_item js-stationselect js-geoselect">Рязанский проспект</li>
                        </ul>
                        <ul class="stations_list_group">
                            <li data-station-id="128" class="stations_list_item js-stationselect js-geoselect">Савеловская</li>
                            <li data-station-id="129" class="stations_list_item js-stationselect js-geoselect">Свиблово</li>
                            <li data-station-id="130" class="stations_list_item js-stationselect js-geoselect">Севастопольская</li>
                            <li data-station-id="131" class="stations_list_item js-stationselect js-geoselect">Семеновская</li>
                            <li data-station-id="132" class="stations_list_item js-stationselect js-geoselect">Серпуховская</li>
                            <li data-station-id="133" class="stations_list_item js-stationselect js-geoselect">Славянский бульвар</li>
                            <li data-station-id="134" class="stations_list_item js-stationselect js-geoselect">Смоленская</li>
                            <li data-station-id="135" class="stations_list_item js-stationselect js-geoselect s-hidden">Смоленская</li>
                            <li data-station-id="136" class="stations_list_item js-stationselect js-geoselect">Сокол</li>
                            <li data-station-id="137" class="stations_list_item js-stationselect js-geoselect">Сокольники</li>
							<li data-station-id="325" class="stations_list_item js-stationselect js-geoselect">Спартак</li>
                            <li data-station-id="138" class="stations_list_item js-stationselect js-geoselect">Спортивная</li>
                            <li data-station-id="139" class="stations_list_item js-stationselect js-geoselect">Сретенский бульвар</li>
                            <li data-station-id="140" class="stations_list_item js-stationselect js-geoselect">Строгино</li>
                            <li data-station-id="141" class="stations_list_item js-stationselect js-geoselect">Студенческая</li>
                            <li data-station-id="142" class="stations_list_item js-stationselect js-geoselect">Сухаревская</li>
                            <li data-station-id="143" class="stations_list_item js-stationselect js-geoselect">Сходненская</li>
                        </ul>
                    </li>
                    <li class="column">
                        <ul class="stations_list_group">
                            <li data-station-id="144" class="stations_list_item js-stationselect js-geoselect">Таганская</li>
                            <li data-station-id="145" class="stations_list_item js-stationselect js-geoselect s-hidden">Таганская</li>
                            <li data-station-id="146" class="stations_list_item js-stationselect js-geoselect">Тверская</li>
                            <li data-station-id="147" class="stations_list_item js-stationselect js-geoselect">Театральная</li>
                            <li data-station-id="148" class="stations_list_item js-stationselect js-geoselect">Текстильщики</li>
                            <li data-station-id="149" class="stations_list_item js-stationselect js-geoselect">Теплый стан</li>
                            <li data-station-id="150" class="stations_list_item js-stationselect js-geoselect">Тимирязевская</li>
                            <li data-station-id="151" class="stations_list_item js-stationselect js-geoselect">Третьяковская</li>
                            <li data-station-id="152" class="stations_list_item js-stationselect js-geoselect s-hidden">Третьяковская</li>
                            <li data-station-id="153" class="stations_list_item js-stationselect js-geoselect">Трубная</li>
                            <li data-station-id="154" class="stations_list_item js-stationselect js-geoselect">Тульская</li>
                            <li data-station-id="155" class="stations_list_item js-stationselect js-geoselect">Тургеневская</li>
                            <li data-station-id="156" class="stations_list_item js-stationselect js-geoselect">Тушинская</li>
                        </ul>
                        <ul class="stations_list_group">
                            <li data-station-id="162" class="stations_list_item js-stationselect js-geoselect">Ул. Академика Янгеля</li>
                            <li data-station-id="161" class="stations_list_item js-stationselect js-geoselect">Ул. Старокачаловская</li>
                            <li data-station-id="157" class="stations_list_item js-stationselect js-geoselect">Улица 1905 года</li>
                            <li data-station-id="158" class="stations_list_item js-stationselect js-geoselect">Улица Горчакова</li>
                            <li data-station-id="160" class="stations_list_item js-stationselect js-geoselect">Улица Скобелевская</li>
                            <li data-station-id="163" class="stations_list_item js-stationselect js-geoselect">Университет</li>
                        </ul>
                        <ul class="stations_list_group">
                            <li data-station-id="164" class="stations_list_item js-stationselect js-geoselect">Филевский парк</li>
                            <li data-station-id="165" class="stations_list_item js-stationselect js-geoselect">Фили</li>
                            <li data-station-id="166" class="stations_list_item js-stationselect js-geoselect">Фрунзенская</li>
                        </ul>
                        <ul class="stations_list_group">
                            <li data-station-id="167" class="stations_list_item js-stationselect js-geoselect">Царицыно</li>
                            <li data-station-id="168" class="stations_list_item js-stationselect js-geoselect">Цветной бульвар</li>
                        </ul>
                        <ul class="stations_list_group">
                            <li data-station-id="169" class="stations_list_item js-stationselect js-geoselect">Черкизовская</li>
                            <li data-station-id="170" class="stations_list_item js-stationselect js-geoselect">Чертановская</li>
                            <li data-station-id="171" class="stations_list_item js-stationselect js-geoselect">Чеховская</li>
                            <li data-station-id="172" class="stations_list_item js-stationselect js-geoselect">Чистые пруды</li>
                            <li data-station-id="173" class="stations_list_item js-stationselect js-geoselect">Чкаловская</li>
                        </ul>
                        <ul class="stations_list_group">
                            <li data-station-id="174" class="stations_list_item js-stationselect js-geoselect">Шаболовская</li>
                            <li data-station-id="184" class="stations_list_item js-stationselect js-geoselect">Шипиловская</li>
                            <li data-station-id="175" class="stations_list_item js-stationselect js-geoselect">Шоссе Энтузиастов</li>
                        </ul>
                        <ul class="stations_list_group">
                            <li data-station-id="176" class="stations_list_item js-stationselect js-geoselect">Щелковская</li>
                            <li data-station-id="177" class="stations_list_item js-stationselect js-geoselect">Щукинская</li>
                        </ul>
                        <ul class="stations_list_group">
                            <li data-station-id="178" class="stations_list_item js-stationselect js-geoselect">Электрозаводская</li>
                        </ul>
                        <ul class="stations_list_group">
                            <li data-station-id="179" class="stations_list_item js-stationselect js-geoselect">Юго-западная</li>
                            <li data-station-id="180" class="stations_list_item js-stationselect js-geoselect">Южная</li>
                        </ul>
                        <ul class="stations_list_group">
                            <li data-station-id="181" class="stations_list_item js-stationselect js-geoselect">Ясенево</li>
                        </ul>
                    </li>
                </ul>
            </noindex>
        </div>
        <div class="js-tabs-tab">
            <noindex>
                <ul class="regions_list columns_4 metro_list_geo">
                    <li class="column">
                        <ul class="stations_list_group">
                            <li>
                                <span class="regions_list_group_title js-regionselect js-regionselect-whole js-geoselect" data-station-id-array="4,8,9,20,21,134,135,14,53,54,63,71,72,75,131,139,155,172,173,178,40,91,101,102,132,151,152,57,58,62,63,139,155,42,67,78,120,121,142,153,168,13,15,36,61,82,157,64,77,112,118,126,144,16,17,20,21,42,53,54,75,80,83,92,100,113,123,146,147,168,171,35,65,104,138,166,95,96,115">ЦАО</span>
                                <ul class="regions_sublist">
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="4,8,9,20,21,134,135">Арбат</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="14,53,54,63,71,72,75,131,139,155,172,173,178">Басманный</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="40,91,101,102,132,151,152">Замоскворечье</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="57,58,62,63,139,155">Красносельский</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="42,67,78,120,121,142,153,168">Мещанский</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="13,15,36,61,82,157">Пресненский</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="64,77,112,118,126,144">Таганский</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="16,17,20,21,42,53,54,75,80,83,92,100,113,123,146,147,168,171">Тверской</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="35,65,104,138,166">Хамовники</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="95,96,115">Якиманка</li>
                                </ul>
                            </li>
                        </ul>
                        <ul class="stations_list_group">
                            <li>
                                <span class="regions_list_group_title js-regionselect js-regionselect-whole js-geoselect" data-station-id-array="10,38,136,16,17,38,29,31,30,29,29,19,29,30,124,111,39,128,136,108,150,124,10,15,114">САО</span>
                                <ul class="regions_sublist">
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="10,38,136">Аэропорт</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="16,17,38">Беговой</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="29">Бескудниковский</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="31">Войковский</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="30">Головинский</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="29">Восточное Дегунино</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="29">Западное Дегунино</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="19">Дмитровский</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="29,30">Коптево</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="124">Левобережный</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="111">Молжаниновский</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="39,128">Савёловский</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="136">Сокол</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="108,150">Тимирязевский</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="124">Ховрино</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="10,15,114">Хорошёвский</li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li class="column">
                        <ul class="stations_list_group">
                            <li>
                                <span class="regions_list_group_title js-regionselect js-regionselect-whole js-geoselect" data-station-id-array="5,27,19,11,19,39,128,150,6,11,29,108,78,125,128,81,11,27,29,99,22,22,129,6,129">СВАО</span>
                                <ul class="regions_sublist">
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="5,27">Алексеевский</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="19">Алтуфьевский</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="11">Бабушкинский</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="19">Бибирево</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="39,128,150">Бутырский</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="6">Лианозово</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="11">Лосиноостровский</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="29,108">Марфино</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="78,125,128">Марьина роща</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="81">Северное Медведково</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="11">Южное Медведково</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="27">Останкинский</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="29,99">Отрадное</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="22">Ростокино</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="22,129">Свиблово</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="6">Северный</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="129">Ярославский</li>
                                </ul>
                            </li>
                        </ul>
                        <ul class="stations_list_group">
                            <li>
                                <span class="regions_list_group_title js-regionselect js-regionselect-whole js-geoselect" data-station-id-array="159,37,176,176,90,106,44,105,106,176,37,159,90,107,37,107,175,117,169,131,175,178,137">ВАО</span>
                                <ul class="regions_sublist">
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="159">Богородское</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="37">Вешняки</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="176">Восточный</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="176">Гольяново</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="90">Ивановское</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="106">Восточное Измайлово</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="44,105,106">Измайлово</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="176">Северное Измайлово</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="37">Косино-Ухтомский</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="159">Метрогородок</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="90,107">Новогиреево</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="37">Новокосино</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="107,175">Перово</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="117,169">Преображенское</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="131,175,178">Соколиная гора</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="137">Сокольники</li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li class="column">
                        <ul class="stations_list_group">
                            <li>
                                <span class="regions_list_group_title js-regionselect js-regionselect-whole js-geoselect" data-station-id-array="37,23,68,1,33,76,23,79,37,32,55,109,148,127,33,148,55,109,148">ЮВАО</span>
                                <ul class="regions_sublist">
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="37">Выхино-Жулебино</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="23">Капотня</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="68">Кузьминки</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="1">Лефортово</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="33,76">Люблино</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="23,79">Марьино</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="37">Некрасовка</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="32">Нижегородский</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="55,109,148">Печатники</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="127">Рязанский</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="33,148">Текстильщики</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="55,109,148">Южнопортовый</li>
                                </ul>
                            </li>
                        </ul>
                        <ul class="stations_list_group">
                            <li>
                                <span class="regions_list_group_title js-regionselect js-regionselect-whole js-geoselect" data-station-id-array="167,162,182,2,154,74,174,60,61,183,184,28,48,49,48,49,56,56,28,87,88,41,98,41,46,167,170,180,116,180,7,162">ЮАО</span>
                                <ul class="regions_sublist">
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="167">Восточное Бирюлёво</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="162">Западное Бирюлёво</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="182">Братеево</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="2,154">Даниловский</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="74,174">Донской</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="60,61,183,184">Зябликово</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="28,48,49">Москворечье-Сабурово</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="48,49,56">Нагатино-Садовники</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="56">Нагатинский затон</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="28,87,88">Нагорный</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="41,98">Северное Орехово-Борисово</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="41">Южное Орехово-Борисово</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="46,167">Царицыно</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="170,180">Северное Чертаново</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="116,180">Центральное Чертаново</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="7,162">Южное Чертаново</li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li class="column">
                        <ul class="stations_list_group">
                            <li>
                                <span class="regions_list_group_title js-regionselect js-regionselect-whole js-geoselect" data-station-id-array="3,122,24,161,25,26,158,160,74,163,47,89,130,170,18,45,59,87,88,89,119,122,45,94,59,149,45,94,122,93,149,181">ЮЗАО</span>
                                <ul class="regions_sublist">
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="3,122">Академический</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="24,161">Северное Бутово</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="25,26,158,160">Южное Бутово</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="74,163">Гагаринский</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="47,89,130,170">Зюзино</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="18,45,59">Коньково</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="87,88,89">Котловка</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="119,122">Ломоносовский</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="45,94">Обручевский</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="59,149">Тёплый Стан</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="45,94,122">Черёмушки</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="93,149,181">Ясенево</li>
                                </ul>
                            </li>
                        </ul>
                        <ul class="stations_list_group">
                            <li>
                                <span class="regions_list_group_title js-regionselect js-regionselect-whole js-geoselect" data-station-id-array="179,50,51,52,73,103,141,66,69,70,85,69,70,179,133,119,35,163,119,179,12,164,165,69,70,110,133,164">ЗАО</span>
                                <ul class="regions_sublist">
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="179">Внуково</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="50,51,52,73,103,141">Дорогомилово</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="66">Крылатское</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="69,70,85">Кунцево</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="69,70">Можайский</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="179">Ново-Переделкино</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="133">Очаково-Матвеевское</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="119">Проспект Вернадского</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="35,163">Раменки</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="119">Солнцево</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="179">Тропарёво-Никулино</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="12,164,165">Филёвский парк</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="69,70,110,133,164">Фили-Давыдково</li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li class="column">
                        <ul class="stations_list_group">
                            <li>
                                <span class="regions_list_group_title js-regionselect js-regionselect-whole js-geoselect" data-station-id-array="111,34,84,156,140,111,143,143,114,97,177">СЗАО</span>
                                <ul class="regions_sublist">
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="111">Куркино</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="34,84">Митино</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="156">Покровское-Стрешнево</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="140">Строгино</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="111,143">Северное Тушино</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="143">Южное Тушино</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="114">Хорошёво-Мневники</li>
                                    <li class="stations_list_item js-regionselect js-geoselect" data-station-id-array="97,177">Щукино</li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                </ul>
            </noindex>
        </div>
    </div>
    <div id="metroControls" class="metro_section_controls">
        <div class="metro_list_selected">
            <span class="metro_selected_title l-b">Выбраны станции:</span><div class="ex_location_list metro_selected" style="display: none; height: auto;"></div>
        </div>
        <div class="metro_list_selected_actions">
            <div class="ui-btn ui-teal input_metro_submit" data-related-form="search_form">Найти клинику</div>
            <span class="pseudo als_metro_deselect mtm" style="display: none;">удалить выбранные станции</span>
        </div>
    </div>
</div>