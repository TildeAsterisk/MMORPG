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
  // Generate next Action Options based on questData
  $actionButtons="";
  foreach ($actionOptions as $key => $value) {
    $questDataEncoded=json_encode($value[1]); 
    $actionButtons .= '<form action="quest.php" method="post">';
    $actionButtons .= "<input style='width:50%;' type='submit' name='action' value='{$value[0]}'>";
    $actionButtons .= "<input type='hidden' name='questData' value='$questDataEncoded'>";
    $actionButtons .= '</form>';
  }
  echo "<center><br><hr>
  Choose your action:<br><br>";
  echo $actionButtons;
  echo "</center>";
}


  //Generate random new confrontation with options based on prev action and quesData
function GenerateRandomQuestConfrontation($previousAction, $questData){
  //Display some quest text
  echo "You were confronted with a <b>{$questData->prevConfrontation}</b>.<br>";
  echo "You chose to <b>{$previousAction}</b>.<br>";
  echo "You are in <b>{$questData->location}</b>.<br><br>";

  //Generate some comments based on prev confrontation, prev action and location
  // Then generate a confrontation with options

  //Default actionOptions
  $nextActionOptions=[
    "action1"=>["Explore",['prevConfrontation'=>"{$questData->prevConfrontation}",'actionType'=>'flight','location'=>"{$questData->location}",'transport'=>'Ship']],
    "action2"=>["Talk",   ["prevConfrontation"=>"{$questData->prevConfrontation}",'actionType'=>'talk',  'location'=>"{$questData->location}",'transport'=>'Walk']],
    "action3"=>["Fight",  ["prevConfrontation"=>"{$questData->prevConfrontation}",'actionType'=>'fight', 'location'=>"{$questData->location}",'transport'=>'Walk']]
  ];

  // MAIN QUEST STATE MACHINE
  if ($questData->location=="Ship"){
      if($previousAction == 'Begin Expedition'&& $questData->location=="Ship"&& $questData->goalType=="Explore"){
        echo "You are now travelling out in the deep unkown.<br><br>";
      }      

      //Generate Ship Confrontations
      // Random Destination, Raided, Obstacle?
      $random_choice = rand(1,3);
      switch ($random_choice){
        case 1:
          echo "You arrived at <b><i>Random Destination</i></b><br><br>";
          //set previous confrontation
          foreach ($nextActionOptions as $key => $value) {
            $nextActionOptions[$key][1]["prevConfrontation"] = 'Random Destination';
          }
          break;
        case 2:
          echo "<b><i>You're getting raided!</i></b><br><br>";
          //NextActionOptions: Negotiate, Fight, Escape
          $nextActionOptions=[
            "action1"=>["Negotiate",['prevConfrontation'=>'raid',"actionType"=>'talk',  'location'=>"{$questData->location}",   'transport'=>'Ship']],
            "action2"=>["Fight",    ['prevConfrontation'=>'raid',"actionType"=>'fight', 'location'=>"{$questData->location}",  'transport'=>'Walk']],
            "action3"=>["Escape",   ['prevConfrontation'=>'raid',"actionType"=>'flight','location'=>"{$questData->location}", 'transport'=>'Walk']]
          ];
          break;
        case 3:
          echo "<b><i>You've hit an obstacle</i></b><br><br>";
          // ???
          $nextActionOptions=[
            "action1"=>["Go around it", ['prevConfrontation'=>'obstacle',"actionType"=>'flight','location'=>"{$questData->location}",'goalType'=>'Explore','transport'=>'Ship']],
            "action2"=>["Investigate",  ['prevConfrontation'=>'obstacle',"actionType"=>'talk',  'location'=>"{$questData->location}",'goalType'=>'Build','transport'=>'Walk']],
            "action3"=>["Destroy it",   ['prevConfrontation'=>'obstacle',"actionType"=>'fight', 'location'=>"{$questData->location}",'goalType'=>'Work','transport'=>'Walk']]
          ];
          break;
        
      }
      
  }

  //if prev:Buy Ship AND location:Home
  if($previousAction == 'Buy Ship' && $questData->location=="Home"){
    echo "Would you like to embark on an expedition?<br>";
    $nextActionOptions['action1'] = [
      "Begin Expedition",
      [
          'prevConfrontation'=>'new ship',
          'location' => 'Ship',
          'goalType' => 'Explore',
          'transport' => 'Ship'
      ]
    ];
    $nextActionOptions['action2'] = [
      "Do some more jobs",
      [
          'prevConfrontation'=>'new ship',
          'location' => 'Home',
          'goalType' => 'Work',
          'transport' => 'Ship'
      ]
    ];
    unset($nextActionOptions['action3']);
  }

  //Generate Quest Action Options buttons from $nextActionOptions
  GenerateQuestActionButtons($nextActionOptions); 
}

if(isset($_POST['action'])){
  //You selected an action

  //Decode quest data
  $questDataDecoded=json_decode($_POST['questData']);

  //Now play game based on decision

  // MAIN QUEST STATE MACHINE
  GenerateRandomQuestConfrontation($_POST['action'], $questDataDecoded);

  //Generate next actions based on current quest data
  //GenerateQuestActionButtons($nextActionOptions); 

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
    'prevConfrontation'=>'ship for sale',
    'location'=>'Home',
    'goalType'=>'Buy',
    'transport'=>'ship'
  ],
  [
    'prevConfrontation'=>'ship for sale',
    'location'=>'Home',
    'goalType'=>'Build',
    'transport'=>'Walk'
  ],
  [
    'prevConfrontation'=>'ship for sale',
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