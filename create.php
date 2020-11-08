<?php
// Pull some required files
require_once 'settings.php';
require_once 'functions.php';

$roomcode = null;

// If our submit button was selected AND our name field has good data
if(isset($_POST['submit']) && !empty(cleanupInput($_POST['password']))) {

  // Grab our POST info
  $roomcode = generateRoomCode($codeLength);
  $password = cleanupInput($_POST['password']);
  $groupname = cleanupInput($_POST['groupname']);
  $dollaramount = cleanupInput($_POST['dollaramount']);

      if (!empty($formatSelection)) {
      $stmt = $DBcon->prepare("Update $DB_rooms SET format = '".$formatSelection."' WHERE roomcode = '".$roomcode."'");
      $stmt->execute();
    }

  // Create our Room
  $stmt = $DBcon->prepare("INSERT INTO $DB_rooms (roomcode, password, groupname, dollaramount, format) VALUES ('".$roomcode."','".$password."','".$groupname."','".$dollaramount."','continuous')");
  $stmt->execute();

  $conn = null;
  $alert = TRUE;

} else {
  $alert = FALSE;
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
    <div class="jumbotron">
      <h1 class="display-5">Create Room</h1>
      <hr class="my-4">
      <?php if ($alert == TRUE) : ?>
        <div class='alert alert-success' role='alert'>
          <h4 class='alert-heading'>Room Code: <?php echo $roomcode; ?></h4>
          <p>Click the "Login to Admin" below to setup your room.</p>
          <button type="button" class="btn btn-info" onclick="location.href = 'login.php?code=<?php echo $roomcode; ?>';">Login to Admin</button>
        </div>
      <?php endif; ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
          <div class="input-group">
            <label class="col-sm-3 col-form-label">Room Password:</label>
            <input type="text" class="form-control" name="password">
          </div>
          <div class="input-group">
            <label class="col-sm-3 col-form-label">Group Name:</label>
            <input type="text" class="form-control" name="groupname">
          </div>
          <div class="input-group">
            <label class="col-sm-3 col-form-label">Dollar Amount:</label>
            <div class="input-group-prepend">
              <span class="input-group-text">$</span>
            </div>
            <input type="number" class="form-control" name="dollaramount">
          </div>
          <br><br>
          <button type="submit" class="btn btn-success" name="submit">Submit</button>
        </form>
        <br>
        <button type="button" class="btn btn-warning" onclick="location.href = 'index.php';">Back</button>
      </div>
    </div>
  </div>
</body>
</html>