<?php
  // Set Database Information
  $DB_host = "db";
  $DB_user = "app-user";
  $DB_pass = "719@dyYmJC";
  $DB_name = "app-db";
  $DB_users = "users";
  $DB_rooms = "rooms";


  // Set how long a code should be (more chatacters, more possibilies)
  $codeLength = 3;


  // Don't modify
  // Database Testing to ensure we are good to go 
  try {
    $DBcon = new PDO("mysql:host={$DB_host};dbname={$DB_name}",$DB_user,$DB_pass);
    $DBcon->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } 
  catch(PDOException $e) {
    echo "ERROR : ".$e->getMessage();
  }
?>
