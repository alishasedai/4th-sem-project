<?php
session_start();       // Starts the session (needed before destroying it)
session_unset();       // Removes all session variables
session_destroy();     // Destroys the session itself
header("Location: login.php");  // Redirects user to login page
exit();                // Stops further script execution
?>