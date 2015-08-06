 <section class="about">
    <ul class="about_list_short">
        <li class="about_item i-howwork">
            <h3>Как работает Diagnostica.docdoc.ru?</h3>
            <p class="mvn">В нашей базе размещены анкеты более 200 диагностических центров <?php echo Yii::app()->city->getTitle('genitive');?>. В профиле каждого центра представлена информация о его месторасположении, времени работы, стоимости услуг, а также указан контактный телефон.</p>
        </li>
        <li class="about_item i-closetohome">
            <h3>Как подобрать диагностический центр?</h3>
            <p class="mvn">На Diagnostica.docdoc.ru Вы задаете параметры поиска «вид диагностики» и «район <?php echo Yii::app()->city->getTitle('genitive');?>» (или «станция метро») для проведения процедуры. В результате Вам будет представлен список центров, ранжированный с учетом стоимости обследования.</p>
        </li>
    </ul>
    <div class="about_list">
        <a href="/map.php" class="about_list_map"><img src="<?php echo Yii::app()->homeUrl;?>st/i/common/map-main.png" alt=""></a>
        <ul class="i-doctor_l">
            <li class="about_item">
                <h3><a href="http://<?php echo Yii::app()->city->getSubDomain();?><?=Yii::app()->params['hosts']['front']?>/" class="about_link i-docdoc" target="_blank">Сервис по поиску врачей</a></h3>
                <p>Нужен квалифицированный врач поближе к дому? Специализированный портал поможет</p>
            </li>
            <li class="about_item">
                <h3><a href="http://<?php echo Yii::app()->city->getSubDomain();?><?=Yii::app()->params['hosts']['front']?>/library" class="about_link i-pacientlib">Медицинская библиотека</a></h3>
                <p>Мы собрали для вас массу полезных статей о врачах, медицинских направлениях, современных методах лечения и диагностики.</p>
            </li>
            <li class="about_item">
                <h3><a href="http://<?php echo Yii::app()->city->getSubDomain();?><?=Yii::app()->params['hosts']['front']?>/illness" class="about_link i-sicklist">Справочник заболеваний</a></h3>
                <p>Здесь Вы можете подобрать врача, который специализируется на лечении конкретного заболевания.</p>
            </li>
        </ul>
        </div>
</section>