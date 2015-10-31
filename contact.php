<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
require_once("models/header.php");

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

	echo "Welcome to the Contact Us Form.  Please fill it out completely.";

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