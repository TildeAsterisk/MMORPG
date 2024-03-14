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
  //set constants, situations and actions
  define('ACTION_COMBAT', 'combat');
  define('ACTION_TALK', 'talk');
  define('ACTION_MOVE', 'move');
  define('SITUATION_COMBAT', 'combat');
  define('SITUATION_DIALOGUE', 'talk');
  define('SITUATION_OBSTACLE', 'obstacle');

  //Display some quest text
  echo "You were confronted with a <b>{$questData->prevConfrontation}</b>.<br>";
  echo "You chose to <b>{$previousAction}</b>.<br>";
  echo "You are in <b>{$questData->location}</b>.<br><br>";
  var_dump($questData);

  //Generate some comments based on prev confrontation, prev action and location
  // Then generate a confrontation with options

  //Default actionOptions
  $nextActionOptions=[
    "action1"=>["Explore",['prevConfrontation'=>"{$questData->prevConfrontation}",'actionType'=>ACTION_MOVE,'location'=>"{$questData->location}",'transport'=>'Ship']],
    "action2"=>["Talk",   ["prevConfrontation"=>"{$questData->prevConfrontation}",'actionType'=>ACTION_TALK,  'location'=>"{$questData->location}",'transport'=>'Walk']],
    "action3"=>["Fight",  ["prevConfrontation"=>"{$questData->prevConfrontation}",'actionType'=>ACTION_COMBAT, 'location'=>"{$questData->location}",'transport'=>'Walk']]
  ];

  // MAIN QUEST STATE MACHINE
  switch ($questData->prevConfrontation){
    case SITUATION_COMBAT:
      switch ($previousAction){
        case ACTION_COMBAT:
          //In Combat, chose Combat
          echo "You chose to fight the enemy.<br>The battle concludes and you continue on your journey.<br><br>";
          break;
        case ACTION_TALK:
          //In combat, chose negotiate
          echo "You chose to negotiate with the enemy.<br>The battle concludes and you continue on your journey.<br><br>";
          break;
        case ACTION_MOVE:
          //in combat, chose evade
          echo "You chose to escape.<br>The battle concludes and you continue on your journey.<br><br>";
          break;
      }
      break;
    case SITUATION_OBSTACLE:
      switch ($previousAction){
        case ACTION_COMBAT:
          //In Obstacle, chose Combat
          echo "You attack the obstacle<br>It breaks and pass right through it's scattered remains.<br><br>";
          break;
        case ACTION_TALK:
          //In obstacle, chose negotiate
          echo "You chose to investigate the obstacle.<br>You have an idea to interact with it and the obstacle move out of your way.<br><br>";
          break;
        case ACTION_MOVE:
          //in obstacle, chose evade
          echo "You chose to move around the obstacle.<br>You make your way around it and continue on.<br><br>";
          break;
        }
      break;
    case SITUATION_DIALOGUE:
      switch($previousAction){  
        case ACTION_COMBAT:
          //In Dialogue, chose Combat
          echo "You shout at the being in anger!<br>The being shreiks in fear and runs away.<br><br>";
          break;
        case ACTION_TALK:
          //In Dialogue, chose negotiate
          echo "You converse with the being neutrally.<br>You come to an understanding and he gifts you a present.<br><br>";
          break;
        case ACTION_MOVE:
          //in Dialogue, chose evade
          echo "You ignore the being.<br>You make your way around it and continue on.<br><br>";
          break;
        }
      break;
  }
  
  //Generate next confrontation based on questdata
  if ($questData->location=="Ship"){
      if($previousAction == 'Begin Expedition'&& $questData->location=="Ship"&& $questData->goalType=="Explore"){
        echo "You are now travelling out in the deep unkown.<br><br>";
      }      

      //Generate Ship Confrontations
      // Random Destination, Raided, Obstacle?
      $random_choice = rand(1,3);
      switch ($random_choice){
        case 1:
          echo "You arrived at <b><i>Random Destination</i></b><br>";
          echo "You are approached by something and it interracts with you.<br>";
          //set previous confrontation
          foreach ($nextActionOptions as $key => $value) {
            $nextActionOptions[$key][1]["prevConfrontation"] = SITUATION_DIALOGUE;
          }
          break;
        case 2:
          echo "<b><i>You're getting raided!</i></b><br><br>";
          //NextActionOptions: Negotiate, Fight, Escape
          $nextActionOptions=[
            "action1"=>["Negotiate",['optionText'=>'Negotiate','prevConfrontation'=>SITUATION_COMBAT,"actionType"=>ACTION_TALK,   'location'=>"{$questData->location}",   'transport'=>'Ship']],
            "action2"=>["Fight",    ['optionText'=>'Fight','prevConfrontation'=>SITUATION_COMBAT,"actionType"=>ACTION_COMBAT, 'location'=>"{$questData->location}",  'transport'=>'Walk']],
            "action3"=>["Escape",   ['optionText'=>'Escape','prevConfrontation'=>SITUATION_COMBAT,"actionType"=>ACTION_MOVE,   'location'=>"{$questData->location}", 'transport'=>'Walk']]
          ];
          break;
        case 3:
          echo "<b><i>You've hit an obstacle</i></b><br><br>";
          // ???
          $nextActionOptions=[
            "action1"=>["Go around it", ['prevConfrontation'=>SITUATION_OBSTACLE,"actionType"=>ACTION_MOVE,'location'=>"{$questData->location}",'goalType'=>'Explore','transport'=>'Ship']],
            "action2"=>["Investigate",  ['prevConfrontation'=>SITUATION_OBSTACLE,"actionType"=>ACTION_TALK,  'location'=>"{$questData->location}",'goalType'=>'Build','transport'=>'Walk']],
            "action3"=>["Destroy it",   ['prevConfrontation'=>SITUATION_OBSTACLE,"actionType"=>ACTION_COMBAT, 'location'=>"{$questData->location}",'goalType'=>'Work','transport'=>'Walk']]
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