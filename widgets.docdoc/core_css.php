/* Универсальные стили для всех виджетов */
.dd-widget,
.dd-widget * {
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	margin: 0;
	padding: 0;
	font-family: Arial, Helvetica, sans-serif;
	border: 0;
}
.dd-button {
	width: 127px;
	height: 37px;
	background: #ffdb4d;
	background: -moz-linear-gradient(top, #ffdb4d 0%, #ffd00e 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #ffdb4d), color-stop(100%, #ffd00e));
	background: -webkit-linear-gradient(top, #ffdb4d 0%, #ffd00e 100%);
	background: -o-linear-gradient(top, #ffdb4d 0%, #ffd00e 100%);
	background: -ms-linear-gradient(top, #ffdb4d 0%, #ffd00e 100%);
	background: linear-gradient(to bottom, #ffdb4d 0%, #ffd00e 100%);
	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffdb4d', endColorstr='#ffd00e', GradientType=0);
	border: 1px solid #9b8e75;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
	cursor: pointer;
	font-size: 17px;
	color: #4c4c4c;
	text-shadow: 0px 1px 1px #ffff38;
	-webkit-box-shadow: 0px 2px 2px 0px rgba(0, 0, 0, 0.1);
	-moz-box-shadow: 0px 2px 2px 0px rgba(0, 0, 0, 0.1);
	box-shadow: 0px 2px 2px 0px rgba(0, 0, 0, 0.1);
	overflow: hidden;
}
.dd-button span {
	display: block;
	height: 35px;
	line-height: 35px;
	-webkit-box-shadow: inset 0px 0px 5px 0px rgba(255, 255, 255, 0.75);
	-moz-box-shadow: inset 0px 0px 5px 0px rgba(255, 255, 255, 0.75);
	box-shadow: inset 0px 0px 5px 0px rgba(255, 255, 255, 0.75);
	border-top: 1px solid #fff4ad;
}
.dd-select-container {
	border: 1px solid #ccc;
	overflow: hidden;
	height: 37px;
	background-position: right 15px;
	background-repeat: no-repeat;
	background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABcAAAAICAYAAAASqmTuAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAIFJREFUeNqskkEOwBAQRb/ewcLCysFcjYNZWVg4hBpVkVQTtC+Z2My8P5lgxpiERbTWbKaPpYy1FkIIKKVeG51zCCGQuMzNyuktAZxzSCkfTd57xBiXxMRxh9AgCUj0h7iXDwO+iPuz9LQTfRFfpiwfVKJfVMFujTZvudsbV04BBgCL0Iy2WbknrgAAAABJRU5ErkJggg==');
	width: 100%;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
}
.dd-select-container select {
	border-radius: 0;
	background: transparent;
	height: 37px;
	padding: 0 10px;
	border: 0;
	font-size: 12px;
	-webkit-appearance: none;
	appearance: none;
	-moz-appearance: none;
	width: 115%;
	outline: none;
}
.dd-icon {
	display: inline-block;
	width: 16px;
	height: 16px;
	vertical-align: middle;
	margin-right: 5px;
}
.dd-placeholder {
	color: #bbb !important;
}