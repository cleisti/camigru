(function() {

	function	addLinks() {
		const divs = document.querySelectorAll('#gallery div');

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
	let innerPopup = document.getElementById('innerPopup');
	console.log("element", element.innerHTML);
	popup.style.display = 'flex';
	innerPopup.style.display = 'flex-column';
	innerPopup.style.justifyContent = 'center';
	popup.style.justifyContent = 'center';
	popup.style.alignItems = "flex-start";
	innerPopup.innerHTML = element.innerHTML;
}

window.onclick = function(event) {
	let popup = document.getElementById('popup');
	console.log(event.target);
	if (event.target == popup) {
		popup.style.display = 'none';
	}
}