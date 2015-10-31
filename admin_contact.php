<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
require_once("models/header.php");

// Gets user_name or display_name from $ID02
// Set to send to var
function get_user_name_2($ID02){
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("SELECT 
		user_name, display_name
		FROM ".$db_table_prefix."users WHERE id=?");
	$stmt->bind_param("i", $ID02);
	$stmt->execute();
	$stmt->bind_result($print_user_name, $print_user_display_name);
	$stmt->fetch();
	$stmt->close();
	// Displays users user_name if display_name is not set
	if(!empty($print_user_display_name)){
		return $print_user_display_name;
	}else{
		return $print_user_name;
	}
	unset($print_user_display_name, $print_user_name);
}

echo "
<body>
<div id='wrapper'>
<div id='top'><div id='logo'></div></div>
<div id='content'>
<h1>UserCake</h1>
<h2>Contact Us</h2>
<div id='left-nav'>";

include("left-nav.php");

echo "
</div>
<div id='main'>
";

// Make sure a user is logged in
if(isUserLoggedIn()){

	// Make sure user is an admin
	if ($loggedInUser->checkPermission(array(2))){
		echo "<div align='center'>";
			// Display page title information
			echo "Contact Requests with newest first.";
			// Get information from database
			global $mysqli, $db_table_prefix;
			$query = "SELECT * FROM ".$db_table_prefix."contact ORDER BY `id` DESC";
			$result_topics = $mysqli->query($query);
			if($result_topics->num_rows > 0){
				while ($row_con = $result_topics->fetch_assoc()) {
					$user_id = $row_con['user_id'];
					$con_title = $row_con['con_title'];
					$con_content = $row_con['con_content'];
					$timestamp = $row_con['timestamp'];
					// Format data output
					$con_title = stripslashes($con_title);
					$con_content = stripslashes(nl2br($con_content));
					// Get user's name based on their id.
					$user_name = get_user_name_2($user_id);
					echo "<table width='80%'><tr><td width='100px' colspan='2'>";
						echo "By <b>$user_name</b> ($user_id) @ $timestamp";
					echo "</td></tr><tr><td>";
						echo "<strong>Title</strong>";
					echo "</td><td>";
						echo "$con_title";
					echo "</td></tr><tr><td width='100px'>";
						echo "<strong>Message</strong>";
					echo "</td><td>";
						echo "$con_content";
					echo "</td></tr></table>";
					echo "<br><br>";
				}
			}else{
				// Display message if there is no data in database
				echo "No Results";
			}
		echo "</div>";
	}else{
		echo "Admins Only.";
	}

// Display message asking visitor to log in.
}else{
	echo "Please Login to view this page.";
}
	
echo "
</div>
<div id='bottom'></div>
</div>
</body>
</html>";

?>