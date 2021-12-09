<?php
include "bd_connection.php";
include "select_data.php";

$tablename = "loc_state";
try {
  $states = SelectData($conn, $dbname, $tablename);
  echo (json_encode($states));
} catch (Exception $e) {
  SendError($e);
}

