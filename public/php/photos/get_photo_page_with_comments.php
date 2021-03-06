<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="/css/default.css">
    <link rel="stylesheet" type="text/css" href="/css/bulma.css">
    <script type="text/javascript" src="/js/parseURL.js"></script>
    <script type="text/javascript" src="/js/editCaption.js"></script>
    <script type="text/javascript" src="/js/submitCaption.js"></script>
    <script type="text/javascript" src="/js/getCaption.js"></script>
    <script type="text/javascript" src="/js/submitPhotoComment.js"></script>
    <script type="text/javascript" src="/js/getPhotoComments.js"></script>
  </head>
  <body>
    <div class="single-photo-container">
<?php
// For use with AJAX request.

// REQUIRE THE DATABASE FUNCTIONS
require_once(realpath(dirname(__FILE__)) . "../../../../resources/db/db_connect.php");
require_once(realpath(dirname(__FILE__)) . "../../../../resources/db/db_query.php");
require_once(realpath(dirname(__FILE__)) . "../../../../resources/db/db_quote.php");
require_once("$_SERVER[DOCUMENT_ROOT]/php/home/header.php");

/*
 * This file is called from the album browse page and will load the clicked photo to the screen dimensions, and include comments below.
 */
function get_photo_page_with_comments($username, $albumId, $photoName) {
    $connection = db_connect(); // Try and connect to the database

    // If connection was not successful, handle the error
    if ($connection === false) {
        // Handle error
    }

    $albumId = db_quote($albumId);
    $photoName= db_quote($photoName);
    $albumId = (int) substr($albumId, 1, strlen($albumId) -2); // Get rid of quotation marks.

    // build query - by default it selects just one.
    $query = "SELECT * FROM photos WHERE albumId = $albumId AND filename = $photoName LIMIT 1";

    // Execute the query
    $qry_result = db_query($query);

    if ($qry_result === false) {
        // No such album.
        echo mysqli_error(db_connect());
    }

    // Get the photo caption
    while($row = $qry_result->fetch_assoc()){
        // scan the directory given
        $caption = $row['caption'];
    }

    $photoName= substr($photoName, 1, strlen($photoName) - 2); // Get rid of quotation marks e.g. from '2' to 2.

    $new_query = "SELECT * FROM albums WHERE albumId = $albumId";
    $new_qry_result = db_query($new_query);

    while ($row= $new_qry_result->fetch_assoc()) {
        $userId = $row['userId'];
        $photo_file = "../../../../album_content/$userId/$albumId/$photoName";

        echo "<div class=\"container\">";

        echo "<figure class=\"image\"><a href=\"../../albums/$albumId\"><img class=\"single-photo-container\" src=\"$photo_file\" alt=\"$caption\"></a></figure><br><div class=\"caption-container\"><h5 id=\"currentCaption\" class=\"title is-5\">$caption</h5></div><br>";
        echo "<div class=\"level\"><div class=\"level-left\">";
        echo "<div class=\"photo-deletion level-item\"><a class=\"button is-info\" onclick=\"editCaption($albumId, '$photoName')\">Edit caption</a></div></div><div class=\"level-right\"><a class=\"button is-danger is-outlined level-item\" href=\"/$username/albums/$albumId/$photoName/delete/\">Delete photo</a></div></div>";
    }


    // Load the photo comments
    getPhotoComments($albumId, $photoName);
}

/**
 * This function returns a list of the photo comments, with a comment input box at the bottom.
 */
function getPhotoComments($albumId, $photoName) {

    $connection = db_connect();

    if ($connection === false) {
        // do something
    } 

    $albumId = db_quote($albumId);
    $photoName = db_quote($photoName);

    $albumId = (int) substr($albumId, 1, strlen($albumId) -2); // Get rid of quotation marks.
    $photoName = substr($photoName, 1, strlen($photoName) -2); // Get rid of quotation marks.

    $query = "SELECT * FROM photos WHERE albumId = $albumId AND filename = '$photoName' LIMIT 1";

    $queryResult = db_query($query);

    if ($queryResult === false) {
        mysqli_error(db_connect());
    } else {
        $row = mysqli_fetch_row($queryResult);
        $photoId = $row[0];
        $newQuery = "SELECT u.userId, u.username, c.message, c.updatedAt FROM comments AS c INNER JOIN users AS u ON u.userId = c.userId WHERE c.photoId = $photoId ORDER BY c.createdAt DESC";

        $newResult = db_query($newQuery);

        if ($newResult === false) {
            echo $newQuery;
            mysqli_error(db_connect());
        } else {
            echo "<div id=\"comments-list\">";
            while ($newRow = $newResult->fetch_assoc()) {
                $userId = $newRow['userId'];
                $message = $newRow['message'];
                $updatedAt = $newRow['updatedAt'];
                $userName = $newRow['username'];

                // echo stuff
                echo "<div id=\"comments-list\"class=\"box\"><article class=\"media\"><div class=\"media-left\"></div><div class=\"media-content\"><div class=\"content\"><p><strong>$userName</strong><small>$updatedAt</small><br>$message</p></div></div></article></div>";

            }

            echo "</div>"; // end comments-list div
            // finally, echo the input box 
            echo "<div class=\"comment-input-container\"><article class=\"media\"><figure class=\"media-left\"><p class=\"image is-32x32\"><img src=\"\"></p></figure><div class=\"media-content\"><p class=\"control\"><textarea id=\"commentInput\"class=\"textarea\" placeholder=\"Type any comments in here\"></textarea></p><nav class=\"level\"><div class=\"level-left\"><div class=\"level-item\"><a class=\"button is-primary\" onclick=\"submitPhotoComment($albumId, '$photoName')\">Post</a></div></div></nav></div></article></div>";

        }
    }
}
?>
  </div>
  </body>
</html>
