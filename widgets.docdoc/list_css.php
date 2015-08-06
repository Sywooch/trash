.dd-widget-list-container {
	width: 700px;
}
.dd-widget-list-container .dd-list-card {
	border-top: 1px solid #e6e6e6;
	margin-top: 10px;
	padding: 20px 14px 20px 20px;
	position: relative;
	background: #fff;
}
.dd-widget-list-container .dd-list-card:hover {
	position: relative;
	z-index: 3;
	background-color: #fafafa;
	box-shadow: 1px 0px 1px #e5e5e5,
		0px 1px 1px #eee,
		2px 1px 1px #e5e5e5
		,1px 2px 1px #eee,
		3px 2px 1px #e5e5e5,
		2px 3px 1px #eee,
		4px 3px 1px #e5e5e5,
		3px 4px 1px #eee,
		5px 4px 1px #e5e5e5,
		4px 5px 1px #eee,
		6px 5px 1px #e5e5e5;
}
.dd-widget-list-container .dd-list-card-left {
	display: inline-block;
	vertical-align: top;
	width: 19%;
}
.dd-widget-list-container .dd-list-card-img-link {
	display: inline-block;
	width: 100%;
	overflow: hidden;
	border-radius: 5px;
	border: 1px solid #999999;
}
.dd-widget-list-container .dd-list-card-img-link img {
	width: 100%;
	height: auto;
	vertical-align: middle;
}
.dd-widget-list-container .dd-list-card-reviews-container {
	text-align: center;
}
.dd-widget-list-container .dd-list-card-reviews-count {
	display: inline-block;
	padding: 5px 10px;
	margin: 10px 0;
	background-color: #fdf9de;
	border-radius: 3px;
	border: 1px solid #cccccc;
	position: relative;
	font-style: italic;
	color: #29aaac;
	font-size: 12px;
	text-decoration: none;
}
.dd-widget-list-container .dd-list-card-reviews-count:hover {
	color: #ff6800;
	border-color: #b8830d;
	background: #fff0ac;
	background: -moz-linear-gradient(top, #fff0ac 0%, #fff0ac 42%, #ffefa6 46%, #ffeb87 58%, #ffe55d 71%, #ffde29 83%, #ffdc10 88%, #ffdb00 92%, #ffd800 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#fff0ac), color-stop(42%,#fff0ac), color-stop(46%,#ffefa6), color-stop(58%,#ffeb87), color-stop(71%,#ffe55d), color-stop(83%,#ffde29), color-stop(88%,#ffdc10), color-stop(92%,#ffdb00), color-stop(100%,#ffd800));
	background: -webkit-linear-gradient(top, #fff0ac 0%,#fff0ac 42%,#ffefa6 46%,#ffeb87 58%,#ffe55d 71%,#ffde29 83%,#ffdc10 88%,#ffdb00 92%,#ffd800 100%);
	background: -o-linear-gradient(top, #fff0ac 0%,#fff0ac 42%,#ffefa6 46%,#ffeb87 58%,#ffe55d 71%,#ffde29 83%,#ffdc10 88%,#ffdb00 92%,#ffd800 100%);
	background: -ms-linear-gradient(top, #fff0ac 0%,#fff0ac 42%,#ffefa6 46%,#ffeb87 58%,#ffe55d 71%,#ffde29 83%,#ffdc10 88%,#ffdb00 92%,#ffd800 100%);
	background: linear-gradient(to bottom, #fff0ac 0%,#fff0ac 42%,#ffefa6 46%,#ffeb87 58%,#ffe55d 71%,#ffde29 83%,#ffdc10 88%,#ffdb00 92%,#ffd800 100%);
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fff0ac', endColorstr='#ffd800',GradientType=0 );
}
.dd-widget-list-container .dd-list-card-reviews-count:before {
	content: "";
	position: absolute;
	width: 12px;
	height: 10px;
	background: url(http://<?=$this->getHost()?>/img/common/ballon_review_edge.png) no-repeat 0px 0px;
	left: 5px;
	bottom: -10px;
	z-index: 50;
}
.dd-widget-list-container .dd-list-card-reviews-count:hover:before {
	background: url(http://<?=$this->getHost()?>/img/common/ballon_review_edge_hover.png) no-repeat 0px 0px;
}
.dd-widget-list-container .dd-list-card-reviews-counter {
	font-weight: bold;
	font-style: normal;
}
.dd-widget-list-container .dd-list-card-info {
	display: inline-block;
	vertical-align: top;
	width: 79%;
	padding-left: 10px;
	position: relative;
	font-size: 14px;
}
.dd-widget-list-container .dd-list-card-info-name {
	font-size: 18px;
	font-weight: bold;
	margin-top: 0;
	width: 75%;
	display: inline-block;
}
.dd-widget-list-container .dd-list-card-info-name a {
	color: #29aaac;
}
.dd-widget-list-container .dd-list-card-info-name a:hover {
	color: #ff6800;
}
.dd-widget-list-container .dd-list-card-info-right {
	position: absolute;
	top: 0px;
	right: 0;
	width:  180px;
	text-align: right;
}
.dd-widget-list-container .dd-list-card-info-left {
	margin-right: 190px;
}
.dd-widget-list-container .dd-list-card-info-right-rating {
	float: right;
	font-family: Calibri, Arial;
	margin-bottom: 10px;
}
.dd-widget-list-container .dd-list-card-info-right-rating-numbers {
	font-size: 24px;
	line-height: 30px;
	font-style: italic;
	color: #cccccc;
	text-align: right;
}
.dd-widget-list-container .dd-list-card-info-right-rating-numbers-main {
	font-size: 35px;
	color: #ff6700;
	font-weight: bold;
}
.dd-widget-list-container .dd-list-card-info-right-rating-numbers-sub {
	font-size: 28px;
	color: #ff6700;
	font-weight: bold;
}
.dd-widget-list-container .dd-list-card-info-right-rating-disclaimer {
	margin: -3px 0 0 0;
	font-size: 11px;
	line-height: 11px;
	color: #4d4d4d;
	width: 100px;
	text-align: right;
}
.dd-widget-list-container .dd-list-card-button {
	width: 150px;
	font-size: 14px;
	margin-top: 10px;
}
.dd-widget-list-container .dd-list-card-info-specialty {
	margin-top: 2px;
	font-size: 12px;
	margin-bottom: 5px;
}
.dd-widget-list-container .dd-list-card-info-experience {
	margin-bottom: 15px;
	font-size: 12px;
}
.dd-widget-list-container .dd-list-card-info-metro-list {
	list-style: none;
	margin: 0 0 5px 0 !important;
}
.dd-widget-list-container .dd-list-card-info-metro-list li {
	display: inline-block !important;
	background: none !important;
	padding: 0 !important;
	margin: 0 !important;
}
.dd-widget-list-container .dd-list-card-info-metro-list li a {
	color: #222;
	text-decoration: none;
	cursor: default;
}

.dd-widget-list-container .dd-metro-line {
	background-image: url(http://<?=$this->getHost()?>/img/sprites/spr_metro.png);
	background-repeat: no-repeat;
	padding-left: 15px;
}
/* Москва */
/* Замоскворецкая */
.dd-widget-list-container .dd-metro-line-1 {
    background-position: 0 -45px;
}
/* Филевская */
.dd-widget-list-container .dd-metro-line-2 {
    background-position: 0 -68px;
}
/* Калининская */
.dd-widget-list-container .dd-metro-line-3 {
    background-position: 0 -161px;
}
/* Серпуховско-Тимирязевская */
.dd-widget-list-container .dd-metro-line-4 {
    background-position: 0 -183px;
}
/* Калужско-Рижская */
.dd-widget-list-container .dd-metro-line-5 {
    background-position: 0 -114px;
}
/* Каховская */
.dd-widget-list-container .dd-metro-line-6 {
    background-position: 0 -68px;
}
/* Сокольническая */
.dd-widget-list-container .dd-metro-line-7 {
    background-position: 0 1px;
}
/* Арбатско-Покровская */
.dd-widget-list-container .dd-metro-line-8 {
    background-position: 0 -22px;
}
/* Таганско-Краснопресненская */
.dd-widget-list-container .dd-metro-line-9 {
    background-position: 0 -137px;
}
/* Люблинская */
.dd-widget-list-container .dd-metro-line-10 {
    background-position: 0 -252px;
}
/* Кольцевая */
.dd-widget-list-container .dd-metro-line-11 {
    background-position: 0 -90px;
}
/* Бутовская */
.dd-widget-list-container .dd-metro-line-12 {
    background-position: 0 -228px;
}
/* Санкт-Петербург *.
/* Кировско-Выборгская */
.dd-widget-list-container .dd-metro-line-13 {
    background-position: 0 1px;
}
/* Московско-Петроградская */
.dd-widget-list-container .dd-metro-line-14 {
    background-position: 0 -68px;
}
/* Невско-Василеостровская */
.dd-widget-list-container .dd-metro-line-15 {
    background-position: 0 -45px;
}
/* Правобережная */
.dd-widget-list-container .dd-metro-line-16 {
    background-position: 0 -114px;
}
/* Фрунзенская */
.dd-widget-list-container .dd-metro-line-17 {
    background-position: 0 -137px;
}

.dd-widget-list-container .dd-list-card-info-adress {
	color: #333333;
	font-style: italic;
	font-size: 14px;
	border-bottom: 1px dashed;
	cursor: pointer;
	white-space: nowrap;
}
.dd-widget-list-container .dd-list-card-info-description {
	font-size: 12px;
	margin-top: 15px;
}
.dd-widget-list-container .dd-list-card-info-price {
	margin: 15px 0;
}

.dd-widget-list-container .dd-list-header {
	overflow: hidden;
	padding: 0 20px;
}
.dd-widget-list-container .dd-list-header-found {
	float: left;
	padding-right: 10px;
	margin-right: 8px;
	border-right: 1px solid #cccccc;
	font-size: 18px;
	font-weight: bold;
	margin-top: 10px;
	margin-bottom: 10px;
}
.dd-widget-list-container .dd-list-header-found span {
	color: #ff6700;
}
.dd-widget-list-container .dd-list-header-filter {
	list-style: none;
	margin: 10px 0 0 0 !important;
	display: block;
}
.dd-widget-list-container .dd-list-header-filter li {
	display: inline-block !important;
	margin: 0 2px !important;
	font-size: 12px;
	padding: 0 !important;
	background: none !important;
}
.dd-widget-list-container .dd-list-header-filter-sort a {
	display: inline-block;
	line-height: 20px;
	color: #00898b;
	border: 1px dashed #00898b;
	border-radius: 3px;
	padding: 0 10px;
	cursor: pointer;
	text-decoration: none;
}
.dd-widget-list-container .dd-list-header-filter-sort a:hover {
	color: #ff6800;
}
.dd-widget-list-container .dd-list-header-filter-active {
	background-color: #fdf9de;
}
.dd-widget-list-container .dd-list-header-filter-checkbox {
	float: right;
	padding-top: 5px;
}
.dd-widget-list-container .dd-list-header-filter-checkbox a {
	text-decoration: none;
	color: #222;
}
.dd-widget-list-container .dd-list-header-filter-checkbox a:hover {
	color: #ff6800;
}
.dd-widget-list-container .dd-list-header-filter-checkbox input {
	vertical-align: bottom;
	position: relative;
	top: -2px;
	position: relative;
	z-index: 0;
}
.dd-widget-list-container .dd-list-header-filter-checkbox label {
	cursor: pointer;
}
.dd-widget-list-container .dd-select-container-list {
	display: inline-block;
	width: 30%;
}

.dd-widget-list-container .dd-list-card-info-right-clinic {
	margin-left: 20px;
	width: 153px;
}
.dd-widget-list-container .dd-list-card-info-clinic-specialty {
	margin-top: 5px;
	font-size: 12px;
	color: #989898;
	margin-bottom: 15px;
}
.dd-widget-list-container .dd-list-card-info-right-phone {
	color: #ff6800;
	font-size: 16px;
	font-weight: bold;
	text-align: right;
	background-position: left center;
	background-repeat: no-repeat;
	background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA0AAAAKCAYAAABv7tTEAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAARNJREFUeNpUUctOAzEMHDvZVkjA/gBXLj2jcuWA+GMkoOUD6O6VL+ihH9BDpSWxsV12uySKIj9mMuOQqsJX230q+ATRKxAJSM55YUGqGZWB48MzeY4c1HYfKhauZIGv9VMU5uum3yqjQJADyG23VWfJoACsuo3OAU7ojdWoEuyB/k2zuBRrK1SjaU+MdvduUhVKyeRx5JMwfqy3MSiNnkYZyoP5WVphCBCbsDsRfD++TLJ5LsU4kF0rKjQtrEpw0n2j/0160s+9SbrdbXSM5+e6f9VQ8RfnEXzIFVw4jJLYiFMB2x2eaQmRMj00gdjHY0P1sSIAHID4K5ObcZF48aSNRYN/ne10LnqfAd1npYkfvwIMAA+omlF69hAlAAAAAElFTkSuQmCC');
	margin-top: 15px;
}
.dd-widget-list-container .dd-list-card-info-right-schedule {
	font-size: 12px;
	color: #999999;
	overflow: hidden;
	padding: 10px 16px 10px 5px;
}
.dd-widget-list-container .dd-list-card-info-right-schedule label {
	display: block;
	padding-bottom: 5px;
}
.dd-widget-list-container .dd-list-card-info-right-schedule-days {
	display: inline-block;
	float: left;
	clear: both;
}
.dd-widget-list-container .dd-list-card-info-right-schedule-time {
	display: inline-block;
	float: right;
	clear: right;
}

.dd-widget-list-container .dd-pagination {
	text-align: center;
	font-family: Tahoma, Geneva, sans-serif;
	font-size: 12px;
	line-height: 40px;
	border-top: 1px solid #e6e6e6;
	list-style: none;
	margin: 0 !important;
}
.dd-widget-list-container .dd-pagination li {
	display: inline-block !important;
	background: none !important;
	padding: 0 !important;
	margin: 0 !important
}
.dd-widget-list-container .dd-pagination span {
	color: #00898b;
	margin: 0 5px;
	cursor: pointer;
	text-decoration: underline;
}
.dd-widget-list-container .dd-pagination span:hover {
	color: #ff6800;
}
.dd-widget-list-container .dd-pagination .dd-active span {
	color: #4d4d4d;
	text-decoration: none;
}
.dd-widget-list-container .dd-pagination .dd-prev,
.dd-widget-list-container .dd-pagination .dd-next {
	font-size: 26px;
	text-decoration: none;
	margin: 0 15px;
}
.dd-widget-list-container .dd-pagination .dd-prev span,
.dd-widget-list-container .dd-pagination .dd-next span {
	text-decoration: none;
}
.dd-widget-list-container .dd-pagination .dd-hide {
	display: none;
}

.dd-widget-list-container .dd-list-top-container {
	background: #f0f0f0;
	border-bottom: 2px solid #ccc;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
	margin: 10px 20px;
	color: #333;
	font-size: 14px;
	padding: 15px 20px;
}
.dd-widget-list-container .dd-list-select-container {
	overflow: hidden;
	margin-bottom: -6px;
	display: inline-block;
}
.dd-widget-list-container .dd-list-select-container select {
	background: transparent;
	-webkit-appearance: none;
	appearance: none;
	-moz-appearance: none;
	outline: none;
	border-bottom: 1px dashed #000;
	color: #009b9e;
	font-size: 16px;
	width: 125%;
}
.dd-widget-list-container .dd-no-find-text {
	color: #a1a1a1;
	padding: 10px 10px 10px 38px;
	background: url(http://<?=$this->host?>/img/icons/i-disclaimer.png) left center no-repeat;
	font-size: 12px;
}
.dd-widget-list-container .dd-district {
	margin-bottom: 5px;
	margin-top: 15px;
}
.dd-widget-list-container .dd-district a {
	color: #222;
	text-decoration: none;
	cursor: default;
}