<?php
/**
 * Created by PhpStorm.
 * User: MKochanski
 * Date: 7/24/2018
 * Time: 3:07 PM
 */
require_once 'config.inc.php';

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
    <h2>Missing Children List</h2>

    <form name="personFirstName" method = "GET">
    <input id="search" type="text" placeholder="Type Here">
    <input id="submit" type="submit" value="Search">
            
    <?php
    // Create connection
    $conn = new mysqli($servername, $username, $password, $database, $port);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

	// Prepare SQL Statement
    $sql = "SELECT P.personFirstName, P.personLastName, MPC.missingPersonCaseCity, MPC.missingPersonCaseDateMissing, P.personID, MPC.missingPersonCaseID 
            FROM MissingPersonCase AS MPC 
            INNER JOIN CasePersonTable AS CPT ON CPT.missingPersonCaseID = MPC.missingPersonCaseID 
            INNER JOIN Person AS P ON CPT.personID = P.personID 
            WHERE CPT.roleCode = \"Victim\" 
            ORDER BY P.personFirstName;";
    $stmt = $conn->stmt_init();
    if (!$stmt->prepare($sql)) {
        echo "failed to prepare";
    }
    else {
		
		// Execute the Statement
        $stmt->execute();
		
		// Loop Through Result
        $stmt->bind_result($firstName,$lastName,$caseCity,$dateMissing,$personID,$mpcID);
        echo "<ul>";
        while ($stmt->fetch()) {
            echo '<li><a href="show_children.php?id='  . $personID . '">' . $firstName," ",$lastName,", ",$caseCity," ",$dateMissing . '</a></li>';
        }
        echo "</ul>";
    }

	// Close Connection
    $conn->close();

    ?>
</div>
</body>
</html>
