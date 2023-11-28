<?php
$file = file_get_contents('.env');
$lines = explode("\n", $file);
$entityBody = json_decode(file_get_contents('php://input'), true);

$servername = "";
$username = "";
$password = "";
$dbname = "";

foreach ($lines as $line) {
    if (strpos($line, "SERVERNAME") !== false) {
        $servername = trim(str_replace("SERVERNAME=", "", $line));
    } if (strpos($line, "USERNAME") !== false) {
        $username = trim(str_replace("USERNAME=", "", $line));
    } if (strpos($line, "PASSWORD") !== false) {
        $password = trim(str_replace("PASSWORD=", "", $line));
    } if (strpos($line, "DBNAME") !== false) {
        $dbname = trim(str_replace("DBNAME=", "", $line));
    }
}

$vote_for = $entityBody['vote_for'];
$vote_from_ip = $_SERVER['REMOTE_ADDR'];
$vote_month = date('m');

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    $sql = "SELECT * FROM votes WHERE vote_from_ip = '$vote_from_ip' AND vote_month = '$vote_month'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        echo "You have already submitted a vote this month.";
    } else {
        $sql = "INSERT INTO votes (vote_for, vote_from_ip, vote_month) VALUES ('$vote_for', '$vote_from_ip', '$vote_month')";
        if ($conn->query($sql) === TRUE) {
            echo "Vote submitted successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();