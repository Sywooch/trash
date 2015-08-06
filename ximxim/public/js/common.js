/**
 * Add new comment
 *
 * @param oFormElement
 *
 * @returns {boolean}
 */
function sendComment(oFormElement) {
	var xhr = typeof XMLHttpRequest != 'undefined' ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
	xhr.onload = function () {
		var data = JSON.parse(xhr.responseText);
		if (data.status === true) {
			var obj = document.getElementById("comments-container");
			var first = obj.firstChild;
			var newNode = document.createElement("div");
			newNode.innerHTML = data.html;
			obj.insertBefore(newNode, first);
			document.getElementById("comment-form").style.display = "none";
			document.getElementById("comment-success").style.display = "block";
		} else {
			alert("Please complete all fields");
		}
	};
	xhr.open(oFormElement.method, oFormElement.action, true);
	xhr.send(new FormData(oFormElement));
	return false;
}