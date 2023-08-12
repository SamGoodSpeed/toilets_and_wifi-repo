<?php

// Establish a connection to the MySQL database
require_once('markers_db_conn.php');

// Query to retrieve location data from the database
$query = "SELECT * FROM locations";

// Execute the query & fetch results
$result = mysqli_query($conn, $query);
$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Build array of locations
$locations = array();
foreach ($rows as $row) {

  $location = [
    'type' => $row['marker_type'],
    'lat' => $row['latitude'], 
    'lng' => $row['longitude']
  ];

  if ($row['marker_type'] === 'wifi') {
    $location['name'] = $row['atribute1'];
    $location['password'] = $row['atribute2'];
  } else if ($row['marker_type'] === 'toilet') {
    $location['women'] = $row['atribute1'];
    $location['men'] = $row['atribute2'];
  }

  $locations[] = $location;

}

// JSON encode results
//header("Access-Control-Allow-Origin: *"); 
header('Content-Type: application/json');
echo json_encode($locations);

?>