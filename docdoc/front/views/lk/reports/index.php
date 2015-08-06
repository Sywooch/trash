
<div class="reports">

	<div class="result_title__ct">
		<h1 class="result_main__title">Отчеты</h1>
	</div>

	<div class="reports_tabs">
		<ul class="">
			<li><a href="#tab-patients">Пациенты</a></li>
			<li><a href="#tab-requests">Заявки</a></li>
			<li><a href="#tab-spec">Специальности</a></li>
		</ul>

		<form id="tab-patients" class="chart_form" type="POST" method="POST" action="/lk/service/chart.php">

			<h2 class="result_main__subtitle">Пациенты</h2>

			<div class="result_filters">
				<div class="filter_date">
					Период
					<span class="filter_date__txt">с</span>
					<input name="crDateFrom" class="filter_date__input datepicker" type="text" value=""/>
					<span class="filter_date__txt">по</span>
					<input name="crDateTill" class="filter_date__input datepicker" type="text" value=""/>
				</div>

				<input type="submit" value="Перерисовать" class="result_filters__submit button_lk" />

				<input type="hidden" name="chartClinic" value="<?php echo $this->_clinic->id; ?>" />
				<input type="hidden" name="chartType" value="byCount" />
			</div>

			<div id="showmemagic" class="highcharts"></div>

		</form>


		<form id="tab-requests" class="chart_form" action="/lk/service/chart.php">

			<h2 class="result_main__subtitle">Заявки</h2>

			<div class="result_filters">
				<div class="filter_date">
					Период
					<span class="filter_date__txt">с</span>
					<input name="crDateFrom" class="filter_date__input datepicker" type="text" value=""/>
					<span class="filter_date__txt">по</span>
					<input name="crDateTill" class="filter_date__input datepicker" type="text" value=""/>
				</div>

				<div class="filter_doctor">
					<label class="filter_doctor__label">
						<span class="filter_doctor__label-txt">поиск по фио доктора:</span>
						<input name="docName" class="filter_doctor__input filter_doctor__docname autocomplete_docname" type="text" placeholder="" value="" />
					</label>

					<br />

					<label class="filter_doctor__label">
						<span class="filter_doctor__label-txt">специальность:</span>
						<input name="docSpec" class="filter_doctor__input filter_doctor__docspec autocomplete_docspec" type="text" placeholder="" value="" />
					</label>

					<span class="link_dropdown link_dropdown__docspec">выбрать из списка</span>
				</div>

				<input type="submit" value="Перерисовать" class="result_filters__submit button_lk" />

				<input type="hidden" name="chartClinic" value="<?php echo $this->_clinic->id; ?>" />
				<input type="hidden" name="chartType" value="byPercentage" />
			</div>

			<div id="showmemagic2" class="highcharts"></div>

		</form>


		<form id="tab-spec" class="chart_form" action="/service/chart.php">

			<h2 class="result_main__subtitle">Специальности</h2>

			<div class="result_filters">
				<div class="filter_date">
					Период
					<span class="filter_date__txt">с</span>
					<input name="crDateFrom" class="filter_date__input datepicker" type="text" value=""/>
					<span class="filter_date__txt">по</span>
					<input name="crDateTill" class="filter_date__input datepicker" type="text" value=""/>
				</div>

				<div class="filter_doctor">
					<label class="filter_doctor__label">
						<span class="filter_doctor__label-txt">поиск по фио доктора:</span>
						<input name="docName" class="filter_doctor__input filter_doctor__docname autocomplete_docname" type="text" placeholder="" value="" />
					</label>

					<br />

					<label class="filter_doctor__label">
						<span class="filter_doctor__label-txt">специальность:</span>
						<input name="docSpec" class="filter_doctor__input filter_doctor__docspec autocomplete_docspec" type="text" placeholder="" value="" />
					</label>

					<span class="link_dropdown link_dropdown__docspec">выбрать из списка</span>
				</div>

				<input type="submit" value="Перерисовать" class="result_filters__submit button_lk" />

				<input type="hidden" name="chartClinic" value="<?php echo $this->_clinic->id; ?>" />
				<input type="hidden" name="chartType" value="byPercentage" />
			</div>

			<div id="showmemagic3" class="highcharts"></div>

		</form>

	</div>

</div>
