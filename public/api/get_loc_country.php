<?php
include "bd_connection.php";
include "select_data.php";

$tablename = "loc_country";
try {
  $countries = SelectData($conn, $dbname, $tablename);
  echo (json_encode($countries));
} catch (Exception $e) {
  SendError($e);
}

