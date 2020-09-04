function	like(element) {
	let img_id = element.id;
	let xhttp = new XMLHttpRequest();
	xhttp.open('POST', 'likes.php', true);
	xhttp.setRequestHeader('Content-type', 'Application/x-www-form-urlencoded');
	xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

	xhttp.onload = function () {
		if (xhttp.status == 200) {
			var result = JSON.parse(xhttp.responseText);
			if (result.success) {
				let paragraph = document.getElementById("likes_" + element.id);
				paragraph.innerHTML = result.likes_total;
			}
			else {
				let error = document.getElementById("error_" + element.id);
				console.log(result.err_mess);
				error.innerHTML = result.err_mess;
				error.style.display = 'block';
			}
		}
	};
	xhttp.send('img_id=' + img_id);
}