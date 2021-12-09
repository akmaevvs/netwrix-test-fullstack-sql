<?php
include "bd_connection.php";
include "select_data.php";

$tablename = "loc_country";
try {
  $partner_locations = SelectData($conn, $dbname, $tablename);
  echo (json_encode($partner_locations));
} catch (Exception $e) {
  SendError($e);
}
