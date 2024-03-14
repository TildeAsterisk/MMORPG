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
<center><h1>Main Quest</h1></center>
<?php

function GenerateQuestActionButtons($actionOptions){
  if($actionOptions==null){
    $actionOptions=[];
  }
  /*
  if(!isset($_POST['questData'])){
    $questData=null;
  }
  else{
    $questData=$_POST['questData'];
  }

  if($questData==null){
    $questData = new stdClass();
    $questData->location=$_POST['location'];
    $questData->focus=$_POST['focus'];
    $questData->previousAction=$_POST['previousAction'];
  }
  */

  // Generate action buttons
  $actionButtons="";
  foreach ($actionOptions as $key => $value) {
    $actionButtons .= '<form action="quest.php" method="post">';
    $actionButtons .= "<input type='submit' name='action' value='$value'>";
    $actionButtons .= '</form>';
  }
  //echo htmlspecialchars($actionButtons, ENT_QUOTES, 'UTF-8');

  $questButtonsHTML="
  <br><hr>
  Choose your action:<br><br>";
  echo $questButtonsHTML;
  echo $actionButtons;
  
}

?>

<center>
<?php 

if(isset($_POST['action'])){
  //You selected an action
  //var_dump($_POST);
  //echo "Location: {$_POST['questData']['location']}, focus: {$_POST['questData']['focus']}, previous action:{$_POST['action']}<br>";
  echo "You have chosen to: {$_POST['action']}<br><br>";

  //Now play game based on decision
  switch ($_POST['action']){
    case "Talk":
      echo "You are talking to someone.<br>";
      break;
    case "Move":
      echo "You chose to move.<br>";
      break;
    case "Fight":
      echo "You chose to fight with someone.<br>";
      break;
    default:
      break;
  }

  //$questDataDecoded = json_decode($_POST['questData']);
  //Decode it twice....
  //$questDataDecoded = json_decode($questDataDecoded);
  //var_dump($questDataDecoded);
  //$location=$questDataDecoded->location;
  //$focus=$questDataDecoded->focus;
  //$previousAction=$questDataDecoded->previousAction;

  echo <<<EOD
  <br><br>
  <table style='width:100%;'>
    <tr>
      <td>Location:</td>
      <td>Focus:</td>
      <td>Previous Action:</td>
    </tr>
    <tr>
      <td></td>
      <td></td>
      <td></td>
    </tr>
  </table>
  EOD;

  GenerateQuestActionButtons(null); 

}
//No previous action
else{
  //Start Quest
  echo "Welcome User, this place is a dump. You should leave this planet.<br>";
  echo "Buy Ship?<br>";
  //Options: Accept   /Decline  /Can't Afford
  //Results: Fly away /Continue /Continue

  $firstOptions=[
    "action1"=>"Accept",
    "action2"=>"Decline",
    "action3"=>"Cant Afford"
  ];
  GenerateQuestActionButtons($firstOptions); 
}
?>
</center>
  <?php
  
}
//include("update_stats.php");
include("footer.php");
?>