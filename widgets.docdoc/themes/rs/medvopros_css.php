.rs-medvopros {
	width: 690px;
	border: 1px solid #cdcdcd;
	border-radius: 5px;
	-webkit-border-radius: 5px;
	background: #ffffff;
	background: -moz-linear-gradient(top, #ffffff 0%, #f9f7f6 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #ffffff), color-stop(100%, #f9f7f6));
	background: -webkit-linear-gradient(top, #ffffff 0%, #f9f7f6 100%);
	background: -o-linear-gradient(top, #ffffff 0%, #f9f7f6 100%);
	background: -ms-linear-gradient(top, #ffffff 0%, #f9f7f6 100%);
	background: linear-gradient(to bottom, #ffffff 0%, #f9f7f6 100%);
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#f9f7f6', GradientType=0 );
	overflow: hidden;
	padding: 19px 18px 13px 18px;
	box-shadow:inset 0 0 0 1px #fff;
	-webkit-box-shadow:inset 0 0 0 1px #fff;
}

.rs-medvopros .dd-title {
	display: none;
}

.rs-medvopros .dd-select,
.rs-medvopros .dd-input {
	zoom: 1;
	position: relative;
	overflow: hidden;
	float: left;
	border: 1px solid #cdcdcd;
	margin: 0 8px 0 0;
	width: 252px;
	background: #fff;
	height: 33px;
	line-height: normal;
	font: normal 12px Arial, Helvetica, sans-serif;
	color: #4b4b4b;
}

.rs-medvopros .dd-select select,
.rs-medvopros .dd-input input {
	line-height: normal;
	border: 0;
	margin: 0 0 0 -1px;
	padding: 8px 0 8px 14px;
	width: 115%;
	font: inherit;
	color: inherit;
	position: relative;
	background: none;
	outline: none;
	-webkit-appearance: none;
}

.rs-medvopros .dd-select,
.rs-medvopros .dd-select select {
	cursor: pointer;
}

.rs-medvopros .dd-select:after {
	content: '';
	position: absolute;
	top: 50%;
	right: 9px;
	margin: -8px 0 0 0;
	width: 16px;
	height: 16px;
	background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAH9JREFUeNrkUrsWgCAIhU5L/v+3ymhBPgDTBoeG7oCK516emFKCFWywiO8FdrEh2EZwXxDvk8F38VcDQIQtgxixIxcYofzOZFsCi/iBCNmJ6GBdD+j61NElG2gijiwxHvfg4J4Uf1ZQab9PgXSkMXk+xkKakMcl/GuVlwVOAQYAN2g2Z4kW3f8AAAAASUVORK5CYII=');
}

.rs-medvopros .dd-input input {
	padding-left: 37px;
	padding-top: 7px;
	width: 154px;
	margin: 0;
	font-size: 14px;
}

.rs-medvopros input.dd-submit {
	outline: none;
	border: 0;
	padding: 0;
	float: right;
	cursor: pointer;
	font: normal 14px Arial, Helvetica, sans-serif;
	border: 1px solid #a18f74;
	height: 35px;
	width: 160px;
	position: relative;
	overflow: hidden;
	color: #303030;
	text-decoration: none;
	background: #ffe157;
	background: -moz-linear-gradient(top, #ffe157 0%, #ffcf05 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #ffe157), color-stop(100%, #ffcf05));
	background: -webkit-linear-gradient(top, #ffe157 0%, #ffcf05 100%);
	background: -o-linear-gradient(top, #ffe157 0%, #ffcf05 100%);
	background: -ms-linear-gradient(top, #ffe157 0%, #ffcf05 100%);
	background: linear-gradient(to bottom, #ffe157 0%, #ffcf05 100%);
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffe157', endColorstr='#ffcf05', GradientType=0 );
	text-shadow: 0 1px 0 #ffff2e;
	box-shadow: inset 0 0 1px #ffff2e;
	-webkit-box-shadow: inset 0 0 1px #ffff2e;
	border-radius: 2px;
	-webkit-border-radius: 2px;
	text-align: center;
	margin: 0 0 0 -5px;
}

.rs-medvopros .dd-small {
	display: block;
	font: normal 11px Arial, Helvetica, sans-serif;
	color: #303030;
	padding: 9px 0 0 0;
	clear: both;
}

.rs-medvopros .dd-phone-request-empty,
.rs-medvopros .dd-phone-request-incorrect {
	margin-left: 265px;
}