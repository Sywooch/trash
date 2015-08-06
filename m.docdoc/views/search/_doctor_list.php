<ul class="list-of-doctors">
    <?php foreach ($doctors as $doctor) { ?>
        <li>
            <a href="<?php echo Yii::app()->createUrl("doctor/detail", ['alias' => $doctor->getAlias()]); ?>" data-transition="slide">
                <?php echo $this->renderPartial('//blocks/_doctor_info', ['doctor' => $doctor]);  ?>
                <i></i>
            </a>
        </li>
    <?php } ?>
</ul>



<?php if(count($doctors) < 1){?>
    <div style="margin-left: 15px; margin-top: 10px;">По вашему запросу ничего не найдено</div>
<?php } ?>

<?php
if ($total > 0) {
	$pages = new CPagination($total);
	$pages->pageSize = Yii::app()->params->page_size;
	$this->widget(
		'CLinkPager',
		array(
			'pages'                => $pages,
			'header'               => false,
			'nextPageLabel'        => "Показать еще " . Yii::app()->params->page_size . " врачей",
			'nextPageCssClass'     => 'next-pager',
			'prevPageLabel'        => false,
			'previousPageCssClass' => 'prev-pager',
			'cssFile'              => null,
			'maxButtonCount'       => 1,
		)
	);
}
?>
