<?php
/**
 * @var array $emails
 */
?>

<main class="l-main l-wrapper section_about_wrap" role="main">

	<?php echo $this->renderPartial('leftmenu', ['menu' => $this->_leftmenu, 'active' => 'team']); ?>

	<section class="page_about" style="float:right;">

		<article>

			<h3>Команда DocDoc</h3>

			<p>Наша команда состоит из умных, амбициозных и талантливых людей. Мы все объединены общей идеей и желанием
				привнести в этот мир что-то хорошее и доброе. Мы искренне верим, что наша работа сделает медицину в
				России на порядок более качественной и доступной для всех жителей нашей необъятной страны. Мы открыты к
				сотрудничеству и всегда рады вопросам и деловым предложениям.</p>

			<div class="page_team">
				<div class="page_team_item">
					<img class="page_team_pic" src="/img/about/team-petruhin.jpg" alt="Дмитрий Петрухин">

					<div class="page_team_text">
						<div class="page_team_head">
							Дмитрий Петрухин
							<a href="mailto:<?php echo $emails["generalManager"]; ?>"
							   class="page_team_mail l-ib"><span class="page_team_mail-tooltip">Отправить письмо</span></a>
						</div>
						<div class="page_team_work">Генеральный директор</div>
						Источник вдохновения для всех сотрудников. Всегда вникает в детали и решает проблемы любого
						уровня сложности. Любит кофе, сладости, кундалини-йогу и деспотии восточного типа. Считает, что
						в любом бизнесе важны три вещи: быстрый запуск, стремительный рост и широкие перспективы.
					</div>
				</div>
				<div class="page_team_item">
					<img class="page_team_pic" src="/img/about/team-fisenko.jpg" alt="Матвей Фисенко">

					<div class="page_team_text">
						<div class="page_team_head">
							Матвей Фисенко
							<a href="mailto:<?php echo $emails["projectManager"]; ?>"
							   class="page_team_mail l-ib"><span class="page_team_mail-tooltip">Отправить письмо</span></a>
						</div>
						<div class="page_team_work">Руководитель проекта</div>
						Структурный и беспощадный. Искусно превращает немыслимый хаос в упорядоченную систему. Обожает
						составлять отчеты и отслеживать статистику всего на свете, фанат Google Таблиц. Человек
						невероятного терпения и выдержки, никогда не теряет самоконтроля.
					</div>
				</div>
				<div class="page_team_item">
					<img class="page_team_pic" src="/img/about/team-moskalenko.jpg" alt="Оксана Москаленко">

					<div class="page_team_text">
						<div class="page_team_head">
							Оксана Москаленко
							<a href="mailto:<?php echo $emails["executiveManager"]; ?>"
							   class="page_team_mail l-ib"><span class="page_team_mail-tooltip">Отправить письмо</span></a>
						</div>
						<div class="page_team_work">Исполнительный директор</div>
						Исполнительна и директивна. Занимается йогой, любит музыку, коллекционирует наушники. Уверена,
						что успех любого бизнеса заключается в соблюдении сроков. Успевает следить за всеми процессами в
						DocDoc.ru и заниматься собственным проектом.
					</div>
				</div>
				<div class="page_team_item">
					<img class="page_team_pic" src="/img/about/team-antonov.jpg" alt="Роман Антонов">

					<div class="page_team_text">
						<div class="page_team_head">
							Роман Антонов
							<a href="mailto:<?php echo $emails["seo"]; ?>"
							   class="page_team_mail l-ib"><span class="page_team_mail-tooltip">Отправить письмо</span></a>
						</div>
						<div class="page_team_work">Директор по маркетингу</div>
						SEO-бог и SEO-человек. В Интернете не существует сайта, о котором он бы не знал, и не существует
						человека, который бы знал о сайтах больше, чем он. Двигает недвижимое и впихивает невпихуемое.
						Благодаря ему о DocDoc.ru знают все на просторах Рунета.
					</div>
				</div>
				<div class="page_team_item">
					<img class="page_team_pic" src="/img/about/team-parshukov.jpg" alt="Алексей Паршуков">

					<div class="page_team_text">
						<div class="page_team_head">
							Алексей Паршуков
							<a href="mailto:<?php echo $emails["development"]; ?>"
							   class="page_team_mail l-ib"><span class="page_team_mail-tooltip">Отправить письмо</span></a>
						</div>
						<div class="page_team_work">Директор по технологиям</div>
						Корпоративный робот-убийца с невероятными знаниями в области программирования. В планах
						превратить DocDoc в международного супертехнологичного монстра, от одного упоминания о котором у
						конкурентов пропадала бы мотивация. Увлекается современной культурой стран дальневосточной Азии
						и спортом.
					</div>
				</div>
				<div class="page_team_item">
					<img class="page_team_pic" src="/img/about/team-zinger.jpg" alt="Анатолий Зингер">

					<div class="page_team_text">
						<div class="page_team_head">
							Анатолий Зингер
							<a href="mailto:<?php echo $emails["sale"]; ?>"
							   class="page_team_mail l-ib"><span class="page_team_mail-tooltip">Отправить письмо</span></a>
						</div>
						<div class="page_team_work">Коммерческий директор</div>
						Мастер переговоров и убеждения. Человек-аргумент и человек-компромисс в одном флаконе,
						умеет найти выход даже из самых сложных споров, а оппонентов превратить в союзников.
						Знает как заработать много денег, а в свободное время готовится к зомби апокалипсису.
					</div>
				</div>
				<div class="page_team_item">
					<img class="page_team_pic" src="/img/about/team-nikishkov.jpg" alt="Владимир Никишков">

					<div class="page_team_text">
						<div class="page_team_head">
							Владимир Никишков
							<a href="mailto:<?php echo $emails["partner"]; ?>"
							   class="page_team_mail l-ib"><span class="page_team_mail-tooltip">Отправить письмо</span></a>
						</div>
						<div class="page_team_work">Менеджер партнерских программ</div>
						Человек, отвечающий за развитие партнерской программы DocDoc.ru. Помогает и направляет
						на путь истинный все сайты - от мала до велика. Основная его цель - научить
						партнеров зарабатывать сумму как минимум с пятью нулями. В свободное время знаимается спортом,
						а также актерским мастерством.
					</div>
				</div>
			</div>

		</article>

	</section>

</main>
