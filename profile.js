window.addEventListener('load', getCheckBoxInfo, false);

function    getCheckBoxInfo() {
	let id = document.getElementById('notifications').name;
	let xhttp = new XMLHttpRequest();
	xhttp.open('POST', 'account/user_functions.php', true);
	xhttp.setRequestHeader('Content-type', 'Application/x-www-form-urlencoded');
	xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xhttp.onload = function () {
		if (xhttp.status == 200) {
			var result = JSON.parse(xhttp.responseText);
			if (result == 1)
				document.getElementById('notifications').checked = false;
			else
				document.getElementById('notifications').checked = true;
		}
	};
	xhttp.send('checkboxInfo=' + id);
}

function    setNotifications(element) {
	let id = element.name;
	let xhttp = new XMLHttpRequest();
	xhttp.open('POST', 'account/user_functions.php', true);
	xhttp.setRequestHeader('Content-type', 'Application/x-www-form-urlencoded');
	xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xhttp.onload = function () {
		if (xhttp.status == 200) {
			var result = JSON.parse(xhttp.responseText);
			if (result == 1)
				document.getElementById('notifications').checked = true;
			else
				document.getElementById('notifications').checked = false;
		}
	};
	xhttp.send('notifications=' + id);
}