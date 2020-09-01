function	like(element) {
	let img_id = element.id;
	console.log(img_id);
	let xhttp = new XMLHttpRequest();
		xhttp.open('POST', 'like.php', true);
		xhttp.setRequestHeader('Content-type', 'Application/x-www-form-urlencoded');
		xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
		xhttp.onload = function () {
			if (xhttp.status == 200) {
				var result = xhttp.responseText;
				let paragraph = document.getElementById("show_" + element.id);
				console.log(paragraph);
				paragraph.innerHTML = result;
				// element.dataset.liked = 1;
			}
		};
		xhttp.send('img_id=' + img_id);
}