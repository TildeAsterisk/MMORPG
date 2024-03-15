<?php
session_start();
include("header.php");
if(!isset($_SESSION['uid'])){
    echo "You must be logged in to view this page!";
}else{
  // OUT OF ENERGY
  if ($stats['energy'] < $_POST['energyCost']){
    echo "You don't have enough energy for this job.";
    echo "<br>Wait a while for your energy to regenerate.";
    return;
  }

  //Initialise new job
  //Pull enemy stats from previous job summary post data
  $enemy_stats = [  // Associative Array / Dictionary
    'attack' => $_POST['attack'],
    'defense' => $_POST['defense'],
    'currency' => $_POST['moneyReward']
  ];
  //generate a random enemy data dict
  $newRandomEnemy=GenerateRandomEnemy($enemy_stats,null);
  //Set other job variables
  $turns=1;//energy modifier?
  $job_energycost=$_POST['energyCost'];
  $job_experiencegained=$_POST['experienceReward'];

  //Subtract energy cost of job
  $energycostquery = mysqli_query($mysql,"UPDATE `stats` SET `energy`=`energy`-'".$job_energycost."' WHERE `id`='".$_SESSION['uid']."'") or die(mysqli_error($mysql));
  //Attack Effect is = some factor * attack
  //$stats['attack'] = $stats['attack'];
  //$enemy_stats['defense'] = $enemy_stats['defense'];
  $miniStatsProfile=<<<EOD
  <table style='width:100%;text-align:center;'>
  <tr>
      <td>
        <b>{$user['username']}</b>
      </td>
      <td rowspan='2'>Vs.</td>
      <td>
        <b>{$newRandomEnemy['name']}</b>
      </td>
    </tr>
    <tr>
      <td>
        {$stats['attack']}$attack_symbol {$stats['defense']}$defense_symbol
      </td>
      <td>
        {$enemy_stats['attack']}$attack_symbol {$enemy_stats['defense']}$defense_symbol
      </td>
    </tr>
  </table>
  EOD;
  //echo "<center><b>Battle</b></center>";
  echo $miniStatsProfile;
  
  //Generate the battle 
  $weaponTxt = $inventory['weapon']??"bare hands";

  //YOUR TURN
  $eDamageDealt = $stats['attack']-$enemy_stats['defense'];
  if($eDamageDealt < 0){$eDamageDealt=0;}
  $eDamageBlocked = $stats['attack'] - $eDamageDealt;
  $eBlockedPercentage=round(($eDamageBlocked/$stats['attack'])*100);
  //enemy equipment
  $values = array_values($newRandomEnemy['equipment']);
  $randomEnemyEquipment = $values[array_rand($values)];
  $randomEnemyEquipmentObj = json_decode($randomEnemyEquipment);
  echo "<hr>";
  echo "You prepare to hit <b>{$newRandomEnemy['name']}</b> with your <b>{$weaponTxt}</b>.<br>";
  //echo "[Calculate the chance to hit...]<br>";
  echo "Your hit lands on <b>{$newRandomEnemy['name']}'s {$randomEnemyEquipmentObj->name}</b> with a force of <b>{$stats['attack']}</b>.<br>";
  echo "Their <b>{$randomEnemyEquipmentObj->name}</b> soaked up <b>{$eBlockedPercentage}%</b> of the damage.<br>";
  echo "You dealt <b>{$eDamageDealt}</b> damage to the {$newRandomEnemy['name']}.<br>";

  // ENEMIES TURN
  $eDamageDealt   = $stats['attack']-$enemy_stats['defense'];
  if($eDamageDealt < 0){$eDamageDealt=0;}
  $pDamageBlocked = $stats['attack'] - $eDamageDealt;
  $pBlockedPercentage=round(($pDamageBlocked/$stats['attack'])*100);
  $playerEquipment=[
    'weapon'    => $inventory['weapon'] ??  '{}',
    'head'      => $inventory['head'] ??    '{}',
    'torso'     => $inventory['torso'] ??   '{}',
    'legs'      => $inventory['legs'] ??    '{}',
    'feet'      => $inventory['feet'] ??    '{}'
  ];
  $values = array_values($playerEquipment);
  $randomPlayerEquipment = $values[array_rand($values)];
  $randomPlayerEquipmentTxt = json_decode($randomPlayerEquipment)->name ?? "bare skin";
  //Enemies turn
  $weaponTxt = $newRandomEnemy['equipment']['weapon']->name ?? "bare hands";
  echo "<hr>";
  echo "<b>{$newRandomEnemy['name']}</b> prepares to hit you with their <b>{$weaponTxt}</b>.<br>";
  //echo "[Calculate the chance to hit...]<br>";
  echo "<b>{$newRandomEnemy['name']}</b> strikes your <b>{$randomPlayerEquipmentTxt}</b> with a force of <b>{$stats['attack']}</b>.<br>";
  echo "Your <b>{$randomPlayerEquipmentTxt}</b> soaked up <b>{$pBlockedPercentage}%</b> of the damage.<br>";
  echo "<b>{$newRandomEnemy['name']}</b> dealt <b>{$eDamageDealt}</b> damage to you.<br>";
  ECHO "<hr>";

  if($stats['attack'] > $enemy_stats['defense']){
      $ratio = ($stats['attack'] - $enemy_stats['defense'])/$stats['attack'] * $turns;
      $ratio = min($ratio,1);
      $gold_stolen = (int)floor($ratio/2 * $enemy_stats['currency']);
      echo "You defeated the enemy!<br> You stole " . $gold_stolen . " gold!";
      echo "<center><h3>Completed the Job successfully!</h3></center>";

      //Add gained experience
      $battle1 = mysqli_query($mysql,"UPDATE `stats` SET `experience`=`experience`+'".$job_experiencegained."' WHERE `id`='".$_SESSION['uid']."'") or die(mysqli_error($mysql));

      //Add gold stolen to user points and update db
      $battle2 = mysqli_query($mysql,"UPDATE `stats` SET `currency`=`currency`+'".$gold_stolen."' WHERE `id`='".$_SESSION['uid']."'") or die(mysqli_error($mysql));
      $stats['currency'] += $gold_stolen;
      
      //Enter battle log into db
      $battle3 = mysqli_query($mysql,"INSERT INTO `logs` (`attacker`,`defender`,`attacker_damage`,`defender_damage`,`currency`,`food`,`time`) 
                              VALUES ('".$_SESSION['uid']."','"."0"."','".$stats['attack']."','".$enemy_stats['defense']."','".$gold_stolen."','0','".time()."')") or die(mysqli_error($mysql));
      //$stats['turns'] -= $turns;
  }else{
    echo "<br>The enemies defenses were too strong...<br>";
    echo "<center><h3>You failed the Job.</h3></center>";
    //MONEY/ENERGY penalty?
  }

  echo "<center><a href='jobs.php'><button>Do another Job.</button></a><center>";

}
include("footer.php");
?>