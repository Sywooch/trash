$(document).ready(function () {

	$(".select-slide-2").on("click", function () {
		$("#step-slides").css("margin-left", "-3800px");
		$("#pagination .pagination-2").removeClass("hide");
		$("#pagination .pagination-1").addClass("hide");
		$("#step-panel .step-2-active").removeClass("hide");
		$("#step-panel .step-1-active").addClass("hide");
		return false;
	});

	$(".select-slide-1").on("click", function () {
		$("#step-slides").css("margin-left", "-1210px");
		$("#pagination .pagination-1").removeClass("hide");
		$("#pagination .pagination-2").addClass("hide");
		$("#step-panel .step-1-active").removeClass("hide");
		$("#step-panel .step-2-active").addClass("hide");
		return false;
	});

	if ($(".select2").length) {
		$(".select2").select2();
	}

	if ($("#lk-accordion").length) {
		$("#lk-accordion").accordion({
			heightStyle: "content"
		});
	}

	if ($("#accordion").length) {
		$("#accordion").accordion({
			heightStyle: "content"
		});
	}

	if ($("#User_phone").length) {
		$("#User_phone").mask("+7 (999) 999 99 99", {placeholder: " "});
	}

	if ($("#Contacts_phone").length) {
		$("#Contacts_phone").mask("+7 (999) 999 99 99", {placeholder: " "});
	}

	if ($("#News_date").length) {
		$("#News_date").datepicker({
			dateFormat: "dd.mm.yy"
		});
	}

	if ($("#News_text").length) {
		tinymce.init({
			selector: "#News_text",
			plugins: [
				"advlist autolink link image lists charmap print preview hr anchor pagebreak",
				"searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking",
				"table contextmenu directionality emoticons paste textcolor responsivefilemanager"
			],
			toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect",
			toolbar2: "| responsivefilemanager | link unlink anchor | image media | forecolor backcolor  | print preview code ",
			image_advtab: true ,

			external_filemanager_path:"/include/filemanager/",
			filemanager_title:"Responsive Filemanager" ,
			relative_urls: false,
			external_plugins: { "filemanager" : "plugins/responsivefilemanager/plugin.min.js"}
		});

		$(".buttons input").on("click", function(){
			tinyMCE.get("News_text").save();
		});
	}

	if ($("#Faq_text").length) {
		tinymce.init({
			selector: "#Faq_text",
			plugins: [
				"advlist autolink link image lists charmap print preview hr anchor pagebreak",
				"searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking",
				"table contextmenu directionality emoticons paste textcolor responsivefilemanager"
			],
			toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect",
			toolbar2: "| responsivefilemanager | link unlink anchor | image media | forecolor backcolor  | print preview code ",
			image_advtab: true ,

			external_filemanager_path:"/include/filemanager/",
			filemanager_title:"Responsive Filemanager" ,
			relative_urls: false,
			external_plugins: { "filemanager" : "plugins/responsivefilemanager/plugin.min.js"}
		});

		$(".buttons input").on("click", function(){
			tinyMCE.get("Faq_text").save();
		});
	}

	if ($("#Text_text").length) {
		tinymce.init({
			selector: "#Text_text",
			plugins: [
				"advlist autolink link image lists charmap print preview hr anchor pagebreak",
				"searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking",
				"table contextmenu directionality emoticons paste textcolor responsivefilemanager"
			],
			toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect",
			toolbar2: "| responsivefilemanager | link unlink anchor | image media | forecolor backcolor  | print preview code ",
			image_advtab: true ,

			external_filemanager_path:"/include/filemanager/",
			filemanager_title:"Responsive Filemanager" ,
			relative_urls: false,
			external_plugins: { "filemanager" : "plugins/responsivefilemanager/plugin.min.js"}
		});
	}
});