<?php

// Establish a connection to the MySQL database
require_once('markers_db_conn.php');

// Get the form data
$selectedOption = $_POST['radioBtn'];
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];
$wifiName = $_POST['wifiName'];
$wifiPassword = $_POST['wifiPassword'];
$womenPassword = $_POST['womenPassword'];
$menPassword = $_POST['menPassword'];

// Insert the data into the database
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check if the authority is correct
    if ($_POST['authority'] === 'frediscool'){ 

        // Check if the 'radioBtn' key exists in the $_POST array
        if (isset($_POST['radioBtn'])) {
            
            $selectedOption = $_POST['radioBtn'];

            // SANITIZE DATA before inserting into Databse
            //marker Type
            $cleanSelectedOption = filter_var($selectedOption,FILTER_SANITIZE_SPECIAL_CHARS);
            $cleanSelectedOption = substr($cleanSelectedOption, 0,10);

            //lat and long
            $cleanLatitude = filter_var($latitude, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $cleanLongitude = filter_var($longitude, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);


            if ($cleanSelectedOption === 'wifi') {
                // WiFi option was selected
                // Prepare SQL with placeholders
                $stmt = $conn->prepare("INSERT INTO locations (marker_type,latitude, longitude, atribute1, atribute2) VALUES (? ,? , ?, ?, ?)");
            
                // Sanitize wifiName 
                $cleanWifiName = htmlspecialchars($wifiName, ENT_QUOTES);
                $cleanWifiName = substr($cleanWifiName, 0, 20);

                // Sanitize wifiPassword
                $cleanWifiPassword = htmlspecialchars($wifiPassword, ENT_QUOTES);
                $cleanWifiPassword = substr($cleanWifiPassword, 0, 20);

                // Bind user input with placeholders
                $stmt->bind_param(
                    "sddss",
                    $cleanSelectedOption,
                    $cleanLatitude,
                    $cleanLongitude,
                    $cleanWifiName,
                    $cleanWifiPassword);
                
                $stmt->execute();
                echo "\nAdded wifi marker!";

            } elseif ($cleanSelectedOption === 'toilet') {
                // Toilet option was selected
                // Prepare SQL with placeholders 
                $stmt = $conn->prepare("INSERT INTO locations (marker_type,latitude, longitude, atribute1, atribute2) VALUES (?, ?, ?, ?, ?)");
                
                // The Women and Men's password are likely to have special characters..
                $cleanWomenPassword = substr($womenPassword, 0, 10);
                $cleanMenPassword = substr($menPassword, 0, 10);

                // Bind user input with placeholders
                $stmt->bind_param(
                    "sddss",
                    $cleanSelectedOption,
                    $cleanLatitude,
                    $cleanLongitude,
                    $cleanWomenPassword,
                    $cleanMenPassword);

                $stmt->execute();
                echo "\nAdded toilet marker!";
            }

        } else {
            // do nothing
            echo "failed to add marker!";
        }
    }else {
        echo "You don't have the authority to add locations!";
    }
}


// Close the database connection
$stmt->close();
mysqli_close($conn);

?>