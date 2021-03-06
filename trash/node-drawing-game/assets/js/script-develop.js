$(function(){

  // This demo depends on the canvas element
  if(!('getContext' in document.createElement('canvas'))){
    alert('Sorry, it looks like your browser does not support canvas!');
    return false;
  }
  //console.log('s');
  //The URL of your web server (the port is set in app.js)

  var ary = location.search.substr(1).split("?", 2);
  var room_key = ary[0];
  var user_name = ary[1];

  //Ajax to get the drawing port
  $.ajax({
    url: './room-admin/set-drawing-port.php?room-key=' + room_key,
    type: 'GET',
    dataType: 'text',
    success: function(msg) { 

      var port_url = 'http://59.127.174.192:5020';
      var doc = $(document);
      var win = $(window);
      //var canvas = $('#paper');
      //console.log(canvas);
      var canvas = document.querySelector("#paper");
      var ctx = $('#paper')[0].getContext('2d');
      var instructions = $('#instructions');
      var offset  = $('#draw').offset();
      var wx = window.innerWidth;
      var wy = window.innerHeight;
      var conf_height_string = $(".gridster ul").css("height");
      var conf_height = conf_height_string.substr(0, conf_height_string.length - 2); 

      $('canvas')[0].width = wx;
      $('canvas')[0].height = wy;

      // Generate an unique ID
      var id = Math.round($.now()*Math.random());
      console.log(id);

      // A flag for drawing activity
      var drawing = false;

      var clients = {};
      var cursors = {};

      var socket = io.connect(port_url);

      socket.on('moving', function (data) {

	if(! (data.id in clients)){
	  // a new user has come online. create a cursor for them
	  cursors[data.id] = $('<div class="cursor" id="' + data.id + '">').appendTo('#cursors');
	  $("#" + data.id).append(data.user_name);
	}

	// Move the mouse pointer
	cursors[data.id].css({
	  'left' : data.x * wx + offset.left,
	  'top' : data.y * wy + offset.top - conf_height
	});
	//console.log(data.x + 'px' + data.y +'px\n');	
	// Is the user drawing?
	if(data.drawing && clients[data.id]){

	  // Draw a line on the canvas. clients[data.id] holds
	  // the previous position of this user's mouse pointer
	  //console.log(clients[data.id]);
	  drawLine(clients[data.id].x * wx, clients[data.id].y * wy, data.x * wx, data.y * wy);
	}

	// Saving the current client state
	clients[data.id] = data;
	clients[data.id].updated = $.now();
      });

      var prev = {};

      canvas.addEventListener('mousedown',function(e){
	console.log(e);
	e.preventDefault();
	drawing = true;
	prev.x = e.offsetX/wx;
	prev.y = e.offsetY/wy;
	// Hide the instructions
	//instructions.fadeOut();
      });

      canvas.addEventListener('touchstart',function(e){
	e.preventDefault();
	drawing = true;
	prev.x = e.offsetX/wx;
	prev.y = e.offsetY/wy;
	// Hide the instructions
	//instructions.fadeOut();
      });

      /*
	 doc.bind('mouseup mouseleave',function(){
	 drawing = false;
	 });
	 doc.bind('touchend mouseleave',function(){
	 drawing = false;
	 });
	 */
      document.addEventListener('mouseup',function(){
	drawing = false;
      });
      document.addEventListener('touchend',function(){
	drawing = false;
      });

      var lastEmit = $.now();

      document.addEventListener('mousemove',function(e){
	if($.now() - lastEmit > 1){
	  socket.emit('mousemove',{
	    'x': e.offsetX/wx,
	    'y': e.offsetY/wy,
	    'drawing': drawing,
	    'id': id,
	    'user_name': user_name
	  });
	  lastEmit = $.now();
	}

	// Draw a line for the current user's movement, as it is
	// not received in the socket.on('moving') event above

	if(drawing){
	  drawLine(prev.x*wx, prev.y*wy, e.offsetX, e.offsetY);
	  prev.x = e.offsetX/wx;
	  prev.y = e.offsetY/wy;
	}
      });
      document.addEventListener('touchmove',function(e){
	alert("touchmove");
	alert(e);
	if($.now() - lastEmit > 1){
	  socket.emit('mousemove',{
	    'x': e.offsetX/wx,
	    'y': e.offsetY/wy,
	    'drawing': drawing,
	    'id': id,
	    'user_name': user_name
	  });
	  lastEmit = $.now();
	}

	// Draw a line for the current user's movement, as it is
	// not received in the socket.on('moving') event above

	if(drawing){
	  drawLine(prev.x*wx, prev.y*wy, e.offsetX, e.offsetY);
	  prev.x = e.offsetX/wx;
	  prev.y = e.offsetY/wy;
	}
      });

      // Remove inactive clients after 10 seconds of inactivity
      setInterval(function(){

	for(ident in clients){
	  if($.now() - clients[ident].updated > 10000){

	    // Last update was more than 10 seconds ago. 
	    // This user has probably closed the page

	    cursors[ident].remove();
	    delete clients[ident];
	    delete cursors[ident];
	  }
	}

      },10000);

      function drawLine(fromx, fromy, tox, toy){
	//console.log("(" + fromx + ", " + fromy + ") -> (" + tox + ", " + toy);
	ctx.moveTo(fromx, fromy);
	ctx.lineTo(tox, toy);
	ctx.stroke();
      }

      $("#clean-button").on("click", function(e) {
	console.log("clean-button");
	$('#paper').remove();
	$('#draw').append('<canvas id="paper" width="1900" height="1000"></canvas>');
	canvas = document.querySelector("#paper");
	ctx = $('#paper')[0].getContext('2d');
	$('canvas')[0].width = wx;
	$('canvas')[0].height = wy;
	canvas.addEventListener('mousedown',function(e){
	  console.log(e);
	  e.preventDefault();
	  drawing = true;
	  prev.x = e.offsetX/wx;
	  prev.y = e.offsetY/wy;
	  // Hide the instructions
	  //instructions.fadeOut();
	});
      });

    }

  });

});
