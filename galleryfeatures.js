(function() {

	function	addLinks() {
		let popup = document.getElementById('popup');
		console.log(popup);
		document.querySelectorAll('.gallery a').forEach(link => {
		link.onclick = e => {
		e.preventDefault();
		let img_meta = link.querySelector('img');
		let img = document.createElement('img');
		img.onload = () => {
			popup.innerHTML = `
				<div>
					<img src="${img.src}>
				</div>
			`;
			popup.style.display = 'flex';
		};
		img.src = img_meta.src;
		};
		});
	}
	window.addEventListener('load', addLinks, false);
})();