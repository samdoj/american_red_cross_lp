<?php
// this php block always lives at the top of the page, before
// the DOCTYPE is declared

$hostname = '127.0.0.1';
$loginname = 'root';
$loginpassword = 'root';

$cn = mysql_connect($hostname, $loginname , $loginpassword) or die( mysql_error() );
mysql_select_db("bayard_php_test_db") or die( mysql_error() );

$thank_you_page = "thankyou.php";   // url of thank you page

if (isset($_POST['btnSubmit'])) {
    // use jquery to validate fields prior to submission
    // use php to determine if form was submitted by a human or a bot

    // define field used to filter bot submission
    $txtNewsletter = $_POST["txtNewsletter"];

    // define variable for the utm source parameter that may/not be part of the URL
    // if the parameter exists, assign the value to a variable
    $strUtmSource = "";
    if ($_GET['utm_source']) {
        $strUtmSource = $_GET['utm_source'];
    }

    // the newsletter form field is hidden, so users cannot enter data into the field
    // if field was not filled out, send information to database and send email(s)
    if (!$txtNewsletter) {

        // loop through all fields to automatically set the form variables
        // using the names of the form's input fields
        // this will not automatically generate variables for
        // the following types of fields:
        // select, input=checkbox, input=radio
        foreach ($_POST as $fldName => $fldValue) {
            ${ $fldName } = $_POST["$fldName"];
        } // end foreach ($_POST as $fldName => $fldValue)

        // get the user's ip address
        $submittedIP = $_SERVER['REMOTE_ADDR'];

        // set date/time to the Mountain time zone
        // the date/time will display in the format: M/D/YYYY H:MM:SS AM/PM
        date_default_timezone_set("America/Denver");
        $submitDateTime = date("m/d/Y g:i:s A");

        // begin resume upload ==============================================================
        // resume will be sent to uploads/resumes -- you will need to manually create this folder
        // it will not automatically create itself if it does not exist when the temporarily uploaded
        // file tries to move to that folder

        // if upload exists, check it
        if (basename($_FILES["file_upload"]["name"]) !== '') {
            $uploadOk = 1;
            $target_dir = "uploads/resumes/";
            $submitDate = date("m-d-Y");
            $target_file = $target_dir .    $submitDate . "-" . $txtFirstName . $txtLastName . "-" . basename($_FILES["file_upload"]["name"]);
            $target_file = str_replace(' ', '_', $target_file);
            $target_file = str_replace('_', '-', $target_file);
            $resumeFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            // get the byte size of the uploaded file
            if( $_FILES["file_upload"]["tmp_name"] ) {
                $check = filesize($_FILES["file_upload"]["tmp_name"]);
                if($check !== false) {
                    $uploadOk = 1;
                } else {
                    $uploadOk = 0;
                }
            }
            // if file already exists OR file size is larger than 5mb OR file type is not doc, docx, or pdf
            // the file is NOT okay
            if ((file_exists($target_file)) || ($_FILES["file_upload"]["size"] > 5000) || ($resumeFileType != "doc" && $resumeFileType != "docx" && $resumeFileType != "pdf")) {
                $uploadOk = 0;
            }
            // if the file is okay, upload the file to the designated directory
            if ($uploadOk = 1) {
                move_uploaded_file($_FILES["file_upload"]["tmp_name"], $target_file);
                $strClientAttachment = str_replace('uploads/resumes/', '', $target_file);
            }
        }
        // end resume upload ================================================================

        // create query to save submission to database
        $txtFirstName           = mysql_real_escape_string($txtFirstName);
        $txtLastName            = mysql_real_escape_string($txtLastName);
        $txtEmail               = mysql_real_escape_string($txtEmail);
        $txtPhone               = mysql_real_escape_string($txtPhone);
        $over21                 = mysql_real_escape_string($over21);
        $location               = mysql_real_escape_string($location);
        $driverExperience       = mysql_real_escape_string($driverExperience);
        $txtSalary              = mysql_real_escape_string($txtSalary);
        $driverLicense          = mysql_real_escape_string($driverLicense);
        $cdl                    = mysql_real_escape_string($cdl);
        $hazMatEndorsement         = mysql_real_escape_string($hazMatEndorsement);
        $hazMatExperience       = mysql_real_escape_string($hazMatExperience);

        // get values of multiple select fields, restructure the array values to a string of
        // comma separated values, and remove the trailing comma
        $typeEmployment   = implode(", ", $_POST['typeEmployment']);
        $typeEmployment   = rtrim($typeEmployment, ", ");

        $strClientAttachment    = mysql_real_escape_string($strClientAttachment);
        $strUtmSource           = mysql_real_escape_string($strUtmSource);

        $sqlSaveSubmission = "INSERT INTO apps_drivers (first_name, last_name, email, phone, over_21, location, driver_experience, salary, type_employment, driver_license, cdl, hazMat_endorsed, hazMat_experience, resume, utm_source, submitted, submitted_ip) VALUES ('$txtFirstName','$txtLastName', '$txtEmail', '$txtPhone', '$over21', '$location', '$driverExperience', '$txtSalary', '$typeEmployment', '$driverLicense', '$cdl', '$hazMatEndorsement', '$hazMatExperience', '$strClientAttachment', '$strUtmSource', '$submitDateTime', '$submittedIP')";

        // execute query
        $sqlSaveSubmissionResult = mysql_query($sqlSaveSubmission);

        // if the data was successfully saved to the client database,
        // send email to client
        if ($sqlSaveSubmissionResult) {

            // send email to the client -----------------------------------------------------------------
            // update the To, From and Subject line to whatever the client requests
            // $strClientTo = "email@yourcompany.com";
            $strClientTo = "joshl@bayardad.com";

            $strClientFrom = "MIME-Version: 1.0" . "\r\n"
                        . "Content-Type: text/html; charset=UTF-8" . "\r\n"
                        . "From: Apria Logistics Careers <noreply@apria.careers>";

            $strClientSubject = "Interest in Apria Logistics Careers";

            $strClientFldMerge = "<html><body><p>"
                        . "<strong>Name:</strong> " . $txtFirstName . "<br>"
                        . "<strong>Name:</strong> " . $txtLastName . "<br>"
                        . "<strong>Email:</strong> " . $txtEmail . "<br>"
                        . "<strong>Phone:</strong> " . $txtPhone . "<br>"
                        . "<strong>Are you 21 or older?</strong> " . $over21 . "<br>"
                        . "<strongLocation:</strong> " . $location . "<br>"
                        . "<strong>Years of (driver/delivery service) experience:</strong> " . $driverExperience . "<br>"
                        . "<strong>Desired salary range:</strong> " . $txtSalary . "<br>"
                        . "<strong>Desired type of employment:</strong> " . $typeEmployment . "<br>"
                        . "<strong>Valid driver's license:</strong> " . $driverLicense . "<br>"
                        . "<strong>Valid CDL:</strong> " . $cdl . "<br>"
                        . "<strong>Do you have a Hazardous Materials Endorsement?</strong> " . $hazMatEndorsement . "<br>"
                        . "<strong>Years of Hazardous Materials Endorsement:</strong> " . $hazMatExperience . "<br>";

                // include link to the uploaded resume if there is one
                if($strClientAttachment) {
                    // add the domain/folder prefix - $strClientAttachment just displays the filename
                    $strClientFldMerge .= "<p><strong>Resume:</strong> http://apria.careers/logistics/uploads/resumes/" . $strClientAttachment . "</p>";
                }
                // include the UTM source parameter value if there is one
                if($strUtmSource) {
                    $strClientFldMerge .= "<p><strong>UTM Source:</strong> " . $strUtmSource . "</p>";
                }
            $strClientFldMerge .= "</body></html>";

            $strClientMsg = wordwrap($strClientFldMerge, 70);

            mail($strClientTo,$strClientSubject,$strClientMsg,$strClientFrom);

        } // end if ($sqlSaveSubmissionResult)
        else {
            echo mysql_errno();
        }
    } // end if (!$txtNewsletter)

    // redirect user to the thank you page
    header('Location: ' . $thank_you_page . '');
} // end if (isset($_POST['btnSubmit']))
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Apria Careers - Driver Technicians</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Favicon -->
<link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="/apple-touch-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="/apple-touch-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="/apple-touch-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="/apple-touch-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon-180x180.png">
<link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
<link rel="icon" type="image/png" href="/favicon-194x194.png" sizes="194x194">
<link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96">
<link rel="icon" type="image/png" href="/android-chrome-192x192.png" sizes="192x192">
<link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
<link rel="manifest" href="/manifest.json">
<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#fdbb30">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="/mstile-144x144.png">
<meta name="theme-color" content="#026cb6">

