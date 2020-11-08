<?php
// Pull some required files
require_once 'settings.php';
require_once 'functions.php';

$landing = null;
$joinedByRoomCode = $_GET['code'];

if (isset($joinedByRoomCode)) {
  $roomCodeValue = $joinedByRoomCode;
} else {
  $roomCodeValue = "";
}

// If our submit button was selected AND our name field has good data
if(isset($_POST['submit']) && !empty(cleanupInput($_POST['name'])) && !empty(cleanRoomCode($_POST['roomcode']))) {

  // Grab our POST info
  $roomcode = cleanRoomCode($_POST['roomcode']);
  $name = cleanupInput($_POST['name']);

  // Check if user is already exists
  $stmt = $DBcon->prepare("SELECT * FROM $DB_users WHERE name = '$name' AND roomcode = '$roomcode' LIMIT 1");
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  $stmt = $DBcon->prepare("SELECT * FROM $DB_rooms WHERE roomcode = '$roomcode' LIMIT 1");
  $stmt->execute();
  $roomInfo = $stmt->fetch(PDO::FETCH_ASSOC);

  // Check if we get good results, if not...
  if ($result == null) {
    $landingPage = TRUE;
    $badNameAlert = TRUE;

  // if true...
  } else {
    $landingPage = FALSE;
    $nameDrawn = TRUE;

    if ($result['viewed'] == FALSE && $roomInfo['live'] == TRUE) {
      $stmt = $DBcon->prepare("UPDATE $DB_users SET viewed = '1' WHERE roomcode = '".$roomcode."' AND name = '".$result['name']."'");
      $stmt->execute();
      $giftPersonShown = ucwords($result['giftperson']);
    } 
    elseif ($result['viewed'] == TRUE && $roomInfo['live'] == TRUE) {
      $giftPersonShown = 'Hidden<br><h4>Name has already been viewed.<br>Ask game admin to unlock your account.</h4>';
    } else {
      $giftPersonShown = 'Not Live<br><h4>Ask the admin to "Go Live".</h4>';
    }
  }
} else {
  $landingPage = TRUE;
}

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
    <?php if ($landingPage == TRUE) : ?>
    <div class="jumbotron">
      <h1 class="display-5">Join Room</h1>
      <hr class="my-4">
      <?php if ($badNameAlert == TRUE) : ?>
        <div class="alert alert-danger" role="alert">
          Sorry, either that room code isn't valid or the user doesn't exist for that room.
        </div>   
      <?php endif; ?> 
      <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <div class="form-group">
          <label>Room Code:</label>
          <input type="text" class="form-control" name="roomcode" placeholder="Room Code" value="<?php echo $roomCodeValue; ?>">
        </div>
        <div class="form-group">
          <label>Your Name:</label>
          <input type="text" class="form-control" name="name" placeholder="Your Name">
        </div>
        <button type="submit" class="btn btn-success" name="submit">Submit</button>
      </form>
    <br><button type="button" class="btn btn-warning" onclick="location.href = 'index.php';">Back</button>
    </div>
    <?php endif; ?>

    <?php if ($landingPage == FALSE) : ?>
      <div class="jumbotron">
        <h1 class="display-8">Hello, <?php echo ucwords($result['name']); ?>!</h1>
        <p class="lead">Welcome to the <?php echo ucwords($roomInfo['groupname']); ?> name drawing!</p>
        <hr class="my-4">
        <p class="lead text-center">Click the button to draw your name!<br>Keep this a secret!</p>
        <p class="lead text-center">Dollar Limit: <strong>$<?php echo $roomInfo['dollaramount']; ?></strong></p>
        <button type="button" class="btn btn-success btn-lg btn-block" data-toggle="modal" data-target="#modalNamePopup">Draw Name</button>
      <br><button type="button" class="btn btn-warning" onclick="location.href = 'join.php';">Back</button>
      </div>
    <?php endif; ?>

    <?php if ($nameDrawn == TRUE) : ?>
    <div class="modal fade" id="modalNamePopup" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">You're Buying a gift for...</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body text-center">
            <h2 class="display-3"><?php echo $giftPersonShown; ?></h1>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    </div>
  </div>
</body>
</html>