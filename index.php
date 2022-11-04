<?php
$mysqli = new mysqli("localhost","my_user","my_password","my_db");

if ($mysqli -> connect_errno) {
  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
  exit();
}

$sql = "SELECT Lastname FROM Persons ORDER BY LastName;";
$sql .= "SELECT Country FROM Customers";

// Execute multi query
if ($mysqli -> multi_query($sql)) {
  do {
    // Store first result set
    if ($result = $mysqli -> store_result()) {
      while ($row = $result -> fetch_row()) {
        printf("%s\n", $row[0]);
      }
     $result -> free_result();
    }
    // if there are more result-sets, the print a divider
    if ($mysqli -> more_results()) {
      printf("-------------\n");
    }
     //Prepare next result set
  } while ($mysqli -> next_result());
}

$mysqli -> close();
?>