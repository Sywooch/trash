var _ga = _ga || {};

_ga.trackSocial = function(opt_pageUrl, opt_trackerName, opt_targetUrl) {
	_ga.trackFacebook(opt_pageUrl, opt_trackerName);
	_ga.trackTwitter(opt_pageUrl, opt_trackerName);
	_ga.trackVkontakte(opt_pageUrl, opt_trackerName, opt_targetUrl);
};

_ga.trackFacebook = function(opt_pageUrl, opt_trackerName) {
	var trackerName = _ga.buildTrackerName_(opt_trackerName);
	try {
		if (FB && FB.Event && FB.Event.subscribe) {
			FB.Event.subscribe('edge.create', function(targetUrl) {
				ga('send', 'social', 'facebook', 'like', targetUrl);
			});
			FB.Event.subscribe('edge.remove', function(targetUrl) {
				ga('send', 'social', 'facebook', 'unlike', targetUrl);
			});
			FB.Event.subscribe('message.send', function(targetUrl) {
				ga('send', 'social', 'facebook', 'send', targetUrl);
			});
		}
	} catch (e) {}
};

_ga.buildTrackerName_ = function(opt_trackerName) {
	return opt_trackerName ? opt_trackerName + '.' : '';
};

_ga.trackTwitter = function(opt_pageUrl, opt_trackerName) {
	var trackerName = _ga.buildTrackerName_(opt_trackerName);
	try {
		if (twttr && twttr.events && twttr.events.bind) {
			twttr.events.bind('tweet', function(event) {
				if (event) {
					var targetUrl; // Default value is undefined.
					if (event.target && event.target.nodeName == 'IFRAME') {
						targetUrl = _ga.extractParamFromUri_(event.target.src, 'url');
					}
					ga('send', 'social', 'twitter', 'tweet', targetUrl);
				}
			});
		}
	} catch (e) {}
};

_ga.trackVkontakte = function(opt_pageUrl, opt_trackerName, opt_targetUrl) {
	var trackerName = _ga.buildTrackerName_(opt_trackerName);
	try {
		if (VK && VK.Observer && VK.Observer.subscribe) {
			VK.Observer.subscribe('widgets.like.liked', function() {
				ga('send', 'social', 'vkontakte', 'like', opt_targetUrl);
			});
			VK.Observer.subscribe('widgets.like.unliked', function() {
				ga('send', 'social', 'vkontakte', 'unlike', opt_targetUrl);
			});
		}
	} catch (e) {}
};

_ga.extractParamFromUri_ = function(uri, paramName) {
	if (!uri) {
		return;
	}
	var uri = uri.split('#')[0];  // Remove anchor.
	var parts = uri.split('?');  // Check for query params.
	if (parts.length == 1) {
		return;
	}
	var query = decodeURI(parts[1]);

	// Find url param.
	paramName += '=';
	var params = query.split('&');
	for (var i = 0, param; param = params[i]; ++i) {
		if (param.indexOf(paramName) === 0) {
			return unescape(param.split('=')[1]);
		}
	}
	return;
};