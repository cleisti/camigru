function	like(element) {
	let img_id = element.id;
	let user_id = <?=$user_id?>;
	console.log(img_id);
	if (user_id) {
		let xhttp = new XMLHttpRequest();
		xhttp.open('GET', 'like.php', true);
		xhttp.setRequestHeader('Content-type', 'Application/x-www-form-urlencoded');
		xhttp.send('img_id=' + img_id + "&user_id=" + user_id);
	}
	else {
		alert("You must be logged in to like.");
	}
}