<?php
/**
 * @var string $host
 */

?><!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">

</head>
<body>

Онлайн-запись на диагностику
<div id="diagn"></div>

<br>

<h3>Запись к врачу</h3>
<br>
<b>Медицинский центр в Марьино -> записаться на прием</b>
<div id="doctor_btn1"></div>

<br>
<b>Медицинский центр в Марьино -> записаться на прием к гастроэнтерологу</b>
<div id="doctor_btn2"></div>

<br>
<b>Медицинский центр в Марьино -> записаться на прием к конкретному врачу с онлайн записью</b>
<div id="doctor_btn3"></div>

<br>
<b>Медицинский центр в Марьино -> записаться на прием к конкретному врачу без расписания</b>
<div id="doctor_btn4"></div>


<script type="text/javascript">
	(function (w, d) {
		var proto = d.location.protocol === "https:" ? "https:" : "http:";
		w.DdFeedAsyncInit = function () {

			DdWidget({
				id: "doctorButon1",
				pid: '14',
				container: 'doctor_btn1',
				city: 'msk',
				widget: 'Button',
				action: 'LoadWidget',
				template: 'Button_common',
				type: 'Doctor',
				clinicId: 904,
				modalTemplate: 'Online'
			});

			DdWidget({
				id: "doctorButon2",
				pid: '14',
				container: 'doctor_btn2',
				city: 'msk',
				widget: 'Button',
				action: 'LoadWidget',
				template: 'Button_common',
				type: 'Doctor',
				clinicId: 904,
				modalTemplate: 'Online',
				specialityId: 71
			});


			DdWidget({
				id: "doctorButon3",
				pid: '14',
				container: 'doctor_btn3',
				city: 'msk',
				widget: 'Button',
				action: 'LoadWidget',
				template: 'Button_common',
				type: 'Doctor',
				clinicId: 904,
				doctorId: 5034,
				modalTemplate: 'Online'
			});

			DdWidget({
				id: "doctorButon4",
				pid: '14',
				container: 'doctor_btn4',
				city: 'msk',
				widget: 'Button',
				action: 'LoadWidget',
				template: 'Button_common',
				type: 'Doctor',
				clinicId: 904,
				doctorId: 5111,
				modalTemplate: 'Online'
			});

			DdWidget({
				pid: '14',
				container: 'diagn',
				city: 'msk',
				widget: 'Button',
				action: 'LoadWidget',
				template: 'Button_common',
				type: 'Diagnostic',
				clinicId: 3215,
				diagnosticId: 66,
				allowOnline:1
			});
		};
		(function (d) {
			var js, id = 'dd-feed', ref = d.getElementsByTagName('script')[0];
			if (d.getElementById(id)) {
				return;
			}
			js = d.createElement('script');
			js.id = id;
			js.async = true;
			js.src = proto + '//<?php echo $host; ?>/widget/js';
			ref.parentNode.insertBefore(js, ref);
		}(d));
	}(window, window.document));
</script>

</body>


</html>