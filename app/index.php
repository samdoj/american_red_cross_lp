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

        $sqlSaveSubmission = "INSERT INTO apps_biomed_careers (first_name, last_name, email, phone, over_21, location, driver_experience, salary, type_employment, driver_license, cdl, hazMat_endorsed, hazMat_experience, resume, utm_source, submitted, submitted_ip) VALUES ('$txtFirstName','$txtLastName', '$txtEmail', '$txtPhone', '$over21', '$location', '$driverExperience', '$txtSalary', '$typeEmployment', '$driverLicense', '$cdl', '$hazMatEndorsement', '$hazMatExperience', '$strClientAttachment', '$strUtmSource', '$submitDateTime', '$submittedIP')";

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
                        . "From: American Red Cross BioMed Careers <noreply@redcrossbiomedcareers.org>";

            $strClientSubject = "Interest in American Red Cross BioMed Careers";

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
                    $strClientFldMerge .= "<p><strong>Resume:</strong> http://redcrossbiomedcareers.org/uploads/resumes/" . $strClientAttachment . "</p>";
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
<title>American Red Cross BioMed Careers</title>
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
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/bootstrap-select.css">
<link rel="stylesheet" href="css/jasny-bootstrap.min.css">
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
<!-- end google analytics code -->
</head>
<body>
    <header id="header" class="header closed-nav">
        <div class="row">
            <img class="logo" id="header-logo" src="media/img/american_red_cross_logo.svg" alt="American Red Cross Logo">
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
        </div><!-- END .row -->
    </header><!-- END #header -->

    <!--==========================================
    =                 HERO HOME                  =
    ===========================================-->
    <section id="hero-home">
        <div class="row">
            <div id="hero-content" class="hero">
                <h1>Countless opportunities.<br>One life-saving mission.<br>Join us.</h1>
            </div><!-- END .hero-content -->
        </div><!-- END .row -->
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
            <div id="form-1-container" class="col-6 bleed-x">
                <div class="form-intro" id="apply1">
                    <h5>Join our team</h5>
                </div><!-- END #apply-1 -->
                <?php include('form.inc'); ?>
            </div><!-- END #form-1-container -->
        </div><!-- END .row -->
    </section><!-- END #welcome -->
    <!--===========  End of WELCOME  ==========-->

    <!--==========================================
    =               WHO WE'RE HIRING             =
    ===========================================-->
    <section class="container" id="hiring">
        <div class="row">
            <h2>Who We're <strong>Hiring</strong></h2>
            <div class="bleed-x col-12">
                <div id="accordion-wrap">
                    <div class="expandable-panel" id="cp-1">
                        <div class="expandable-panel-heading">
                            <h6>Phlebotomists<span class="icon-close-open"></span></h6>
                        </div>
                        <div class="expandable-panel-content">
                            <img src="http://placehold.it/1140x720" alt="">
                            <h6>Want to channel your customer-service skills into a dynamic job opportunity? Consider a phlebotomy career at the American Red Cross. (We train!)</h6>
                            <div class="description">
                                <p>As the front line of our blood collection services, you will be the face of the American Red Cross to our blood donors, making a personal connection with them and ensuring they have a top-notch donation experience. We’re looking for compassionate people who can connect with others, pay careful attention to details and support their team while working together to fulfill our mission. This role requires a highly flexible schedule and comes with competitive pay and benefits. No experience is necessary. We’ll train and support you as you learn your new skills, with our full-time, intensive phlebotomy training program. All costs are covered and you will be paid throughout the program!</p>
                            </div>
                            <div class="requirements">
                                <h5>Requirements</h5>
                                <ul>
                                    <li>High school diploma or equivalent</li>
                                    <li>A current valid driver's license and good driving record</li>
                                    <li>Some prior medical experience preferred but not required</li>
                                </ul>
                                <a href="#form-1-container">Contact Us</a>
                            </div>
                        </div>
                    </div>

                    <div class="expandable-panel" id="cp-2">
                        <div class="expandable-panel-heading">
                            <h6>Nurses<span class="icon-close-open"></span></h6>
                        </div>
                        <div class="expandable-panel-content">
                            <img src="http://placehold.it/1140x720" alt="">
                            <h6>Are you a mission-oriented nurse who likes to travel and meet new people? You will thrive at the American Red Cross.</h6>
                            <div class="description">
                                <p>We’re looking for both charge and apheresis nurses to accompany us on blood drives throughout the community. In this role, you’ll focus on customer service and compassion, working and making connections with healthy, happy blood donors. As a charge nurse you will lead a team, draw blood and, on occasion, supervise blood drives. As an apheresis nurse you will perform clinical apheresis procedures, along with other tasks, ensuring safe and appropriate care of patients. We work hard when we’re on the road and need a nurse who can accommodate a highly flexible schedule. You should also be comfortable performing venipuncture. You will need an RN (or LPN, depending on state requirements) license in the state prior to starting work.</p>
                            </div>
                            <div class="requirements">
                                <h5>Requirements</h5>
                                <ul>
                                    <li>Registered nurse license (LPN may be acceptable in some states)</li>
                                    <li>A current valid driver's license and good driving record</li>
                                </ul>
                                <a href="#form-1-container">Contact Us</a>
                            </div>
                        </div>
                    </div>

                    <div class="expandable-panel" id="cp-3">
                        <div class="expandable-panel-heading">
                            <h6>Phlebotomist/Drivers<span class="icon-close-open"></span></h6>
                        </div>
                        <div class="expandable-panel-content">
                            <img src="http://placehold.it/1140x720" alt="">
                            <h6>A unique opening for qualified truck drivers who want to transition into a new career making a difference in people’s lives.</h6>
                            <div class="description">
                                <p>An American Red Cross driving career gives you a rewarding shift from day-to-day deliveries to helping save lives in your community. Say goodbye to boredom, because every day on the job is different. As part of our team, you’ll be transporting, setting up and breaking down blood collection equipment at work sites across the community, interacting with our donors and ensuring they have a top notch experience. In some locations, you’ll also work as a phlebotomist, but don’t worry – no experience is necessary! We’ll train and support you as you learn your new skills, through our full time, intensive phlebotomy training program. All costs are covered and you will be paid throughout the program! To be successful in this position you’ll need a flexible schedule to work variable shifts, and have experience driving large trucks.</p>
                            </div>
                            <div class="requirements">
                                <h5>Requirements</h5>
                                <ul>
                                    <li>High school diploma or equivalent</li>
                                    <li>A current valid driver's license and good driving record</li>
                                    <li>A valid CDL (Class A or B) may be required in some locations</li>
                                    <li>Ability to lift, push and pull up to 75lbs</li>
                                    <li>DOT certification may be required in some locations</li>
                                </ul>
                                <a href="#form-1-container">Contact Us</a>
                            </div>
                        </div>
                    </div>

                    <div class="expandable-panel" id="cp-4">
                        <div class="expandable-panel-heading">
                            <h6>Medical Technologists<span class="icon-close-open"></span></h6>
                        </div>
                        <div class="expandable-panel-content">
                            <img src="http://placehold.it/1140x720" alt="">
                            <h6>Use your passion for blood banking to support our life-saving mission.</h6>
                            <div class="description">
                                <p>Before our blood products go to patients in need, our immunohematology reference labs perform critical testing for safety. In this role, you will conduct basic and advanced donor and patient tests to resolve compatibility issues, and consult with hospitals and transfusion services—all while adhering to standard operating procedures and regulatory requirements.</p>
                            </div>
                            <div class="requirements">
                                <h5>Requirements</h5>
                                <p><strong>Medical Technologist I</strong></p>
                                <ul>
                                    <li>MT (ASCP) and/or BB (ASCP) certification, or equivalent; plus Bachelor’s degree with major in biological science or chemistry plus 3 years blood banking experience<br>
                                    - or -<br>
                                    MLT (ASCP) certification plus 5 years laboratory experience
                                    </li>
                                </ul>
                                <p><strong>Medical Technologist II</strong></p>
                                <ul>
                                    <li>MT (ASCP) and/or BB (ASCP) certification, or equivalent; plus Bachelor's degree with major in biological science or chemistry plus 6 years blood banking experience<br>
                                    - or -<br>
                                    MLT (ASCP) certification plus 4 years laboratory experience
                                    </li>
                                </ul>
                                <p><strong>Medical Technologist III</strong></p>
                                <ul>
                                    <li>MT (ASCP) and/or BB (ASCP) certification, or equivalent; plus Bachelor’s degree with major in biological science or chemistry plus blood banking experience<li>
                                </ul>
                                <p><strong>All</strong></p>
                                <ul>
                                    <li>State license where applicable<li>
                                </ul>
                                <a href="#form-1-container">Contact Us</a>
                            </div>
                        </div>
                    </div>

                    <div class="expandable-panel" id="cp-5">
                        <div class="expandable-panel-heading">
                            <h6>Sales Professionals<span class="icon-close-open"></span></h6>
                        </div>
                        <div class="expandable-panel-content">
                            <img src="http://placehold.it/1140x720" alt="">
                            <h6>Bring your business-to-business sales experience to a philanthropic venture!</h6>
                            <div class="description">
                                <p>We’re looking for sales and marketing professionals who enjoy working with the public to set up, coordinate and promote blood drives with local businesses and civic organizations in their communities. We incentivize all hard work with bonuses and a supportive, growth-oriented atmosphere. All of our work centers around donors and volunteers, so great people skills, compassion and communication are priorities. Here, you’ll play a direct role in our life-saving mission, ensuring that others continue to receive the gift of life. Successful account managers should also be able to close deals and have a highly flexible schedule to accommodate the needs of our blood donors.</p>
                            </div>
                            <div class="requirements">
                                <h5>Requirements</h5>
                                <ul>
                                    <li>Bachelor's degree in marketing, sales, or communications OR equivalent combination of related education and experience</li>
                                    <li>One year (minimum) related experience</li>
                                    <li>A current, valid driver's license and good driving record</li>
                                </ul>
                                <a href="#form-1-container">Contact Us</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="tabs">
                    <div class="container">

                        <ul class="tabs">
                            <li class="tab-link current" data-tab="tab-1">Tab One</li>
                            <li class="tab-link" data-tab="tab-2">Tab Two</li>
                            <li class="tab-link" data-tab="tab-3">Tab Three</li>
                            <li class="tab-link" data-tab="tab-4">Tab Four</li>
                            <li class="tab-link" data-tab="tab-4">Tab Five</li>
                        </ul>

                        <div id="tab-1" class="tab-content current">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                        </div>
                        <div id="tab-2" class="tab-content">
                             Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                        </div>
                        <div id="tab-3" class="tab-content">
                            Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
                        </div>
                        <div id="tab-4" class="tab-content">
                            Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                        </div>
                        <div id="tab-5" class="tab-content">
                            Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                        </div>
                    </div>
                </div><!-- container -->
            </div>
        </div>
    </section>
    <!--======= End of WHO WE'RE HIRING =======-->

    <!--==========================================
    =                  BENEFITS                  =
    ===========================================-->
    <section id="benefits">
        <div class="row">
            <h2>Benefits with <strong>you</strong> in mind</h2>
            <p>As a mission-based organization, we believe our team needs great support to do great work. Our comprehensive benefits help you in balancing home and work, retirement, getting healthy and more. With our resources and perks, you have amazing possibilities at the American Red Cross to advance and learn.</p>
            <div class="col-12">
                <ul class="icon-wrap">
                    <li class="icon">
                        <img src="media/img/icon1.svg" alt="icon">
                        <p>Medical</p>
                    </li>
                    <li class="icon">
                        <img src="media/img/icon2.svg" alt="icon">
                        <p>Prescription Drugs</p>
                    </li>
                    <li class="icon">
                        <img src="media/img/icon3.svg" alt="icon">
                        <p>Dental</p>
                    </li>
                    <li class="icon">
                        <img src="media/img/icon4.svg" alt="icon">
                        <p>Vision</p>
                    </li>
                    <li class="icon">
                        <img src="media/img/icon5.svg" alt="icon">
                        <p>Health Savings Account</p>
                    </li>
                    <li class="icon">
                        <img src="media/img/icon6.svg" alt="icon">
                        <p>Wellness Incentive Program</p>
                    </li>
                    <li class="icon">
                        <img src="media/img/icon7.svg" alt="icon">
                        <p>Preventative Care</p>
                    </li>
                    <li class="icon">
                        <img src="media/img/icon8.svg" alt="icon">
                        <p>Financial Benefits</p>
                    </li>
                    <li class="icon">
                        <img src="media/img/icon9.svg" alt="icon">
                        <p>Savings Plan 401(k) and Match</p>
                    </li>
                    <li class="icon">
                        <img src="media/img/icon10.svg" alt="icon">
                        <p>Supplemental Hospital Indemnity Plan</p>
                    </li>
                    <li class="icon">
                        <img src="media/img/icon11.svg" alt="icon">
                        <p>Paid Time Off</p>
                    </li>
                    <li class="icon">
                        <img src="media/img/icon12.svg" alt="icon">
                        <p>Leave of Absence</p>
                    </li>
                    <li class="icon">
                        <img src="media/img/icon13.svg" alt="icon">
                        <p>Personal Plans &amp; Discounts</p>
                    </li>
                    <li class="icon">
                        <img src="media/img/icon14.svg" alt="icon">
                        <p>National Recognition Program</p>
                    </li>
                    <li class="icon">
                        <img src="media/img/icon15.svg" alt="icon">
                        <p>Service Awards</p>
                    </li>
                    <li class="icon">
                        <img src="media/img/icon16.svg" alt="icon">
                        <p>Commuter Benefits</p>
                    </li>
                    <li class="icon">
                        <img src="media/img/icon17.svg" alt="icon">
                        <p>Employee Assistance Program</p>
                    </li>
                </ul><!-- END #icon-wrap -->
            </div><!-- END .content -->
        </div><!-- END .row -->
    </section><!-- END #benefits -->
    <!--==========  End of BENEFITS  ==========-->

    <!--==========================================
    =                   DIVERSITY                =
    ===========================================-->
    <section id="diversity">
        <div class="row">
            <h2>We believe in a <strong>diverse</strong> and <strong>inclusive</strong> environment</h2>
            <div class="col-6 bleed-x"></div>
            <div class="col-6">
                <p>Serving people in America and around the world requires a diverse group of employees ready to meet the challenge. Cultural sensitivity is key to how we operate with the public, and we want to ensure our employees reflect a rich diversity. Through external relationships and internal initiatives, we seek to achieve diversity in our workforce, partners and suppliers.</p>
                <p>We are also proud to offer several resource groups for our employees. African-American, LGBT, and Latino Team Member Resource Groups provide mentoring and give voice to concerns and opinions of these valuable team members.</p>
            </div><!-- END .col-6 -->
        </div><!-- END .row -->
    </section><!-- END #diversity -->
    <!--=========  End of DIVERSITY  ==========-->

    <!--==========================================
    =                 WHO WE ARE                 =
    ===========================================-->

    <section id="about">
        <div class="row">
            <h2>Who we are is <strong>more</strong> than just blood products</h2>
            <div class="col-6">
                <p>As one of the nation’s oldest and most respected humanitarian organizations, the American Red Cross has been at the forefront of helping save lives since its founding in 1881. We strive to provide disaster relief in America and abroad. We support our active-duty military, veterans and their families. We collect, process and distribute about 40 percent of the nation’s blood supply. Health and safety education and training are also part of our goal.</p>
                <p>We are a strong organization of passionate supporters, volunteers and employees dedicated to helping others. Every year, we collect over 5 million units of blood; respond to 66,000 disasters; provide more than 367,000 services to active-duty military, veterans and their families; and more.</p>
            </div><!-- END .col-6 -->
            <div class="col-6">
                <p>Watch this short video to learn more about the life-saving impact our employees have every day.</p>
                <div class="video-container bleed-x">
                    <iframe width="560" height="315" src="https://www.youtube.com/embed/kYLaY2wI3lg" frameborder="0" allowfullscreen="allowfullscreen"></iframe>
                </div><!-- END .video-container -->
            </div><!-- END .col-6 -->
        </div><!-- END .row -->
    </section><!-- END #about -->
    <!--=========  End of WHO WE ARE  =========-->

    <!--==========================================
    =                   FOOTER                   =
    ===========================================-->
    <footer id="footer">
        <div class="row">
            <div class="wrap">
                <p>The American Red Cross is an Equal Opportunity/Affirmative Action employer. All qualified applicants will receive consideration for employment without regard to sex, gender identity, sexual orientation, race, color, religion, national origin, disability, protected veteran status, age, or any other characteristic protected by law.</p>
                <!-- <a href="http://bayardad.com/careers/privacy-policy/" target="_blank">Privacy Policy</a> -->
            </div>
        </div>
    </footer><!-- END #footer -->
    <!--===========  End of FOOTER  ===========-->

    <!-- begin tracking scripts -->
    <div style="display:none;">
        <!-- Google Code for Remarketing Tag -->
        <!-- ------------------------------------------------
        Remarketing tags may not be associated with personally identifiable information or placed on pages related to sensitive categories. See more information and instructions on how to setup the tag on: http://google.com/ads/remarketingsetup
        -------------------------------------------------- -->


        <!-- begin appcast/appfeeder conversion code -->
        <!-- JOB VIEW -->

        <!-- end appcast/appfeeder conversion code -->
    </div><!-- end tracking scripts -->

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-select.min.js"></script>
    <script src="js/jasny-bootstrap.min.js"></script>
    <script src="js/app.min.js"></script>
<!--     // <script src="js/scrolltop_accordion.js"></script> -->
    <script src="js/accordion.js"></script>
</body>
</html>