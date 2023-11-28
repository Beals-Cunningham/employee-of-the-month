<?php
$file = file_get_contents('.env');
$lines = explode("\n", $file);
$entityBody = json_decode(file_get_contents('php://input'), true);

$servername = "";
$username = "";
$password = "";
$dbname = "";
$employee_of_the_month_password = "";

$all_employees = 
    [
        'Sayer',
        'Abbie',
        'Amelia',
        'Ashley',
        'Avery',
        'Claire',
        'Don',
        'Jamie',
        'Joseph',
        'KaCee',
        'Karsten',
        'Kellen',
'Kelli',
'Kelsi',
'Kris',
'Maddy D.',
'Madison Z.',
'Makk',
'Phil',
'Mary',
'Matt',
'Nick',
'Paul',
'Sharon',
'Tiffani M.',
'Tomasz',
'Wes',
'Yvone',
'Zak'
    ];

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


$votes_per_employee = [];

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
    foreach($all_employees as $employee) {
        $sql = "SELECT * FROM votes WHERE vote_for = '$employee'";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            $votes = $result->num_rows;
            $votes_per_employee[$employee] = $votes;
        } else {
            $votes_per_employee[$employee] = 0;
        }
    }
    arsort($votes_per_employee);
    echo json_encode($votes_per_employee);
} 

