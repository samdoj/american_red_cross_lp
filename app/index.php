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
        <div class="row">
            <img class="logo" id="header-logo" src="media/img/apria_logo.svg" alt="Apria Healthcare">
            <div id="nav-btn">
                <div class="nav-btn-line"></div>
                <div class="nav-btn-line"></div>
                <div class="nav-btn-line"></div>
            </div><!-- END #nav-btn -->
            <nav id="main-nav">
                <ul class="nav-wrap">
                    <li class="nav-item">
                        <a class="nav-link" href="#hiring">We're Hiring</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#benefits">Benefits</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#day">Culture</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#mission">Who we are</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#form-1-container"><strong>Join us</strong></a>
                    </li>
                </ul><!-- END .nav-wrap -->
            </nav><!-- END .main-nav -->
        </div><!-- END .wrap -->
    </header><!-- END #header -->

    <!--==========================================
    =                 HERO HOME                  =
    ===========================================-->
    <section id="hero-home">
        <div class="row">
            <div id="hero-content" class="hero">
                <h1>Delivering life.</h1>
            </div><!-- END .hero-content -->
        </div><!-- END .wrap -->
    </section><!-- END #hero-home -->
    <!--=========  End of HERO HOME  ==========-->

    <!--==========================================
    =                   WELCOME                  =
    ===========================================-->
    <section id="welcome">
        <div class="row">
            <div class="col-6">
                <p>If you’re looking to gain new skills, help save lives and contribute to the mission of one of the nation’s most respected humanitarian organizations, consider a career in biomedical services at the American Red Cross. We have openings nationwide for phlebotomists, nurses, truck drivers, medical technologists and sales professionals. As a Red Cross employee, you’ll have opportunities to grow your career and be recognized and rewarded for your efforts. Most importantly, you’ll work alongside dedicated individuals like yourself who live the values of our organization every day. Join us for a career that will motivate you to be your very best.</p>
                <p>Our mission to prevent and alleviate human suffering in the face of emergencies inspires us daily. As the provider of nearly half of the nation’s blood supply, we collect, test and distribute over 5 million life-saving units of blood a year. Fulfilling our mission requires focused, energized employees, so we offer outstanding benefits that really help you, as you help others.</p>
            </div><!-- END .content-narrow-center -->
            <div id="form-1-container" class="col-6">
                <div class="form-intro" id="apply1">
                    <h5>Join our team</h5>
                </div><!-- END #apply-1 -->
                <?php include('form.inc'); ?>
            </div><!-- END #form-1-container -->
        </div><!-- END .wrap -->
    </section><!-- END #welcome -->
    <!--===========  End of WELCOME  ==========-->
    <!--==========================================
    =               WHO WE'RE HIRING             =
    ===========================================-->
    <section class="container">
        <div class="row">
            <div class="col-12">
                <h2>Who We're Hiring</h2>
                <p><strong>Note:</strong> The <strong>data-parent</strong> attribute makes sure that all collapsible elements under the specified parent will be closed when one of the collapsible item is shown.</p>
                <div class="panel-group" id="accordion">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">Phlebotomists</a>
                            </h4>
                            </div>
                            <div id="collapse1" class="panel-collapse collapse">
                                <div class="panel-body ">
                                    <figure>
                                        <img src="http://placehold.it/1200x420" alt="">
                                        <figcaption>Want to channel your customer-service skills into a dynamic job opportunity? Consider a phlebotomy career at the American Red Cross. (We train!)</figcaption>
                                    </figure>
                                <div>
                                    <p>As the front line of our blood collection services, you will be the face of the American Red Cross to our blood donors, making a personal connection with them and ensuring they have a top-notch donation experience. We’re looking for compassionate people who can connect with others, pay careful attention to details and support their team while working together to fulfill our mission. This role requires a highly flexible schedule and comes with competitive pay and benefits. No experience is necessary. We’ll train and support you as you learn your new skills, with our full-time, intensive phlebotomy training program. All costs are covered and you will be paid throughout the program!</p>
                                </div>
                                <div class="requirements">
                                    <h6>Requirements</h6>
                                    <ul>
                                        <li>High school diploma or equivalent</li>
                                        <li>A current valid driver's license and good driving record</li>
                                        <li>Some prior medical experience preferred but not required</li>
                                    </ul>
                                    <div class="btn-sml">
                                        <a href="#">Contact Us</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-group" id="accordion">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">Phlebotomists</a>
                            </h4>
                            </div>
                            <div id="collapse2" class="panel-collapse collapse">
                                <div class="panel-body ">
                                    <figure>
                                        <img src="http://placehold.it/1200x420" alt="">
                                        <figcaption>Want to channel your customer-service skills into a dynamic job opportunity? Consider a phlebotomy career at the American Red Cross. (We train!)</figcaption>
                                    </figure>
                                <div>
                                    <p>As the front line of our blood collection services, you will be the face of the American Red Cross to our blood donors, making a personal connection with them and ensuring they have a top-notch donation experience. We’re looking for compassionate people who can connect with others, pay careful attention to details and support their team while working together to fulfill our mission. This role requires a highly flexible schedule and comes with competitive pay and benefits. No experience is necessary. We’ll train and support you as you learn your new skills, with our full-time, intensive phlebotomy training program. All costs are covered and you will be paid throughout the program!</p>
                                </div>
                                <div class="requirements">
                                    <h6>Requirements</h6>
                                    <ul>
                                        <li>High school diploma or equivalent</li>
                                        <li>A current valid driver's license and good driving record</li>
                                        <li>Some prior medical experience preferred but not required</li>
                                    </ul>
                                    <div class="btn-sml">
                                        <a href="#">Contact Us</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-group" id="accordion">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse3">Phlebotomists</a>
                            </h4>
                            </div>
                            <div id="collapse3" class="panel-collapse collapse">
                                <div class="panel-body ">
                                    <figure>
                                        <img src="http://placehold.it/1200x420" alt="">
                                        <figcaption>Want to channel your customer-service skills into a dynamic job opportunity? Consider a phlebotomy career at the American Red Cross. (We train!)</figcaption>
                                    </figure>
                                <div>
                                    <p>As the front line of our blood collection services, you will be the face of the American Red Cross to our blood donors, making a personal connection with them and ensuring they have a top-notch donation experience. We’re looking for compassionate people who can connect with others, pay careful attention to details and support their team while working together to fulfill our mission. This role requires a highly flexible schedule and comes with competitive pay and benefits. No experience is necessary. We’ll train and support you as you learn your new skills, with our full-time, intensive phlebotomy training program. All costs are covered and you will be paid throughout the program!</p>
                                </div>
                                <div class="requirements">
                                    <h6>Requirements</h6>
                                    <ul>
                                        <li>High school diploma or equivalent</li>
                                        <li>A current valid driver's license and good driving record</li>
                                        <li>Some prior medical experience preferred but not required</li>
                                    </ul>
                                    <div class="btn-sml">
                                        <a href="#">Contact Us</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-group" id="accordion">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse4">Phlebotomists</a>
                            </h4>
                            </div>
                            <div id="collapse4" class="panel-collapse collapse">
                                <div class="panel-body ">
                                    <figure>
                                        <img src="http://placehold.it/1200x420" alt="">
                                        <figcaption>Want to channel your customer-service skills into a dynamic job opportunity? Consider a phlebotomy career at the American Red Cross. (We train!)</figcaption>
                                    </figure>
                                <div>
                                    <p>As the front line of our blood collection services, you will be the face of the American Red Cross to our blood donors, making a personal connection with them and ensuring they have a top-notch donation experience. We’re looking for compassionate people who can connect with others, pay careful attention to details and support their team while working together to fulfill our mission. This role requires a highly flexible schedule and comes with competitive pay and benefits. No experience is necessary. We’ll train and support you as you learn your new skills, with our full-time, intensive phlebotomy training program. All costs are covered and you will be paid throughout the program!</p>
                                </div>
                                <div class="requirements">
                                    <h6>Requirements</h6>
                                    <ul>
                                        <li>High school diploma or equivalent</li>
                                        <li>A current valid driver's license and good driving record</li>
                                        <li>Some prior medical experience preferred but not required</li>
                                    </ul>
                                    <div class="btn-sml">
                                        <a href="#">Contact Us</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-group" id="accordion">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse5">Phlebotomists</a>
                            </h4>
                            </div>
                            <div id="collapse5" class="panel-collapse collapse">
                                <div class="panel-body ">
                                    <figure>
                                        <img src="http://placehold.it/1200x420" alt="">
                                        <figcaption>Want to channel your customer-service skills into a dynamic job opportunity? Consider a phlebotomy career at the American Red Cross. (We train!)</figcaption>
                                    </figure>
                                <div>
                                    <p>As the front line of our blood collection services, you will be the face of the American Red Cross to our blood donors, making a personal connection with them and ensuring they have a top-notch donation experience. We’re looking for compassionate people who can connect with others, pay careful attention to details and support their team while working together to fulfill our mission. This role requires a highly flexible schedule and comes with competitive pay and benefits. No experience is necessary. We’ll train and support you as you learn your new skills, with our full-time, intensive phlebotomy training program. All costs are covered and you will be paid throughout the program!</p>
                                </div>
                                <div class="requirements">
                                    <h6>Requirements</h6>
                                    <ul>
                                        <li>High school diploma or equivalent</li>
                                        <li>A current valid driver's license and good driving record</li>
                                        <li>Some prior medical experience preferred but not required</li>
                                    </ul>
                                    <div class="btn-sml">
                                        <a href="#">Contact Us</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--======= End of WHO WE'RE HIRING =======-->

    <!--==========================================
    =                  BENEFITS                  =
    ===========================================-->
    <section id="benefits">
        <div class="row">
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
        <div class="row">
            <div class="content-narrow-center">
                <h2><strong>A day in the life</strong></h2>
                <p>It begins the night before with the driver leaving the vehicle in road-ready condition, gassed and with the right paperwork.  Each day starts the same: clock in, login in with the Nextel smart phone, check with the Logistics Center of Excellence (LCE) for route planning, load and secure equipment for delivery, complete the necessary Driver Daily Log paperwork, then do the pre-trip vehicle inspection checklist before hitting the road.  The hours may vary each day with potential overtime, so if you’re flexible and looking for a rewarding position with the opportunity for growth (DT to PST to CST and beyond) then this is the opportunity for you!  If you’re a student looking for part-time work, an ex-truck driver looking to be home each night or if you’re seeking to give back in a meaningful way, then you’re on the right path!</p>
            </div><!-- END .content-narrow-center -->
            <div class="content-narrow-center">
                <div class="video-container">
                    <iframe width="560" height="315" src="https://www.youtube.com/embed/kYLaY2wI3lg" frameborder="0" allowfullscreen="allowfullscreen"></iframe>
                </div><!-- END .video-container -->
            </div><!-- END .content -->
        </div><!-- END .wrap -->
    </section><!-- END #day -->
    <!--======  End of DAY IN THE LIFE  =======-->

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

    <!-- // <script src="js/jquery.min.js"></script> -->
    <!-- // <script src="js/bootstrap.min.js"></script> -->
    <!-- // <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script> -->
    <!-- // <script src="js/bootstrap-select.min.js"></script> -->
    <!-- // <script src="js/jasny-bootstrap.min.js"></script> -->
    
    <!-- // <script src="js/app.min.js"></script> -->
    <script src="js/scrolltop_accordion.js"></script>
</body>
</html>