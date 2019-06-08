
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
    <h2>Search by Name</h2>
    <?php

    // Create connection
    $conn = new mysqli($servername, $username, $password, $database, $port);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

	// Check the Request is an Update from User -- Submitted via Form
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $personFirstName = $_GET['personFirstName'];
        if ($personFirstName === null)
            echo "<div><i>Enter a value</i></div>";
        else if ($personFirstName === false)
            echo "<div><i>Enter a value</i></div>";
        else if (trim($personFirstName) === "")
            echo "<div><i>Enter a value</i></div>";
        else {
			
            /* perform update using safe parameterized sql */
            $sql = "SELECT P.personFirstName, P.personLastName, MPC.missingPersonCaseCity, MPC.missingPersonCaseDateMissing, P.personID, MPC.missingPersonCaseID 
            FROM MissingPersonCase AS MPC 
            INNER JOIN CasePersonTable AS CPT ON CPT.missingPersonCaseID = MPC.missingPersonCaseID 
            INNER JOIN Person AS P ON CPT.personID = P.personID 
            WHERE CPT.roleCode = \"Victim\" 
            WHERE P.personFirstName LIKE '%?%'";
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
    $sql = "SELECT P.personFirstName, P.personLastName, MPC.missingPersonCaseCity, MPC.missingPersonCaseDateMissing, P.personID, MPC.missingPersonCaseID 
    FROM MissingPersonCase AS MPC 
    INNER JOIN CasePersonTable AS CPT ON CPT.missingPersonCaseID = MPC.missingPersonCaseID 
    INNER JOIN Person AS P ON CPT.personID = P.personID 
    WHERE CPT.roleCode = \"Victim\" 
    WHERE P.personFirstName LIKE '%?%'";
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
            echo '<a href="show_children.php?id='  . $personID . '">' . '</a><br>' . "Name: " . $firstName," ",$middleName," ",$lastName . "\r\n" . 
             $cityName . "\r\n" . "Missing on: ",$dateMissing . "\r\n" . "Weight: " . $weight . "\r\n" . "height" . $height . "\r\n" . "Birth date: " . $birthDate;
        }
    ?><br><br>
            <form name="personFirstName" method = "GET">
            <input id="search" type="text" placeholder="Type Here">
            <input id="submit" type="submit" value="Search">
        </form>
    <?php
    }

    $conn->close();

    ?>
</>
</body>
</html>
