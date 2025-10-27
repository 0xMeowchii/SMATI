<?php
date_default_timezone_set('Asia/Manila');

function connectToDB() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $db_name = "dbsmati";

    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $db_name);

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    return $conn;
}


?>