<?php
include('_session.php');

function download_csv_results($results, $name = NULL) {
	if( ! $name)
	{
		$name = md5(uniqid() . microtime(TRUE) . mt_rand()). '.csv';
	}

	header('Content-Type: text/csv');
	header('Content-Disposition: attachment; filename='. $name);
	header('Pragma: no-cache');
	header("Expires: 0");

	$output = fopen("php://output", "w");

	foreach((array) $results as $result)
	{
		fputcsv($output, $result);
	}

	fclose($output);
} // end function download_csv_results

date_default_timezone_set("America/Denver");
$downloadDate = date('Y-m-d');
$downloadFile = "apps_biomed " . $downloadDate . ".csv" ;
download_csv_results($results, $downloadFile);

// include database login credentials
include_once('login_info.php');

$sql = 'SELECT * FROM apps_biomed ORDER BY id DESC';

$result = mysql_query($sql);

if (mysql_num_rows($result) > 0) {

	$line = 'First Name, Last Name, Email, Phone, Location, Position, Recruiter, B2B Experience, CDL, MedTech License, MedTech Certification, Nurse License, Variable Schedule, Driver\'s License/Good Driving Record, Resume, UTM Campaign, UTM Medium, UTM Source, Date Submitted, Submitted IP' . "\n";

	while ($row = mysql_fetch_array($result)) {
		$line .= "\"" . $row['first_name'] . "\",";
		$line .= "\"" . $row['last_name'] . "\",";
		$line .= "\"" . $row['email'] . "\",";
		$line .= "\"" . $row['phone'] . "\",";
		$line .= "\"" . $row['location'] . "\",";
		$line .= "\"" . $row['position'] . "\",";
		$line .= "\"" . $row['recruiter'] . "\",";
		$line .= "\"" . $row['b2b_sales_experience'] . "\",";
		$line .= "\"" . $row['phleb_cdl'] . "\",";
		$line .= "\"" . $row['medtech_license'] . "\",";
		$line .= "\"" . $row['medtech_cert'] . "\",";
		$line .= "\"" . $row['nurse_license'] . "\",";
		$line .= "\"" . $row['phleb_variable_sched'] . "\",";
		$line .= "\"" . $row['driving_record'] . "\",";
		$line .= "\"" . $row['resume'] . "\",";
		$line .= "\"" . $row['utm_campaign'] . "\",";
		$line .= "\"" . $row['utm_medium'] . "\",";
		$line .= "\"" . $row['utm_source'] . "\",";
		$line .= "\"" . $row['submitted'] . "\",";
		$line .= "\"" . $row['submitted_ip'] . "\",";
		$line .= "\n";
	}

	echo $line;

} // output results to csv file

// close database connection
mysql_close($cn);

exit();
?>