<?php
require_once("$_SERVER[DOCUMENT_ROOT]/php/friendCircles/friends-in-circle.php");
require_once("$_SERVER[DOCUMENT_ROOT]/php/home/header.php");
$currentCircle = $_SESSION['circleId'];
?>

<div class="container">
  <br><h2 class="title is-2">Circle Name</h2><hr>
  <div class="columns">
    <div class="column is-one-quarter">
      <h4 class="title is-4"><strong>Add member</strong></h4>
        <div class="content">
          Use this area to add your friends to your existing circles.
        </div>
        <p class="control">
          <span class="select">
            <select>
              <option>Friend</option>
              <option>Friend 1</option>
              <option>Friend 2</option>
              <option>Friend 3</option>
            </select>
          </span>
          <span class="select">
            <select>
              <option>Circle</option>
              <option>Friend 1</option>
            </select>
          </span>
        </p>
        <p class="control">
          <a class="button">Add</a><br>
        </p>
    </div>
    <div class="column is-1"></div>
    <div class="column">
      <?php displayCircleUsers($currentCircle); ?>
    </div>
  </div>
</div>



<?php

function displayCircleUsers($circleId) {
  $result = retrieve_friendcircle_friends($circleId);

  if ($result === false) {
      mysqli_error(db_connect());
  }
  else if (mysqli_num_rows($result) === 0) {
      echo "There are currently no users interested in this...lol.";
  }

  else {
    while ($user = $result->fetch_assoc()) {
      displaySearchResult($user, $circleId);
    }
    echo "<div class=\"container\">";
  }
}


function displaySearchResult($user, $circleId) {
	$image = "";
	$full_name = $user['fName'] .  " " . $user['lName'];
	$biography = $user['description'];
	$location = $user['city'];
	$userId = $user['userId'];
	$username = $user['username'];
	$tags = getTags($userId);

	$search_result = "
			<article id=\"cu_$userId\" class=\"media\">
      <div class=\"media-left\">
        <button class=\"delete\" onclick=\"deleteCircleUser($userId, $circleId);\"></button>
      </div>
				<figure class=\"media-left\">
					<p class=\"image is-64x64\">
						<img src=\"http://bulma.io/images/placeholders/128x128.png\">
					</p>
				</figure>
				<div class=\"media-content\">
					<div class=\"content\">
						<p>
							<a href=\"/$username\"><strong>$full_name</strong></a><br><small>$location</small><br>
							$biography
						</p>
					</div>
				<div id=\"content alltags\">$tags</div>
				</div>
				<div class=\"media-right\">
					<button class=\"delete\"></button>
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
	}
	return $tags;
}

?>
