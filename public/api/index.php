<?php
header('Access-Control-Allow-Origin: *');

function SendError($error) {
  echo (json_encode(["error" => $error]));
}
