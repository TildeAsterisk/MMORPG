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
    $actionOptions=[
      "action1"=>["Move",   
        [
          'location'=>'Ship',
          'goalType'=>'Explore',
          'transport'=>'ship'
        ]
      ],
      "action2"=>["Talk",   
        [
          'location'=>'Home',
          'goalType'=>'Build',
          'transport'=>'Walk'
        ]
      ],
      "action3"=>["Fight",  
        [
          'location'=>'Home',
          'goalType'=>'Work',
          'transport'=>'Walk'
        ]
      ]
    ];

  }
  //$questDataEncoded=json_encode($questData);  

  // Generate next Action Options based on questData
  $actionButtons="";
  foreach ($actionOptions as $key => $value) {
    $questDataEncoded=json_encode($value[1]); 
    $actionButtons .= '<form action="quest.php" method="post">';
    $actionButtons .= "<input style='width:50%;' type='submit' name='action' value='{$value[0]}'>";
    $actionButtons .= "<input type='hidden' name='questData' value='$questDataEncoded'>";
    $actionButtons .= '</form>';

    //echo "<br><br>var dump:";
    //var_dump($value);
  }
  //echo htmlspecialchars($actionButtons, ENT_QUOTES, 'UTF-8');

  echo "<center><br><hr>
  Choose your action:<br><br>";
  echo $actionButtons;
  echo "</center>";
  
}

?>


<?php 

if(isset($_POST['action'])){
  //You selected an action
  //var_dump($_POST);
  //echo "Location: {$_POST['questData']['location']}, focus: {$_POST['questData']['focus']}, previous action:{$_POST['action']}<br>";
  echo "You have chosen to: {$_POST['action']}<br><br>";
  echo "Your quest data: {$_POST['questData']}<br><br>";

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

  GenerateQuestActionButtons(null); 

  //$questDataDecoded = json_decode($_POST['questData']);
  //Decode it twice....
  //$questDataDecoded = json_decode($questDataDecoded);
  //var_dump($questDataDecoded);
  //$location=$questDataDecoded->location;
  //$focus=$questDataDecoded->focus;
  //$previousAction=$questDataDecoded->previousAction;
  /*
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
  */

}
//No previous action
else{
  //Start Quest
  echo "Welcome User, this place is a dump. You should leave this planet.<br>";
  echo "You can buy a ship and explore, or you can build your empire right here.<br>";
  echo "What will you do?<br>";
  //Options: Accept   /Decline  /Can't Afford
  //Results: Fly away /Continue /Continue

  $firstOptions_results=[[
    'location'=>'Ship',
    'goalType'=>'Explore',
    'transport'=>'ship'
  ],
  [
    'location'=>'Home',
    'goalType'=>'Build',
    'transport'=>'Walk'
  ],
  [
    'location'=>'Home',
    'goalType'=>'Work',
    'transport'=>'Walk'
  ]
];
  $firstOptions=[
    "action1"=>["Buy Ship",               $firstOptions_results[0]],
    "action2"=>["Build Base",             $firstOptions_results[1]],
    "action3"=>["Get back on the grind!", $firstOptions_results[2]]
  ];
  GenerateQuestActionButtons($firstOptions); 
}
?>


  <?php
  
}
//include("update_stats.php");
include("footer.php");
?>