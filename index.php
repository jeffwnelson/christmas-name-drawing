<?php
  require_once 'settings.php';
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
      <h1 class="display-5">Christmas Name Drawing</h1>
      <hr class="my-4">
      <p class="text-left">Welcome! To get started, select "Create Room" to build a custom room. <br>To change how your game works, select "Admin Panel".</p>
      <button type="button" class="btn btn-primary float-md-left mx-2" onclick="location.href = 'create.php';">Create Room</button>
      <button type="button" class="btn btn-success float-md-left mx-2" onclick="location.href = 'join.php';">Join Room</button>
      <button type="button" class="btn btn-warning float-md-right mx-2" onclick="location.href = 'login.php';">Admin Panel</button>
    </div>
  </div>
</body>
</html>