var width = 320;
var height = 0;
var streaming = false;  
var video = null;
var canvas = null;
var index = 0;
var count = 0;
var startbutton = null;
var imgContainer = null;
var canvasData = null;

(function() {

	function startup() {
	  video = document.getElementById('video');
	  canvas = document.getElementById('canvas');
	  startbutton = document.getElementById('startbutton');
	  imgContainer = document.getElementById('photo');

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
		startbutton.disabled = true;

		const filters = document.querySelectorAll('.filter');

		filters.forEach(function(filter) {
			filter.addEventListener('click', function() {
				selectFilter(this);
			});
		});

		let image_divs = document.querySelectorAll('#gallery div');
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
	window.addEventListener('load', load_images, false);
})();

function	takepicture() {
	let context = canvas.getContext('2d');
	
	if (width && height && filters) {
		canvas.width = width;
		canvas.height = height;
		context.drawImage(video, 0, 0, width, height);

		canvasData = canvas.toDataURL('image/png');

		let newPhoto = document.createElement('img');
		newPhoto.setAttribute('width', width);
		newPhoto.setAttribute('height', height);
		newPhoto.setAttribute('src', canvasData);
	
		imgContainer.appendChild(newPhoto);
		let camera = document.getElementById('camera');
		camera.insertBefore(imgContainer, camera.firstChild);

		let cameraBtn = document.getElementById('startbutton');
		cameraBtn.style.display = 'none';
		
		let newBtn = document.getElementById('new');
		newBtn.style.display = 'block';
		newBtn.addEventListener('click', function() {
			newPicture();
		});

		let saveBtn = document.getElementById('save');
		saveBtn.style.display = 'block';
		saveBtn.addEventListener('click', function() {
			save();
		});
		index++;
	}
}

function	selectFilter(element) {
	console.log(element.width);
	var imgContainer = document.getElementById('photo');
	
	if (element.dataset.clickcount == 0 && width && height) {
		element.setAttribute('style', 'border: 2px solid red;');
		let selectedFilters = document.querySelector('#selectedFilters');
		let filter = document.createElement('img');
		filter.setAttribute('name', element.id);
		filter.setAttribute('width', width);
		filter.setAttribute('height', height);
		filter.setAttribute('style', 'position: absolute; z-index: 2;');
		filter.setAttribute('src', element.src);
		selectedFilters.appendChild(filter);
		element.dataset.clickcount = 1;
		count++;

	}
	else {
		element.removeAttribute('style', 'border: 2px solid red;');
		let h = document.getElementsByName(element.id);
		h[0].remove();
		element.dataset.clickcount = 0;
		count--;
	}
	if (count > 0 && imgContainer.dataset.uploaded == 0)
	{
		startbutton.disabled = false;
	}
	else
		startbutton.disabled = true;
}

function	save() {
		let	filters;
		let src;
		let filter_images = document.querySelectorAll('#selectedFilters img');
		filter_images.forEach(function(filter) {
			let fil = filter.name + ',';
			if (!filters)
				filters = fil;
			else
				filters += fil;
		})
		src = encodeURIComponent(imgContainer.lastChild.src);
		uploaded = (imgContainer.dataset.uploaded == 1) ? 1 : 0;
		var xhttp = new XMLHttpRequest();
		xhttp.open('POST', 'save.php', true);
		xhttp.setRequestHeader('Content-type', 'Application/x-www-form-urlencoded');
		if (xhttp.status == 200) {
			console.log(xhttp.responseText);
			update_gallery();
		}
		xhttp.send('filter=' + filters + '&image=' + canvasData + '&uploaded=' + uploaded);
}

function	newPicture() {
	imgContainer.innerHTML = "";
	imgContainer.dataset.uploaded = 0;

	document.getElementById('new').style.display = 'none';

	document.getElementById('startbutton').style.display = 'block';

	let filters = document.querySelectorAll('#selectedFilters img');
	filters.forEach(function(filter) {
		let filterId = document.getElementById(filter.name);
		selectFilter(filterId);
	})

	let saveBtn = document.getElementById('save')
	saveBtn.removeEventListener('click', function(){
		save()
	});
	saveBtn.style.display = 'none';
}

function	remove_image(element) {
	let parent = element.parentNode;
	let images = parent.getElementsByTagName('img');
	let image = images[0];
	alert("Remove image?");
	var xhttp = new XMLHttpRequest();
	xhttp.open('POST', 'remove.php', true);
	xhttp.setRequestHeader('Content-type', 'Application/x-www-form-urlencoded');
	if (xhttp.status == 200) {
		console.log(xhttp.responseText);
		update_gallery(); // create this function
	}
	xhttp.send('img_id=' + image.id);
}

function	uploadImageToCanvas(element) {
	canvas.width = width;
	canvas.height = height;
	var img = new Image;
	img.src = URL.createObjectURL(element.files[0]);
	img.onload = function() {
		canvas.getContext('2d').drawImage(img, 0, 0, width, height);
		canvasData = canvas.toDataURL("image/png");
	}

	img.setAttribute('style', 'z-index: 1;'); // make a class
	img.setAttribute('width', width);
	img.setAttribute('height', height);
	imgContainer.appendChild(img);
	imgContainer.dataset.uploaded = 1;

	let newBtn = document.getElementById('new');
	newBtn.style.display = 'block';
	newBtn.addEventListener('click', function() {
		newPicture();
	});

	let saveBtn = document.getElementById('save');
	saveBtn.style.display = 'block';
	saveBtn.addEventListener('click', function() {
		save();
	});
}

function	load_images() {
	
}