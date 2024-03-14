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

if(isset($_POST['action'])){
  //You selected an action
  echo "You chose to {$_POST['action']}.<br><br>";
  //echo "You have chosen to: {$_POST['action']}<br><br>";
  //echo "Your quest data: {$_POST['questData']}<br><br>";

  //Decode quest data
  $questDataDecoded=json_decode($_POST['questData']);

  //Default actionOptions
  $nextActionOptions=[
    "action1"=>["Explore",   
      [
        'location'=>'Ship',
        'goalType'=>'Explore',
        'transport'=>'Ship'
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

  //Now play game based on decision

  // MAIN QUEST STATE MACHINE
  //if prev:Buy Ship AND location:Home
  if($_POST['action'] == 'Buy Ship'&& $questDataDecoded->location=="Home"){
    echo "You just bought a ship and are at home.<br>";
    echo "Would you like to embark on an expedition?<br>";
    // Explore, location:ship, goalType:explore
    $nextActionOptions['action1'] = [
      "Begin Expedition",
      [
          'location' => 'Ship',
          'goalType' => 'Explore',
          'transport' => 'Ship'
      ]
    ];
    $nextActionOptions['action2'] = [
      "Do some more jobs",
      [
          'location' => 'Home',
          'goalType' => 'Work',
          'transport' => 'Ship'
      ]
    ];
    unset($nextActionOptions['action3']);
  }

  if($_POST['action'] == 'Begin Expedition'&& $questDataDecoded->location=="Ship"&& $questDataDecoded->goalType=="Explore"){
    echo "You are now travelling out in the deep unkown.<br>";
    echo "You either reach safely, get raided, or hit obstacle.<br>";
    echo "<b>You are confronted.</b><br>";
    $nextActionOptions['action1'] = [
      "Continue on journey",
      [
          'location' => 'Ship',
          'goalType' => 'Explore',
          'transport' => 'Ship'
      ]
    ];
    $nextActionOptions['action2'] = [
      "Attack fellow traveller",
      [
          'location' => 'Ship',
          'goalType' => 'Combat',
          'transport' => 'Ship'
      ]
    ];
    $nextActionOptions['action3'] = [
      "Trade fellow traveller",
      [
          'location' => 'Ship',
          'goalType' => 'Trade',
          'transport' => 'Ship'
      ]
    ];
    //unset($nextActionOptions['action3']);
  }

  //echo "You chose to {$_POST['action']}.<br>";

  //Generate next actions based on current quest data
  GenerateQuestActionButtons($nextActionOptions); 

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
    'location'=>'Home',
    'goalType'=>'Buy',
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