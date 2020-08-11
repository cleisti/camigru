(function() {

	function	addLinks() {
		document.querySelectorAll('.gallery a').forEach(link => {
		link.onclick = e => {
			e.preventDefault();
			openImagePopup(this);
		};
		});
	}
	window.addEventListener('load', addLinks, false);
})();

function 	openImagePopup(element) {
	let popup = document.getElementById('popup');
	console.log(popup);
	popup.style.display = 'block';
	popup.innerHTML = "hello";
	alert('here');
}