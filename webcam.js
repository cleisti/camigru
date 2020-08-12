var width = 320;
var height = 0;
var index = 0;

(function() {
  
	var streaming = false;
  
	var video = null;
	var canvas = null;
	var startbutton = null;

	function startup() {
	  video = document.getElementById('video');
	  canvas = document.getElementById('canvas');
	  startbutton = document.getElementById('startbutton');

	  navigator.mediaDevices.getUserMedia({video: true, audio: false})
	  .then(function(stream) {
		video.srcObject = stream;
		// video.setAttribute('style', 'filter: contrast(300%);');
		video.play();
	  })
	  .catch(function(err) {
		console.log("An error occurred: " + err);
	  });

	  video.addEventListener('canplay', function(ev){
		if (!streaming) {
			height = video.videoHeight / (video.videoWidth/width);

			if (isNaN(height)) {
				height = width / (4/3);
			}

			video.setAttribute('width', width);
			video.setAttribute('height', height);
			canvas.setAttribute('width', width);
			canvas.setAttribute('height', height);
			streaming = true;
		}
	  }, false);
  
		startbutton.addEventListener('click', function(ev) {
			takepicture();
			ev.preventDefault();
		}, false);

		const filters = document.querySelectorAll('.filter');

		filters.forEach(function(filter) {
			filter.addEventListener('click', function() {
				selectFilter(this);
			});
		});

		let image_divs = document.querySelectorAll('.gallery div');
		console.log(image_divs);

		image_divs.forEach(function(div) {
			let remove = document.createElement('img');
			remove.setAttribute('class', 'remove');
			remove.setAttribute('src', 'icons/trash.png');
			remove.addEventListener('click', function() {
				remove_image(this);
			})
			div.appendChild(remove);
		})
	}
	
	window.addEventListener('load', startup, false);
})();

	function	addFilters(imgContainer) {
		let filters = document.querySelectorAll('.camera img');

		filters.forEach(function(filter) {
			let add = document.createElement('img');
			add.setAttribute('id', filter.id);
			add.setAttribute('width', (width / 2));
			add.setAttribute('height', (height / 2));
			add.setAttribute('style', 'position: absolute; z-index: 1;');
			add.setAttribute('src', filter.src);
			imgContainer.appendChild(add);
			console.log('filter added to preview');
		})
	}

function	takepicture() {
	let context = canvas.getContext('2d');
	let name = "photo" + index;
	
	if (width && height && filters) {
		canvas.width = width;
		canvas.height = height;
		context.drawImage(video, 0, 0, width, height);

		let data = canvas.toDataURL('image/png');
		let	imgContainer = document.createElement('div');
		imgContainer.setAttribute("id", name);
		imgContainer.setAttribute("name", name);
		imgContainer.setAttribute('position', 'relative');
		imgContainer.setAttribute('display', 'inline-block');
		imgContainer.addEventListener('click', function() {
			save(this);
		});

		let newPhoto = document.createElement('img');
		newPhoto.setAttribute('width', (width / 2));
		newPhoto.setAttribute('height', (height / 2));
		newPhoto.setAttribute('style', 'z-index: -1;');
		newPhoto.setAttribute('src', data);
	
		addFilters(imgContainer);
		imgContainer.appendChild(newPhoto);
		let output = document.getElementById('output');
		output.insertBefore(imgContainer, output.firstChild);
		console.log(index); // remove
		// document.getElementById('image-tag').value = data;
		// var data2 = data;
		// $(".image-tag").val(data);
		index++;
	}
}

function	selectFilter(element) {
	console.log(element.width);
	
	if (element.dataset.clickcount == 0 && width && height) {
		element.setAttribute('style', 'border: 2px solid red;');
		let cam = document.querySelector('.camera');
		let filter = document.createElement('img');
		filter.setAttribute('id', element.id);
		filter.setAttribute('name', element.id);
		filter.setAttribute('width', width);
		filter.setAttribute('height', height);
		filter.setAttribute('style', 'position: absolute; z-index: 1;');
		filter.setAttribute('src', element.src);
		cam.appendChild(filter);
		console.log('filter added'); //remove
		element.dataset.clickcount = 1;
	}
	else {
		element.removeAttribute('style', 'border: 2px solid red;');
		let cam = document.querySelector('.camera');
		let filters = document.querySelectorAll('.camera img');
		let h = document.getElementsByName(element.id);
		console.log(element.id, h); // remove
		let id = element.id;
		cam.removeChild(h[0]);
		element.dataset.clickcount = 0;
	}
}

function	save(element) {
	if (confirm("Save image?")) {
		let	filters;
		console.log(element.id); // remove
		let filter_images = element.querySelectorAll('img');
		console.log(filter_images); // remove
		filter_images.forEach(function(filter) {
			let fil = filter.id + ',';
			console.log(fil); // remove
			if (!filters)
				filters = fil;
			else
				filters += fil;
		})
		console.log(filters); // remove
		var xhttp = new XMLHttpRequest();
		xhttp.open('POST', 'save.php', true);
		xhttp.setRequestHeader('Content-type', 'Application/x-www-form-urlencoded');
		xhttp.send('filter=' + filters + '&image=' + encodeURIComponent(element.lastChild.src));
	}
}

function	remove_image(element) {
	let parent = element.parentNode;
	let images = parent.getElementsByTagName('img');
	let image = images[0];
	alert("Remove image?");
	console.log(image.id);
	var xhttp = new XMLHttpRequest();
	xhttp.open('POST', 'remove.php', true);
	xhttp.setRequestHeader('Content-type', 'Application/x-www-form-urlencoded');
	xhttp.send('img_id=' + image.id);
}