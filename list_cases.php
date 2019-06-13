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
    <h2>List of Reporting Agencies</h2>
    <?php
    // Create connection
    $conn = new mysqli($servername, $username, $password, $database, $port);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

	// Prepare SQL
    /*$sql = "SELECT ItemNumber,ItemDescription,CategoryCode FROM catalogitem";*/
    $sql = "SELECT `RA`.`agencyID`, `RA`.`agencyTitle`, `RA`.`agencyMainPhoneNumber`, `RA`.`agencyCity`, `RA`.`stateCode`
            FROM `ReportingAgency` AS `RA`;";

    $stmt = $conn->stmt_init();
    if (!$stmt->prepare($sql)) {
        echo "failed to prepare";
    }
    else {
		
		// Execute Statement
        $stmt->execute();
		
		// Process Results using Cursor
        $stmt->bind_result($agencyID,$title,$phone,$city,$state);
        while ($stmt->fetch()) {
            echo "<p>" . $title," ",$phone,", ",$city,", ",$state . "</p>";
        }
    }

    $conn->close();

    ?>
</div>
</body>
</html>
