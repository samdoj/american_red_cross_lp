<?php
// --- begin template code ----------------------------------
// buffer larger content areas like the main page content
ob_start();
$pagetitle = "Apria Dashboard";
// --- end template code ------------------------------------

include('_session.php');
?>


<?php
// assign all page specific variables
$pagemaincontent = ob_get_contents();
ob_end_clean();

// apply the template
include("_template.php");
?>