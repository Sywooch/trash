<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:import href="hotBanners.xsl" />
    <xsl:import href="specList.xsl" />

    <xsl:output method="html" encoding="utf-8"/>

    <xsl:template match="root">
        <xsl:call-template name="staticSickList" />
    </xsl:template>

    <xsl:template name="staticSickList">
        <div class="l-wrapper">
        <div id="content" class="page_static">

        <div class="right" style="margin-top: -15px;">

            <xsl:call-template name="specList" />

            <xsl:call-template name="hotBanners" />

        </div>
        <div class="box-left">
            <div class="illness-box">
                <div class="doctor"></div>
                <div class="round">
                    <h1>Справочник заболеваний</h1>

                    <div style="margin:20px 0 10px; font-size:16px;">А</div>
                    <div>
                        <a href="/illness/adenoma_prostaty" title="аденома простаты">Аденома простаты</a>
                        / 					<a href="/illness/adenomioz" title="аденомиоз">Аденомиоз</a>
                        / 					<a href="/illness/adneksit" title="аднексит">Аднексит</a>
                        / 					<a href="/illness/alopetsiia" title="алопеция">Алопеция</a>
                        / 					<a href="/illness/alveolit" title="альвеолит">Альвеолит</a>
                        / 					<a href="/illness/amenoreia" title="аменорея">Аменорея</a>
                        / 					<a href="/illness/analnaya_treshina" title="анальная трещина">Анальная трещина</a>
                        / 					<a href="/illness/analnij_zud" title="анальный зуд">Анальный зуд</a>
                        / 					<a href="/illness/anafilakticheskij_shok" title="анафилактический шок">Анафилактический шок</a>
                        / 					<a href="/illness/angina" title="ангина">Ангина</a>
                        / 					<a href="/illness/aritmiia_serdtsa" title="аритмия сердца">Аритмия сердца</a>
                        / 					<a href="/illness/astigmatizm" title="астигматизм">Астигматизм</a>
                        / 					<a href="/illness/ateroma" title="атерома">Атерома</a>
                        / 					<a href="/illness/ateroskleroz" title="атеросклероз">Атеросклероз</a>
                    </div>
                    <div style="margin:20px 0 10px; font-size:16px;">Б</div>
                    <div>
                        <a href="/illness/balanit" title="баланит">Баланит</a>
                        / 					<a href="/illness/muzhskoe_besplodie" title="бесплодие у мужчин">Бесплодие у мужчин</a>
                        / 					<a href="/illness/blizorukost" title="близорукость">Близорукость</a>
                        / 					<a href="/illness/bolezn_krona" title="болезнь крона">Болезнь Крона</a>
                        / 					<a href="/illness/borodavki" title="бородавки">Бородавки</a>
                        / 					<a href="/illness/borodavki_podoshvennie" title="бородавки подошвенные">Бородавки подошвенные</a>
                        / 					<a href="/illness/bronkhialnaya_astma" title="бронхиальная астма">Бронхиальная астма</a>
                        / 					<a href="/illness/bronkhit" title="бронхит">Бронхит</a>
                        / 					<a href="/illness/obstruktivnij_bronkhit" title="бронхит обструктивный">Бронхит обструктивный</a>
                    </div>
                    <div style="margin:20px 0 10px; font-size:16px;">В</div>
                    <div>
                        <a href="/illness/vaginit" title="вагинит">Вагинит</a>
                        / 					<a href="/illness/varikoz" title="варикозное расширение вен">Варикозное расширение вен</a>
                        / 					<a href="/illness/varikotsele" title="варикоцеле">Варикоцеле</a>
                        / 					<a href="/illness/vegetososudistaya_distonia" title="вегетососудистая дистония (всд)">Вегетососудистая дистония (ВСД)</a>
                        / 					<a href="/illness/vezikulit" title="везикулит">Везикулит</a>
                        / 					<a href="/illness/venoznaya_nedostatochnost" title="венозная недостаточность">Венозная недостаточность</a>
                        / 					<a href="/illness/vetryanka" title="ветрянка">Ветрянка</a>
                        / 					<a href="/illness/virus_pappilomi_cheloveka" title="вирус папилломы человека">Вирус папилломы человека</a>
                        / 					<a href="/illness/vich" title="вич">ВИЧ</a>
                        / 					<a href="/illness/vulvit" title="вульвит">Вульвит</a>
                        / 					<a href="/illness/vulvovaginit" title="вульвовагинит">Вульвовагинит</a>
                        / 					<a href="/illness/vipadenie_pryamoj_kishki" title="выпадение прямой кишки">Выпадение прямой кишки</a>
                    </div>
                    <div style="margin:20px 0 10px; font-size:16px;">Г</div>
                    <div>
                        <a href="/illness/gaimorit" title="гайморит">Гайморит</a>
                        / 					<a href="/illness/gastrit" title="гастрит">Гастрит</a>
                        / 					<a href="/illness/atroficheskij_gastrit" title="гастрит атрофический">Гастрит атрофический</a>
                        / 					<a href="/illness/poverkhnostnij_gastrit" title="гастрит поверхностный">Гастрит поверхностный</a>
                        / 					<a href="/illness/erozivnij_gastrit" title="гастрит эрозивный">Гастрит эрозивный</a>
                        / 					<a href="/illness/gemorroy" title="геморрой">Геморрой</a>
                        / 					<a href="/illness/gepatit_c" title="гепатит с">Гепатит С</a>
                        / 					<a href="/illness/gerpes" title="герпес">Герпес</a>
                        / 					<a href="/illness/ginekomasiia" title="гинекомастия">Гинекомастия</a>
                        / 					<a href="/illness/giperkaltsiemiya" title="гиперкальциемия">Гиперкальциемия</a>
                        / 					<a href="/illness/giperparatireoz" title="гиперпаратиреоз">Гиперпаратиреоз</a>
                        / 					<a href="/illness/giperprolaktinemiia" title="гиперпролактинемия">Гиперпролактинемия</a>
                        / 					<a href="/illness/gipertireoz" title="гипертиреоз">Гипертиреоз</a>
                        / 					<a href="/illness/gipertonicheskii_kriz" title="гипертонический криз">Гипертонический криз</a>
                        / 					<a href="/illness/gipogonadizm" title="гипогонадизм">Гипогонадизм</a>
                        / 					<a href="/illness/gipokaltsiemiya" title="гипокальциемия">Гипокальциемия</a>
                        / 					<a href="/illness/glaukoma" title="глаукома">Глаукома</a>
                        / 					<a href="/illness/gonoreia" title="гонорея">Гонорея</a>
                        / 					<a href="/illness/gribok_kozhi" title="грибок кожи">Грибок кожи</a>
                        / 					<a href="/illness/gripp" title="грипп">Грипп</a>
                    </div>
                    <div style="margin:20px 0 10px; font-size:16px;">Д</div>
                    <div>
                        <a href="/illness/dalnozorkost" title="дальнозоркость">Дальнозоркость</a>
                        / 					<a href="/illness/dermatit" title="дерматит">Дерматит</a>
                        / 					<a href="/illness/allergicheslii_dermatit" title="дерматит аллергический">Дерматит аллергический</a>
                        / 					<a href="/illness/atopicheskij_dermatit" title="дерматит атопический">Дерматит атопический</a>
                        / 					<a href="/illness/kontaktnyi_dermatit" title="дерматит контактный">Дерматит контактный</a>
                        / 					<a href="/illness/pelenochnyi_dermatit" title="дерматит пеленочный">Дерматит пеленочный</a>
                        / 					<a href="/illness/perioralnyi_dermatit" title="дерматит периоральный">Дерматит периоральный</a>
                        / 					<a href="/illness/Diabeticheskay_stopa" title="диабетическая стопа">Диабетическая стопа</a>
                        / 					<a href="/illness/diareia" title="диарея">Диарея</a>
                        / 					<a href="/illness/disbakterioz_kishechnika_vzrosllye" title="дисбактериоз кишечника у взрослых">Дисбактериоз кишечника у взрослых</a>
                        / 					<a href="/illness/disbakterioz_kishechnika_detskii" title="дисбактериоз кишечника у детей">Дисбактериоз кишечника у детей</a>
                        / 					<a href="/illness/displazia_sustavov" title="дисплазия суставов">Дисплазия суставов</a>
                        / 					<a href="/illness/displazia_sustavov_tazobedrennix" title="дисплазия тазобедренных суставов">Дисплазия тазобедренных суставов</a>
                        / 					<a href="/illness/duodenit" title="дуоденит">Дуоденит</a>
                    </div>
                    <div style="margin:20px 0 10px; font-size:16px;">Ж</div>
                    <div>
                        <a href="/illness/djelchekamennaya_bolezn" title="желчекаменная болезнь">Желчекаменная болезнь</a>
                    </div>
                    <div style="margin:20px 0 10px; font-size:16px;">З</div>
                    <div>
                        <a href="/illness/zapor" title="запор">Запор</a>
                        / 					<a href="/illness/zob" title="зоб">Зоб</a>
                        / 					<a href="/illness/zob_diffuznij" title="зоб диффузный">Зоб диффузный</a>
                        / 					<a href="/illness/kistoznij_zob" title="зоб кистозный">Зоб кистозный</a>
                        / 					<a href="/illness/zob_uzlovoj" title="зоб узловой">Зоб узловой</a>
                        / 					<a href="/illness/Zubnoy_kamen" title="зубной камень">Зубной камень</a>
                    </div>
                    <div style="margin:20px 0 10px; font-size:16px;">И</div>
                    <div>
                        <a href="/illness/immunodefitsit" title="иммунодефицит">Иммунодефицит</a>
                        / 					<a href="/illness/impotentsia" title="импотенция">Импотенция</a>
                        / 					<a href="/illness/insult" title="инсульт">Инсульт</a>
                        / 					<a href="/illness/infarkt_miokarda" title="инфаркт миокарда">Инфаркт миокарда</a>
                        / 					<a href="/illness/ishemicheskaiia_bolezn_serdtsa" title="ишемическая болезнь сердца">Ишемическая болезнь сердца</a>
                    </div>
                    <div style="margin:20px 0 10px; font-size:16px;">К</div>
                    <div>
                        <a href="/illness/kamni_v_pochkax" title="камни в почках">Камни в почках</a>
                        / 					<a href="/illness/karbunkul" title="карбункул">Карбункул</a>
                        / 					<a href="/illness/katarakta" title="катаракта">Катаракта</a>
                        / 					<a href="/illness/kista_iaichnika" title="киста яичника">Киста яичника</a>
                        / 					<a href="/illness/kolit" title="колит">Колит</a>
                        / 					<a href="/illness/conjunctivit" title="конъюнктивит">Конъюнктивит</a>
                        / 					<a href="/illness/kor" title="корь">Корь</a>
                        / 					<a href="/illness/kosoglazie" title="косоглазие">Косоглазие</a>
                        / 					<a href="/illness/kosolapost" title="косолапость">Косолапость</a>
                        / 					<a href="/illness/krapivnica" title="крапивница">Крапивница</a>
                        / 					<a href="/illness/krasnukha" title="краснуха">Краснуха</a>
                        / 					<a href="/illness/krivosheya" title="кривошея">Кривошея</a>
                    </div>
                    <div style="margin:20px 0 10px; font-size:16px;">Л</div>
                    <div>
                        <a href="/illness/laringit" title="ларингит">Ларингит</a>
                        / 					<a href="/illness/legochnij_fibroz" title="лёгочный фиброз">Лёгочный фиброз</a>
                        / 					<a href="/illness/Lechenie_zubov" title="лечение зубов">Лечение зубов</a>
                        / 					<a href="/illness/limfogranulematoz" title="лимфогранулематоз">Лимфогранулематоз</a>
                        / 					<a href="/illness/lipoma" title="липома">Липома</a>
                        / 					<a href="/illness/lishai" title="лишай">Лишай</a>
                        / 					<a href="/illness/lobkovyi_perikulez" title="лобковый педикулез">Лобковый педикулез</a>
                        / 					<a href="/illness/logjnij_krup" title="ложный круп">Ложный круп</a>
                    </div>
                    <div style="margin:20px 0 10px; font-size:16px;">М</div>
                    <div>
                        <a href="/illness/mastit" title="мастит">Мастит</a>
                        / 					<a href="/illness/mezhpozvonochnaya_grizha" title="межпозвоночная грыжа">Межпозвоночная грыжа</a>
                        / 					<a href="/illness/meningit" title="менингит">Менингит</a>
                        / 					<a href="/illness/migren" title="мигрень">Мигрень</a>
                        / 					<a href="/illness/mikoplazmoz" title="микоплазмоз">Микоплазмоз</a>
                        / 					<a href="/illness/miokardit" title="миокардит">Миокардит</a>
                        / 					<a href="/illness/mioma_matki" title="миома матки">Миома матки</a>
                        / 					<a href="/illness/molochnica" title="молочница">Молочница</a>
                        / 					<a href="/illness/mochekamennaya_bolezn" title="мочекаменная болезнь">Мочекаменная болезнь</a>
                    </div>
                    <div style="margin:20px 0 10px; font-size:16px;">Н</div>
                    <div>
                        <a href="/illness/narushenie_menstrualnogo_cikla" title="нарушение менструального цикла">Нарушение менструального цикла</a>
                        / 					<a href="/illness/nevralgiya" title="невралгия">Невралгия</a>
                        / 					<a href="/illness/nevralgia_sedalishnogo_nerva" title="невралгия седалищного нерва">Невралгия седалищного нерва</a>
                        / 					<a href="/illness/nevralgia_trojnichnogo_nerva" title="невралгия тройничного нерва">Невралгия тройничного нерва</a>
                        / 					<a href="/illness/nevrozi" title="неврозы">Неврозы</a>
                        / 					<a href="/illness/nederdzanie_mochi" title="недержание мочи">Недержание мочи</a>
                        / 					<a href="/illness/neirodermit" title="нейродермит">Нейродермит</a>
                        / 					<a href="/illness/nefroptoz" title="нефроптоз">Нефроптоз</a>
                    </div>
                    <div style="margin:20px 0 10px; font-size:16px;">О</div>
                    <div>
                        <a href="/illness/ozhirenie" title="ожирение">Ожирение</a>
                        / 					<a href="/illness/orkhit" title="орхит">Орхит</a>
                        / 					<a href="/illness/osteoartroz" title="остеоартроз">Остеоартроз</a>
                        / 					<a href="/illness/osteoporoz" title="остеопороз">Остеопороз</a>
                        / 					<a href="/illness/osteokhondroz" title="остеохондроз">Остеохондроз</a>
                        / 					<a href="/illness/kresttsovij_osteokhondroz" title="остеохондроз крестцовый">Остеохондроз крестцовый</a>
                        / 					<a href="/illness/ostekhondroz_poyasnichnij" title="остеохондроз поясничный">Остеохондроз поясничный</a>
                        / 					<a href="/illness/otek_kvinke" title="отек квинке">Отек Квинке</a>
                        / 					<a href="/illness/otit" title="отит">Отит</a>
                        / 					<a href="/illness/gnojnij_otit" title="отит гнойный">Отит гнойный</a>
                    </div>
                    <div style="margin:20px 0 10px; font-size:16px;">П</div>
                    <div>
                        <a href="/illness/pankreatit" title="панкреатит">Панкреатит</a>
                        / 					<a href="/illness/paraproktit" title="парапроктит">Парапроктит</a>
                        / 					<a href="/illness/perikardit" title="перикардит">Перикардит</a>
                        / 					<a href="/illness/pielonefrit" title="пиелонефрит">Пиелонефрит</a>
                        / 					<a href="/illness/pishevaya_allergia" title="пищевая аллергия">Пищевая аллергия</a>
                        / 					<a href="/illness/plevrit" title="плеврит">Плеврит</a>
                        / 					<a href="/illness/plechelopatochnij_periartroz" title="плечелопаточный периартроз">Плечелопаточный периартроз</a>
                        / 					<a href="/illness/pnevmonia" title="пневмония">Пневмония</a>
                        / 					<a href="/illness/polikistoz_pochek" title="поликистоз почек">Поликистоз почек</a>
                        / 					<a href="/illness/pollinoz" title="поллиноз">Поллиноз</a>
                        / 					<a href="/illness/prezhdevremennaia_eiakuliatsiia" title="преждевременная эякуляция">Преждевременная эякуляция</a>
                        / 					<a href="/illness/prostatit" title="простатит">Простатит</a>
                        / 					<a href="/illness/psoriaz" title="псориаз">Псориаз</a>
                    </div>
                    <div style="margin:20px 0 10px; font-size:16px;">Р</div>
                    <div>
                        <a href="/illness/rakhit" title="рахит">Рахит</a>
                        / 					<a href="/illness/rektotsele" title="ректоцеле">Ректоцеле</a>
                        / 					<a href="/illness/nasmork_rinit" title="ринит (насморк)">Ринит (насморк)</a>
                        / 					<a href="/illness/rinit_allergicheskij" title="ринит аллергический">Ринит аллергический</a>
                        / 					<a href="/illness/rodinki" title="родинки">Родинки</a>
                        / 					<a href="/illness/rozatsea" title="розацеа">Розацеа</a>
                    </div>
                    <div style="margin:20px 0 10px; font-size:16px;">С</div>
                    <div>
                        <a href="/illness/sarkoidoz" title="саркоидоз">Саркоидоз</a>
                        / 					<a href="/illness/Saxarniy_diabet" title="сахарный диабет">Сахарный диабет</a>
                        / 					<a href="/illness/sakharnij_diabet_1_tipa" title="сахарный диабет 1-го типа">Сахарный диабет 1-го типа</a>
                        / 					<a href="/illness/sakharnij_diabet_2_tipa" title="сахарный диабет 2-го типа">Сахарный диабет 2-го типа</a>
                        / 					<a href="/illness/svinka" title="свинка">Свинка</a>
                        / 					<a href="/illness/svish_pryamoj_kishki" title="свищ прямой кишки">Свищ прямой кишки</a>
                        / 					<a href="/illness/serdechnaia_nedostatochnost" title="сердечная недостаточность">Сердечная недостаточность</a>
                        / 					<a href="/illness/sindrom_Kushinga" title="синдром кушинга">Синдром Кушинга</a>
                        / 					<a href="/illness/scarlatina" title="скарлатина">Скарлатина</a>
                        / 					<a href="/illness/sosudistie_zvezdochki" title="сосудистые звездочки">Сосудистые звездочки</a>
                        / 					<a href="/illness/spid" title="спид">СПИД</a>
                        / 					<a href="/illness/stenokardiia" title="стенокардия">Стенокардия</a>
                    </div>
                    <div style="margin:20px 0 10px; font-size:16px;">Т</div>
                    <div>
                        <a href="/illness/tireoidit" title="тиреоидит">Тиреоидит</a>
                        / 					<a href="/illness/trakheit" title="трахеит">Трахеит</a>
                        / 					<a href="/illness/trikhomoniaz" title="трихомониаз">Трихомониаз</a>
                        / 					<a href="/illness/tromboz_ven" title="тромбоз вен">Тромбоз вен</a>
                        / 					<a href="/illness/tromboflebit" title="тромбофлебит">Тромбофлебит</a>
                        / 					<a href="/illness/troficheskaya_yazva" title="трофическая язва">Трофическая язва</a>
                        / 					<a href="/illness/tuberkulez" title="туберкулез">Туберкулез</a>
                    </div>
                    <div style="margin:20px 0 10px; font-size:16px;">У</div>
                    <div>
                        <a href="/illness/ugrevaiya_sip" title="угревая сыпь">Угревая сыпь</a>
                        / 					<a href="/illness/ureaplazmoz" title="уреаплазмоз">Уреаплазмоз</a>
                        / 					<a href="/illness/uretrit" title="уретрит ">Уретрит </a>
                    </div>
                    <div style="margin:20px 0 10px; font-size:16px;">Ф</div>
                    <div>
                        <a href="/illness/faringit" title="фарингит">Фарингит</a>
                        / 					<a href="/illness/feochromotsitoma" title="феохромоцитома">Феохромоцитома</a>
                        / 					<a href="/illness/funktsionalnaia_dispepsiia" title="функциональная диспепсия ">Функциональная диспепсия </a>
                        / 					<a href="/illness/furunkul" title="фурункул">Фурункул</a>
                    </div>
                    <div style="margin:20px 0 10px; font-size:16px;">Х</div>
                    <div>
                        <a href="/illness/khlamidioz" title="хламидиоз">Хламидиоз</a>
                        / 					<a href="/illness/kholesteroz" title="холестероз">Холестероз</a>
                        / 					<a href="/illness/kholetsistit" title="холецистит">Холецистит</a>
                    </div>
                    <div style="margin:20px 0 10px; font-size:16px;">Ц</div>
                    <div>
                        <a href="/illness/tseliakiia" title="целиакия">Целиакия</a>
                        / 					<a href="/illness/cerebralnij_ateroskleroz" title="церебральный атеросклероз">Церебральный атеросклероз</a>
                        / 					<a href="/illness/tsirroz_pecheni" title="цирроз печени">Цирроз печени</a>
                        / 					<a href="/illness/tcictit" title="цистит">Цистит</a>
                        / 					<a href="/illness/tsitomegalovirus" title="цитомегаловирус">Цитомегаловирус</a>
                    </div>
                    <div style="margin:20px 0 10px; font-size:16px;">Э</div>
                    <div>
                        <a href="/illness/ezofagit" title="эзофагит">Эзофагит</a>
                        / 					<a href="/illness/ekzema" title="экзема">Экзема</a>
                        / 					<a href="/illness/endokardit" title="эндокардит">Эндокардит</a>
                        / 					<a href="/illness/endometrioz" title="эндометриоз">Эндометриоз</a>
                        / 					<a href="/illness/entsefalit" title="энцефалит">Энцефалит</a>
                        / 					<a href="/illness/epididimit" title="эпидидимит">Эпидидимит</a>
                        / 					<a href="/illness/epilepsia" title="эпилепсия">Эпилепсия</a>
                        / 					<a href="/illness/eroziia_sheiki_matki" title="эрозия шейки матки">Эрозия шейки матки</a>
                    </div>
                    <div style="margin:20px 0 10px; font-size:16px;">Я</div>
                    <div>
                        <a href="/illness/yazva_12perstnoj_kishki" title="язва двенадцатиперстной кишки">Язва двенадцатиперстной кишки</a>
                        / 					<a href="/illness/yazva_zheludka" title="язва желудка">Язва желудка</a>
                        / 					<a href="/illness/iazvennyi_kolit" title="язвенный колит">Язвенный колит</a>
                        / 					<a href="/illness/yachmen" title="ячмень">Ячмень</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
        </div>
    </xsl:template>

</xsl:stylesheet>