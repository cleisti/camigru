(function() {

	function	addLinks() {
		const divs = document.querySelectorAll('.gallery div');

		divs.forEach(function(div) {
			div.addEventListener('click', function() {
				openImagePopup(this);
			});
		});
	}
	window.addEventListener('load', addLinks, false);
})();

function 	openImagePopup(element) {
	let popup = document.getElementById('popup');
	console.log("element", element.innerHTML);
	popup.style.display = 'block';
	popup.innerHTML = element.innerHTML;
	element.innerHTML.style.width = '100%';
}

window.onclick = function(event) {
	let popup = document.getElementById('popup');
	console.log(event.target);
	if (event.target == popup) {
		popup.style.display = 'none';
	}
}