var width = 320;    // We will scale the photo width to this
var height = 0;		// This will be computed based on the input stream
var index = 0;

(function() {
	// The width and height of the captured photo. We will set the
	// width to the value defined here, but the height will be
	// calculated based on the aspect ratio of the input stream.
  
	// |streaming| indicates whether or not we're currently streaming
	// video from the camera. Obviously, we start at false.
  
	var streaming = false;
  
	// The various HTML elements we need to configure or control. These
	// will be set by the startup() function.
  
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

		  // Firefox currently has a bug where the height can't be read from
		  // the video, so we will make assumptions if this happens.

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
  
	  startbutton.addEventListener('click', function(ev){
		takepicture();
		ev.preventDefault();
	  }, false);
	}
	
	window.addEventListener('load', startup, false);
})();

	// Capture a photo by fetching the current contents of the video
	// and drawing it into a canvas, then converting that to a PNG
	// format data URL. By drawing it on an offscreen canvas and then
	// drawing that to the screen, we can change its size and/or apply
	// other changes before drawing it.
  
	function takepicture() {
	  var context = canvas.getContext('2d');
	  if (width && height) {
		canvas.width = width;
		canvas.height = height;
		context.drawImage(video, 0, 0, width, height);
	  
		var data = canvas.toDataURL('image/png');

		var name = "photo" + index;
		var newPhoto = document.createElement('IMG');
		newPhoto.setAttribute("id", name);
		newPhoto.setAttribute('width', (width / 2));
		newPhoto.setAttribute('height', (height / 2));
		newPhoto.setAttribute('src', data);
		newPhoto.addEventListener('click', function() {
			save(this);
		});
		var element = document.getElementById('output');
		element.insertBefore(newPhoto, element.firstChild);
		console.log(index);


		// document.getElementById('image-tag').value = data;

		// var data2 = data;

		// $(".image-tag").val(data);

		index++;
	  }
	}

	function	addFilter(filter) {
		if (width & height) {
			var filterCanvas = document.getElementById("filtercanvas");
			if (filterCanvas)
				filter.parentNode.removeChild(filter);
			var fCanvas = document.createElement('canvas');
			var context = canvas.getContext('2d');
			fCanvas.width = width;
			fCanvas.heigh = height;
			fCanvas.draggable = true;
			fCanvas.id = 'filtercanvas';
			document.querySelector('.camera').appendChild(fCanvas);
			var img = new Image();
			img.src = filter.src;
			context.drawImage(img, 0, 0, width, height);
		}
	}

	// document.querySelector('#bwnoise').addEventListener('click', addFilter(this));

	function	save(element) {
		if (confirm("Save image?")) {
			var id = element.id;
			console.log(id);
			var xhttp = new XMLHttpRequest();
			xhttp.open('POST', 'save.php', true);
			xhttp.setRequestHeader('Content-type', 'Application/x-www-form-urlencoded');
			xhttp.send('sticker=bwnoise&image='+encodeURIComponent(element.src));
		}
	}

	// function handleDragStart(e) {
	// 	this.style.opacity = '0.4';

	// 	dragSrcEl = this;

    // 	e.dataTransfer.effectAllowed = 'copy';
    // 	e.dataTransfer.setData('image/*', this.innerHTML);
	// }
	
	// function handleDragEnd(e) {
	// 	this.style.opacity = '1';

	// 	items.forEach(function (item) {
	// 		item.classList.remove('over');
	// 	});
	// }
	  
	// function handleDragOver(e) {
	//   if (e.preventDefault) {
	// 	e.preventDefault();
	//   }
	
	//   return false;
	// }
	
	// function handleDragEnter() {
	//   this.classList.add('over');
	// }
	
	// function handleDragLeave() {
	//   this.classList.remove('over');
	// }

	// function handleDrop(e) {
	// 	e.stopPropagation(); // stops the browser from redirecting.

	// 	if (dragSrcEl !== this) {
	// 		dragSrcEl.innerHTML = this.innerHTML;
	// 		this.innerHTML = e.dataTransfer.getData('image/*');
	// 	}

	// 	return false;
	// }

	// let items = document.querySelectorAll('#sunglasses');
	// items.forEach(function(item) {
    // 	item.addEventListener('dragstart', handleDragStart, false);
	// 	item.addEventListener('dragover', handleDragOver, false);
	// 	item.addEventListener('dragenter', handleDragEnter, false);
	// 	item.addEventListener('dragleave', handleDragLeave, false);
	// 	item.addEventListener('dragend', handleDragEnd, false);
	// });


	// function	openFile(event) {
	// 	var input = event.target;
	// 	alert("hello");

	// 	var reader = new FileReader();
	// 	reader.onload = function() {
	// 		var dataURL = reader.result;
    //   		var output = document.createElement('IMG');
    //   		var name = "photo" + index;
	// 		output.setAttribute("id", name);
	// 		output.setAttribute('width', (width / 2));
	// 		output.setAttribute('height', (height / 2));
    //   		output.setAttribute('src', dataURL);
    //   		var element = document.getElementById('output');
    //   		element.insertBefore(output, element.firstChild);
    //   		index++
	// 	};
	// 	reader.readAsDataURL(input.files[0]);