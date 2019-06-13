
<html>
<head>
    <title>Search Results</title>
    <link rel="stylesheet" href="base.css">
</head>
<body>
<?php
    require_once 'config.inc.php';
    // Create connection
    $conn = new mysqli($servername, $username, $password, $database, $port);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $query = $_GET['query']; 
    // gets value sent over search form
    $min_length = 0;
    // you can set minimum length of the query if you want
     
    if(strlen($query) >= $min_length){ // if query length is more or equal minimum length then
         
        // Changes the user's query to remove any special characters. Protects against certain HTML and Javascript attacks
        $query = htmlspecialchars($query); 
         
        //$query = mysqli_real_escape_string($query);
        // makes sure nobody uses SQL injection
         
        // Get Query
        $sql ="SELECT P.personFirstName, P.personLastName, MPC.missingPersonCaseCity, MPC.missingPersonCaseDateMissing, P.personID, MPC.missingPersonCaseID 
        FROM MissingPersonCase AS MPC 
        INNER JOIN CasePersonTable AS CPT ON CPT.missingPersonCaseID = MPC.missingPersonCaseID 
        INNER JOIN Person AS P ON CPT.personID = P.personID 
        WHERE CPT.roleCode = \"Victim\" 
        AND P.personFirstName LIKE '%".$query."%'";
        
        echo "Search Query: ";
        echo $query;

        $stmt = $conn->stmt_init(); // This line initilizes the variable, $stmt
        if (!$stmt->prepare($sql)) { 
            echo "failed to prepare"; // Query Breaks
        }
        else {
            //$stmt->bind_param('s',$query); // Replaces question marks with queries
            $stmt->execute(); // Activate SQL
            $stmt->bind_result($firstName,$lastName,$cityName,$dateMissing, $personID, $caseID);
            ?>
            <form method="GET">
                <input type="hidden" name="query" value="<?= $query ?>">
            <?php
            while ($stmt->fetch()) { // This should pull until the program hits an EOL
                echo '<a href="show_children.php?query='  . $personID . '">' .  "Name: " . $firstName," " . $lastName . '</a><br>' . "\r\n" . 
                 $cityName . "<br>" . "Missing on: ",$dateMissing . "</br>";
            }
        }
        //$result = mysqli_query($conn, $sql);
         
        
        // if(mysqli_num_rows($result) > 0){ // if one or more rows are returned do following
             
        //     while($results = mysqli_fetch_array($result, MYSQLI_NUM)){
        //         $stmt = $conn->stmt_init(); // Initialize variable
        //         $stmt->bind_result($firstName,$lastName,$caseCity,$dateMissing,$personID,$mpcID); // Allocate retrieved values
        //         echo "<ul>";
        //         while ($stmt->fetch()) {
        //         echo '<li><a href="show_children.php?id='  . $personID . '">' . $firstName," ",$lastName,", ",$caseCity," ",$dateMissing . '</a></li>';
        //         }
        //     }
        // echo "</ul>";
        // }
        // else{ // if there is no matching rows do following
        //     echo "No results";
        // }
         
    }
    else{ // if query length is less than minimum
        echo "Minimum length is ".$min_length;
    }
?>