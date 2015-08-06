.dd-sign-up-popup {
	position: fixed;
	z-index: 9999;
	width: 480px;
	height: 320px;
	left: 50%;
	top: 50%;
	margin-left: -240px;
	margin-top: -160px;
	background: #fff;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
	-webkit-box-shadow: 0px 2px 6px 0px rgba(50, 50, 50, 0.75);
	-moz-box-shadow: 0px 2px 6px 0px rgba(50, 50, 50, 0.75);
	box-shadow: 0px 2px 6px 0px rgba(50, 50, 50, 0.75);
	display: none;
}
.dd-sign-up-popup-success, .dd-sign-up-popup-error {
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
	border: 1px solid #ccc;
	position: fixed;
	z-index: 9999;
	width: 340px;
	height: 220px;
	left: 50%;
	top: 50%;
	margin-left: -170px;
	margin-top: -110px;
	background: #fff;
	background: -moz-linear-gradient(top, #ffffff 0%, #ffffff 50%, #f7f7f5 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #ffffff), color-stop(50%, #ffffff), color-stop(100%, #f7f7f5));
	background: -webkit-linear-gradient(top, #ffffff 0%, #ffffff 50%, #f7f7f5 100%);
	background: -o-linear-gradient(top, #ffffff 0%, #ffffff 50%, #f7f7f5 100%);
	background: -ms-linear-gradient(top, #ffffff 0%, #ffffff 50%, #f7f7f5 100%);
	background: linear-gradient(to bottom, #ffffff 0%, #ffffff 50%, #f7f7f5 100%);
	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#f7f7f5', GradientType=0);
	display: none;
}

.dd-sign-up-overlay {
	position: fixed;
	z-index: 8888;
	width: 100%;
	height: 100%;
	left: 0;
	top: 0;
	background: rgba(0, 0, 0, .3);
	display: none;
}
.dd-sign-up-popup-close {
	background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA0AAAAOCAYAAAD0f5bSAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAYFJREFUeNp8UstKw1AQzautaSWGtA24SFe6cOVGBMlWN24s/oo7V4Jf4A/oN+jCL3AtFOoDIVBbWtJFkxpMDEmTeG505JKFA5M7j5w5M/eOUBSF8KvbnF3VFnSTfPYx8zz3V6tVkGXZG3yNByB2jXyCM8Q/dwS68DyvcByn8H0/RXKOmEGAOI59lmOaJMkS8T1F4ARg5nc0TXvFeZ+m6cl0OtUpX5Q0QkNk7UHfx+PxGiqXyXa7ndZqtS/XdTUCmKYZqqo6kGXZFn/AwiX6PptMJioBeel2u1Gz2XwCYJ/5BGJiAfgyGo1aPIBnoJhEBgDnuJ20ygLmhiiKWzA3KFYyAXCDofuz2UznmP/EMIwMl+NJkrQDd8GYbDD02S0RAC199nq9AC3RrcpBEHRQfEDtHYVhqFdmGCqKYluWFaE6AUW80zrMYxZZoGLMAZ7hH8AdYpYrMEZUsA7B8cgerA7Noihagv6jundo/RbzLtlmwH6gNSLg6T8Luws9JP9bgAEApRlcEoM7tzMAAAAASUVORK5CYII=') no-repeat center center;
	width: 30px;
	height: 30px;
	cursor: pointer;
	position: absolute;
	top: 0;
	right: 0;
}
.dd-sign-up-popup-title {
	line-height: 25px;
	padding: 20px;
	text-align: center;
	color: #7c7c7c;
	font-size: 16px;
}
.dd-sign-up-popup-form-container {
	min-height: 37px;
	margin: 11px 39px;
	padding-left: 110px;
	position: relative;
}
.dd-sign-up-popup-form-label {
	width: 110px;
	position: absolute;
	left: 0;
	top: 0;
	color: #999;
	font-size: 12px;
	line-height: 37px;
}
.dd-sign-up-popup-form-label span {
	color: #ff6700;
}
.dd-sign-up-popup-form-input {
	height: 35px !important;
	width: 100%;
	border: 1px solid #ccc;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
	padding-left: 7px !important;
	padding-right: 47px !important;
	color: #010101;
	font-size: 14px !important;
	font-weight: bold;
	position: relative;
	z-index: 1;
}
.dd-sign-up-popup-form-success {
	position: absolute;
	z-index: 2;
	width: 14px;
	height: 10px;
	right: 16px;
	top: 13px;
	background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA4AAAAKCAYAAACE2W/HAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAFlJREFUeNpi/P//PwOxQGv1WrDia6HBjIzEaoRpggEmcjTBNYIksEni0gRyKhOyBLoiXJqwOhWmGJ8mEAAHDi5n4tIEtxFdkJAmFKfi0oxLnAmfInwuAQgwAKBGN4r84RLTAAAAAElFTkSuQmCC') no-repeat;
	display: none;
}
.dd-sign-up-popup-under-form-description {
	padding-left: 150px;
	margin-top: 15px;
	font-size: 11px;
	color: #999;
}
.dd-sign-up-popup-submit-button-container {
	text-align: center;
	padding-top: 30px;
}
.dd-sign-up-popup-form-error {
	font-size: 11px;
	color: #F00;
	display: none;
}
.dd-success-message {
	display: none;
}
.dd-sign-up-popup-success-text, .dd-sign-up-popup-error-text {
	color: #7c7c7c;
	font-size: 12px;
	text-align: center;
	padding: 0 20px;
}
.dd-sign-up-popup-error-text {
	color: #f00;
}