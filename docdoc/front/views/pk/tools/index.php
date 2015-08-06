<?php
/**
 * @var array $groups
 * @var array $widgets
 * @var \dfs\docdoc\models\PartnerModel $partner
 */
?>

<div class="result_title__ct">
	<h1 class="result_main__title">Инструменты</h1>
</div>

<br />
<p>Ваша партнерская ссылка: <a href="http://www.docdoc.ru/?pid=<?php echo $partner->id; ?>">www.docdoc.ru/?pid=<?php echo $partner->id; ?></a></p>
<br />
<?php
$phone = $partner->getPhoneNumber();

if ($phone) { ?>
	<p>Ваш партнерский телефон: <?php echo $phone->prettyFormat(); ?></p>
	<br />
<?php } ?>

<p>
	В случае возникновения вопросов обращайтесь по адресу <b class="strong"><?php echo Yii::app()->params['email']['affiliate']; ?></b>
	или в скайп: <b class="strong">vladimir.nikishkov</b>
</p>
<br />

<div id="WidgetList">
	<br /><br />
	<div>Вставьте этот код внутрь тега <?php echo htmlentities("<head>"); ?> на вашей странице</div>
	<div class="source">
		<?php echo htmlentities("<script src=\"http://docdoc.ru/widget/js\" type=\"text/javascript\"></script>"); ?>
	</div>

	<?php foreach ($groups as $name => $group): ?>

		<h2><a href="#">Виджет "<?php echo $group['title']; ?>"</a></h2>

		<div class="widget" style="display: none;">

			<?php foreach ($group['widgets'] as $name): ?>
				<h3><?php echo $widgets[$name]['title']; ?></h3>
				<div class="example">
					<div id="<?php echo $widgets[$name]['params']['container']; ?>"></div>
				</div>
			<?php endforeach; ?>

			<br /><br />

			<p>Для вывода виджета вставьте данный код внутри тега body:</p>
			<div class="source">
				<code style="white-space: pre-wrap"><?php
					$name = reset($group['widgets']);
					$p = [
						'partner' => $partner,
						'widgets' => [ $name => $widgets[$name] ],
						'divId' => $name,
					];
					echo trim(htmlentities($this->renderPartial('widgets', $p, true)));
				?></code>
			</div>

			<?php if (!empty($group['desc'])): ?>
				<p>Для вывода по умолчанию определенных Специализаций, Района/Метро или Города, внесите изменения в параметры конфигурации.</p>
				<p>Более подробная информация в <a href="/static/docs/partner-api.pdf">Документации</a>.</p>
			<?php endif; ?>

		</div>
	<?php endforeach; ?>
</div>

<?php $this->renderPartial('widgets', [ 'partner' => $partner, 'widgets' => $widgets ]); ?>

<script type="text/javascript">
	$(document).ready(function() {
		$('#WidgetList h2 a').click(function() {
			$(this).parent().next('.widget').slideToggle();
			console.log($(this).parent().next('.widget'));
			return false;
		});
	});
</script>
