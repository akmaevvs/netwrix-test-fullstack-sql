<?php
header('Access-Control-Allow-Origin: *');

// echo phpinfo();

function SendError($error) {
  echo (json_encode(["error" => $error]));
}
