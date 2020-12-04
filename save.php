<?php
// Pull some required files
require_once 'settings.php';
require_once 'functions.php';
require_once 'authentication.php';

// If our submit button was selected AND our name field has good data
if(isset($_POST['save'])) {
  $saveTab = $_POST['saveId'];
  $roomcode = $_SESSION["code"];


  // Saving "Settings"
  if ($saveTab == 1) {
    $password = cleanupInput($_POST['password']);
    $groupname = cleanupInput($_POST['groupname']);
    $dollaramount = cleanupInput($_POST['dollaramount']);
    $formatSelection = cleanupInput($_POST['formatSelection']);

    if (!empty($password)) {
      $stmt = $DBcon->prepare("UPDATE $DB_rooms SET password = '".$password."' WHERE roomcode = '".$roomcode."'");
      $stmt->execute();
    }

    if (!empty($groupname)) {
      $stmt = $DBcon->prepare("UPDATE $DB_rooms SET groupname = '".$groupname."' WHERE roomcode = '".$roomcode."'");
      $stmt->execute();
    }

    if (!empty($dollaramount)) {
      $stmt = $DBcon->prepare("UPDATE $DB_rooms SET dollaramount = '".$dollaramount."' WHERE roomcode = '".$roomcode."'");
      $stmt->execute();
    }

    if (!empty($formatSelection)) {
      $stmt = $DBcon->prepare("Update $DB_rooms SET format = '".$formatSelection."' WHERE roomcode = '".$roomcode."'");
      $stmt->execute();
    }
    header ("Location: admin.php");  
  }


  // Saving "Names"
  elseif ($saveTab == 2) {

    $addnames = $_POST['addnames'];

    if (!empty($addnames)) {

      $stmt = $DBcon->prepare("DELETE FROM $DB_users WHERE roomcode = '".$roomcode."'");
      $stmt->execute();

      for ($i=0; $i<count($addnames); $i++) {
        $nameInstance = cleanupInput($addnames[$i]);

        if (!empty($nameInstance)) {
          $stmt = $DBcon->prepare("REPLACE INTO $DB_users (roomcode, name) VALUES ('".$roomcode."','".$nameInstance."')");
          $stmt->execute();
        }
      }
    }
    header ("Location: admin.php");  
  }


  // Saving "Restrictions"
  elseif ($saveTab == 3) {

    $candraw = $_POST['candraw'];

    $stmt = $DBcon->prepare("SELECT name FROM $DB_users WHERE roomcode = '$roomcode'");
    $stmt->execute();
    $allNames = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    $allPossibilities = array_fill_keys($allNames, $allNames);

    $diffResult = check_diff($allPossibilities,$candraw);

    for ($i=0; $i<count($allNames); $i++) {
      $noDrawList = implode(",", $diffResult[$allNames[$i]]);
      $stmt = $DBcon->prepare("UPDATE $DB_users SET nodraw = '".$noDrawList."' WHERE roomcode = '".$roomcode."' AND name = '".$allNames[$i]."'");
      $stmt->execute();
    }
    header ("Location: admin.php");  
  }


  // Saving "Go Live"
  elseif ($saveTab == 4) {
    $stmt = $DBcon->prepare("SELECT * FROM $DB_rooms WHERE roomcode = '$roomcode' LIMIT 1");
    $stmt->execute();
    $roomInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($roomInfo['live'] == TRUE) {
      $successfulGame = TRUE;
      $disableGame = TRUE;
    } else {
      $successfulGame = FALSE;
      $disableGame = FALSE;
    }

    if ($roomInfo['format'] == 'continuous') {
      $continuousFormat = TRUE;
    } elseif ($roomInfo['format'] == 'random') {
      $continuousFormat = FALSE;
    } else {
      $continuousFormat = TRUE;
    }


    // Get our players
    $stmt = $DBcon->prepare("SELECT name FROM $DB_users WHERE roomcode = '$roomcode'");
    $stmt->execute();
    $allNames = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

    // Get our table data
    $stmt = $DBcon->prepare("SELECT name,nodraw FROM $DB_users WHERE roomcode = '$roomcode'");
    $stmt->execute();
    $tableData = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $solutionFound = FALSE;
    $gameRetries = 0;
    $maxGameRetries = count($allNames) * count($allNames) * 2;
    $gameRestarted = TRUE;
    $gameMessage = "";


    do {
      if ($gameRestarted) {
        $randomInt = rand(0, count($allNames)-1);
        $remainingNames = $allNames;
        $alreadyDrawn = array();
        $firstPlayer = "";
        $lastPlayer = "";
      }

      $gameRestarted = FALSE;

      if (empty($lastPlayer)) {
        $player = $tableData[$randomInt]['name'];
        $firstPlayer = $player;
        array_push($alreadyDrawn, $firstPlayer);
      } else{ 
        $player = $lastPlayer;
      }

      $thisPlayerIndex = array_search($player, $allNames);

      $noDrawPersonal = explode(",", $tableData[$thisPlayerIndex]['nodraw']);

      $noDrawNames = array_unique(array_merge($noDrawPersonal, $alreadyDrawn));

      $validNameChoices = array_diff($remainingNames, $noDrawNames);
      
      if (empty($validNameChoices) && count($remainingNames) != 1 ) {
        $gameRetries++;
        $gameRestarted = TRUE;

        if ($gameRetries > $maxGameRetries) {
          break;
        }

      } elseif (empty($validNameChoices) && count($remainingNames) == 1 ) {
        $chosenName = $firstPlayer;

        if (in_array($firstPlayer, $noDrawPersonal)) {
          $solutionFound = FALSE;
          $successfulGame = FALSE;

        } else {
          $solutionFound = TRUE;
          $successfulGame = TRUE;
        }
      } else {
        $chosenName = $validNameChoices[array_rand($validNameChoices)];
      }
     
      $key = array_search($chosenName, $remainingNames);
      unset($remainingNames[$key]);

      $stmt = $DBcon->prepare("UPDATE $DB_users SET giftperson = '".$chosenName."' WHERE roomcode = '".$roomcode."' AND name = '".$player."'");
      $stmt->execute();

      array_push($alreadyDrawn, $chosenName);

      $lastPlayer = $chosenName;

    } while (!$solutionFound);

    // We're done...
    if ($successfulGame == TRUE) {
      if ($disableGame == TRUE) {
        $live = "0";
      } else {
        $live = "1";
      }

      $stmt = $DBcon->prepare("UPDATE $DB_rooms SET live = $live WHERE roomcode = '".$roomcode."'");
      $stmt->execute(); 

      $stmt = $DBcon->prepare("UPDATE $DB_rooms SET solution_attempts = $gameRetries WHERE roomcode = '".$roomcode."'");
      $stmt->execute(); 

      header ("Location: admin.php");
    } else {
      header ("Location: admin.php?error=badgolive");
    }
  }


  // Saving "Unlock Users"
  elseif ($saveTab == 5) {
    $viewed = $_POST['viewed'];

    if (!empty($viewed)) {
      for ($i=0; $i<count($viewed); $i++) {
        $stmt = $DBcon->prepare("UPDATE $DB_users SET viewed = '0' WHERE roomcode = '".$roomcode."' AND name = '".$viewed[$i]."'");
        $stmt->execute();
      }
    }
    header ("Location: admin.php");  
  }


  // Saving "Reset"
  elseif ($saveTab == 6) {
    $stmt = $DBcon->prepare("DELETE FROM $DB_users WHERE roomcode = '".$roomcode."'");
    $stmt->execute();

    $stmt = $DBcon->prepare("UPDATE $DB_rooms SET live = '0' WHERE roomcode = '".$roomcode."'");
    $stmt->execute();
  }
  header ("Location: admin.php");  
}

?>

<!DOCTYPE HTML>
<html>
<head> 
  <title>Christmas Name Drawing</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!--<meta http-equiv="refresh" content="60; URL='admin.php'" /> -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</head>
<body>
</body>
</html>
