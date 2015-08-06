<?php
/**
 * @var int $countDoctors
 * @var \dfs\docdoc\models\SectorModel $selectedSpeciality
 */

$isLandingPage = false; // /root/srvInfo/IsLandingPage
$selectedSpeciality = null; // srvInfo/SearchParams/SelectedSpeciality;
$selectedStations = null; // srvInfo/SearchParams/SelectedStations/Element
$searchWord = ''; // /root/srvInfo/SearchParams/SearchWord

$countDoctors = 10;

$urls = [
	'OrderRating' => '',
	'OrderExperience' => '',
	'OrderPrice' => '',
	'KidsReception' => '',
	'Departure' => '',
	'Booking' => '',
];

$order = 'rating';
$orderDir = 'asc';

$filterSort = [
	'rating' => [
		'url' => $urls['OrderRating'],
		'order' => 'rating',
		'title' => 'Рейтингу',
	],
	'experience' => [
		'url' => $urls['OrderExperience'],
		'order' => 'experience',
		'title' => 'Стажу',
	],
	'price' => [
		'url' => $urls['OrderPrice'],
		'order' => 'price',
		'title' => 'Стоимости',
	],
];

$filters = [
	'KidsReception' => [
		'url' => $urls['KidsReception'],
		'title' => 'Детский врач',
	],
	'Departure' => [
		'url' => $urls['Departure'],
		'title' => 'Выезд на дом',
	],
	'Booking' => [
		'url' => $urls['Booking'],
		'title' => 'Онлайн-запись',
	],
];
?>

<div class="list_header">

	<div class="h1 mvm<?php echo $isLandingPage ? '' : 'doctor_list_title'; ?>">
		<?php if ($isLandingPage): ?>

			Самые востребованные <span class="t-orange t-fs-xl">врачи<?php $selectedSpeciality ? '-' . RussianTextUtils::wordInNominative(mb_strtolower($selectedSpeciality->name), true) : ''; ?></span> портала DocDoc

		<?php elseif ($countDoctors > 0 && ($selectedSpeciality || $selectedStations || $searchWord)): ?>

			<?php echo RussianTextUtils::caseForNumber($countDoctors, ['Найден', 'Найдено', 'Найдено']); ?>
			<span class="t-orange t-fs-xl"><?php echo $countDoctors; ?></span>
			<?php echo RussianTextUtils::caseForNumber($countDoctors, ['врач', 'врача', 'врачей']); ?>

		<?php elseif ($countDoctors < 1 && !($selectedSpeciality || $selectedStations || $searchWord)): ?>

			Найдено 0 врачей
			К сожалению, по Вашему запросу врачей не найдено. <a href="/request">Заполните заявку</a> и мы подберем Вам врача в ближайшее время.
			<span class="keys" style="display:none" title="/contextSearch/keywords/<?php echo $searchWord; ?>"/>

		<?php elseif ($countDoctors > 1): ?>

			Все врачи

		<?php endif; ?>
	</div>

	<?php if (!$searchWord && $countDoctors > 0 && !$isLandingPage): ?>

		<div style="clear: both;">
			<ul class="filter_list">

				<li class="filter_item">Сортировка по</li>

				<?php foreach ($filterSort as $filter): ?>
					<?php
						$class = $order == $filter['order'] ? ' s-active ' . ($orderDir == 'asc' ? 'i-asc' : 'i-dsc') : '';
					?>
					<li class="filter_item filter_sort">
						<a href="<?php echo $filter['url']; ?>" class="filter_label<?php echo $class; ?>" rel="nofollow">
							<?php echo $filter['title']; ?>
						</a>
					</li>
				<?php endforeach; ?>

				<li class="filter_item filter_item_checkbox">
					<?php foreach ($filters as $name => $filter): ?>
						<a class="link-departure" href="<?php echo $filter['url']; ?>" style="margin-right:10px;">
							<label class="filter_label_checkbox">
								<input class="filter_input_checkbox" type="checkbox" name="kidsReception"<?php echo empty($params[$name]) ? '' : ' checked="checked"'; ?>/>
								<?php echo $filter['title']; ?>
							</label>
						</a>
					<?php endforeach; ?>
				</li>

			</ul>
		</div>
	<?php endif; ?>

</div>
