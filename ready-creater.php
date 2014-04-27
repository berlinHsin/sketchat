<?php
require_once("./room-admin/db-config.php");
session_start();

$room_key = $_SESSION['room-key'];
$user_name = $_SESSION['user-name'];

if($room_key != NULL && $user_name != NULL) {
  $result = mysql_query("SELECT * FROM room WHERE access_key='$room_key' AND over_time is NULL");
  $result_num = mysql_num_rows($result);
  $row = mysql_fetch_assoc($result);
  $room_name = $row['name'];
  $user_id = $row['creater'];
  if($result_num == 1) { 
    $result = mysql_query("SELECT * FROM user WHERE name='$user_name'");
    $result_num = mysql_num_rows($result);
    $row = mysql_fetch_assoc($result); 
    if($user_id == $row['id']) 
      $url = "http://".$_SERVER['HTTP_HOST']."/sketchat/room.php?".$room_key;
    else
      header("location: ./");
  }
  else 
    header("location: ./");
}
else
  header("location: ./");

?>

<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Sketchat</title>

    <!-- Bootstrap core CSS -->
    <link href="./css/bootstrap.css" rel="stylesheet">
    <link href="./css/style.css" rel="stylesheet">
    <link href="./css/ready-creater.css" rel="stylesheet">

  </head>

  <body>

    <div id="header">
      <nav id="navbar" class="navbar navbar-default" role="navigation">
	  <!-- Brand and toggle get grouped for better mobile display -->
	  <div class="navbar-header">
	    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
	      <span class="sr-only">Toggle navigation</span>
	      <span class="icon-bar"></span>
	      <span class="icon-bar"></span>
	      <span class="icon-bar"></span>
	    </button>
	    <a class="navbar-brand" href="./" style="font-size: 30px; padding-left: 0px; letter-spacing:1px;"><img src="./img/index/sketchat-logo.png" height="35"/> Sketchat</a>
	  </div>
	  <!-- Collect the nav links, forms, and other content for toggling -->
	  <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
	    <ul class="nav navbar-nav navbar-right">
	      <li><a href="./about.html" target="_blank" data-loc="about" class="menu-list">About</a></li>
	      <li><a href="./features.html" target="_blank" data-loc="feature" class="menu-list">Features</a></li>
	    </ul>
	  </div><!-- /.navbar-collapse -->
      </nav>
    </div>

    <section class="scroll-panel" id="welcome">
      <div class="container">

	<div class="jumbotron">
	  <div class="row">

	    <div class="jumbotron text-centered">
	      <div class="welcome-container">
		<div class="url-container col-md-6 col-md-offset-3">
		  <p>People who want to sketchat you only connect to this URL.<br />Share this URL to someone you want to sketchat.</p> 
		  <input id="url-text" type="text" class="form-control sign-form" value="<?php echo $url; ?>"/>
		  <div id="qrcode" style="width: 168px; height: 168px; margin-top: 20px;"></div>
		  <a href="<?php echo $url; ?>" id="start-button" class="btn btn-lg btn-primary btn-embossed start-button">Start to Sketchat</a>
		</div>
	      </div>
	    </div>
	  </div>
	</div>

      </div>
    </section>

    <footer>
      <nav role="navigation">
	<div class="container">
	  <ul class="site-footer-links">
	    <li class="footer-link"><a href="./about.html">About</a></li>
	    <li class="footer-link"><a href="./about.html">Privacy</a></li>
	    <li class="footer-link"><a href="./contact.html">Contact</a></li>
	  </ul>
	  <p class="copyright">&copy; 2013 Sketchat &nbsp;All rights reserved.</p>
	</div>
      </nav>
    </footer>

    <script src="./js/jquery.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    <script src="./js/qrcodejs/qrcode.js"></script>
<script type="text/javascript">
var qrcode = new QRCode(document.getElementById("qrcode"), {
  text: "<?php echo $url; ?>",
    width: 168,
    height: 168,
    colorDark : "#000000",
    colorLight : "#ffffff",
    correctLevel : QRCode.CorrectLevel.H
});
var width = $(".url-container").css("width").substr(0, $(".url-container").css("width").length - 2);
$("#qrcode").css("margin-left", (width - 167) * 0.5 + "px");
</script>

  </body>
</html>

