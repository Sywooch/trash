<?php
/**
 * @var SiteController  $this
 * @var string          $message
 * @var string          $order
 * @var integer         $code
 * @var array           $ratingParams
 * @var int             $total
 * @var array           $doctors
 * @var CityModel       $city
 * @var SpecialityModel $speciality
 * @var MetroModel      $metro
 */

if(isset($speciality)){
    $this->pageTitle =
		"{$speciality->getName()} " .
		Yii::app()->city->getModel()->getName() .
		", запись на прием, рейтинги и отзывы на DocDoc.ru";
}
else {
    $this->pageTitle = 'Поиск врачей в г. ' . Yii::app()->city->getModel()->getName() . ' по всем специальностям';
}
?>

<div data-role="page" id="find-doctor" data-title="<?php echo $this->pageTitle ?>">
    <?php echo $this->renderPartial('//blocks/_right_panel');  ?>
    <div class="fixed header new-head">
        <div data-transition="slide">
            <div class="fixed-inn">
				<a href="/" data-transition="slide" data-direction="reverse" class="back-link ui-link"></a>
                <a class="logo" href="/"></a>
                <a href="#rightPanel" class="panel-btn"></a>
            </div>
        </div>
	</div>
	<div class="search-under-header">
        <form
			class="search-block-doctors internal"
			method="post"
			action=""
			data-href="<?php echo Yii::app()->createUrl("site/index");?>"
			data-location-type="<?php echo Yii::app()->city->getModel()->hasMetro() ? "metro" : "district"; ?>"
			>
            <div class="top-index-link f-s">
                <select name="specialist" id="select-specialist" class="search-redirect-on-change">
					<option>Выбрать специалиста</option>
                    <?php foreach($specialityList as $specLetter => $specItems){?>
                        <?php
						/**
						 * @var SpecialityModel[] $specItems
						 */
						foreach ($specItems as $spec) { if ($spec->isSimple()) {
						?>
                            <option
								data-alias="<?php echo $spec->getAlias();?>"
								<?php echo ($this->getActiveSpecId() == $spec->getId()) ? " selected" : ""; ?>
								><?php echo $spec->getName();?></option>
                        <?php } } ?>
                    <?php }?>
                </select>
            </div>
            <div class="top-index-link f-m">
                <?php if (Yii::app()->city->getModel()->hasMetro()) { ?>
                    <select name="location" id="select-location" class="search-redirect-on-change">
						<option>Ближайшее метро</option>
                        <?php foreach($metroList as $metroLetter => $metroItems){?>
                            <?php
							/**
							 * @var MetroModel[] $metroItems
							 */
							foreach($metroItems as $metro){?>
                                <option
									data-alias="<?php echo $metro->getAlias();?>"
									data-id="<?php echo $metro->getId();?>"
									<?php echo ($this->getActiveMetroId() == $metro->getId()) ? " selected" : ""; ?>
									><?php echo $metro->getName();?></option>
                            <?php }?>

                        <?php }?>
                    </select>
                <?php } else { ?>
					<select name="location" id="select-location" class="search-redirect-on-change">
						<option>Ближайший район</option>
                        <?php foreach ($districtList as $districtLetter => $districtItems) { ?>
                            <?php
                            /**
                             * @var dfs\models\DistrictModel[] $districtItems
                             */
                            ?>
                            <?php foreach ($districtItems as $district) { ?>
                                <option
									data-alias="<?php echo $district->getAlias(); ?>"
									data-id="<?php echo $district->getId(); ?>"
									<?php echo ($this->getActiveDistrictId() == $district->getId()) ? " selected" : ""; ?>
									><?php echo $district->getName(); ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                <?php } ?>
            </div>
        </form>
        <div class="top-sort-block w-500">
            <div class="count-doctors">
				<strong>
					<span class="count"><?php echo $total; ?></span>
					<?php echo Yii::t('', 'врач|врача|врачей', [$total]); ?>
				</strong>
			</div>
            <?php if($total > 0){?>
            <div class="sort-doctor-wrapper">
                <select id="select-sort">
					<?php if ($order == 'rating_internal') { ?>
						<option selected="selected"
								data-name=""
								data-url="<?php echo Yii::app()->createUrl(
									"search/search",
									array_merge($ratingParams)
								); ?>"
							>
							Порядок сортировки
						</option>
					<?php } ?>
                    <option <?php if ($order == 'rating'){ ?>selected="selected"<?php } ?>
							value="rating"
							data-name="Рейтингу"
							data-url="<?php echo Yii::app()->createUrl("search/search", array_merge($ratingParams, ['order' => 'rating', 'direction' => 'desc'])); ?>">
                        Сортировать по рейтингу
                    </option>
                    <option <?php if ($order == 'price'){ ?>selected="selected"<?php } ?>
							value="price"
							data-name="Стоимости"
                            data-url="<?php echo Yii::app()->createUrl("search/search", array_merge($ratingParams, ['order' => 'price', 'direction' => 'asc'])); ?>">
                        Сортировать по стоимости
                    </option>
                    <option <?php if ($order == 'experience'){ ?>selected="selected"<?php } ?>
							value="experience"
							data-name="Стажу"
                            data-url="<?php echo Yii::app()->createUrl("search/search", array_merge($ratingParams, ['order' => 'experience', 'direction' => 'desc'])); ?>">
                        Сортировать по стажу
                    </option>
                </select><span class="triangle"></span>
            </div>
            <?php }?>
        </div>
    </div>
    <div role="main" class="ui-content padding-zero">
        <?php echo $this->renderPartial('_doctor_list', ['doctors' => $doctors, 'total' => $total]);  ?>
    </div>
    <div data-role="footer" id="footer" data-position="fixed" data-tap-toggle="false" class="new-footer">
        <div class="fixed-inn">
            <div class="w-500"><span class="filter-number">поможем&nbsp;найти&nbsp;врача</span>

                <div class="call-doctor-btn"><a href="tel:<?php echo $city->getPhoneSkypeFormat();?>"><i></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="mask-overlay"></div>
</div>
