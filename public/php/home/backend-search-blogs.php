<?php

//include "$_SERVER[DOCUMENT_ROOT]/php/home/session.php";
require (dirname(__FILE__) . '/../../../resources/db/db_connect.php');



/* Attempt MySQL server connection. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
$connection = db_connect();


session_start();

$userId = $_SESSION['userId'];

// Check connection
if($connection === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Escape user inputs for security
$query = mysqli_real_escape_string($connection, $_POST['query']);

// search friends first name of last name like input
if(isset($query)){
    // Attempt select query execution

    // search content of currently logged in user's friends' blogs
    // uses a user's 'everyone' friendCircle for search
    $sql =  "select substr(b.content, 1, 15) as trimcontent, fName, lName, username, blogId from blogs as b
            inner join users as u on b.userId = u.userId
            inner join friendcircle_users as fcu on fcu.userId = b.userId
            inner join friendcircles as fc on fc.circleId = fcu.circleId
            where fc.userId = 3 and fc.name = 'everyone' and content like '%".$query."%'";

    if($result = mysqli_query($connection, $sql)){
        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_array($result)){
                // TODO: INSERT CORRECT URL FOR USER'S BLOG
                // assuming url is /username/#blogId
                echo "<a href=/".$row['username']."/#".$row['blogId']."><p>". $row['fName']." ".$row['lName']." ". $row['trimcontent'] ."...</p></a>";
            }
            // Close result set
            mysqli_free_result($result);
        } else{
            echo "<p>No blogs found containing <b>$query</b></p>";
        }
    } else{
        echo "ERROR: Could not execute $sql. " . mysqli_error($connection);
    }

    // search all of a user's friends (in all of their friendCircles)
    // could probably user their everyone friendCircle but should be same
    $sql = "select * from users as u
            inner join friendcircle_users as fcu on fcu.userId = u.userId
            inner join friendcircles as fc on fc.circleId = fcu.circleId
            where fc.userId = $userId and concat(fName, ' ', lName) like '%".$query."%'";


    if($result = mysqli_query($connection, $sql)){
        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_array($result)){
                // TODO: INSERT CORRECT URL FOR USER'S PROFILE
                echo "<a href=".$row['username']."><p>" . $row['fName'] . " ". $row['lName'] ."</p></a>";
            }
            // Close result set
            mysqli_free_result($result);
        } else{
            echo "<p>No friends found for <b>$query</b></p>";
        }
    } else{
        echo "ERROR: Could not execute $sql. " . mysqli_error($connection);
    }
}

// close connection
mysqli_close($connection);
?>
