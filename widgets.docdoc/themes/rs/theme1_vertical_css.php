.rs-theme1-vertical {
	width: 100%;
}

.rs-theme1-vertical .dd-select,
.rs-theme1-vertical .dd-input {
	zoom: 1;
	position: relative;
	overflow: hidden;
	border: 1px solid #719ebc;
	margin: 10px;
	padding: 0;
	background: #fff;
	height: 31px;
	line-height: normal;
	font: normal 12px Arial, Helvetica, sans-serif;
	color: #4b4b4b;
}

.rs-theme1-vertical .dd-select select,
.rs-theme1-vertical .dd-input input {
	line-height: normal;
	border: 0;
	margin: 0 0 0 -1px;
	padding: 7px 0 7px 14px;
	width: 115%;
	font: inherit;
	color: inherit;
	position: relative;
	background: none;
	outline: none;
}

.rs-theme1-vertical .dd-select select {
	padding: 0 0 0 14px;
	height: 31px;
}

.rs-theme1-vertical .dd-select,
.rs-theme1-vertical .dd-select select {
	cursor: pointer;
}

.rs-theme1-vertical .dd-select:after {
	content: '';
	position: absolute;
	top: 50%;
	right: 9px;
	margin: -8px 0 0 0;
	width: 16px;
	height: 16px;
	background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAH9JREFUeNrkUrsWgCAIhU5L/v+3ymhBPgDTBoeG7oCK516emFKCFWywiO8FdrEh2EZwXxDvk8F38VcDQIQtgxixIxcYofzOZFsCi/iBCNmJ6GBdD+j61NElG2gijiwxHvfg4J4Uf1ZQab9PgXSkMXk+xkKakMcl/GuVlwVOAQYAN2g2Z4kW3f8AAAAASUVORK5CYII=');
}

.rs-theme1-vertical .dd-input input {
	padding-left: 37px;
	padding-top: 6px;
	width: 100%;
	margin: 0;
	font-size: 14px;
}

.rs-theme1-vertical input.dd-submit {
	outline: none;
	border: 0;
	padding: 0;
	cursor: pointer;
	font: bold 14px Arial, Helvetica, sans-serif;
	height: 33px;
	width: 161px;
	position: relative;
	overflow: hidden;
	color: #fff;
	text-decoration: none;
	background: #e22716;
	background: -moz-linear-gradient(top, #fd2d18 0%, #c82114 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #fd2d18), color-stop(100%, #c82114));
	background: -webkit-linear-gradient(top, #fd2d18 0%, #c82114 100%);
	background: -o-linear-gradient(top, #fd2d18 0%, #c82114 100%);
	background: -ms-linear-gradient(top, #fd2d18 0%, #c82114 100%);
	background: linear-gradient(to bottom, #fd2d18 0%, #c82114 100%);
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fd2d18', endColorstr='#c82114', GradientType=0 );
	text-align: center;
	margin: 10px auto;
	display: block;
}

.rs-theme1-vertical .dd-title {
	display: none;
}

.rs-theme1-vertical .dd-name-request-empty,
.rs-theme1-vertical .dd-phone-request-empty,
.rs-theme1-vertical .dd-phone-request-incorrect {
	margin: 10px;
}