<!-- Styles -->
<!-- <link rel="stylesheet" href="css/bootstrap.css"> -->
<!-- <link rel="stylesheet" href="css/bootstrap-select.css"> -->
<!-- <link rel="stylesheet" href="css/jasny-bootstrap.min.css"> -->
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="css/style.css">

<!-- Typekit -->
<script src="https://use.typekit.net/okk1ztn.js"></script>
<script>try{Typekit.load({ async: true });}catch(e){}</script>

<!-- Outdated Browser Warning Script -->
<script>
    var $buoop = {vs:{i:8,f:25,o:17,s:6},c:2, text:"It looks like you are using an outdated browser. To see our site correctly, please update your browser."};
    function $buo_f(){
     var e = document.createElement("script");
     e.src = "//browser-update.org/update.js";
     document.body.appendChild(e);
    };
    try {document.addEventListener("DOMContentLoaded", $buo_f,false)}
    catch(e){window.attachEvent("onload", $buo_f)}
</script>

<!-- begin google analytics code -->
<!-- <script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-69937701-2', 'auto');
ga('send', 'pageview');

</script> -->
<!-- end google analytics code -->
</head>
<body>
    <header id="header" class="header closed-nav">
        <div class="wrap">
            <img class="logo" id="header-logo" src="media/img/apria_logo.svg" alt="Apria Healthcare">
            <div id="nav-btn">
                <div class="nav-btn-line"></div>
                <div class="nav-btn-line"></div>
                <div class="nav-btn-line"></div>
            </div><!-- END #nav-btn -->
            <nav id="main-nav">
                <ul class="nav-wrap">
                    <li class="nav-item">
                        <a class="nav-link" href="#top-form-wrap"><strong>Apply now</strong></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#hiring">Job description</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#benefits">Benefits</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#day">Video</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#mission">Stories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">Who we are</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#awards">Awards</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="http://www.lifeatapria.com" target="_blank"><strong>lifeatapria</strong></a>
                    </li>
                </ul><!-- END .nav-wrap -->
            </nav><!-- END .main-nav -->
        </div><!-- END .wrap -->
    </header><!-- END #header -->

    <!--==========================================
    =                 HERO HOME                  =
    ===========================================-->
    <section id="hero-home">
        <div class="wrap">
            <div id="hero-content" class="hero">
                <h1>Delivering life.</h1>
            </div><!-- END .hero-content -->
        </div><!-- END .wrap -->
    </section><!-- END #hero-home -->
    <!--=========  End of HERO HOME  ==========-->

    <!--==========================================
    =                   HIRING                   =
    ===========================================-->
    <section id="hiring">
        <div class="wrap">
            <div class="content-2-3">
                <div class="intro-txt">
                    <h2>Apria is looking for Driver&nbsp;Technicians</h2>
                    <p>As an Apria Technician, you’ll deliver the necessary products and services on which patients rely such as Oxygen, Nebulizers, Negative Pressure Wound Therapy equipment, Non-Invasive Ventilators and much more.</p>
                    <p>Apria has a fleet of 2,000 vehicles and 1,400 drivers; and each day, there are 1,350 routes to serve our patients nationwide.  No two days are the same, but the average is about 16 stops per day.</p>
                </div><!-- END .intro-txt -->
                <h3>We offer Full time, Part-time and Per Diem Opportunities</h3>
                <div class="jobs">
                    <div class="content-1-2">
                        <h4>Positions available</h4>
                        <ul class="position-list">
                            <li class="position"><p><strong>Delivery Technician</strong> (DT)</p>
                                <ul class="requirements-list">
                                    <li class="requirement">Class C Driver’s License required only</li>
                                    <li class="requirement">No prior driver experience required</li>
                                </ul>
                            </li>
                            <li class="position"><p><strong>Patient Services Technician</strong> (PST)</p>
                                <ul class="requirements-list">
                                    <li class="requirement">Commercial Driver’s License</li>
                                    <li class="requirement">HazMat Endorsement</li>
                                    <li class="requirement">1 year experience as a CDL driver</li>
                                </ul>
                            </li>
                            <li class="position"><p><strong>Clinical Service Technician</strong> (CST)</p>
                                <ul class="requirements-list">
                                    <li class="requirement">Commercial Driver’s License</li>
                                    <li class="requirement">HazMat Endorsement</li>
                                    <li class="requirement">3 years experience as a CDL driver</li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="content-1-2"></div>
                </div><!-- END .jobs -->
            </div><!-- END .content-2-3 -->
        </div><!-- END .wrap -->
    </section><!-- END #hiring -->
    <!--===========  End of HIRING  ===========-->

    <!--==========================================
    =                   AWARDS                   =
    ===========================================-->
    <section id="awards">
        <div class="wrap">
            <h2>Did you <strong>know?</strong></h2>
            <div class="line"></div>
            <div class="content content-1-2">
                <h5>Apria ranks in the top 100 in the nation for commercial truck fleet size.</h5>
                <img src="media/img/icon_trophy.svg" alt="Apria ranks in the top 100 in the nation for commercial truck fleet size.">
            </div><!-- END .content-1-2 -->
            <div class="content content-1-2">
                <img src="media/img/img_top_500.jpg" alt="America's Top 500 Private Fleets">
            </div><!-- END .content-1-2 -->
        </div><!-- END .wrap -->
    </section><!-- END #awards -->
    <!--=========== End of AWARDS =============-->

    <!--==========================================
    =                   FORM 1                   =
    ===========================================-->
    <section class="container">
      <h2>Accordion Example</h2>
      <p><strong>Note:</strong> The <strong>data-parent</strong> attribute makes sure that all collapsible elements under the specified parent will be closed when one of the collapsible item is shown.</p>
      <div class="panel-group" id="accordion">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">Collapsible Group 1</a>
            </h4>
          </div>
          <div id="collapse1" class="panel-collapse collapse">
            <div class="panel-body">Lorem ipsum dolor sit amet, consectetur adipisicing elit,
            sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</div>
            <div class="panel-body">Lorem ipsum dolor sit amet, consectetur adipisicing elit,
            sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</div>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">Collapsible Group 2</a>
            </h4>
          </div>
          <div id="collapse2" class="panel-collapse collapse">
            <div class="panel-body">Lorem ipsum dolor sit amet, consectetur adipisicing elit,
            sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</div>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a data-toggle="collapse" data-parent="#accordion" href="#collapse3">Collapsible Group 3</a>
            </h4>
          </div>
          <div id="collapse3" class="panel-collapse collapse">
            <div class="panel-body">Lorem ipsum dolor sit amet, consectetur adipisicing elit,
            sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</div>
            <div class="panel-body">Lorem ipsum dolor sit amet, consectetur adipisicing elit,
            sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</div>
            <div class="panel-body">Lorem ipsum dolor sit amet, consectetur adipisicing elit,
            sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</div>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a data-toggle="collapse" data-parent="#accordion" href="#collapse4">Collapsible Group 4</a>
            </h4>
          </div>
          <div id="collapse4" class="panel-collapse collapse">
            <div class="panel-body">Lorem ipsum dolor sit amet, consectetur adipisicing elit,
            sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</div>
            <div class="panel-body">Lorem ipsum dolor sit amet, consectetur adipisicing elit,
            sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</div>
            <div class="panel-body">Lorem ipsum dolor sit amet, consectetur adipisicing elit,
            sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</div>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a data-toggle="collapse" data-parent="#accordion" href="#collapse5">Collapsible Group 5</a>
            </h4>
          </div>
          <div id="collapse5" class="panel-collapse collapse">
            <div class="panel-body">Lorem ipsum dolor sit amet, consectetur adipisicing elit,
            sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</div>
            <div class="panel-body">Lorem ipsum dolor sit amet, consectetur adipisicing elit,
            sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</div>
            <div class="panel-body">Lorem ipsum dolor sit amet, consectetur adipisicing elit,
            sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</div>
          </div>
        </div>
      </div> 
    </section>
    <!--========== End of POSITIONS ===========-->

    <!--==========================================
    =                   FORM 1                   =
    ===========================================-->
    <div class="wrap" id="top-form-wrap">
        <div id="form-1-container" class="content-1-3">
            <div class="form-intro" id="apply1">
                <h5>Join our team</h5>
                <p>Complete the short form below and someone from our recruiting team will get in touch with&nbsp;you.</p>
            </div><!-- END #apply-1 -->
            <?php include('form.inc'); ?>
        </div><!-- END #form-1-container -->
    </div><!-- END .wrap -->
    <!--=========== End of FORM 1 =============-->

    <!--==========================================
    =                  BENEFITS                  =
    ===========================================-->
    <section id="benefits">
        <div class="wrap">
            <h2><strong>Benefits</strong> with your well-being in mind</h2>
            <div class="line"></div>
            <div class="content-full">
                <ul class="icon-wrap">
                    <li class="icon">
                        <img src="media/img/icon1.svg" alt="icon">
                        <p>Bonus potential</p>
                    </li>
                    <li class="icon">
                        <img src="media/img/icon2.svg" alt="icon">
                        <p>Training/Cross training</p>
                    </li>
                    <li class="icon">
                        <img src="media/img/icon3.svg" alt="icon">
                        <p>Promote from within</p>
                    </li>
                    <li class="icon">
                        <img src="media/img/icon4.svg" alt="icon">
                        <p>Career Advancement</p>
                    </li>
                    <li class="icon">
                        <img src="media/img/icon5.svg" alt="icon">
                        <p>GPS provided</p>
                    </li>
                    <li class="icon">
                        <img src="media/img/icon6.svg" alt="icon">
                        <p>Shoe allowance</p>
                    </li>
                    <li class="icon">
                        <img src="media/img/icon7.svg" alt="icon">
                        <p>Paid uniforms</p>
                    </li>
                    <li class="icon">
                        <img src="media/img/icon8.svg" alt="icon">
                        <p>375+ branch network</p>
                    </li>
                </ul><!-- END #icon-wrap -->
            </div><!-- END .content -->
        </div><!-- END .wrap -->
    </section><!-- END #benefits -->
    <!--==========  End of BENEFITS  ==========-->

    <!--==========================================
    =                DAY IN THE LIFE             =
    ===========================================-->
    <section id="day">
        <div class="wrap">
            <div class="content-narrow-center">
                <h2><strong>A day in the life</strong></h2>
                <p>It begins the night before with the driver leaving the vehicle in road-ready condition, gassed and with the right paperwork.  Each day starts the same: clock in, login in with the Nextel smart phone, check with the Logistics Center of Excellence (LCE) for route planning, load and secure equipment for delivery, complete the necessary Driver Daily Log paperwork, then do the pre-trip vehicle inspection checklist before hitting the road.  The hours may vary each day with potential overtime, so if you’re flexible and looking for a rewarding position with the opportunity for growth (DT to PST to CST and beyond) then this is the opportunity for you!  If you’re a student looking for part-time work, an ex-truck driver looking to be home each night or if you’re seeking to give back in a meaningful way, then you’re on the right path!</p>
            </div><!-- END .content-narrow-center -->
            <div class="content-narrow-center">
                <div class="video-container">
                    <iframe width="560" height="315" src="https://www.youtube.com/embed/kYLaY2wI3lg" frameborder="0" allowfullscreen="allowfullscreen"></iframe>
                </div><!-- END .video-container -->
            </div><!-- END .content -->
            <div class="content-narrow-center">
                <h6>Interested? <em>Join our team.</em></h6>
                <div class="btn-sml">
                    <a href="#form-1-container">Get Started - Apply</a>
                </div>
            </div><!-- END .content-narrow-center -->
        </div><!-- END .wrap -->
    </section><!-- END #day -->
    <!--=======  End of DAY IN THE LIFE  =======-->

    <!--==========================================
    =               MISSION STATEMENT            =
    ===========================================-->
    <section id="mission">
        <div class="wrap">
            <div class="content-narrow-center">
                <h2>Apria's mission statement in action&hellip;</h2>
                <h4>Improving the quality of life for our patients at home.</h4>
                <p>IA much appreciated and grateful patient called recently to the Kingston, NY branch to express his gratitude to <strong>Michael, Patient Service Technician.</strong> Michael went to the patient's home to deliver his oxygen supply, rang the door several times, and didn’t get a response.</p>
                <p>He quickly called the patient by phone, who finally answered. Michael asked if he was OK, but the patient responded he was wobbly and disoriented due to his blood sugar being low. He indicated he had collapsed on his bed.</p>
                <p>Michael called 911 and the ambulance rushed the patient to the hospital.</p>
                <p>When the patient called the branch to share his thanks, he wanted everyone there to know that Michael really came through for him and is the reason he is breathing today.</p>
            </div><!-- END .content-narrow-center -->
            <div class="line"></div>
            <div class="content-narrow-center">
                <figure class="figure-lg">
                    <img src="media/img/img_patient_service_technician.jpg" alt="">
                    <figcaption><em>“It’s not just about <strong>serving people,</strong> it’s about <strong>being there</strong> for people.”</em></figcaption>
                </figure>
            </div><!-- END .content-narrow-center -->
        </div><!-- END .wrap -->
    </section><!-- END #mission -->
    <!--======  End of MISSION STATEMENT  =====-->

    <!--==========================================
    =                MARKET POSITION             =
    ===========================================-->
    <section id="market-position">
        <div class="wrap">
            <figure class="figure-sml">
                <div>
                    <img src="media/img/icon_ranking.svg" alt="We're number 1!">
                    <figcaption>Market position in Home Medical&nbsp;Equipment<br>
                        <em>(Managed Care contracts)</em>
                    </figcaption>
                </div>
            </figure>
        </div><!-- END .wrap -->
    </section><!-- END #market-position -->
    <!--======  End of MARKET POSITION  =======-->

    <!--==========================================
    =                  WHO WE ARE                =
    ===========================================-->
    <section id="about">
        <div class="wrap">
            <div class="content-1-2">
                <h2><strong>Who we are</strong></h2>
                <p>As the nation’s largest, most successful provider of home healthcare products and services, Apria helps 1.8 million patients to live healthier and feel better – every day. We own and operate more than 375 locations throughout the United States. We do all this with the help of our team; we support each other’s talent to help each other learn and grow. As a member of our team, you’ll have all of this support, too.</p>
                <h5>We’re at the forefront of respiratory care and other home healthcare product lines</h5>
                <ul>
                    <li><strong>#1 Market position</strong> in Sleep Apnea products and services</li>
                    <li><strong>#1 Market position</strong> in Home Respiratory products/services (Managed Care contracts)</li>
                    <li><strong>#1 Market position</strong> in Home Medical Equipment <em>(Managed Care contracts)</em></li>
                    <li><strong>#2 Market position</strong> in Negative Pressure Wound Therapy products/services</li>
                    <li><strong>Apria Healthcare has been continuously accredited</strong> for more than 25 years by The Joint Commission</li>
                </ul>
                <div class="contact" id="contact02">
                    <h6>Interested? <em>Join our team.</em></h6>
                    <div class="btn-sml">
                        <a href="#form-1-container">Get Started - Apply</a>
                    </div>
                </div>
            </div><!-- END .content-1-2 -->
            <div class="content-1-2"></div>
        </div><!-- END .wrap -->
    </section><!-- END #about -->
    <!--=========  End of WHO WE ARE  ==========-->

    <!--==========================================
    =                    FORM 2                  =
    ===========================================-->
    <section id="bottom-form">
        <div class="wrap">
            <div class="content-2-3-center">
                <div class="form-intro" id="form-intro-2">
                    <h2>Interested?</h2>
                    <div class="line"></div>
                    <h3>Join our team</h3>
                    <p>Complete the short form below and someone from our recruiting team will get in touch with&nbsp;you.</p>
                </div><!-- END #form-intro-2 -->
                <?php include('form.inc'); ?>
            </div><!-- END .content-2-3-center -->
        </div><!-- END .wrap -->
    </section><!-- END #mission -->
    <!--======  End of MISSION STATEMENT  =====-->

    <!--==========================================
    =                   FOOTER                   =
    ===========================================-->
    <footer id="footer">
        <a href="http://bayardad.com/careers/privacy-policy/" target="_blank">Privacy Policy</a>
    </footer><!-- END #footer -->
    <!--===========  End of FOOTER  ===========-->

    <!-- begin tracking scripts -->
    <div style="display:none;">
        <!-- Google Code for Remarketing Tag -->
        <!-- ------------------------------------------------
        Remarketing tags may not be associated with personally identifiable information or placed on pages related to sensitive categories. See more information and instructions on how to setup the tag on: http://google.com/ads/remarketingsetup
        -------------------------------------------------- -->


        <!-- begin appcast/appfeeder conversion code -->
        <!-- <script type='text/javascript' src='https://click.appcast.io/pixels/bayard1-1353.js?ent=8'></script> -->
        <!-- JOB VIEW -->

        <!-- end appcast/appfeeder conversion code -->
    </div><!-- end tracking scripts -->

    <script src="js/jquery.min.js"></script>
    <!-- // <script src="js/bootstrap.min.js"></script> -->
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script src="js/bootstrap-select.min.js"></script>
    <!-- // <script src="js/jasny-bootstrap.min.js"></script> -->
    
    <!-- // <script src="js/app.min.js"></script> -->
</body>
</html>