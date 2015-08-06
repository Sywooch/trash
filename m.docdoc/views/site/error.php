<?php
/**
 * @var SiteController $this
 * @var string $message
 * @var integer $code
 */
$this->pageTitle = 'DocDoc.ru - Ошибка';
?>

<div data-role="page" id="index-page" data-title="DocDoc.ru">
    <div data-role="header">
		<?php $this->renderPartial("/blocks/_city_change"); ?>
    </div>
    <div class="vertical-center">
        <div class="index-img-wrapper"><i></i>
        </div>
        <ul class="items-listing">

        </ul>
        <div role="main" class="ui-content">
            <div class="index-main-block-wrapper">
                <div class="index-main-block">
                    <h3>Ошибка <?php echo $code ?>. <?php echo CHtml::encode($message); ?>

                        <a href="<?php echo Yii::app()->createUrl("site/index");?>">Главная страница</a>
                    </h3>
                </div>
            </div>
        </div>
    </div>
</div>