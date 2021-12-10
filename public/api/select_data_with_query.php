<?php
function SelectDataWithQuery($conn, $dbname, $tablename, $type, $company_address)
{
  if ($type != "" and $company_address != "") {
    $stmtSelectData = "SELECT * FROM $dbname.$tablename WHERE `status` = '$type' AND (`company` LIKE '%$company_address%' OR `address` LIKE '%$company_address%')";
  } else if ($type != "" and $company_address == "") {
    $stmtSelectData = "SELECT * FROM $dbname.$tablename WHERE `status` = '$type'";
  } else if ($type == "" and $company_address != "") {
    $stmtSelectData = "SELECT * FROM $dbname.$tablename WHERE `company` LIKE '%$company_address%' OR `address` LIKE '%$company_address%'";
  } 
  return $conn->query($stmtSelectData)->fetchAll(PDO::FETCH_ASSOC);
}
