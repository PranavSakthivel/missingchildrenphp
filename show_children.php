<?php
/**
 * Created by PhpStorm.
 * User: MKochanski
 * Date: 7/24/2018
 * Time: 3:07 PM
 */
require_once 'config.inc.php';
// Get Customer Number
$id = $_GET['id'];
if ($id === "") {
    header('location: list_children.php');
    exit();
}
if ($id === false) {
    header('location: list_children.php');
    exit();
}
if ($id === null) {
    header('location: list_children.php');
    exit();
}
?>
<html>
<head>
    <title>Missing Children PHP</title>
    <link rel="stylesheet" href="base.css">
</head>
<body>
<?php
require_once 'header.inc.php';
?>
<div>
    <h2>Show Missing Person Information</h2>
    <?php

    // Create connection
    $conn = new mysqli($servername, $username, $password, $database, $port);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL using Parameterized Form (Safe from SQL Injections)
    $sql = "SELECT P.personID, P.personFirstName, P.personLastName, P.personMiddleName, P.personCity, CPT.CasePersonBirthDate, CPT.CasePersonWeight, CPT.CasePersonHeight, 
        MPC.missingPersonCaseDateMissing, CPT.CasePersonAge, P.genderCode, CPT.raceCode, CPT.eyeColor, MPC.stateCode
        FROM Person AS P 
        INNER JOIN CasePersonTable AS CPT ON CPT.personID = P.personID 
        INNER JOIN MissingPersonCase AS MPC ON CPT.missingPersonCaseID = MPC.missingPersonCaseID
        WHERE CPT.personID = ?;";
    $stmt = $conn->stmt_init();
    if (!$stmt->prepare($sql)) {
        echo "failed to prepare";
    }
    else {
		
		// Bind Parameters from User Input
        $stmt->bind_param('s',$id);
		
		// Execute the Statement
        $stmt->execute();
		
		// Process Results Using Cursor
        $stmt->bind_result($personID,$firstName,$lastName,$middleName,$cityName,$birthDate,$weight,$height,$dateMissing, $age, $gender, $race, $eyeColor, $state);
        echo "<div>";
        while ($stmt->fetch()) {
            echo '<a href="show_children.php?id='  . $personID . '">' . '</a><br>' . "Name: " . $firstName," ",$middleName," ",$lastName . "\r\n" . 
              "
              Missing Since: ",$dateMissing . "\r\n" . "
              Missing From: " . $cityName . " " . $state . "\r\n" . "
              Birth date: " . $birthDate . "\r\n" . "
              Age Now: " . $age. "\r\n" . "
              Gender: " . $gender . "\r\n" . "
              Race: ". $race . "\r\n" . "
              Eyes Color: " . $eyeColor . "\r\n" . "
              Weight: " . $weight . "\r\n" . "
              Height: " . $height . "\r\n";
        }
        echo "</div>";
    ?>
        <div>
            <a href="update_report.php?id=<?= $personID ?>">Update Information</a>
        </div>
    <?php
    }

    $conn->close();

    ?>
</>
</body>
</html>
