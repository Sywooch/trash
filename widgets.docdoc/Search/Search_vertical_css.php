@charset "utf-8";

.dd-widget-search-vertical, .dd-widget-search-vertical * {
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	border: 0;
	outline: none;
	margin: 0;
	padding: 0;
	font: normal 14px Arial, Helvetica, sans-serif;
}

.dd-widget-search-vertical {
	margin: 0;
	padding: 12px;
	zoom: 1;
	position: relative;
	overflow: hidden;
	background: #fff;
	border: 1px solid #cdcdcd;
}

.dd-widget-search-vertical form {
	margin: 0 !important;
	padding: 0 !important;
}

.dd-widget-search-vertical .dd-title {
	font: bold 15px Arial, Helvetica, sans-serif;
	text-align: center;
	margin-bottom: 10px;
}

.dd-widget-search-vertical .dd-title:before {
	content: '';
	display: inline-block;
	vertical-align: middle;
	position: relative;
	top: -1px;
	margin: 0 10px 0 0;
	width: 16px;
	height: 16px;
}

.dd-widget-search-vertical .dd-select,
.dd-widget-search-vertical .dd-input {
	position: relative;
	overflow: hidden;
	border: 1px solid #cdcdcd;
	border-radius: 2px;
	-webkit-border-radius: 2px;
	margin-bottom: 15px;
	display: block;
	background: #fff;
	height: 33px;
	line-height: normal;
	color: #7c7c7c;
}

.dd-widget-search-vertical .dd-select select,
.dd-widget-search-vertical .dd-input input {
	line-height: normal;
	margin: 0 0 0 -1px;
	padding: 8px 0 6px 12px;
	width: 115%;
	font: inherit;
	color: inherit;
	position: relative;
	background: none;
	-webkit-appearance: none;
	font-size: 12px;
	z-index: 2;
}

.dd-widget-search-vertical .dd-select,
.dd-widget-search-vertical .dd-select select {
	cursor: pointer;
}

.dd-widget-search-vertical .dd-select:after {
	content: '';
	position: absolute;
	top: 50%;
	right: 7px;
	margin: -8px 0 0 0;
	width: 16px;
	height: 16px;
	background-repeat: no-repeat;
	z-index: 1;
}

.dd-widget-search-vertical .dd-input input {
	padding-left: 37px;
	padding-top: 6px;
	width: 154px;
	font-size: 14px;
}

.dd-widget-search-vertical .dd-label {
	display: block;
	color: #7c7c7c;
	margin: 0 0 8px 4px;
}

.dd-widget-search-vertical .dd-submit {
	height: 30px;
	padding: 0 20px;
	cursor: pointer;
}

.dd-widget-search-vertical .dd-logo {
	margin-bottom: 15px;
	display: block;
	text-align: center;
}

.dd-widget-search-vertical .dd-logo img {
	max-width: 100%;
}

.dd-widget-search-vertical .dd-search-vertical-footer {
	position: absolute;
	left: 0;
	right: 0;
	bottom: 0;
	height: 39px;
	background: #fff;
	border-top: 1px solid #ccc;
	text-align: center;
	padding-top: 7px;
	line-height: 12px;
	font-size: 11px;
}

.dd-widget-search-vertical .dd-search-vertical-footer * {
	font-size: 11px;
}