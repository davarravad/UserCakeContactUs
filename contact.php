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
	// Check for action command if any
	if(isset($_POST['action'])){ $action = $_POST['action']; }else{ $action = ""; }
	if(isset($_GET['success'])){ $success = $_GET['success']; }else{ $success = "FALSE"; }

	// Check to see if user has successfully submitted a contact form
	if ($success == "true"){
		echo "<div align='center'>";
			echo "<h2><font color='green'>";
				// Display success message
				echo "Thank You for your Contact submission.";
			echo "</font></h2>";
		echo "</div>";
	}
	// Check to see if user is admin wanting to view all contact requests
	else if($action == "view_contact_requests"){
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
			}
	}
	// Check to see if user is submitting a contact form
	else if($action == "create_new"){
		// Gather information to send to database
		if(isset($_POST['con_title'])){ $con_title = $_POST['con_title']; }else{ $con_title = ""; }
		if(isset($_POST['con_content'])){ $con_content = $_POST['con_content']; }else{ $con_content = ""; }
		// Format the text box for database
		$con_title = htmlspecialchars(strip_tags(addslashes($con_title)));
		// Format the text area for database
		$con_content = htmlspecialchars(addslashes($con_content));
		// Ready the user's id
		$user_id = $loggedInUser->user_id;
		// Submit information to database
		global $mysqli, $db_table_prefix;
		$stmt = $mysqli->prepare("INSERT INTO ".$db_table_prefix."contact(user_id, con_title, con_content) VALUES (?, ?, ?)");
		$stmt->bind_param("iss", $user_id, $con_title, $con_content);
		$stmt->execute();
		$stmt->close();	
		// Redirect member back to contact page and show success message
		$redir_link_url = "contact.php?success=true";
		header("Location: $redir_link_url");
		exit;
	}else{
		// Display basic title of page
		echo "<div align='center'>";
			echo "Welcome to the Contact Us Form.  Please fill it out completely.";
		echo "</div>";
		
		// Display contact form
		echo "<div align='center'>";
			// Start the form
			echo "<form enctype=\"multipart/form-data\" action=\"\" method=\"POST\" onsubmit=\"submitmystat.disabled = true; return true;\" >";
				echo "<input type=\"hidden\" name=\"action\" value=\"create_new\" />";
				echo "<table width='80%'><tr><td width='100px'>";
					echo "Title";
				echo "</td><td>";
					echo "<input name=\"con_title\" type=\"text\" value=\"\" style='width:100%;'>";
				echo "</td></tr><tr><td>";
					echo "Message";
				echo "</td><td>";
					echo "<textarea style='width:100%;height:200px;' name='con_content' id='con_content'></textarea>";
				echo "</td></tr><tr><td colspan='2' align='center'>";
					echo "<input type=\"submit\" value=\"Submit Form\" name=\"submitcontactus\" class=\"\" onClick=\"this.value = 'Please Wait....'\" />";
				echo "</td></tr></table>";
			echo "</form>";
			// End of form
			// If admin is logged in show them a link to be able to view all current contact requests
			if ($loggedInUser->checkPermission(array(2))){
				echo "<form enctype=\"multipart/form-data\" action=\"\" method=\"POST\" onsubmit=\"submitmystat.disabled = true; return true;\" >";
					echo "<input type=\"hidden\" name=\"action\" value=\"view_contact_requests\" />";
					echo "<input type=\"submit\" value=\"Admin : View Recent Contact Requests\" name=\"viewrecentcontactus\" class=\"\" onClick=\"this.value = 'Please Wait....'\" />";
				echo "</form>";
			}
		echo "</div>";
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