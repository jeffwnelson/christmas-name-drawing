<?php
// Pull some required files
require_once 'settings.php';
require_once 'functions.php';
require_once 'authentication.php';

$roomcode = $_SESSION["code"];
$error = $_GET["error"];

$stmt = $DBcon->prepare("SELECT * FROM $DB_rooms WHERE roomcode= '$roomcode' LIMIT 1");
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result['live'] == TRUE) {
  $gameLive = TRUE;
  $tabDisabled = "";
} else {
  $gameLive = FALSE;
  $tabDisabled = " disabled";
}

if ($error == "badgolive") {
  $badCodeAlert = TRUE;
  $goodCodeAlert = FALSE;
} else {
  $badCodeAlert = FALSE;
  $goodCodeAlert = FALSE;
}


$stmt = $DBcon->prepare("SELECT name,nodraw,viewed FROM $DB_users WHERE roomcode= '$roomcode'");
$stmt->execute();
$userTableResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE HTML>
<html>
<head> 
  <title>Christmas Name Drawing</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="style.css">  
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
  <script src="extrajs.js"></script>
</head>
<body>
  <div class="container">
    <div class="jumbotron">
      <h1 class="display-5">Welcome admin!</h1>
      <hr class="my-4">
      <p class="lead text-center">Your Room Code: <strong><?php echo $roomcode; ?></strong></p>

      <?php if ($gameLive == FALSE) : ?>
        <div class="alert alert-warning" role="alert">
          <div class="text-center">
            <h4 class="alert-heading">Game Not Live!</h4>
            <p class="mb-0">Add your names and restrictions. <br>Select "Go Live" when ready.</p>
          </div>
        </div>   
      <?php endif; ?>

      <?php if ($badCodeAlert == TRUE) : ?>
        <div class="alert alert-danger" role="alert">
          <div class="text-center">
            <h4 class="alert-heading">Game Issues!</h4>
            <p class="mb-0">The game attempted to solve for a possible solution but no solutions were found.</p>
            <p class="mb-0">You can attempt to try again; however, there maybe no solutions and you may need to remove some restrictions.</p>
          </div>
        </div>   
      <?php endif; ?>

      <?php if ($goodCodeAlert == TRUE || $gameLive == TRUE) : ?>
        <div class="alert alert-success" role="alert">
          <div class="text-center">
            <h4 class="alert-heading">Game is ready!</h4>
            <p class="mb-0">Share the room link with those participating in the drawing!</p>
            <div class="btn-group text-center">
              <a class="btn btn-light"><?php echo getBaseURL()."/join.php?code=".$roomcode; ?></a>
              <button type="button" class="btn btn-dark btn-copy js-tooltip js-copy" data-toggle="tooltip" data-placement="bottom" data-copy="<?php echo getBaseURL()."/join.php?code=".$roomcode; ?>" title="Copy to clipboard" value="COPY">COPY</button>
            </div>
          </div>
        </div>
      <?php endif; ?>  
      <hr class="my-4">
      

      <div class="card text-left">
        <!-- Create Tabs -->
        <div class="card-header">
          <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
              <a class="nav-link active" id="settings-tab" data-toggle="tab" href="#settings" role="tab" aria-controls="settings" aria-selected="true">Settings</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="names-tab" data-toggle="tab" href="#names" role="tab" aria-controls="names" aria-selected="false">Names</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="restrictions-tab" data-toggle="tab" href="#restrictions" role="tab" aria-controls="restrictions" aria-selected="false">Restrictions</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="go-live-tab" data-toggle="tab" href="#go-live" role="tab" aria-controls="go-live" aria-selected="false">Go Live</a>
            </li>
            <li class="nav-item">
              <a class="nav-link<?php echo $tabDisabled; ?>" id="unlock-users-tab" data-toggle="tab" href="#unlock-users" role="tab" aria-controls="unlock-users" aria-selected="false">Unlock Users</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="reset-tab" data-toggle="tab" href="#reset" role="tab" aria-controls="reset" aria-selected="false">Reset</a>
            </li>
          </ul>
        </div>
       <!-- Create Tabs -->



        <!-- Settings Tab -->
        <div class="tab-content" id="myTabContent">
          <div class="tab-pane p-md-3 fade show active" id="settings" role="tabpanel" aria-labelledby="set-settings-tab">
            <form method="post" action="save.php">
              <div class="input-group">
                <label class="col-sm-3 col-form-label">Room Password:</label>
                <input type="text" class="form-control" name="password" value="<?php echo $result['password']; ?>">
              </div>
              <div class="input-group">
                <label class="col-sm-3 col-form-label">Group Name:</label>
                <input type="text" class="form-control" name="groupname" value="<?php echo ucwords($result['groupname']); ?>">
              </div>
              <div class="input-group">
                <label class="col-sm-3 col-form-label">Dollar Amount:</label>
                <div class="input-group-prepend">
                  <span class="input-group-text">$</span>
                </div>
                <input type="number" class="form-control" name="dollaramount" value="<?php echo $result['dollaramount']; ?>">
              </div>
              <div class="input-group">
                <label class="col-sm-3 col-form-label">Draw Format:</label>
                <select class="custom-select" id="formatSelection" name="formatSelection">
                  <option value="continuous" <?php if ($result['format'] == 'continuous') { echo "selected"; } ?>>Continuous</option>
                  <option value="random" <?php if ($result['format'] == 'random') { echo "selected"; } ?>>Random</option>
                </select>
              </div>
            <br><br>
            <div class="input-group justify-content-center">
              <div class="card" style="width: 30rem;">
                <img class="card-img-top p3" src="drawFormatExample.png" alt="Draw Format Example">
                <div class="card-body">
                  <p class="card-text">
                    <b>Continuous</b>: Players will be drawn in order so that a single gift exchange loop isn't broken. (We find this method to be more enjoyable.) This also means players will never have each other.
                    <hr>
                    <b>Random</b>: Players will be drawn at random, regardless of order. This means there may be situations were players have each other, or the main gift exchange loop ends where it started and some people haven't drawn yet. 
                  </p>
                </div>
              </div>
            </div>
            <br><br>
            <input type="hidden" id="saveId" name="saveId" value="1">
            <button type="submit" class="btn btn-success" name="save">Save</button>
            </form>
          </div>
          <!-- Settings Tab -->



          <!-- Add Names Tab -->
          <div class="tab-pane p-md-3 fade" id="names" role="tabpanel" aria-labelledby="name-tab">
            <p>
              Add or remove names to the drawing pool.
            </p>
            <div class="container">
              <form method="post" action="save.php">
                <div class="form-group">
                  <div data-role="dynamic-fields">
                    <?php

                      for ($i=0; $i<count($userTableResults); $i++) {
                        echo '<div class="form-inline">';
                        echo '<div class="form-group">';
                        echo '<input type="text" class="form-control" id="field-name" name="addnames[]" value="'.ucwords($userTableResults[$i]['name']).'"">';
                        echo '</div>';
                        echo '<button class="btn btn-danger" data-role="remove"><i class="fa fa-minus" aria-hidden="true"></i></button>';
                        echo '<button class="btn btn-primary" data-role="add"><i class="fa fa-plus" aria-hidden="true"></i></button>';
                        echo '</div>';
                      }
                    ?>
                      <div class="form-inline">
                        <div class="form-group">
                          <input type="text" class="form-control" id="field-name" name="addnames[]">
                        </div>
                        <button class="btn btn-danger" data-role="remove"><i class="fa fa-minus" aria-hidden="true"></i></button>
                        <button class="btn btn-primary" data-role="add"><i class="fa fa-plus" aria-hidden="true"></i></button>
                      </div>
                  </div>
                </div>
                <input type="hidden" id="saveId" name="saveId" value="2">
                <button type="submit" class="btn btn-success" name="save">Save</button>
              </form>
            </div>
          </div>
          <!-- Add Names Tab -->



          <!-- Restrictions Tab -->
          <div class="tab-pane p-md-3 fade" id="restrictions" role="tabpanel" aria-labelledby="restrictions-tab">
            <p class="p-md-3">
              Add restrictions as to what players can draw other players. (IE: Spouses, specific family members) <br>
              Under each person, removing checkmarks from their sub-names prevents them from being drawn by that Person.<br>
              <strong>(By default, players are unable to draw themselves.)</strong>
            </p>
            <div class="container">
              <form method="post" action="save.php">
                <div class="accordion" id="accordionNameList">
                  <?php
                    for ($i=0; $i<count($userTableResults); $i++) {

                      echo '<div class="card">';
                      echo '<div class="card-header" id="heading'.$i.'">';
                      echo '<h2 class="mb-0">';
                      echo '<button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse'.$i.'" aria-expanded="true" aria-controls="collapse'.$i.'">';
                      echo ucwords($userTableResults[$i]['name']);
                      echo '</button>';
                      echo '</h2>';
                      echo '</div>';
                      echo '<div id="collapse'.$i.'" class="collapse" aria-labelledby="heading'.$i.'" data-parent="#accordionNameList">';
                      echo '<div class="card-body">';

                      for ($j=0; $j<count($userTableResults); $j++) {

                        $noDrawList = explode(",", $userTableResults[$i]['nodraw']);

                        if (in_array($userTableResults[$j]['name'], $noDrawList)) {
                          $checked = "";
                        } else {
                          $checked = " checked";
                        }

                        echo '<div class="form-check">';

                        if ($userTableResults[$j]['name'] != $userTableResults[$i]['name']) {
                          echo '<div class="input-group">';
                          echo '  <div class="input-group-prepend">';
                          echo '    <div class="input-group-text">';
                          echo '      <input type="checkbox" value="'.$userTableResults[$j]['name'].'" name="candraw['.$userTableResults[$i]['name'].'][]" id="defaultCheck'.$i.'"'.$checked.'>';
                          echo '    </div>';
                          echo '  </div>';
                          echo '  <fieldset disabled>';
                          echo '  <input type="text" class="form-control" value="'.ucwords($userTableResults[$j]['name']).'">';
                          echo '  </fieldset>';
                          echo '</div>';
                        }
                        echo '</div>';
                      }
                      echo '</div>';
                      echo '</div>';
                      echo '</div>';
                    }
                  ?>
                </div>
                <br>
                <input type="hidden" id="saveId" name="saveId" value="3">
                <button type="submit" class="btn btn-success" name="save">Save</button>
              </form>
            </div>
          </div>
          <!-- Restrictions Tab -->




          <!-- Go Live Tab -->
          <div class="tab-pane p-md-3 fade" id="go-live" role="tabpanel" aria-labelledby="go-live-tab">
            <p>Select the options below to "Enable" or "Disable" the drawing. 
            </p>
            <form method="post" action="save.php">
              <input type="hidden" id="saveId" name="saveId" value="4">
              <?php if ($gameLive == FALSE) : ?>
                <button type="submit" class="btn btn-success" name="save">Enable</button>
              <?php endif; ?>
              <?php if ($gameLive == TRUE) : ?>
                <button type="submit" class="btn btn-danger" name="save">Disable</button>
              <?php endif; ?>
            </form>
          </div>
          <!-- Go Live Tab -->



          <!-- Unlock Users Tab -->
          <div class="tab-pane p-md-3 fade" id="unlock-users" role="tabpanel" aria-labelledby="unlock-users-tab">
            <p>To prevent users from peaking at other player's drawing results, they are locked automatically after they view their gift person.<br>
              Use this menu below to unlock users and let them review their gift person.<br>
              <strong>Note that after they view their name again - it will re-lock.</strong>
            </p>
            <div class="container">
              <form method="post" action="save.php">
                <?php

                  for ($i=0; $i<count($userTableResults); $i++) {
                    if ($userTableResults[$i]['viewed'] == FALSE) {
                      $checked = " checked";
                    } else {
                      $checked = "";
                    }

                    echo '<div class="input-group">';
                    echo '  <div class="input-group-prepend">';
                    echo '    <div class="input-group-text">';
                    echo '      <input type="checkbox" value="'.$userTableResults[$i]['name'].'" name="viewed[]" id="defaultCheck'.$i.'"'.$checked.'>';
                    echo '    </div>';
                    echo '  </div>';
                    echo '  <fieldset disabled>';
                    echo '  <input type="text" class="form-control" value="'.ucwords($userTableResults[$i]['name']).'">';
                    echo '  </fieldset>';
                    echo '</div>';

                    //echo '<div class="form-check">';
                    //echo '  <input class="form-check-input" type="checkbox" value="'.$userTableResults[$i]['name'].'" name="viewed[]" id="defaultCheck'.$i.'"'.$checked.'>';
                    //echo '  <label class="form-check-label" for="defaultCheck'.$i.'">'.ucwords($userTableResults[$i]['name']).'</label>';
                    //echo '</div>';
                  }
                ?>
                <br>
                <input type="hidden" id="saveId" name="saveId" value="5">
                <button type="submit" class="btn btn-success" name="save">Unlock Users</button>
             </form>
            </div>
          </div>
          <!-- Unlock Users Tab -->




          <!-- Reset Tab -->
          <div class="tab-pane p-md-3 fade" id="reset" role="tabpanel" aria-labelledby="reset-tab">
            <p>Remove all names from the database?
            </p>
            <form method="post" action="save.php">
              <input type="hidden" id="saveId" name="saveId" value="6">
              <button type="submit" class="btn btn-danger" name="save">Delete Everything</button>
            </form>
          </div>
          <!-- Reset Tab -->


        </div>
      </div>
      <br><button type="button" class="btn btn-warning" onclick="location.href = 'index.php';">Back</button>
    </div>
  </div>
</body>
</html>


