<?php
function cleanupInput($string) {
  $string = strtolower($string);
  $string = trim($string);
  $string = htmlspecialchars($string);
  return $string;
}


function getBaseURL() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $domainName = $_SERVER['HTTP_HOST'];
    return $protocol.$domainName;
}


function generateRoomCode($codeLength) {
  $roomcode = "";
  
  $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
  
  for($i=0; $i<$codeLength; $i++) {
    $index = rand(0, strlen($chars) - 1);
    $roomcode .= $chars[$index];
  }
  return $roomcode;
}

function cleanRoomCode($code) {
  $code = htmlspecialchars(cleanupInput($code));
  $code = strtoupper($code);
  return $code;
}


function check_diff($arr1, $arr2){
    $check = (is_array($arr1) && count($arr1)>0) ? true : false;
    $result = ($check) ? ((is_array($arr2) && count($arr2) > 0) ? $arr2 : array()) : array();
    if($check){
        foreach($arr1 as $key => $value){
            if(isset($result[$key])){
                $result[$key] = array_diff($value,$result[$key]);
            }else{
                $result[$key] = $value;
            }
        }
    }

    return $result;
}