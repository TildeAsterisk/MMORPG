<?php
include_once("functions.php");
//connect();
include_once("connection.php");

//$GLOBALS['mysql'] = connect();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <title>~* MMORTS</title>
  <link href="style.css" rel="stylesheet" type="text/css" />
  <link rel="icon" type="image/x-icon" href="tabIcon.ico">
  <!--script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script-->
</head>
<body>
  <?php include("ASCII_Gallery.php"); ?>
<!--iframe id="ASCII_Gallery" src="ASCII_Gallery.html" frameborder="0" style="display: none;"></iframe-->

  <!--div id="header"><h5 style="margin:5px 10px;"><a style="text-decoration:none;" href="index.php">~* MMORTS</a></h5></div-->
    <div id="container">
        <?php 
        if(isset($_SESSION['uid'])){
          include("safe.php");
          //echo "Logged in.";
          //echo $stats['points'];
        ?>

          <h1 style="padding:0;margin:0;">~* MMO RPG</h1>
          
          <!--a href="profile.php"><div id="miniProfile">
            <?php echo "<center><b><i><u>".$user['username']."</u></i></b></center>"; ?>
            <?php echo "<b>".$stats['currency']."</b>".$currency_symbol; ?>
            <?php echo "<b>".$stats['energy']."</b>".$energy_symbol; ?>
            <?php echo "<b>".$stats['experience']."</b>".$experience_symbol; ?><br>
            
            <div style="background-color:grey;margin-top:5px;">
              <div style="background-color:lightgreen;height:12px;width:20%"></div>
            </div>

          </div></a-->

          <div id="headerProfile">
            <table style="width:100%;">
              <tr>
                <td><?php echo $currency_symbol ?> Money</td>
                <td><?php echo $energy_symbol ?> Energy</td>
                <td><?php echo $level_symbol ?> Experience</td>
                <td><a href="profile.php"><?php echo "<center><b><i><u>".$user['username']."</u></i></b></center>"; ?></a></td>
              </tr>
              <tr>
                <td><?php echo "<b>".$stats['currency']."</b>".$currency_symbol; ?></td>
                <td><?php echo "<b>".$stats['energy']."</b>".$energy_symbol; ?></td>
                <td colspan='2'>
                <?php echo "<b>".$stats['experience']."</b>".$experience_symbol; ?>/<b>10,000</b>xp
                  <div id="experienceProgressBar" style="background-color:grey;margin-top:5px;">
                    <div style="background-color:lightgreen;height:12px;width:<?php echo ($stats['experience']/10000)*100; ?>%"></div>
                  </div>
                </td>
              </tr>
            </table>
          </div>

          <hr>
          <div id="navbar">
            <!--a href="main.php">Home</a-->
            <a href="quest.php">Quest</a>
            <!--a href="profile.php">Profile</a-->
            <a href="jobs.php">Jobs</a>
            <a href="inventory.php">Inventory</a>
            <a href="shop.php">Market</a>
            <a href="rankings.php">Ranking</a>
            <a href="logout.php">Log-Out</a>
          </div>
          <hr>

        <?php
        }
        else{
          ?>
          <div id="loginHeaderDiv">
          <form action="login.php" method="post" >
          Username:<input style="width:100%;" type="text" name="username"/><br />
          Password:<input style="width:100%;" type="password" name="password"/><br /><br/>
          <input type="submit" name="login" value="Log-in"/>
          </form>
          </div>
          <?php
        }
      ?>
      
      <div id="content">