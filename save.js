(function() {
	// The width and height of the captured photo. We will set the
	// width to the value defined here, but the height will be
	// calculated based on the aspect ratio of the input stream.
  
	var width = 320;    // We will scale the photo width to this
	var height = 0;     // This will be computed based on the input stream
  
	// |streaming| indicates whether or not we're currently streaming
	// video from the camera. Obviously, we start at false.
  
	var streaming = false;
  
	// The various HTML elements we need to configure or control. These
	// will be set by the startup() function.
  
	var video = null;
	var canvas = null;
	var startbutton = null;

	var index = 0;
  
	function startup() {
	  video = document.getElementById('video');
	  canvas = document.getElementById('canvas');
	  startbutton = document.getElementById('startbutton');
  
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

		$(".image-tag").val(data);

		// var name = "photo" + index;
		// var newPhoto = document.createElement('img');
		// newPhoto.setAttribute('id', name);
		// newPhoto.setAttribute('width', (width / 2));
		// newPhoto.setAttribute('height', (height / 2));
		// newPhoto.setAttribute('src', data);
		// // newPhoto.addEventListener('click', function() {
		// // 	save(this);
		// // });
		// var element = document.getElementById('output');
		// element.insertBefore(newPhoto, element.firstChild);
		// console.log(index);

		index++;
	  }
	}

	function	save(element) {
		if (confirm("Save image?")) {
			var data = element.src;
			console.log(data);
			$output = document.getElementById('image-tag');
			$output.value = data;
			// $(".image-tag").value(data);
		}
	}
  
	// Set up our event listener to run the startup process
	// once loading is complete.
	window.addEventListener('load', startup, false);
  })();


  <div class="contentarea">
  <h2>Create a new image</h2>
  <form method="post" action="">
    <div class="camera">
      <video id="video">Video stream not available.</video>
      <button id="startbutton">Take photo</button>
      <input type="hidden" name="image" id="image-tag" value="">
    </div>
  </form>
  <canvas id="canvas">
  </canvas>
  <div id="output">
  </div>
</div>
</body>
</html>