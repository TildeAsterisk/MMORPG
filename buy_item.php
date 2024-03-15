<?php
session_start();
include("header.php");
if(!isset($_SESSION['uid'])){
    echo "You must be logged in to view this page!";
}else{
  if(isset($_POST["buy"])){
    $newItem=[
      'name'        => $_POST['name'],
      'description' => $_POST['description'],
      'price'       => $_POST['price'],
      'attack'      => $_POST['attack'],
      'defense'     => $_POST['defense'],
      'itemType'     => $_POST['itemType']
    ];


    if ($stats['currency'] < $newItem['price']){
      echo "You can't afford that, sorry.";
      return;
    }

    //Add to inventory
    //get current inventory
    $getPlayerInvQuery = mysqli_query($mysql,"SELECT * FROM `inventory` WHERE `id`='".$_SESSION['uid']."'") or die(mysqli_error($mysql));
    $playerInv = mysqli_fetch_assoc($getPlayerInvQuery);
    $playerInvDecoded=json_decode($playerInv['items']);

    //if inventory is at capacity
    if (sizeof((array)$playerInvDecoded) >= $inventory['capacity']) {
      echo "Your inventory is full.";
      
      return;
    }
    if($playerInv == NULL){
      $playerInv='{}';
    }
    $playerInvDecoded = json_decode($playerInv['items'], true);
    array_push($playerInvDecoded, $newItem);
    $playerInvJson = json_encode($playerInvDecoded);
    //update JSON in DB
    //$updatePlayerInvQuery = mysqli_query($mysql,"UPDATE `inventory` SET `items`=`items`+'".$newItemJson."' WHERE `id`='".$_SESSION['uid']."'") or die(mysqli_error($mysql));
    $updateQuery = "UPDATE `inventory` SET `items` = '$playerInvJson' WHERE `id` = '".$_SESSION['uid']."'";
    mysqli_query($mysql, $updateQuery) or die(mysqli_error($mysql));

  //Subtract cost of item
  $energycostquery = mysqli_query($mysql,"UPDATE `stats` SET `currency`=`currency`-'".$newItem['price']."' WHERE `id`='".$_SESSION['uid']."'") or die(mysqli_error($mysql));



    echo "You have purchased ".$newItem['name'].".<br>";
    echo "<a href='shop.php'><button>Back to Market.</button></a><br>";
    echo "<a href='inventory.php'><button>See in Inventory.</button></a>";
    include("update_stats.php");

  }
  else{
    output("You have visited this page incorrectly.");
  }

}
include("footer.php");
?>