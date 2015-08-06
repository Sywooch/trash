<?php
/**
 * @var dfs\components\Controller $this
 * @var string $content Контент страници
 */

$baseUrl = Yii::app()->request->baseUrl;

Yii::app()->clientScript
	->registerScriptFile($baseUrl . '/js/libs/jquery/jquery.min.js')
	->registerScriptFile($baseUrl . '/js/libs/jquery-mobile/jquery-mobile-min.js')
	->registerScriptFile('https://maps.google.com/maps/api/js?sensor=true')
	->registerScriptFile($baseUrl . '/js/libs/infobox/infobox.js')
	->registerScriptFile($baseUrl . '/js/libs/jquery.maskedinput.min.js')
	->registerScriptFile(
		Yii::app()->assetManager->publish(
			Yii::getPathOfAlias('application.public.js').'/script.js'
		)
	)
	->registerCssFile(
		Yii::app()->assetManager->publish(
			Yii::getPathOfAlias('application.public.css').'/style.css'
		)
	);
?><!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="utf-8">
	<script>
		var cityName = "<?php echo Yii::app()->city->getModel()->getName(); ?>";
	</script>
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
	<link href="//<?php echo Yii::app()->params->main_site; ?>/img/common/favicon.ico" rel="icon" type="image/x-icon" />
	<link rel="apple-touch-icon-precomposed"
		  href="//<?php echo Yii::app()->params->main_site; ?>/img/common/touch-icon-iphone-precomposed.png"/>
	<link rel="apple-touch-icon-precomposed" sizes="76x76"
		  href="//<?php echo Yii::app()->params->main_site; ?>/img/common/touch-icon-ipad-precomposed.png"/>
	<link rel="apple-touch-icon-precomposed" sizes="120x120"
		  href="//<?php echo Yii::app()->params->main_site; ?>/img/common/touch-icon-iphone-retina-precomposed.png"/>
	<link rel="apple-touch-icon-precomposed" sizes="152x152"
		  href="//<?php echo Yii::app()->params->main_site; ?>/img/common/touch-icon-ipad-retina-precomposed.png"/>
	<link rel="canonical" href="<?=Yii::app()->city->getCanonicalUrl();?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
</head>

<body>

<?php echo $content;?>

<?php echo $this->renderPartial('//elements/analytics');  ?>

</body>
</html>