$(function() {
	$(document).on('requestCreated', function(e, form, req_id, params) {
		if ($(form).hasClass('callback_form')) {
			trackCallBackForm(req_id, params);
		} else {
			trackRequestForm(req_id, params);
		}
	});

	// фиксирует нажатие кнопки “Записаться на прием”
	$('form.doctor_desc__request input.ui-btn').click(function() {
		trackRequestClick();
	});

	// фиксирует нажатие кнопки “мы перезвоним вам”
	$('.js-callmeback-tr').click(function() {
		trackCallBackClick();
	});

	function trackCallBackForm(req_id, params) {
		(function(h){function k(){var a=function(d,b){if(this instanceof AdriverCounter)d=a.items.length||1,a.items[d]=this,b.ph=d,b.custom&&(b.custom=a.toQueryString(b.custom,";")),a.request(a.toQueryString(b));else return a.items[d]};a.httplize=function(a){return(/^\/\//.test(a)?location.protocol:"")+a};a.loadScript=function(a){try{var b=g.getElementsByTagName("head")[0],c=g.createElement("script");c.setAttribute("type","text/javascript");c.setAttribute("charset","windows-1251");c.setAttribute("src",a.split("![rnd]").join(Math.round(1E6*Math.random())));c.onreadystatechange=function(){/loaded|complete/.test(this.readyState)&&(c.onload=null,b.removeChild(c))};c.onload=function(){b.removeChild(c)};b.insertBefore(c,b.firstChild)}catch(f){}};a.toQueryString=function(a,b,c){b=b||"&";c=c||"=";var f=[],e;for(e in a)a.hasOwnProperty(e)&&f.push(e+c+escape(a[e]));return f.join(b)};a.request=function(d){var b=a.toQueryString(a.defaults);a.loadScript(a.redirectHost+"/cgi-bin/erle.cgi?"+d+"&rnd=![rnd]"+(b?"&"+b:""))};a.items=[];a.defaults={tail256:document.referrer||"unknown"};a.redirectHost=a.httplize("//ad.adriver.ru");return a}var g=document;"undefined"===typeof AdriverCounter&&(AdriverCounter=k());new AdriverCounter(0,h)})
		({"sid":201377,"sz":"Spasibo_zvonok","bt":62,"custom":{"152":req_id,"153":params.cl_id}});
	};

	function trackRequestForm(req_id, params) {
		(function(h){function k(){var a=function(d,b){if(this instanceof AdriverCounter)d=a.items.length||1,a.items[d]=this,b.ph=d,b.custom&&(b.custom=a.toQueryString(b.custom,";")),a.request(a.toQueryString(b));else return a.items[d]};a.httplize=function(a){return(/^\/\//.test(a)?location.protocol:"")+a};a.loadScript=function(a){try{var b=g.getElementsByTagName("head")[0],c=g.createElement("script");c.setAttribute("type","text/javascript");c.setAttribute("charset","windows-1251");c.setAttribute("src",a.split("![rnd]").join(Math.round(1E6*Math.random())));c.onreadystatechange=function(){/loaded|complete/.test(this.readyState)&&(c.onload=null,b.removeChild(c))};c.onload=function(){b.removeChild(c)};b.insertBefore(c,b.firstChild)}catch(f){}};a.toQueryString=function(a,b,c){b=b||"&";c=c||"=";var f=[],e;for(e in a)a.hasOwnProperty(e)&&f.push(e+c+escape(a[e]));return f.join(b)};a.request=function(d){var b=a.toQueryString(a.defaults);a.loadScript(a.redirectHost+"/cgi-bin/erle.cgi?"+d+"&rnd=![rnd]"+(b?"&"+b:""))};a.items=[];a.defaults={tail256:document.referrer||"unknown"};a.redirectHost=a.httplize("//ad.adriver.ru");return a}var g=document;"undefined"===typeof AdriverCounter&&(AdriverCounter=k());new AdriverCounter(0,h)})
		({"sid":201377,"sz":"Spasibo_zapis","bt":62,"custom":{"151":req_id,"153":params.cl_id}});
	};

	function trackCallBackClick() {
		(function (h)
		{function k() {
			var a = function (d,b) {
				if (this instanceof AdriverCounter) d = a.items.length || 1,
					a.items[d] = this, b.ph = d,
				b.custom && (b.custom = a.toQueryString(b.custom,";")),
					a.request(a.toQueryString(b));
				else return a.items[d]};
			a.httplize = function (a) {return (/^\/\//.test(a)?location.protocol:"")+a};
			a.loadScript = function (a) {
				try {
					var b = g.getElementsByTagName("head")[0],
						c = g.createElement("script");
					c.setAttribute("type", "text/javascript");
					c.setAttribute("charset", "windows-1251");
					c.setAttribute("src",a.split("![rnd]").join(Math.round(1E6*Math.random())));
					c.onreadystatechange = function () {
						/loaded|complete/.test(this.readyState)&&(c.onload=null,b.removeChild(c))};
					c.onload = function () {b.removeChild(c)};
					b.insertBefore(c,b.firstChild)} catch (f) {}};
			a.toQueryString = function (a,b,c) {
				b = b || "&";c = c || "=";var f = [],e;
				for (e in a) a.hasOwnProperty(e) && f.push(e+c+escape(a[e]));
				return f.join(b)};
			a.request = function (d) {var b = a.toQueryString(a.defaults);
				a.loadScript(a.redirectHost+"/cgi-bin/erle.cgi?"+d+"&rnd=![rnd]"+(b?"&"+b:""))};
			a.items = [];
			a.defaults = { tail256: document.referrer || "unknown" };
			a.redirectHost = a.httplize("//ad.adriver.ru");return a}
			var g = document;
			"undefined" === typeof AdriverCounter && (AdriverCounter = k());
			new AdriverCounter(0, h)})
		({"sid":201377,"sz":"Obratnyi_zvonok","bt":62,"custom":{}});
	};

	function trackRequestClick() {
		(function (h)
		{function k() {
			var a = function (d,b) {
				if (this instanceof AdriverCounter) d = a.items.length || 1,
					a.items[d] = this, b.ph = d,
				b.custom && (b.custom = a.toQueryString(b.custom,";")),
					a.request(a.toQueryString(b));
				else return a.items[d]};
			a.httplize = function (a) {return (/^\/\//.test(a)?location.protocol:"")+a};
			a.loadScript = function (a) {
				try {
					var b = g.getElementsByTagName("head")[0],
						c = g.createElement("script");
					c.setAttribute("type", "text/javascript");
					c.setAttribute("charset", "windows-1251");
					c.setAttribute("src",a.split("![rnd]").join(Math.round(1E6*Math.random())));
					c.onreadystatechange = function () {
						/loaded|complete/.test(this.readyState)&&(c.onload=null,b.removeChild(c))};
					c.onload = function () {b.removeChild(c)};
					b.insertBefore(c,b.firstChild)} catch (f) {}};
			a.toQueryString = function (a,b,c) {
				b = b || "&";c = c || "=";var f = [],e;
				for (e in a) a.hasOwnProperty(e) && f.push(e+c+escape(a[e]));
				return f.join(b)};
			a.request = function (d) {var b = a.toQueryString(a.defaults);
				a.loadScript(a.redirectHost+"/cgi-bin/erle.cgi?"+d+"&rnd=![rnd]"+(b?"&"+b:""))};
			a.items = [];
			a.defaults = { tail256: document.referrer || "unknown" };
			a.redirectHost = a.httplize("//ad.adriver.ru");return a}
			var g = document;
			"undefined" === typeof AdriverCounter && (AdriverCounter = k());
			new AdriverCounter(0, h)})
		({"sid":201377,"sz":"zapis_priem","bt":62,"custom":{}});
	}
});
