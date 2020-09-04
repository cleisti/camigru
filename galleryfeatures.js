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
			console.log('id=' + id);
			let xhttp = new XMLHttpRequest();
			xhttp.open('POST', 'likes.php', true);
			xhttp.setRequestHeader('Content-type', 'Application/x-www-form-urlencoded');
			xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
			xhttp.onload = function () {
				if (xhttp.status == 200) {
					var result = JSON.parse(xhttp.responseText);
					console.log(result);
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
			console.log('id: ' + id);
			let xhttp = new XMLHttpRequest();
			xhttp.open('POST', 'comments.php', true);
			xhttp.setRequestHeader('Content-type', 'Application/x-www-form-urlencoded');
			xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
			xhttp.onload = function () {
				if (xhttp.status == 200) {
					var result = JSON.parse(xhttp.responseText);
					console.log(result);
					comment.innerHTML = result.comments_total;
					
				}
			};
			xhttp.send('nb_comments=' + id);
		})
	}

	window.addEventListener('load', addLinks, false);
	window.addEventListener('load', addLikes, false);
	window.addEventListener('load', addComments, false);
})();

function 	openImagePopup(element) {
	let popup = document.getElementById('popup');
	let innerPopup = document.getElementById('innerPopup');
	// console.log("element", element.innerHTML);
	popup.style.display = 'flex';
	innerPopup.style.display = 'flex-column';
	innerPopup.style.justifyContent = 'center';
	popup.style.justifyContent = 'center';
	popup.style.alignItems = "flex-start";
	innerPopup.innerHTML = element.parentNode.innerHTML;

	let commentBox = document.createElement('div');
	commentBox.style.display = 'flex';
	innerPopup.appendChild(commentBox);
}

window.onclick = function(event) {
	let popup = document.getElementById('popup');
	if (event.target == popup) {
		popup.style.display = 'none';
	}
}