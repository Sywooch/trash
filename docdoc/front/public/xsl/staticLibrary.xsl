<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:import href="hotBanners.xsl" />
    <xsl:import href="specList.xsl" />

    <xsl:output method="html" encoding="utf-8"/>

    <xsl:template match="root">
        <xsl:call-template name="staticLibrary" />
    </xsl:template>

    <xsl:template name="staticLibrary">
    <div class="l-wrapper">
    <div id="content" class="page_static">

        <div class="right" style="margin-top: -15px;">

        <xsl:call-template name="specList" />

        <xsl:call-template name="hotBanners" />

        </div>
        <div class="box-left library">
            <div class="doctor"></div>
            <div class="h1Size">Справочник пациента</div>
            <ul class="lib-list">
                <div id="yw0" class="list-view">
                    <div class="items">
                    <li>
                        <a href="library/akusherstvo">Акушерство</a>
                        (2)
                    </li><li>
                        <a href="library/allergologiya">Аллергология</a>
                        (5)
                    </li><li>
                        <a href="library/andrologiia">Андрология</a>
                        (2)
                    </li><li>
                        <a href="library/anestesiologia">Анестезиология</a>
                        (2)
                    </li><li>
                        <a href="library/venerologiya">Венерология</a>
                        (5)
                    </li><li>
                        <a href="library/gastroenterologiya">Гастроэнтерология</a>
                        (7)
                    </li><li>
                        <a href="library/gematologia">Гематология</a>
                        (2)
                    </li><li>
                        <a href="library/gepatologia">Гепатология</a>
                        (2)
                    </li><li>
                        <a href="library/ginekologiya">Гинекология</a>
                        (7)
                    </li><li>
                        <a href="library/gomeopatia">Гомеопатия</a>
                        (2)
                    </li><li>
                        <a href="library/dermatologiya">Дерматология</a>
                        (4)
                    </li><li>
                        <a href="library/dietologiia">Диетология</a>
                        (3)
                    </li><li>
                        <a href="library/immunologia">Иммунология</a>
                        (2)
                    </li><li>
                        <a href="library/kardiologiya">Кардиология</a>
                        (6)
                    </li><li>
                        <a href="library/kosmetologiia">Косметология</a>
                        (2)
                    </li><li>
                        <a href="library/logopediia">Логопедия</a>
                        (2)
                    </li><li>
                        <a href="library/mammologiya">Маммология</a>
                        (3)
                    </li><li>
                        <a href="library/manualnaiia_terapiia">Мануальная терапия</a>
                        (2)
                    </li><li>
                        <a href="library/massazh">Массаж</a>
                        (2)
                    </li><li>
                        <a href="library/narkologiia">Наркология</a>
                        (2)
                    </li><li>
                        <a href="library/nevrologiya">Неврология</a>
                        (4)
                    </li><li>
                        <a href="library/nefrologia">Нефрология</a>
                        (2)
                    </li><li>
                        <a href="library/onkologiia">Онкология</a>
                        (2)
                    </li><li>
                        <a href="library/ortopediia">Ортопедия</a>
                        (2)
                    </li><li>
                        <a href="library/otolaringologiia">Отоларингология</a>
                        (3)
                    </li><li>
                        <a href="library/oftalmologiia">Офтальмология</a>
                        (3)
                    </li><li>
                        <a href="library/pediatriya">Педиатрия</a>
                        (7)
                    </li><li>
                        <a href="library/plasticheskaiia_khirurgiia">Пластическая хирургия</a>
                        (2)
                    </li><li>
                        <a href="library/proktologiia">Проктология</a>
                        (2)
                    </li><li>
                        <a href="library/psikhiatriia">Психиатрия</a>
                        (2)
                    </li><li>
                        <a href="library/psikhologiia">Психология</a>
                        (2)
                    </li><li>
                        <a href="library/psikhoterapiia">Психотерапия</a>
                        (2)
                    </li><li>
                        <a href="library/pulmonologia">Пульмонология</a>
                        (2)
                    </li><li>
                        <a href="library/revmatologia">Ревматология</a>
                        (2)
                    </li><li>
                        <a href="library/reproduktologiia">Репродуктология</a>
                        (2)
                    </li><li>
                        <a href="library/seksologia">Сексология</a>
                        (2)
                    </li><li>
                        <a href="library/stomatologiya">Стоматология</a>
                        (6)
                    </li><li>
                        <a href="library/terapevtiya">Терапия</a>
                        (4)
                    </li><li>
                        <a href="library/trikhologiia">Трихология</a>
                        (2)
                    </li><li>
                        <a href="library/uz_diagnostika">УЗ-диагностика</a>
                        (2)
                    </li><li>
                        <a href="library/urologiya">Урология</a>
                        (8)
                    </li><li>
                        <a href="library/flebologiya">Флебология</a>
                        (5)
                    </li><li>
                        <a href="library/khirurgiia">Хирургия</a>
                        (2)
                    </li><li>
                        <a href="library/endokrinologiia">Эндокринология</a>
                        (2)
                    </li></div><div class="keys" style="display:none" title="/library"><span>42</span><span>24</span><span>26</span><span>52</span><span>16</span><span>17</span><span>51</span><span>54</span><span>15</span><span>47</span><span>18</span><span>31</span><span>48</span><span>14</span><span>32</span><span>49</span><span>20</span><span>33</span><span>46</span><span>34</span><span>13</span><span>53</span><span>30</span><span>35</span><span>29</span><span>28</span><span>21</span><span>36</span><span>43</span><span>37</span><span>38</span><span>39</span><span>50</span><span>55</span><span>44</span><span>56</span><span>10</span><span>12</span><span>40</span><span>45</span><span>11</span><span>22</span><span>41</span><span>27</span></div>
                </div>				</ul>
            <div class="h1Size">Новые статьи</div>
            <div class="articles">
                <div id="yw1" class="list-view">
                    <div class="items">
                        <div class="news-item" style="display: block;">
                            <h2><a href="/library/disbakterioz-kishechnika">
                                Дисбактериоз кишечника</a></h2>
                            <p>Организм человека – очень сложная и слаженная система, для нормальной работы которой важно поддержание внутреннего баланса. Но у организма есть и помощники – полезные бактерии, живущие в кишечнике. Они помогают переваривать пищу, получать питательные вещества, более того, они необходимы для работы иммунной системы и защищают организм от распространения вредных, патогенных бактерий. Однако иногда баланс микроорганизмов нарушается, что приводит к неприятным последствиям – развивается дисбактериоз.</p></div><div class="news-item" style="display: block;">
                        <h2><a href="/library/kak-delaut-rentgen">
                            Как делают рентген</a></h2>
                        <p>С помощью рентгена могут быть изучены почти все органы тела – костно-суставная система, сердце, легкие, органы малого таза и т.д. Чаще всего рентген используют в стоматологии и травматологии. Чтобы сделать рентгеновский снимок более информативным, иногда применяют искусственное контрастирование органов. Для этого в кровь пациента вводят водорастворимые йодсодержащие вещества (этот прием используется, например, для изучения состояния почек и мочевых путей, кровеносных сосудов). После процедуры контраст выводится естественным путем.</p></div><div class="news-item" style="display: block;">
                        <h2><a href="/library/kogda-delat-3d-uzi">
                            Когда делать 3D УЗИ</a></h2>
                        <p>Ультразвуковое исследование (в том числе 3D УЗИ) во время беременности проводится минимум три раза. Это плановое обследование, и проводится оно в профилактических целях – для раннего выявления осложнений беременности и патологий развития плода.</p></div><div class="news-item" style="display: block;">
                        <h2><a href="/library/pitanie-pri-disbakterioze">
                            Питание при дисбактериозе кишечника</a></h2>
                        <p>Дисбактериоз кишечника проявляется в тот или иной момент у большинства взрослых людей и практически у всех детей. Причин, вызывающих это состояние может быть множество и, конечно, уберечь себя от всех причин развития дисбактериоза просто невозможно. На состоянии кишечной микрофлоры может отразиться и экология, и хронический стресс, и различные заболевания и даже эмоциональное состояние. Однако здоровье человека, и особенно состояние пищеварительной системы, во многом зависит от того, что он ест. Лучшим способом профилактики дисбактериоза является полноценное, сбалансированное питание.</p></div><div class="news-item" style="display: block;">
                        <h2><a href="/library/podgotovka-k-kolonoskopii">
                            Подготовка к колоноскопии</a></h2>
                        <p>Решение о необходимости проведения колоноскопии принимает врач в зависимости от клинических задач (подтверждение диагноза, забор материала для исследования и т.д.). Обследование позволяет осмотреть весь кишечник.</p></div><div class="news-item" style="display:none;">
                        <h2><a href="/library/podgotovka-k-uzi-malogo-taza">
                            Подготовка к  узи малого таза</a></h2>
                        <p>Обследование каждого органа имеет свои особенности. В профилактических целях желательно ежегодно проводить УЗИ органов малого таза. Подготовка к исследованию, как правило, не сложная.</p></div><div class="news-item" style="display:none;">
                        <h2><a href="/library/priznaki-disbakterioza-kishtchnika">
                            Признаки дисбактериоза кишечника</a></h2>
                        <p>Дисбактериоз кишечника – состояние, при котором нарушается состав нормальной микрофлоры кишечника, что может приводить к появлению неприятных и даже опасных симптомов. Последствия нарушения баланса могут сказаться не только на функции пищеварительной системы, но и на общем состоянии организма. Симптомы дисбактериоза зависят от стадии заболевания.</p></div><div class="news-item" style="display:none;">
                        <h2><a href="/library/uzi-novorojdennomu">
                            УЗИ новорожденному</a></h2>
                        <p>Ультразвуковое исследование (УЗИ) безболезненно, высокоинформативно и не требует от пациента особой подготовки. В основе метода УЗИ лежит принцип эхо-локации; ультразвук не несет никакой лучевой нагрузки на организм и абсолютно безопасен. Поэтому УЗИ не имеет возрастных ограничений и проводится даже новорожденным.</p></div><div class="news-item" style="display:none;">
                        <h2><a href="/library/mrt">
                            Все о магнитно-резонансной томографии</a></h2>
                        <p>Магнитно-резонансная томография применяется для исследования отдельных органов и тканей путем магнитного резонанса. Томография  позволяет изучить внутреннюю структуру объекта посредством его просвечивания под разным углом. Принцип действия МРТ основан на взаимодействии тела человека и магнитного поля.</p></div></div><div class="keys" style="display:none" title="/library"><span>6</span><span>7</span><span>8</span><span>11</span><span>12</span><span>13</span><span>14</span><span>16</span><span>18</span></div>
                </div>
            </div>
            <div class="more"><a id="more">Еще</a></div>
        </div>
    </div>
    </div>
    </xsl:template>

</xsl:stylesheet>