<?php

require_once(realpath(dirname(__FILE__)) . "../../../../resources/db/db_connect.php");
require_once(realpath(dirname(__FILE__)) . "../../../../resources/db/db_query.php");
require_once(realpath(dirname(__FILE__)) . "../../../../resources/db/db_quote.php");
require_once("$_SERVER[DOCUMENT_ROOT]/php/friends/mutual.php");
require_once("$_SERVER[DOCUMENT_ROOT]/php/home/header.php");

    
    $friends = db_query("SELECT * FROM users WHERE userId IN (SELECT userId FROM friendcircle_users WHERE circleId=(SELECT circleId from friendCircles WHERE userId=".$_SESSION['userId']." AND name='everyone'))");
 
function displayAllResults($userId) {
	$connection = db_connect();

	$query2 = "SELECT * FROM users WHERE userId IN (SELECT userId FROM friendcircle_users WHERE circleId=(SELECT circleId from friendCircles WHERE userId=".$userId." AND name='everyone'))";

	$result = db_query($query2);

	if ($result === false) {
			mysqli_error(db_connect());
	}
	else if (mysqli_num_rows($result) === 0) {
			echo "Sorry you have no friends...lol.";
	}
	else {

		echo "<div class=\"container\">";
		echo "<br><h2 class=\"title is-2\">Friends List</h2><hr>";
		while ($user = $result->fetch_assoc()) {
			displaySearchResult($user);
		}
		echo "<div class=\"container\">";
	}
}

function displaySearchResult($user) {

	$image = "";
	$full_name = $user['fName'] .  " " . $user['lName'];
	$biography = $user['description'];
	$location = $user['city'];
	$userId = $user['userId'];
	$username = $user['username'];
	$tags = getTags($userId);
	$count = mutualFriends($userId);

	$search_result = "
			<article class=\"media\">
				<figure class=\"media-left\">
					<p class=\"image is-64x64\">
						<img src=\"http://bulma.io/images/placeholders/128x128.png\">
					</p>
				</figure>
				<div class=\"media-content\">
					<div class=\"content\">
						<p>
							<a href=\"/$username\"><strong>$full_name</strong></a><br><small>$location</small><br>
							$biography<br><a href=/mutual?id=$userId >You have <b> $count </b> mutual friends!</a><br>
						</p>
					</div>
				<div id=\"alltags\">
					$tags
				</div>
				</div>
				<div class=\"media-right\">
					<button class=\"button is-info\">Delete Friend</button>
				</div>
			</article>";

	echo $search_result;
}

function getTags($userId) {
	$usertags = db_query("SELECT * FROM tags INNER JOIN tag_users ON tags.tagId = tag_users.tagId WHERE userId = '$userId'");
	$tags = "";
	while ( $row = $usertags->fetch_assoc()){
		$name = $row['name'];
		$tags = $tags . "<span id=\"tag_$name\" class=\"tag is-medium is-light\"><a href=\"/tags/$name\">$name</a></span>" . "\r\n";
		// echo ("<span id=\"tag_$name\" class=\"tag is-medium is-light\"><a href=\"/tags/$name\">$name</a></span>");
	}
	return $tags;
}

displayAllResults($_SESSION['userId']);


?>