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
if(isset($_POST['submit']) && !empty(cleanupInput($_POST['roomcode'])) && !empty(cleanRoomCode($_POST['password']))) {

  // Grab our POST info
  $roomcode = cleanRoomCode($_POST['roomcode']);
  $password = cleanupInput($_POST['password']);

  // Check if user is already exists
  $stmt = $DBcon->prepare("SELECT * FROM $DB_rooms WHERE roomcode = '$roomcode'");
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  // Check if password was correct
  if ($password == $result['password']) {
    session_start();
    $_SESSION["authenticated"] = 'true';
    $_SESSION["code"] = $roomcode;
    header('Location: admin.php');

  } else {
    $badCodeAlert = TRUE;
    $landingPage = TRUE;
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
    <div class="jumbotron">
      <h1 class="display-5">Admin Portal</h1>
      <hr class="my-4">
      <?php if ($badCodeAlert == TRUE) : ?>
        <div class="alert alert-danger" role="alert">
          Sorry, either that room code isn't valid or that password was incorrect.
        </div>   
      <?php endif; ?> 

      <?php if ($landingPage == TRUE) : ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
          <div class="form-group row">
            <label class="col-sm-3 col-form-label">Room Code:</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="roomcode" placeholder="Room Code" value="<?php echo $roomCodeValue; ?>">
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-3 col-form-label">Room Password:</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="password" placeholder="Room Password">
            </div>
          </div>
          <button type="submit" class="btn btn-success" name="submit">Submit</button>
        </form>
      <?php endif; ?>

      <?php if ($landingPage == TRUE) : ?>
        <br><button type="button" class="btn btn-warning" onclick="location.href = 'index.php';">Back</button>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>