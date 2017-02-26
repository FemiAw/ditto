<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="../css/default.css">
    <script type="text/javascript" src="../js/dropzone.js"></script>
    <script type="text/javascript" src="../js/parseURL.js"></script>
    <script type="text/javascript" src="../js/getAlbumPhotos.js"></script>
    <script type="text/javascript" src="../js/deleteAlbum.js"></script>
    <script type="text/javascript" >
      Dropzone.options.myAwesomeDropzone = {
        init: function() {
          this.on('complete', function(file) {
            console.log(file.xhr);
            var photos = getAlbumPhotos(parseURL(document.location.href, 1))
            // document.getElementById('ajaxResult').innerHTML = 
          });
        },
        url: '../php/photos/upload_photo.php',
        paramName: "file",
        maxFilesize: 2, // MB
        acceptedFiles: ".jpg, .jpeg, .png, .gif",
        sending: function(file, xhr, formData) {
          formData.append('userId', '1'); // TODO put session here
          formData.append('albumId', parseURL(document.location.href, 1)); // TODO put albumId from album object here - get the url link
        }
      }
    </script>
  </head>
  <body>
    <a href="../../../../albums"><p>Back to albums</p></a>
    <p>Result:</p>
    <div id="ajaxResult">
<?php
// REQUIRE THE DATABASE FUNCTIONS
require_once(realpath(dirname(__FILE__)) . "../../../../resources/db/db_connect.php");
require_once(realpath(dirname(__FILE__)) . "../../../../resources/db/db_query.php");
require_once(realpath(dirname(__FILE__)) . "../../../../resources/db/db_quote.php");

function non_ajax_get_album_photos() {
	$connection = db_connect(); // Try and connect to the database

	// If connection was not successful, handle the error
	if ($connection === false) {
		// Handle error
	}

	// Retrieve data from request and escape.
	// $userId = db_quote($_REQUEST['userId']); // userId for person trying to view data.
	$albumId = db_quote($_REQUEST['albumId']);

	// TODO check that userId is allowed to view the album

	// build query - by default it selects just one.
	$query = "SELECT * FROM albums WHERE albumId = $albumId";

	// Execute the query
	$qry_result = db_query($query);

	if ($qry_result === false) {
		// No such album.
		echo mysqli_error(db_connect());
	}

	$albumId = (int) substr($albumId, 1, strlen($albumId) - 2); // Get rid of quotation marks e.g. from '2' to 2.
	
	$first = true;
	while($row = $qry_result->fetch_assoc()){
		// scan the directory given
		if ($first === true) {
			echo $row['albumName'];
			$first = false;
		}	
		$userId = $row['userId'];
		$photo_files = scandir("../../../resources/album_content/$userId/$albumId");
	}

	$photo_files = array_diff($photo_files, array('.', '..')); // remove . and .. directories

	foreach($photo_files as $photoName) {
		$file = "../../album_content/$userId/$albumId/$photoName";
		echo "<div class=\"photo-thumbnail\"><a href=\"../../../../albums/$albumId/$photoName\"><img class=\"photo-thumbnail\" src=\"$file\" alt=\"Test\"></a></div>"; // TODO get captions
	}
}
non_ajax_get_album_photos();
?>
</div>

  <form action="../php/photos/upload_photo.php" class="dropzone" id="my-awesome-dropzone">
    <div class="fallback">
      <input name="file" type="file" multiple>
    </div>
  </form>
  <button type="button" onclick="deleteAlbum()">Delete album</button>
  </body>
</html>
