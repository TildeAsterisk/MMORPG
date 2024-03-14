<?php
session_start();
include_once("header.php");

// Check if player is logged in
//$_SESSION['uid'] = null;
if(!isset($_SESSION['uid'])){
  echo "You must be logged in to view this page!";
}
else{
  //Player is logged in, show main page
  ?>

<!--center><h1>Legend</h1></center-->
* Message Inbox<br>
* Player Logs<br>
* Inventory (Compact)<br>
* Questing

  <?php
}
//include("update_stats.php");
include("footer.php");
?>