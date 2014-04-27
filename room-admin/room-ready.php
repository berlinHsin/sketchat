<?php

require_once("./db-config.php");
session_start();

$room_name = $_GET['room-name'];
$user_name = $_GET['user-name'];

if($room_name != NULL && $user_name != NULL) {
  //Check the room ID exist or not
  $result = mysql_query("SELECT * FROM room WHERE name='$room_name' AND over_time is NULL");
  $num = mysql_num_rows($result);
  if($num == 0) {
    $time = date("H-m-d H:i:s");
	$room_id = get_table_row_num('room') + 1 ; 
    //Step1: New an user
    $ip = get_client_ip();
    $device = $_SERVER['HTTP_USER_AGENT'];
    mysql_query("INSERT INTO user SET name='$user_name', ip='$ip', device='$device', room ='$room_id', enter_time='$time'");
	$user_id = mysql_insert_id();
    //Step2: New a room
    $key = md5($room_name."-".$user_name."-".$time);
    $port = get_idle_port($room_id);
    $url = "http://".$_SERVER['HTTP_HOST']."/sketchat/room.php?".$key;
    $q = mysql_query("INSERT INTO `room` SET name='$room_name', creater='$user_id', create_time='$time', access_key='$key', port='$port'");
	if(!$q)exit(mysql_error());
    //Redirect to the room
    $_SESSION['room-key'] = $key;
    $_SESSION['user-name'] = $user_name;
    header("location: ../ready-creater.php");
  }
  else if($num == 1)
    echo "<script>alert('This room name is using now, Please rename an another name.'); location.href='../'</script>";
  else
    header("location: ../");
}
else
  header("location: ../");


function get_table_row_num($table_name) {
  $result = mysql_query("SELECT * FROM $table_name");
  return mysql_num_rows($result);
}

function get_idle_port($room_id) {
  $result = mysql_query("SELECT * FROM port_table");
  while($row = mysql_fetch_assoc($result)) {
    if($row['room_id'] == NULL) {
      $result2 = mysql_query("UPDATE port_table SET room_id=$room_id WHERE port_num={$row['port_num']}");
      return $row['port_num'];
    }
  }
}


function get_client_ip() {
  $ipaddress = '';
  if ($_SERVER['HTTP_CLIENT_IP'])
    $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
  else if($_SERVER['HTTP_X_FORWARDED_FOR'])
    $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
  else if($_SERVER['HTTP_X_FORWARDED'])
    $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
  else if($_SERVER['HTTP_FORWARDED_FOR'])
    $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
  else if($_SERVER['HTTP_FORWARDED'])
    $ipaddress = $_SERVER['HTTP_FORWARDED'];
  else if($_SERVER['REMOTE_ADDR'])
    $ipaddress = $_SERVER['REMOTE_ADDR'];
  else
    $ipaddress = 'UNKNOWN';

  return $ipaddress; 
}



?>


