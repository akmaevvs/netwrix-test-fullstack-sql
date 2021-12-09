<?php
function SelectData($conn, $dbname, $tablename) {
  $stmtSelectData = "SELECT * FROM $dbname.$tablename";
  return $conn->query($stmtSelectData)->fetchAll(PDO::FETCH_ASSOC);
}