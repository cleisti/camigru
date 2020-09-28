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

		document.getElementById('save').addEventListener('click', function() {
			save();
		});
		document.getElementById('new').addEventListener('click', function() {
			newPicture();
		});

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
	}
	
	window.addEventListener('load', startup, false);
})();

function	takepicture() {
	let context = canvas.getContext('2d');
	
	if (width && height && filters) {
		canvas.width = width;
		canvas.height = height;
		context.drawImage(video, 0, 0, width, height);

		canvasData = canvas.toDataURL('image/png');

		let newPhoto = document.createElement('img');
		newPhoto.setAttribute('id', 'newPhoto');
		newPhoto.setAttribute('width', width);
		newPhoto.setAttribute('height', height);
		newPhoto.setAttribute('src', canvasData)
	
		imgContainer.appendChild(newPhoto);
		let camera = document.getElementById('camera');
		camera.insertBefore(imgContainer, camera.firstChild);

		let cameraBtn = document.getElementById('startbutton');
		cameraBtn.style.display = 'none';
		
		let newBtn = document.getElementById('new');
		newBtn.style.display = 'block';

		let saveBtn = document.getElementById('save');
		saveBtn.style.display = 'block';
		index++;
		document.getElementById('filters').style.display = 'none';
	}
}

function	selectFilter(element) {
	var imgContainer = document.getElementById('photo');
	
	if (element.dataset.clickcount == 0 && width && height) {
		element.setAttribute('style', 'border: 2px solid #0f005a;');
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
		let filter_images = document.querySelectorAll('#selectedFilters img');
		filter_images.forEach(function(filter) {
			let fil = filter.name + ',';
			if (!filters)
				filters = fil;
			else
				filters += fil;
		})
		var xhttp = new XMLHttpRequest();
		xhttp.open('POST', 'gallery/save.php', true);
		xhttp.setRequestHeader('Content-type', 'Application/x-www-form-urlencoded');
		xhttp.onload = function() {
			if (xhttp.status == 200) {
				load_images();
			}
		};
		xhttp.send('filter=' + filters + '&image=' + canvasData);
		newPicture();
}

function	newPicture() {
	imgContainer.innerHTML = "";
	imgContainer.dataset.uploaded = 0;

	document.getElementById('new').style.display = 'none';
	document.getElementById('save').style.display = 'none';

	document.getElementById('startbutton').style.display = 'block';

	let filters = document.querySelectorAll('#selectedFilters img');
	filters.forEach(function(filter) {
		let filterId = document.getElementById(filter.name);
		selectFilter(filterId);
	})

	document.getElementById('filters').style.display = 'flex';
}

function	remove_image(element) {
	let parent = element.parentNode;
	let images = parent.getElementsByTagName('img');
	let image = images[0];
	alert("Remove image?");
	var xhttp = new XMLHttpRequest();
	xhttp.open('POST', 'gallery/remove.php', true);
	xhttp.setRequestHeader('Content-type', 'Application/x-www-form-urlencoded');
	xhttp.onload = function() {
		if (xhttp.status == 200) {
			load_images();
		}
	};
	xhttp.send('img_id=' + image.id);
}

function	uploadImageToCanvas(element) {
	canvas.width = width;
	canvas.height = height;
	var img = new Image();
	img.src = URL.createObjectURL(element.files[0]);
	img.onload = function() {
		canvas.getContext('2d').drawImage(img, 0, 0, width, height);
		canvasData = canvas.toDataURL("image/png");
	}

	img.setAttribute('style', 'z-index: 1;');
	img.setAttribute('width', width);
	img.setAttribute('height', height);
	imgContainer.appendChild(img);
	imgContainer.dataset.uploaded = 1;

	let newBtn = document.getElementById('new');
	newBtn.style.display = 'block';

	let saveBtn = document.getElementById('save');
	saveBtn.style.display = 'block';
}

function	load_images() {
	let xhttp = new XMLHttpRequest();
	xhttp.open('POST', 'gallery/getimages.php', true);
	xhttp.setRequestHeader('Content-type', 'Application/x-www-form-urlencoded');
	xhttp.onload = function () {
		if (xhttp.status == 200) {
			let images = JSON.parse(xhttp.response);
			let gallery = document.getElementById('gallery');
			gallery.innerHTML = "";
			images.forEach(image => {
				gallery.innerHTML += "<div class='card' style='margin: 5px; max-width: 150px;'>" +
				"<img class='card-img-top' name='image' id='" + image.img_id +
				"' src='" + image.path + "'><img class='remove' src='icons/trash.png' onclick='remove_image(this)'></div></div>";
			})
		}
	};
	xhttp.send('get_images=1');
}

window.addEventListener('load', load_images, false);