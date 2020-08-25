var width = 320;
var height = 0;
var streaming = false;  
var video = null;
var canvas = null;
var index = 0;
var count = 0;
var startbutton = null;
var imgContainer = null;

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
})();

	// function	addFilters(imgContainer) {
	// 	let filters = document.querySelectorAll('.camera img');

	// 	filters.forEach(function(filter) {
	// 		let add = document.createElement('img');
	// 		add.setAttribute('name', filter.name);
	// 		add.setAttribute('width', width);
	// 		add.setAttribute('height', height);
	// 		add.setAttribute('style', 'position: absolute;');
	// 		add.setAttribute('src', filter.src);
	// 		imgContainer.appendChild(add);
	// 		console.log('filter added to preview');
	// 		let filterId = document.getElementById(filter.name);
	// 		console.log("FilterID: ", filterId);
	// 		selectFilter(filterId);
	// 	})
	// }

function	takepicture() {
	let context = canvas.getContext('2d');
	
	if (width && height && filters) {
		canvas.width = width;
		canvas.height = height;
		context.drawImage(video, 0, 0, width, height);

		let data = canvas.toDataURL('image/png');
		// let	imgContainer = document.getElementById('photo');

		let newPhoto = document.createElement('img');
		newPhoto.setAttribute('width', width);
		newPhoto.setAttribute('height', height);
		// newPhoto.setAttribute('style', 'z-index: -1;');
		newPhoto.setAttribute('src', data);
	
		// addFilters(imgContainer);
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
		console.log(index); // remove
		// document.getElementById('image-tag').value = data;
		// var data2 = data;
		// $(".image-tag").val(data);
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
		// filter.setAttribute('id', element.id);
		filter.setAttribute('name', element.id);
		filter.setAttribute('width', width);
		filter.setAttribute('height', height);
		filter.setAttribute('style', 'position: absolute; z-index: 2;');
		filter.setAttribute('src', element.src);
		selectedFilters.appendChild(filter);
		console.log('filter added'); //remove
		element.dataset.clickcount = 1;
		count++;

	}
	else {
		element.removeAttribute('style', 'border: 2px solid red;');
		let cam = document.querySelector('.camera');
		// let h = document.querySelectorAll('.camera img');
		let h = document.getElementsByName(element.id);
		console.log(element.id, h); // remove
		let id = element.id;
		h[0].remove();
		// cam.removeChild(h[0]);
		element.dataset.clickcount = 0;
		count--;
	}
	console.log("uploaded: ", imgContainer.width);
	if (count > 0 && imgContainer.dataset.uploaded == 0)
	{
		console.log('count: ', count);
		startbutton.disabled = false;
	}
	else
		startbutton.disabled = true;
}

function	save() {
		// let imgContainer = document.getElementById('photo');
		let	filters;
		let src;
		console.log(imgContainer.id); // remove
		let filter_images = document.querySelectorAll('#selectedFilters img');
		console.log(filter_images); // remove
		filter_images.forEach(function(filter) {
			let fil = filter.name + ',';
			console.log(fil); // remove
			if (!filters)
				filters = fil;
			else
				filters += fil;
		})
		console.log(filters); // remove
		src = encodeURIComponent(imgContainer.lastChild.src);
		console.log(src);
		uploaded = (imgContainer.dataset.uploaded == 1) ? 1 : 0;
		var xhttp = new XMLHttpRequest();
		xhttp.open('POST', 'save.php', true);
		xhttp.setRequestHeader('Content-type', 'Application/x-www-form-urlencoded');
		xhttp.send('filter=' + filters + '&image=' + src + '&uploaded=' + uploaded);
}

function	newPicture() {
	// let imgContainer = document.getElementById('photo');
	// imgContainer.style.display = 'none';
	imgContainer.innerHTML = "";
	imgContainer.dataset.uploaded = 0;

	document.getElementById('new').style.display = 'none';

	document.getElementById('startbutton').style.display = 'block';

	let filters = document.querySelectorAll('#selectedFilters img');
	console.log(filters);
	filters.forEach(function(filter) {
		let filterId = document.getElementById(filter.name);
		console.log("FilterID: ", filterId);
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
	console.log(image.id);
	var xhttp = new XMLHttpRequest();
	xhttp.open('POST', 'remove.php', true);
	xhttp.setRequestHeader('Content-type', 'Application/x-www-form-urlencoded');
	xhttp.send('img_id=' + image.id);
}

function	uploadImageToCanvas(element) {
	let file = element.files[0];
	console.log("filesize: ", file.size);
	let img = document.createElement('img');

	let reader = new FileReader();
	reader.onload = function(e) {
		img.setAttribute('src', e.target.result);
	};
	reader.readAsDataURL(file);
	// img.src = reader.result;

	console.log("src: ", img.src);

	let context = canvas.getContext('2d');
	canvas.width = width;
	canvas.height = height;
	context.drawImage(img, 0, 0, width, height);
	let data = canvas.toDataURL('image/png');
	let newPhoto = document.createElement('img');
	newPhoto.setAttribute('width', width);
	newPhoto.setAttribute('height', height);
	newPhoto.setAttribute('style', 'z-index: 1;');
	newPhoto.setAttribute('src', data);
	imgContainer.appendChild(newPhoto);

	// img.setAttribute('style', 'z-index: 1;');
	// img.setAttribute('width', width);
	// img.setAttribute('height', height);
	// imgContainer.appendChild(img);
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