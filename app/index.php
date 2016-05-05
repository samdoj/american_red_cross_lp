<?php
// this php block always lives at the top of the page, before
// the DOCTYPE is declared

$hostname = '127.0.0.1';
$loginname = 'root';
$loginpassword = 'root';

$cn = mysql_connect($hostname, $loginname , $loginpassword) or die( mysql_error() );
mysql_select_db("arc_biomed_database") or die( mysql_error() );

$thank_you_page = "thankyou.php";   // url of thank you page

if (isset($_POST['btnSubmit'])) {
	// use jquery to validate fields prior to submission
	// use php to determine if form was submitted by a human or a bot

	// define field used to filter bot submission
	$txtNewsletter = $_POST["txtNewsletter"];

	// define variables for the URL parameters that may/not be part of the URL
	// if the utm_campaign parameter exists, assign the value to a variable
	$strUtmCampaign = "";
	if ($_GET['utm_campaign']) {
		$strUtmCampaign = $_GET['utm_campaign'];
	}
	// if the utm_medium parameter exists, assign the value to a variable
	$strUtmMedium = "";
	if ($_GET['utm_medium']) {
		$strUtmMedium = $_GET['utm_medium'];
	}
	// if the utm_source parameter exists, assign the value to a variable
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

		// get form values and assign them to PHP variables
		$strLocation = $_POST["location"];
		$strPosition = $_POST["position"];

		// separate location into city and state
		$strLocationParts = explode(",", $strLocation);
		$strCity = $strLocationParts[0];
		$strState = $strLocationParts[1];

		// remove plus signs that were added to the city name
		// for cities with multiple words to be recognized as one string in the URL
		$strCity = str_replace('+', ' ', $strCity);

		// reassign location variable to a pretty "city, state" string to save to the database
		$strLocation = $strCity . ", " . $strState;

		// remove plus signs that were added to the position name
		// for positions with multiple words to be recognized as one string in the URL
		$strPosition = str_replace('+', ' ', $strPosition);

		// get the recruiter associated with the city, state, and position selected
		$sqlRecruiter = "SELECT * FROM recruiters WHERE city LIKE '$strCity' AND state LIKE '$strState' AND position LIKE '$strPosition'";
		$resRecruiter = mysql_query($sqlRecruiter);

		while($row = mysql_fetch_array($resRecruiter)) {
			// assign the recruiter's email address to a variable
			$strRecruiterContact = $row['recruiter'];
		}

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
		$txtFirstName        = mysql_real_escape_string($txtFirstName);
		$txtLastName         = mysql_real_escape_string($txtLastName);
		$txtEmail            = mysql_real_escape_string($txtEmail);
		$txtPhone            = mysql_real_escape_string($txtPhone);
		$strLocation         = mysql_real_escape_string($strLocation);
		$strPosition         = mysql_real_escape_string($strPosition);
		$strRecruiterContact = mysql_real_escape_string($strRecruiterContact);
		$rdoAcctMgrB2B       = mysql_real_escape_string($rdoAcctMgrB2B);
		$rdoDriverPhlebCDL   = mysql_real_escape_string($rdoDriverPhlebCDL);
		$rdoMedTechLicense   = mysql_real_escape_string($rdoMedTechLicense);
		$txtMedTechLicense   = mysql_real_escape_string($txtMedTechLicense);
		$rdoNurseLicense     = mysql_real_escape_string($rdoNurseLicense);
		$rdoPhlebSched       = mysql_real_escape_string($rdoPhlebSched);
		$rdoDriveRecord      = mysql_real_escape_string($rdoDriveRecord);
		$strClientAttachment = mysql_real_escape_string($strClientAttachment);
		$strUtmCampaign      = mysql_real_escape_string($strUtmCampaign);
		$strUtmMedium        = mysql_real_escape_string($strUtmMedium);
		$strUtmSource        = mysql_real_escape_string($strUtmSource);

		$sqlSaveSubmission = "INSERT INTO apps_biomed (first_name, last_name, email, phone, location, position, recruiter, b2b_sales_experience, phleb_cdl, medtech_license, medtech_cert, nurse_license, phleb_variable_sched, driving_record, resume, utm_campaign, utm_medium, utm_source, submitted, submitted_ip) VALUES ('$txtFirstName','$txtLastName', '$txtEmail', '$txtPhone', '$strLocation', '$strPosition', '$strRecruiterContact', '$rdoAcctMgrB2B', '$rdoDriverPhlebCDL', '$rdoMedTechLicense', '$txtMedTechLicense', '$rdoNurseLicense', '$rdoPhlebSched', '$rdoDriveRecord', '$strClientAttachment', '$strUtmCampaign', '$strUtmMedium', '$strUtmSource', '$submitDateTime', '$submittedIP')";

		// execute query
		$sqlSaveSubmissionResult = mysql_query($sqlSaveSubmission);

		// if the data was successfully saved to the client database,
		// send email to client
		if ($sqlSaveSubmissionResult) {

			// send email if the recruiter is not "unassigned"
			if ($strRecruiterContact != "Unassigned") {

				// send email to the client -----------------------------------------------------------------
				// update the To, From and Subject line to whatever the client requests
				// $strClientTo = "email@yourcompany.com";
				// $strClientTo = $strRecruiterContact;
				$strClientTo = "mountain.taste@gmail.com";

				$strClientFrom = "MIME-Version: 1.0" . "\r\n"
							. "Content-Type: text/html; charset=UTF-8" . "\r\n"
							. "From: American Red Cross BioMed Careers <noreply@redcrossbiomedcareers.org>";

				$strClientSubject = "Interest in American Red Cross BioMed Careers";

				$strClientFldMerge = "<html><body><p>"
							. "<strong>First Name:</strong> " . $txtFirstName . "<br>"
							. "<strong>Last Name:</strong> " . $txtLastName . "<br>"
							. "<strong>Email:</strong> " . $txtEmail . "<br>"
							. "<strong>Phone:</strong> " . $txtPhone . "<br>"
							. "<strong>Location:</strong> " . $strLocation . "<br>"
							. "<strong>Position:</strong> " . $strPosition . "<br>";

					// begin -- display the appropriate position specific question
					if ($strPosition == "Account Manager/DRD") {
						$strClientFldMerge .= "<strong>Do you have B2B sales experience?</strong> " . $rdoAcctMgrB2B . "<br>";
					}
					if ($strPosition == "Driver/Phlebotomist") {
						$strClientFldMerge .= "<strong>Do you have your CD-L?</strong> " . $rdoDriverPhlebCDL . "<br>";
					}
					if ($strPosition == "Medical Technologist") {
						$strClientFldMerge .= "<strong>Do you hold any state license?</strong> " . $rdoMedTechLicense . "<br>"
						. "<strong>What certifications/licenses do you have?</strong> " . $txtMedTechLicense . "<br>";
					}
					if ($strPosition == "Nurse") {
						$strClientFldMerge .= "<strong>Are you a state-licensed RN or LPN?</strong> " . $rdoNurseLicense . "<br>";
					}
					if ($strPosition == "Phlebotomist") {
						$strClientFldMerge .= "<strong>Can you work a variable schedule?</strong> " . $rdoPhlebSched . "<br>";
					}
					// end -- display the appropriate position specific question

					$strClientFldMerge .= "<strong>Do you have a current valid driver's license and good driving record?:</strong> " . $rdoDriveRecord . "</p>";

					// remove this line after testing
					$strClientFldMerge .= "<p>Recruiter: " . $strRecruiterContact . "</p>";

					// include link to the uploaded resume if there is one
					if($strClientAttachment) {
						// add the domain/folder prefix - $strClientAttachment just displays the filename
						$strClientFldMerge .= "<p><strong>Resume:</strong> http://redcrossbiomedcareers.org/uploads/resumes/" . $strClientAttachment . "</p>";
					}

					// begin -- display the UTM parameter values if there are any
					if($strUtmCampaign) {
						$strClientFldMerge .= "<div><strong>UTM Campaign:</strong> " . $strUtmCampaign . "</div>";
					}
					if($strUtmMedium) {
						$strClientFldMerge .= "<div><strong>UTM Medium:</strong> " . $strUtmMedium . "</div>";
					}
					if($strUtmSource) {
						$strClientFldMerge .= "<div><strong>UTM Source:</strong> " . $strUtmSource . "</div>";
					}
					// end -- display the UTM parameter values if there are any

				$strClientFldMerge .= "</body></html>";

				$strClientMsg = wordwrap($strClientFldMerge, 70);

				mail($strClientTo,$strClientSubject,$strClientMsg,$strClientFrom);

			} // end if ($strRecruiterContact != "Unassigned")
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
<link rel="apple-touch-icon" sizes="57x57" href="media/favicons/apple-touch-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="media/favicons/apple-touch-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="media/favicons/apple-touch-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="media/favicons/apple-touch-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="media/favicons/apple-touch-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="media/favicons/apple-touch-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="media/favicons/apple-touch-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="media/favicons/apple-touch-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="media/favicons/apple-touch-icon-180x180.png">
<link rel="icon" type="image/png" href="media/favicons/favicon-32x32.png" sizes="32x32">
<link rel="icon" type="image/png" href="media/favicons/android-chrome-192x192.png" sizes="192x192">
<link rel="icon" type="image/png" href="media/favicons/favicon-96x96.png" sizes="96x96">
<link rel="icon" type="image/png" href="media/favicons/favicon-16x16.png" sizes="16x16">
<link rel="manifest" href="media/favicons/manifest.json">
<link rel="mask-icon" href="media/favicons/safari-pinned-tab.svg" color="#ed1b2e">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="msapplication-TileImage" content="/mstile-144x144.png">
<meta name="theme-color" content="#ed1b2e">

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
<script>
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	ga('create', 'UA-75537316-1', 'auto');
	ga('send', 'pageview');
</script>
<!-- end google analytics code -->
</head>
<body>
	<header id="header" class="header closed-nav">
		<div class="row">
			<img class="logo" id="header-logo" src="media/img/american_red_cross_logo.png" alt="American Red Cross Logo">
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
						<a class="nav-link" href="#diversity">Culture</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#about">Who we are</a>
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
				<p>Our mission to prevent and alleviate human suffering in the face of emergencies inspires us daily. As the provider of nearly half of the nation’s blood supply, we collect, test and distribute over 5 million life-saving units of blood a year. Fulfilling our mission requires focused, energized employees, so we offer outstanding benefits that really help you, as you help&nbsp;others.</p>
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
			<h2>Who We're&nbsp;<strong>Hiring</strong></h2>
			<div id="accordion-wrap">
				<div class="expandable-panel" id="cp-1">
					<div class="expandable-panel-heading">
						<h6>Phlebotomists<span class="icon-close-open-toggle"></span></h6>
					</div>
					<div class="expandable-panel-content">
						<div class="col-12 bleed-x"></div>
						<h6>Want to channel your customer-service skills into a dynamic job opportunity? Consider a phlebotomy career at the American Red Cross. (We train!)</h6>
						<div class="description">
							<p>As the front line of our blood collection services, you will be the face of the American Red Cross to our blood donors, making a personal connection with them and ensuring they have a top-notch donation experience. We’re looking for compassionate people who can connect with others, pay careful attention to details and support their team while working together to fulfill our mission. This role requires a highly flexible schedule and comes with competitive pay and benefits. No experience is necessary. We’ll train and support you as you learn your new skills, with our full-time, intensive phlebotomy training program. All costs are covered and you will be paid throughout the&nbsp;program!</p>
						</div>
						<div class="requirements">
							<h5>Requirements</h5>
							<ul>
								<li><span>High school diploma or equivalent</span></li>
								<li><span>A current valid driver's license and good driving record</span></li>
								<li><span>Some prior medical experience preferred but not required</span></li>
							</ul>
							<div class="btn-sml bleed-x">
								<a href="#form-1-container">Contact Us</a>
							</div>
						</div><!-- END .requirements -->
					</div><!-- END .expandable-panel-content -->
				</div><!-- END #cp-1 -->

				<div class="expandable-panel" id="cp-2">
					<div class="expandable-panel-heading">
						<h6>Nurses<span class="icon-close-open-toggle"></span></h6>
					</div>
					<div class="expandable-panel-content">
						<div class="col-12 bleed-x"></div>
						<h6>Are you a mission-oriented nurse who likes to travel and meet new people? You will thrive at the American Red Cross.</h6>
						<div class="description">
							<p>We’re looking for both charge and apheresis nurses to accompany us on blood drives throughout the community. In this role, you’ll focus on customer service and compassion, working and making connections with healthy, happy blood donors. As a charge nurse you will lead a team, draw blood and, on occasion, supervise blood drives. As an apheresis nurse you will perform clinical apheresis procedures, along with other tasks, ensuring safe and appropriate care of patients. We work hard when we’re on the road and need a nurse who can accommodate a highly flexible schedule. You should also be comfortable performing venipuncture. You will need an RN (or LPN, depending on state requirements) license in the state prior to starting&nbsp;work.</p>
						</div>
						<div class="requirements">
							<h5>Requirements</h5>
							<ul>
								<li><span>Registered nurse license (LPN may be acceptable in some states)</span></li>
								<li><span>A current valid driver's license and good driving record</span></li>
							</ul>
							<div class="btn-sml bleed-x">
								<a href="#form-1-container">Contact Us</a>
							</div>
						</div><!-- END .requirements -->
					</div><!-- END .expandable-panel-content -->
				</div><!-- END #cp-2 -->

				<div class="expandable-panel" id="cp-3">
					<div class="expandable-panel-heading">
						<h6>Phlebotomist/Drivers<span class="icon-close-open-toggle"></span></h6>
					</div>
					<div class="expandable-panel-content">
						<div class="col-12 bleed-x"></div>
						<h6>A unique opening for qualified truck drivers who want to transition into a new career making a difference in people’s&nbsp;lives.</h6>
						<div class="description">
							<p>An American Red Cross driving career gives you a rewarding shift from day-to-day deliveries to helping save lives in your community. Say goodbye to boredom, because every day on the job is different. As part of our team, you’ll be transporting, setting up and breaking down blood collection equipment at work sites across the community, interacting with our donors and ensuring they have a top notch experience. In some locations, you’ll also work as a phlebotomist, but don’t worry – no experience is necessary! We’ll train and support you as you learn your new skills, through our full time, intensive phlebotomy training program. All costs are covered and you will be paid throughout the program! To be successful in this position you’ll need a flexible schedule to work variable shifts, and have experience driving large&nbsp;trucks.</p>
						</div>
						<div class="requirements">
							<h5>Requirements</h5>
							<ul>
								<li><span>High school diploma or equivalent</span></li>
								<li><span>A current valid driver's license and good driving record</span></li>
								<li><span>A valid CDL (Class A or B) may be required in some locations</span></li>
								<li><span>Ability to lift, push and pull up to 75lbs</span></li>
								<li><span>DOT certification may be required in some locations</span></li>
							</ul>
							<div class="btn-sml bleed-x">
								<a href="#form-1-container">Contact Us</a>
							</div>
						</div><!-- END .requirements -->
					</div><!-- END .expandable-panel-content -->
				</div><!-- END #cp-3 -->

				<div class="expandable-panel" id="cp-4">
					<div class="expandable-panel-heading">
						<h6>Medical Technologists<span class="icon-close-open-toggle"></span></h6>
					</div>
					<div class="expandable-panel-content">
						<div class="col-12 bleed-x"></div>
						<h6>Use your passion for blood banking to support our life-saving&nbsp;mission.</h6>
						<div class="description">
							<p>Before our blood products go to patients in need, our immunohematology reference labs perform critical testing for safety. In this role, you will conduct basic and advanced donor and patient tests to resolve compatibility issues, and consult with hospitals and transfusion services—all while adhering to standard operating procedures and regulatory&nbsp;requirements.</p>
						</div>
						<div class="requirements">
							<h5>Requirements</h5>
							<p><strong>Medical Technologist I</strong></p>
							<ul>
								<li><span>MT (ASCP) and/or BB (ASCP) certification, or equivalent; plus Bachelor’s degree with major in biological science or chemistry plus 3 years blood banking experience<br>
								- or -<br>
								MLT (ASCP) certification plus 5 years laboratory experience
								</span></li>
							</ul>
							<p><strong>Medical Technologist II</strong></p>
							<ul>
								<li><span>MT (ASCP) and/or BB (ASCP) certification, or equivalent; plus Bachelor's degree with major in biological science or chemistry plus 6 years blood banking experience<br>
								- or -<br>
								MLT (ASCP) certification plus 4 years laboratory experience
								</span></li>
							</ul>
							<p><strong>Medical Technologist III</strong></p>
							<ul>
								<li><span>MT (ASCP) and/or BB (ASCP) certification, or equivalent; plus Bachelor’s degree with major in biological science or chemistry plus blood banking experience</span></li>
							</ul>
							<p><strong>All</strong></p>
							<ul>
								<li><span>State license where applicable</span></li>
							</ul>
							<div class="btn-sml bleed-x">
								<a href="#form-1-container">Contact Us</a>
							</div>
						</div><!-- END .requirements -->
					</div><!-- END .expandable-panel-content -->
				</div><!-- END #cp-4 -->

				<div class="expandable-panel" id="cp-5">
					<div class="expandable-panel-heading">
						<h6>Sales Professionals<span class="icon-close-open-toggle"></span></h6>
					</div><!-- END .expandable-panel-heading -->
					<div class="expandable-panel-content">
						<div class="col-12 bleed-x"></div>
						<h6>Bring your business-to-business sales experience to a philanthropic&nbsp;venture!</h6>
						<div class="description">
							<p>We’re looking for sales and marketing professionals who enjoy working with the public to set up, coordinate and promote blood drives with local businesses and civic organizations in their communities. We incentivize all hard work with bonuses and a supportive, growth-oriented atmosphere. All of our work centers around donors and volunteers, so great people skills, compassion and communication are priorities. Here, you’ll play a direct role in our life-saving mission, ensuring that others continue to receive the gift of life. Successful account managers should also be able to close deals and have a highly flexible schedule to accommodate the needs of our blood&nbsp;donors.</p>
						</div><!-- END .description -->
						<div class="requirements">
							<h5>Requirements</h5>
							<ul>
								<li><span>Bachelor's degree in marketing, sales, or communications<br>
								- or -<br>
								Equivalent combination of related education and experience</span></li>
								<li><span>One year (minimum) related experience</span></li>
								<li><span>A current, valid driver's license and good driving record</span></li>
							</ul>
							<div class="btn-sml bleed-x">
								<a href="#form-1-container">Contact Us</a>
							</div>
						</div><!-- END .requirements -->
					</div><!-- END .expandable-panel-content -->
				</div><!-- END #cp-5 -->
			</div><!-- END #accordion-wrap -->
		</div><!-- END .row -->
		<div id="tabs">
			<div class="row">
				<div class="col-12">
					<ul class="tabs">
						<li class="tab-link current" data-tab="tab-1">Phlebotomists</li>
						<li class="tab-link" data-tab="tab-2">Nurses</li>
						<li class="tab-link" data-tab="tab-3">Phlebotomist/<br>Drivers</li>
						<li class="tab-link" data-tab="tab-4">Medical<br>Technologists</li>
						<li class="tab-link" data-tab="tab-5">Sales Account<br>Managers</li>
					</ul>
					<div id="tab-1" class="tab-content current">
						<div class="col-12"></div>
						<h6>A unique opening for qualified truck drivers who want to transition into a new career making a difference in people’s&nbsp;lives.</h6>
						<div class="description col-6">
							<p>An American Red Cross driving career gives you a rewarding shift from day-to-day deliveries to helping save lives in your community. Say goodbye to boredom, because every day on the job is different. As part of our team, you’ll be transporting, setting up and breaking down blood collection equipment at work sites across the community, interacting with our donors and ensuring they have a top notch experience. In some locations, you’ll also work as a phlebotomist, but don’t worry – no experience is necessary! We’ll train and support you as you learn your new skills, through our full time, intensive phlebotomy training program. All costs are covered and you will be paid throughout the program! To be successful in this position you’ll need a flexible schedule to work variable shifts, and have experience driving large&nbsp;trucks.</p>
						</div>
						<div class="requirements col-6">
							<h5>Requirements</h5>
							<ul>
								<li><span>High school diploma or equivalent</span></li>
								<li><span>A current valid driver's license and good driving record</span></li>
								<li><span>A valid CDL (Class A or B) may be required in some locations</span></li>
								<li><span>Ability to lift, push and pull up to 75lbs</span></li>
								<li><span>DOT certification may be required in some locations</span></li>
							</ul>
							<div class="btn-sml">
								<a href="#form-1-container">Contact Us</a>
							</div>
						</div><!-- END .requirements -->
					</div><!-- END #tab-1 -->
					<div id="tab-2" class="tab-content">
						<div class="col-12"></div>
						<h6>Are you a mission-oriented nurse who likes to travel and meet new people? You will thrive at the American Red Cross.</h6>
						<div class="description col-6">
							<p>We’re looking for both charge and apheresis nurses to accompany us on blood drives throughout the community. In this role, you’ll focus on customer service and compassion, working and making connections with healthy, happy blood donors. As a charge nurse you will lead a team, draw blood and, on occasion, supervise blood drives. As an apheresis nurse you will perform clinical apheresis procedures, along with other tasks, ensuring safe and appropriate care of patients. We work hard when we’re on the road and need a nurse who can accommodate a highly flexible schedule. You should also be comfortable performing venipuncture. You will need an RN (or LPN, depending on state requirements) license in the state prior to starting&nbsp;work.</p>
						</div>
						<div class="requirements col-6">
							<h5>Requirements</h5>
							<ul>
								<li><span>Registered nurse license (LPN may be acceptable in some states)</span></li>
								<li><span>A current valid driver's license and good driving record</span></li>
							</ul>
							<div class="btn-sml bleed-x">
								<a href="#form-1-container">Contact Us</a>
							</div>
						</div><!-- END .requirements -->
					</div><!-- END #tab-2 -->
					<div id="tab-3" class="tab-content">
						<div class="col-12"></div>
						<h6>A unique opening for qualified truck drivers who want to transition into a new career making a difference in people’s&nbsp;lives.</h6>
						<div class="description col-6">
							<p>An American Red Cross driving career gives you a rewarding shift from day-to-day deliveries to helping save lives in your community. Say goodbye to boredom, because every day on the job is different. As part of our team, you’ll be transporting, setting up and breaking down blood collection equipment at work sites across the community, interacting with our donors and ensuring they have a top notch experience. In some locations, you’ll also work as a phlebotomist, but don’t worry – no experience is necessary! We’ll train and support you as you learn your new skills, through our full time, intensive phlebotomy training program. All costs are covered and you will be paid throughout the program! To be successful in this position you’ll need a flexible schedule to work variable shifts, and have experience driving large&nbsp;trucks.</p>
						</div>
						<div class="requirements col-6">
							<h5>Requirements</h5>
							<ul>
								<li><span>High school diploma or equivalent</span></li>
								<li><span>A current valid driver's license and good driving record</span></li>
								<li><span>A valid CDL (Class A or B) may be required in some locations</span></li>
								<li><span>Ability to lift, push and pull up to 75lbs</span></li>
								<li><span>DOT certification may be required in some locations</span></li>
							</ul>
							<div class="btn-sml bleed-x">
								<a href="#form-1-container">Contact Us</a>
							</div>
						</div><!-- END .requirements -->
					</div><!-- END #tab-3 -->
					<div id="tab-4" class="tab-content">
						<div class="col-12 bleed-x"></div>
						<h6>Use your passion for blood banking to support our life-saving&nbsp;mission.</h6>
						<div class="description col-6">
							<p>Before our blood products go to patients in need, our immunohematology reference labs perform critical testing for safety. In this role, you will conduct basic and advanced donor and patient tests to resolve compatibility issues, and consult with hospitals and transfusion services—all while adhering to standard operating procedures and regulatory&nbsp;requirements.</p>
						</div>
						<div class="requirements col-6">
							<h5>Requirements</h5>
							<ul>
								<p class="job-title"><strong>Medical Technologist I</strong></p>
								<li><span>MT (ASCP) and/or BB (ASCP) certification, or equivalent; plus Bachelor’s degree with major in biological science or chemistry plus 3 years blood banking experience<br>
								- or -<br>
								MLT (ASCP) certification plus 5 years laboratory experience
								</span></li>
							</ul>
							<ul>
								<p class="job-title"><strong>Medical Technologist II</strong></p>
								<li><span>MT (ASCP) and/or BB (ASCP) certification, or equivalent; plus Bachelor's degree with major in biological science or chemistry plus 6 years blood banking experience<br>
								- or -<br>
								MLT (ASCP) certification plus 4 years laboratory experience
								</span></li>
							</ul>
							<ul>
								<p class="job-title"><strong>Medical Technologist III</strong></p>
								<li><span>MT (ASCP) and/or BB (ASCP) certification, or equivalent; plus Bachelor’s degree with major in biological science or chemistry plus blood banking experience</span></li>
							</ul>
							<ul>
								<p class="job-title"><strong>All</strong></p>
								<li><span>State license where applicable</span></li>
							</ul>
							<div class="btn-sml bleed-x">
								<a href="#form-1-container">Contact Us</a>
							</div>
						</div><!-- END .requirements -->
					</div><!-- END #tab-4 -->
					<div id="tab-5" class="tab-content">
						<div class="col-12"></div>
						<h6>Bring your business-to-business sales experience to a philanthropic&nbsp;venture!</h6>
						<div class="description col-6">
							<p>We’re looking for sales and marketing professionals who enjoy working with the public to set up, coordinate and promote blood drives with local businesses and civic organizations in their communities. We incentivize all hard work with bonuses and a supportive, growth-oriented atmosphere. All of our work centers around donors and volunteers, so great people skills, compassion and communication are priorities. Here, you’ll play a direct role in our life-saving mission, ensuring that others continue to receive the gift of life. Successful account managers should also be able to close deals and have a highly flexible schedule to accommodate the needs of our blood&nbsp;donors.</p>
						</div><!-- END .description -->
						<div class="requirements col-6">
							<h5>Requirements</h5>
							<ul>
								<li><span>Bachelor's degree in marketing, sales, or communications<br>
								- or -<br>
								Equivalent combination of related education and experience</span></li>
								<li><span>One year (minimum) related experience</span></li>
								<li><span>A current, valid driver's license and good driving record</span></li>
							</ul>
							<div class="btn-sml bleed-x">
								<a href="#form-1-container">Contact Us</a>
							</div>
						</div><!-- END .requirements -->
					</div><!-- END #tab-5 -->
				</div><!-- END .col-12 -->
			</div><!-- END .row -->
		</div><!-- END #tabs -->
	</section><!-- END #hiring -->
	<!--======= End of WHO WE'RE HIRING =======-->

	<!--==========================================
	=                  BENEFITS                  =
	===========================================-->
	<section id="benefits">
		<div class="row">
			<h2>Benefits with <strong>you</strong> in&nbsp;mind</h2>
			<p>As a mission-based organization, we believe our team needs great support to do great work. Our comprehensive benefits help you in balancing home and work, retirement, getting healthy and more. With our resources and perks, you have amazing possibilities at the American Red Cross to advance and&nbsp;learn.</p>
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
				<div class="text">
					<p>Serving people in America and around the world requires a diverse group of employees ready to meet the challenge. Cultural sensitivity is key to how we operate with the public, and we want to ensure our employees reflect a rich diversity. Through external relationships and internal initiatives, we seek to achieve diversity in our workforce, partners and suppliers.</p>
					<p>We are also proud to offer several resource groups for our employees. African-American, LGBT, and Latino Team Member Resource Groups provide mentoring and give voice to concerns and opinions of these valuable team&nbsp;members.</p>
				</div>
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
				<p>We are a strong organization of passionate supporters, volunteers and employees dedicated to helping others. Every year, we collect over 5 million units of blood; respond to 66,000 disasters; provide more than 367,000 services to active-duty military, veterans and their families; and&nbsp;more.</p>
			</div><!-- END .col-6 -->
			<div class="col-6">
				<p>Watch this short video to learn more about the life-saving impact our employees have every&nbsp;day.</p>
				<div class="video-container bleed-x">
					<iframe width="560" height="315" src="https://www.youtube.com/embed/Hg3XJCA8RQ0" frameborder="0" allowfullscreen="allowfullscreen"></iframe>
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
				<p>The American Red Cross is an Equal Opportunity/Affirmative Action employer. All qualified applicants will receive consideration for employment without regard to sex, gender identity, sexual orientation, race, color, religion, national origin, disability, protected veteran status, age, or any other characteristic protected by&nbsp;law.</p>
				<a class="privacy-policy" href="http://bayardad.com/careers/privacy-policy/" target="_blank">Privacy Policy</a>
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
</body>
</html>