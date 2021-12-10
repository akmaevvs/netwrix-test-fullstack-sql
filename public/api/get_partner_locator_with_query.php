<?php
include "bd_connection.php";
include "select_data_with_query.php";
// include "index.php";

// header('Content-Type: application/json');

// $data = json_decode(file_get_contents('php://input'), true);

// print_r($data);
$type = $_GET['type'];
$company_address = $_GET['company_address'];

// $type = "Type";
// $company_address = "tech";

$tablename = "partner_locator";

try {
  $partner_locations = SelectDataWithQuery($conn, $dbname, $tablename, $type, $company_address);
  echo (json_encode($partner_locations));
} catch (Exception $e) {
  SendError($e);
}
