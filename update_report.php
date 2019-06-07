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
    <title>Missing Children Database</title>
    <link rel="stylesheet" href="base.css">
</head>
<body>
<?php
require_once 'header.inc.php';
?>
<div>
    <h2>Update Child</h2>
    <?php

    // Create connection
    $conn = new mysqli($servername, $username, $password, $database, $port);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

	// Check the Request is an Update from User -- Submitted via Form
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $personFirstName = $_POST['personFirstName'];
        if ($personFirstName === null)
            echo "<div><i>Specify a new name</i></div>";
        else if ($personFirstName === false)
            echo "<div><i>Specify a new name</i></div>";
        else if (trim($personFirstName) === "")
            echo "<div><i>Specify a new name</i></div>";
        else {
			
            /* perform update using safe parameterized sql */
            /*$sql = "UPDATE Customer SET CustomerName = ? WHERE CustomerNumber = ?";*/
            $sql = "UPDATE Person P SET P.personFirstName = ? WHERE personID = ?";
            $stmt = $conn->stmt_init();
            if (!$stmt->prepare($sql)) {
                echo "failed to prepare";
            } else {
				
				// Bind user input to statement
                $stmt->bind_param('ss', $personFirstName,$id);
				
				// Execute statement and commit transaction
                $stmt->execute();
                $conn->commit();
            }
        }
    }

    /* Refresh the Data */
    /*$sql = "SELECT CustomerNumber,CustomerName,StreetAddress,CityName,StateCode,PostalCode FROM Customer C " .
        "INNER JOIN Address A ON C.defaultAddressID = A.addressID WHERE CustomerNumber = ?";*/
    $sql = "SELECT P.personID, P.personFirstName, P.personLastName, P.personMiddleName, P.personCity, CPT.CasePersonBirthDate, CPT.CasePersonWeight, CPT.CasePersonHeight, 
    MPC.missingPersonCaseDateMissing
    FROM Person AS P 
    INNER JOIN CasePersonTable AS CPT ON CPT.personID = P.personID 
    INNER JOIN MissingPersonCase AS MPC ON CPT.missingPersonCaseID = MPC.missingPersonCaseID
    WHERE CPT.personID = ?;";
    $stmt = $conn->stmt_init();
    if (!$stmt->prepare($sql)) {
        echo "failed to prepare";
    }
    else {
        $stmt->bind_param('s',$id);
        $stmt->execute();
        $stmt->bind_result($personID,$firstName,$lastName,$middleName,$cityName,$birthDate,$weight,$height,$dateMissing);
        ?>
        <form method="post">
            <input type="hidden" name="id" value="<?= $id ?>">
        <?php
        while ($stmt->fetch()) {
            echo '<a href="show_children.php?id='  . $personID . '">' . '</a><br>' . "Name: " . $firstName," ",$middleName," ",$lastName . PHP_EOL . 
             $cityName . PHP_EOL . "Missing on: ",$dateMissing . PHP_EOL . "Weight: " . $weight . PHP_EOL . "height" . $height . PHP_EOL . "Birth date: " . $birthDate;
        }
    ?><br><br>
            New Name: <input type="text" name="personFirstName">
            <button type="submit">Update</button>
        </form>
    <?php
    }

    $conn->close();

    ?>
</>
</body>
</html>
