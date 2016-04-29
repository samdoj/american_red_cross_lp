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

$downloadDate = date('Y-m-d');
$downloadFile = "apps-" . $downloadDate . ".csv" ;
download_csv_results($results, $downloadFile);

// include database login credentials
include_once('login_info.php');

$sql = 'SELECT * FROM apps ORDER BY id DESC';

$result = mysql_query($sql);

if (mysql_num_rows($result) > 0) {

	$line = 'ID,Name,Email,Phone,Call experience,Position,Health experience,Resume,Submitted,Submitted IP' . "\n";

	while ($row = mysql_fetch_array($result)) {
		$line .= "\"" . $row['id'] . "\",";
		$line .= "\"" . $row['name'] . "\",";
		$line .= "\"" . $row['email'] . "\",";
		$line .= "\"" . $row['phone'] . "\",";
		$line .= "\"" . $row['question1'] . "\",";
		$line .= "\"" . $row['question2'] . "\",";
		$line .= "\"" . $row['question3'] . "\",";
		$line .= "\"http://altegramemberengagementcareers.com/uploads/resumes/" . $row['resume'] . "\",";
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