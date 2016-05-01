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
            <img class="logo" id="header-logo" src="http://placehold.it/120x80" alt="Apria Healthcare">
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
            <div id="form-1-container" class="col-6">
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
            <div class="col-12">
                <h2>Who We're Hiring</h2>
                <div id="accordion-wrap">
                    <div class="expandable-panel" id="cp-1">
                        <div class="expandable-panel-heading">
                            <h2>Phlebotomists<span class="icon-close-open"></span></h2>
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
                                <div class="btn-sml"><a href="#">Contact Us</a></div>
                            </div>
                        </div>
                    </div>

                    <div class="expandable-panel" id="cp-2">
                        <div class="expandable-panel-heading">
                            <h2>Content heading 2<span class="icon-close-open"></span></h2>
                        </div>
                        <div class="expandable-panel-content">
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Qui debitis eius nobis rerum blanditiis consectetur, doloribus ea veniam alias quisquam tempora voluptas omnis dignissimos minus cum illo doloremque assumenda. Voluptatibus.</p>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Est labore, deserunt repudiandae ullam quibusdam praesentium excepturi, quia nemo dolores dignissimos corporis quo, doloribus nihil natus iusto debitis reiciendis sapiente alias!</p>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Est labore, deserunt repudiandae ullam quibusdam praesentium excepturi, quia nemo dolores dignissimos corporis quo, doloribus nihil natus iusto debitis reiciendis sapiente alias!</p>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Qui debitis eius nobis rerum blanditiis consectetur, doloribus ea veniam alias quisquam tempora voluptas omnis dignissimos minus cum illo doloremque assumenda. Voluptatibus.</p>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Est labore, deserunt repudiandae ullam quibusdam praesentium excepturi, quia nemo dolores dignissimos corporis quo, doloribus nihil natus iusto debitis reiciendis sapiente alias!</p>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Est labore, deserunt repudiandae ullam quibusdam praesentium excepturi, quia nemo dolores dignissimos corporis quo, doloribus nihil natus iusto debitis reiciendis sapiente alias!</p>

                        </div>
                    </div>

                    <div class="expandable-panel" id="cp-3">
                        <div class="expandable-panel-heading">
                            <h2>Content heading 3<span class="icon-close-open"></span></h2>
                        </div>
                        <div class="expandable-panel-content">
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Qui debitis eius nobis rerum blanditiis consectetur, doloribus ea veniam alias quisquam tempora voluptas omnis dignissimos minus cum illo doloremque assumenda. Voluptatibus.</p>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Est labore, deserunt repudiandae ullam quibusdam praesentium excepturi, quia nemo dolores dignissimos corporis quo, doloribus nihil natus iusto debitis reiciendis sapiente alias!</p>
                        </div>
                    </div>
                    <div class="expandable-panel" id="cp-4">
                        <div class="expandable-panel-heading">
                            <h2>Content heading 4<span class="icon-close-open"></span></h2>
                        </div>
                        <div class="expandable-panel-content">
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Qui debitis eius nobis rerum blanditiis consectetur, doloribus ea veniam alias quisquam tempora voluptas omnis dignissimos minus cum illo doloremque assumenda. Voluptatibus.</p>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Est labore, deserunt repudiandae ullam quibusdam praesentium excepturi, quia nemo dolores dignissimos corporis quo, doloribus nihil natus iusto debitis reiciendis sapiente alias!</p>
                        </div>
                    </div>
                    <div class="expandable-panel" id="cp-5">
                        <div class="expandable-panel-heading">
                            <h2>Content heading 5<span class="icon-close-open"></span></h2>
                        </div>
                        <div class="expandable-panel-content">
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Qui debitis eius nobis rerum blanditiis consectetur, doloribus ea veniam alias quisquam tempora voluptas omnis dignissimos minus cum illo doloremque assumenda. Voluptatibus.</p>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Est labore, deserunt repudiandae ullam quibusdam praesentium excepturi, quia nemo dolores dignissimos corporis quo, doloribus nihil natus iusto debitis reiciendis sapiente alias!</p>
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
            <h2>Benefits with <strong>you</strong> in mind</h2>
            <p>As a mission-based organization, we believe our team needs great support to do great work. Our comprehensive benefits help you in balancing home and work, retirement, getting healthy and more. With our resources and perks, you have amazing possibilities at the American Red Cross to advance and learn.</p>
            <div class="content-full">
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
                        <img src="media/img/icon1.svg" alt="icon">
                        <p>Savings Plan 401(k) and Match</p>
                    </li>
                    <li class="icon">
                        <img src="media/img/icon2.svg" alt="icon">
                        <p>Supplemental Hospital Indemnity Plan</p>
                    </li>
                    <li class="icon">
                        <img src="media/img/icon3.svg" alt="icon">
                        <p>Paid Time Off</p>
                    </li>
                    <li class="icon">
                        <img src="media/img/icon4.svg" alt="icon">
                        <p>Leave of Absence</p>
                    </li>
                    <li class="icon">
                        <img src="media/img/icon5.svg" alt="icon">
                        <p>Personal Plans &amp; Discounts</p>
                    </li>
                    <li class="icon">
                        <img src="media/img/icon6.svg" alt="icon">
                        <p>National Recognition Program</p>
                    </li>
                    <li class="icon">
                        <img src="media/img/icon7.svg" alt="icon">
                        <p>Service Awards</p>
                    </li>
                    <li class="icon">
                        <img src="media/img/icon8.svg" alt="icon">
                        <p>Commuter Benefits</p>
                    </li>
                    <li class="icon">
                        <img src="media/img/icon8.svg" alt="icon">
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
            <div class="col-6"></div>
            <div class="col-6">
                <h2>We believe in a <strong>diverse</strong> and <strong>inclusive</strong> environment</h2>
                <p>Serving people in America and around the world requires a diverse group of employees ready to meet the challenge. Cultural sensitivity is key to how we operate with the public, and we want to ensure our employees reflect a rich diversity. Through external relationships and internal initiatives, we seek to achieve diversity in our workforce, partners and suppliers.</p>
                <p>We are also proud to offer several resource groups for our employees. African-American, LGBT, and Latino Team Member Resource Groups provide mentoring and give voice to concerns and opinions of these valuable team members.</p>
            </div><!-- END .content-narrow-center -->
        </div><!-- END .row -->
    </section><!-- END #diversity -->
    <!--=========  End of DIVERSITY  ==========-->

    <!--==========================================
    =                 WHO WE ARE                 =
    ===========================================-->

    <section id="about">
        <div class="row">
            <div class="content-narrow-center">
                <h2>Who we are is <strong>more</strong> than just blood products</h2>
                <p>As one of the nation’s oldest and most respected humanitarian organizations, the American Red Cross has been at the forefront of helping save lives since its founding in 1881. We strive to provide disaster relief in America and abroad. We support our active-duty military, veterans and their families. We collect, process and distribute about 40 percent of the nation’s blood supply. Health and safety education and training are also part of our goal.</p>
                <p>We are a strong organization of passionate supporters, volunteers and employees dedicated to helping others. Every year, we collect over 5 million units of blood; respond to 66,000 disasters; provide more than 367,000 services to active-duty military, veterans and their families; and more.</p>
                <p>Watch this short video to learn more about the life-saving impact our employees have every day.</p>
            </div><!-- END .content-narrow-center -->
            <div class="col-6">
                <div class="video-container">
                    <iframe width="560" height="315" src="https://www.youtube.com/embed/kYLaY2wI3lg" frameborder="0" allowfullscreen="allowfullscreen"></iframe>
                </div><!-- END .video-container -->
            </div><!-- END .content -->
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
        <!-- <script type='text/javascript' src='https://click.appcast.io/pixels/bayard1-1353.js?ent=8'></script> -->
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