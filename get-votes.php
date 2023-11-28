<?php
$file = file_get_contents('.env');
$lines = explode("\n", $file);
$entityBody = json_decode(file_get_contents('php://input'), true);

$servername = "";
$username = "";
$password = "";
$dbname = "";
$employee_of_the_month_password = "";

foreach ($lines as $line) {
    if (strpos($line, "SERVERNAME") !== false) {
        $servername = trim(str_replace("SERVERNAME=", "", $line));
    } if (strpos($line, "USERNAME") !== false) {
        $username = trim(str_replace("USERNAME=", "", $line));
    } if (strpos($line, "PASSWORD") !== false) {
        $password = trim(str_replace("PASSWORD=", "", $line));
    } if (strpos($line, "DBNAME") !== false) {
        $dbname = trim(str_replace("DBNAME=", "", $line));
    } if (strpos($line, "EMPLOYEE_OF_THE_MONTH_PASSWORD") !== false) {
        $employee_of_the_month_password = trim(str_replace("EMPLOYEE_OF_THE_MONTH_PASSWORD=", "", $line));
    }
}


$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($entityBody === null) {
    die("Invalid JSON data");
}

$postedPassword = $entityBody['password'];
if ($postedPassword !== $employee_of_the_month_password) {
    die("Incorrect password");
} else {
    $employee = $entityBody['employee'];
    if ($employee) {
        $sql = "SELECT * FROM votes WHERE vote_for = '$employee'";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            $votes = $result->num_rows;
            echo $votes;
        } else {
            echo 0;
        }
    }
} 

