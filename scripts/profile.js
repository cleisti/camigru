window.addEventListener('load', startUp, false);
var usertitle;
var username;
var email;
var checkbox;

function	setUserInfo() {
	let params = "username=''";

	let xhttp = new XMLHttpRequest();
	xhttp.open('POST', 'get_user.php', true);
	xhttp.setRequestHeader('Content-type', 'Application/x-www-form-urlencoded');
	xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xhttp.onload = function () {
		if (xhttp.status == 200) {
			var result = JSON.parse(xhttp.response);
			usertitle.innerHTML = result.username;
			username.setAttribute('placeholder', result.username);
			email.setAttribute('placeholder', result.email);
			notifications.setAttribute('name', result.user_id)
		}
	};
	xhttp.send(params);
}

function	getCheckboxInfo() {
	let params = "getCheckboxInfo=''";

	let xhttp = new XMLHttpRequest();
	xhttp.open('POST', 'account/user_forms.php', true);
	xhttp.setRequestHeader('Content-type', 'Application/x-www-form-urlencoded');
	xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xhttp.onload = function () {
		if (xhttp.status == 200) {
			var result = xhttp.response;
			if (result == 1)
				document.getElementById('notifications').checked = false;
			else
				document.getElementById('notifications').checked = true;
		}
	};
	xhttp.send(params);
}

function    startUp() {
	setUserInfo();
	getCheckboxInfo();

	document.getElementById('notifications').addEventListener('change', function(e) {
		setNotifications();
	});
	document.getElementById('change_un').addEventListener('click', function(e) {
		e.preventDefault();
		changeUsername();
	});
	document.getElementById('change_email').addEventListener('click', function(e) {
		e.preventDefault();
		changeEmail();
	});
	document.getElementById('change_password').addEventListener('click', function(e) {
		e.preventDefault();
		changePassword();
	});

	usertitle = document.getElementById('userTitle');
	username = document.getElementsByName('new_un')[0];
	email = document.getElementsByName('new_email')[0];
	checkbos = document.getElementById('notifications');
}

function    setNotifications() {
	let params = "setNotifications=''";

	let xhttp = new XMLHttpRequest();
	xhttp.open('POST', 'account/user_forms.php', true);
	xhttp.setRequestHeader('Content-type', 'Application/x-www-form-urlencoded');
	xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xhttp.onload = function () {
		if (xhttp.status == 200) {
			var result = xhttp.response;
			if (result == 1)
				document.getElementById('notifications').checked = true;
			else
				document.getElementById('notifications').checked = false;
		}
	};
	xhttp.send(params);
}

function	changeUsername() {
	let params = "submit=ChangeUsername&new_un=" + document.getElementsByName('new_un')[0].value;
	let xhttp = new XMLHttpRequest();
	xhttp.open('POST', 'account/user_forms.php', true);
	xhttp.setRequestHeader('Content-type', 'Application/x-www-form-urlencoded');
	xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xhttp.onload = function () {
		if (xhttp.status == 200) {
			var message = document.getElementById('message');
			var result = xhttp.responseText;
			message.innerHTML = result;
			setUserInfo();
		}
	};
	xhttp.send(params);
	document.getElementById("username").reset();
}

function	changeEmail() {
	let params = "submit=ChangeEmail&new_email=" +
					document.getElementsByName('new_email')[0].value + "&validate_email=" +
					document.getElementsByName('validate_email')[0].value;
	let xhttp = new XMLHttpRequest();
	xhttp.open('POST', 'account/user_forms.php', true);
	xhttp.setRequestHeader('Content-type', 'Application/x-www-form-urlencoded');
	xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xhttp.onload = function () {
		if (xhttp.status == 200) {
			var message = document.getElementById('message');
			var result = xhttp.responseText;
			message.innerHTML = result;
			setUserInfo();
		}
	};
	xhttp.send(params);
	document.getElementById("email").reset();
}

function	changePassword() {
	let params = "submit=ChangePassword&new_pw=" +
					document.getElementsByName('new_pw')[0].value + "&validate_pw=" +
					document.getElementsByName('validate_pw')[0].value + "&old_pw=" +
					document.getElementsByName('old_pw')[0].value;
	let xhttp = new XMLHttpRequest();
	xhttp.open('POST', 'account/user_forms.php', true);
	xhttp.setRequestHeader('Content-type', 'Application/x-www-form-urlencoded');
	xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xhttp.onload = function () {
		if (xhttp.status == 200) {
			var message = document.getElementById('message');
			var result = xhttp.responseText;
			message.innerHTML = result;
			setUserInfo();
		}
		};
	xhttp.send(params);
	document.getElementById("password").reset();
}