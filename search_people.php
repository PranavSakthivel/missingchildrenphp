
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
         
        $query = htmlspecialchars($query); 
        // changes characters used in html to their equivalents, for example: < to &gt;
         
        //$query = mysqli_real_escape_string($query);
        // makes sure nobody uses SQL injection
         
        // Get Query
        $sql ="SELECT P.personFirstName, P.personLastName, MPC.missingPersonCaseCity, MPC.missingPersonCaseDateMissing, P.personID, MPC.missingPersonCaseID 
        FROM MissingPersonCase AS MPC 
        INNER JOIN CasePersonTable AS CPT ON CPT.missingPersonCaseID = MPC.missingPersonCaseID 
        INNER JOIN Person AS P ON CPT.personID = P.personID 
        WHERE CPT.roleCode = \"Victim\" 
        WHERE P.personFirstName LIKE '?';"; 
        if (!$stmt->prepare($sql)) {
            echo "failed to prepare"; // Query Breaks
        }
        else {
            $stmt->bind_param('s',$query); // No idea
            $stmt->execute(); // Activate SQL
            $stmt->bind_result($firstName,$lastName,$cityName,$dateMissing, $personID, $caseID);
            ?>
            <form method="get">
                <input type="hidden" name="query" value="<?= $query ?>">
            <?php
            while ($stmt->fetch()) { // No idea
                echo '<a href="show_children.php?query='  . $firstname . '">' . '</a><br>' . "Name: " . $firstName," ",$middleName," ",$lastName . "\r\n" . 
                 $cityName . "<br>" . "Missing on: ",$dateMissing . "</br>" . "Weight: " . $weight . "<br>" . "height" . $height . "</br>" . "Birth date: " . $birthDate;
            }
        }
        // * means that it selects all fields, you can also write: `id`, `title`, `text`
        // articles is the name of our table
         
        // '%$query%' is what we're looking for, % means anything, for example if $query is Hello
        // it will match "hello", "Hello man", "gogohello", if you want exact match use `title`='$query'
        // or if you want to match just full word so "gogohello" is out use '% $query %' ...OR ... '$query %' ... OR ... '% $query'
         
        if(mysql_num_rows($sql) > 0){ // if one or more rows are returned do following
             
            while($results = mysql_fetch_array($sql)){
            // $results = mysql_fetch_array($sql) puts data from database into array, while it's valid it does the loop
             
                //echo "<p><h3>".$results['title']."</h3>".$results['text']."</p>";
                // posts results gotten from database(title and text) you can also show id ($results['id'])
                $stmt = $conn->stmt_init();
                $stmt->bind_result($firstName,$lastName,$caseCity,$dateMissing,$personID,$mpcID);
                echo "<ul>";
                while ($stmt->fetch()) {
                echo '<li><a href="show_children.php?id='  . $personID . '">' . $firstName," ",$lastName,", ",$caseCity," ",$dateMissing . '</a></li>';
                }
            }
        echo "</ul>";
            
             
        }
        else{ // if there is no matching rows do following
            echo "No results";
        }
         
    }
    else{ // if query length is less than minimum
        echo "Minimum length is ".$min_length;
    }
?>