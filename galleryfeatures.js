(function() {

	function	addLinks() {
		const imgs = document.querySelectorAll('.image');

		imgs.forEach(function(img) {
			img.addEventListener('click', function() {
				openImagePopup(this);
			});
		});
	}

	function	addLikes() {
		const likes = document.querySelectorAll('.likes');

		likes.forEach(function(like) {
			let id = like.id.substr(6);
			let xhttp = new XMLHttpRequest();
			xhttp.open('POST', 'likes.php', true);
			xhttp.setRequestHeader('Content-type', 'Application/x-www-form-urlencoded');
			xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
			xhttp.onload = function () {
				if (xhttp.status == 200) {
					var result = JSON.parse(xhttp.responseText);
					like.innerHTML = result.likes_total;
					
				}
			};
			xhttp.send('nb_likes=' + id);
		})
	}

	function	addComments() {
		const comments = document.querySelectorAll('.comments');

		comments.forEach(function(comment) {
			let id = comment.id.substr(9);
			let xhttp = new XMLHttpRequest();
			xhttp.open('POST', 'comments.php', true);
			xhttp.setRequestHeader('Content-type', 'Application/x-www-form-urlencoded');
			xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
			xhttp.onload = function () {
				if (xhttp.status == 200) {
					var result = JSON.parse(xhttp.response);
					comment.innerHTML = result.comments_total;
					
				}
			};
			xhttp.send('nb_comments=' + id);
		})
	}

	window.addEventListener('load', addLinks, false);
	window.addEventListener('load', addLikes, false);
	window.addEventListener('load', addComments, false);
	window.addEventListener('resize', resize, false);
})();

function	resize() {
	let c = document.getElementById('allComments');
	if (document.getElementById('popup').style.display == 'flex') {
		let imgHeight = document.getElementById('bigImage').height;
		c.style.height = (window.innerWidth > 575) ? imgHeight - 130 + 'px': window.innerHeight - imgHeight - 200 + 'px';
		console.log(window.innerHeight)
		console.log(c.style.height);
		console.log(imgHeight);
	}
}

function	like(element) {
	let img_id = element.name;
	let xhttp = new XMLHttpRequest();
	xhttp.open('POST', 'likes.php', true);
	xhttp.setRequestHeader('Content-type', 'Application/x-www-form-urlencoded');
	xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

	xhttp.onload = function () {
		if (xhttp.status == 200) {
			var result = JSON.parse(xhttp.responseText);
			if (result.success) {
				let likeBoxes = document.querySelectorAll("#likes_" + element.name);
				likeBoxes.forEach(function(like) {
					like.innerHTML = result.likes_total;
				})
			}
			else {
				let error = document.getElementById("error_" + element.name);
				console.log(result.err_mess);
				error.innerHTML = result.err_mess;
				error.style.display = 'block';
				document.getElementById('errorBox').innerHTML = result.err_mess;
				document.getElementById('errorBox').style.display = 'block';
				setTimeout(function(){
					document.getElementById('errorBox').className = 'hide';
				}, 5000);
			}
		}
	};
	xhttp.send('img_id=' + img_id);
}

function	comment(element) {
	let img_id = element.name;
	let comment = document.getElementById('newComment').value;
	document.getElementById('newComment').value = "";
	console.log(element);
	console.log(comment);
	let xhttp = new XMLHttpRequest();
	xhttp.open('POST', 'comments.php', true);
	xhttp.setRequestHeader('Content-type', 'Application/x-www-form-urlencoded');
	xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xhttp.onload = function () {
		if (xhttp.status == 200) {
			var result = JSON.parse(xhttp.responseText);
			if (result.success) { // add success boolean to JSON
				fetchAllComments(element.name);
			} else {
				let error = document.getElementById("error_" + element.name);
				error.innerHTML = result['err_mess'];
				error.style.display = 'block';
				document.getElementById('errorBox').innerHTML = result['err_mess'];
				document.getElementById('errorBox').style.display = 'block';
			} 
		}
	};
	console.log('img_id=' + img_id + 'newComment=' + comment);
	xhttp.send('newComment=' + comment + '&img_id=' + img_id);
	comment = "";
}

function	fetchAllComments(id) {
	let comments = document.getElementById('allComments');
	comments.innerHTML = "";

	let xhttp = new XMLHttpRequest();
	xhttp.open('POST', 'comments.php', true);
	xhttp.setRequestHeader('Content-type', 'Application/x-www-form-urlencoded');
	xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xhttp.onload = function () {
		if (xhttp.status == 200) {
			let result = JSON.parse(xhttp.responseText);
			if (result['err_mess']) {
				document.getElementById('commentHeader').innerHTML = result['err_mess'];
			} else {
				let nbComms = result.length;
				if (nbComms == 1)
					document.getElementById('commentHeader').innerHTML = nbComms + " comment";
				else
					document.getElementById('commentHeader').innerHTML = nbComms + " comments";
				document.getElementById('comments_' + id).innerHTML = nbComms;
				for (var key in result) {
					let data = result[key];
					comments.innerHTML += "<b>" + data.uname + "</b>  " + data.comment + "<br />";
				}
			}
		}
		comments.scrollTop = comments.scrollHeight;
	};
	xhttp.send('allComments=' + id);
}

function 	openImagePopup(element) {
	let popup = document.getElementById('popup');
	popup.style.display = 'flex';
	popup.style.justifyContent = 'center';

	document.getElementById('creator').innerHTML = document.getElementById('creator_' + element.name).innerHTML;

	let image = document.createElement('img');
	image.setAttribute('class', 'popupImage');
	image.setAttribute('id', 'bigImage');
	image.src = document.getElementById(element.name).src;
	document.getElementById('imageBox').appendChild(image);

	fetchAllComments(element.name);

	let like = document.getElementById('likes_' + element.name);
	let likesClone = like.cloneNode(true);
	let likeImg = document.getElementById('likeImg_' + element.name);
	let likeImgClone = likeImg.cloneNode(true);

	document.getElementById('likeBox').appendChild(likeImgClone);
	document.getElementById('likeBox').appendChild(likesClone);

	let comButton = document.getElementById('commentSubmit');
	comButton.setAttribute('name', element.name);
	comButton.addEventListener('click', function(e) {
		e.preventDefault();
		comment(this);
	});

	var closeButton = document.getElementsByClassName("close")[0];
	closeButton.addEventListener('click', function(e) {
		e.preventDefault();
		close();
	});
	image.onload = function() {
		resize();
	};
}

function 	close() {
	document.getElementById('popup').style.display = 'none';
	document.getElementById('imageBox').innerHTML = "";
	document.getElementById('allComments').innerHTML = "";
	document.getElementById('allComments').style.height = 0 + 'px';
	document.getElementById('likeBox').innerHTML = "";
	document.getElementById('commentSubmit').removeAttribute('name');
}

window.onclick = function(event) {
	let popup = document.getElementById('popup');

	if (event.target == popup) {
		popup.style.display = 'none';
		document.getElementById('imageBox').innerHTML = "";
		document.getElementById('allComments').innerHTML = "";
		document.getElementById('allComments').style.height = 0 + 'px';
		document.getElementById('likeBox').innerHTML = "";
		document.getElementById('commentSubmit').removeAttribute('name');
	}
}