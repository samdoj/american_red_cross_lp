<?php
include("login_info.php");

if ($_GET['location']) {
	// get positions available in the selected location
	$sltLocation= mysql_real_escape_string($_GET['location']);

	$sltLocationParts = explode(",", $sltLocation);
	$strCity = $sltLocationParts[0];
	$strState = $sltLocationParts[1];

	$sqlPostion = "SELECT * FROM recruiters WHERE city LIKE '$strCity' AND state LIKE '$strState' ORDER BY position ASC";
	$resPosition = mysql_query($sqlPostion);

	echo "<option class=\"refresh\" value=\"\" disabled selected>Select a position</option>";

	while($row = mysql_fetch_array($resPosition)) {
		$encodedPosition = $row['position'];
		$encodedPosition = str_replace(' ', '+', $encodedPosition);
		echo "<option value=\"" . $encodedPosition . "\">" . $row['position'] . "</option>\n";
	}
}

if ($_GET['position']) {
	// display conditional questions based on selected position
	$sltPosition= mysql_real_escape_string($_GET['position']);

	if ($sltPosition == "Account Manager/DRD") {
		echo "
			<p>
			<label>Do you have B2B sales experience?</label><br>
			<input type=\"radio\" name=\"rdoAcctMgrB2B\" value=\"Yes\"> Yes
			<input type=\"radio\" name=\"rdoAcctMgrB2B\" value=\"No\"> No
			</p>
		";
	}
	if ($sltPosition == "Driver/Phlebotomist") {
		echo "
			<p>
			<label>Do you have your CDL?*</label><br>
			<input type=\"radio\" name=\"rdoDriverPhlebCDL\" value=\"Yes\"> Yes
			<input type=\"radio\" name=\"rdoDriverPhlebCDL\" value=\"No\"> No
			</p>
		";
	}
	if ($sltPosition == "Medical Technologist") {
		echo "
			<p>
			<label>Do you hold any state license?*</label><br>
			<input type=\"radio\" name=\"rdoMedTechLicense\" value=\"Yes\"> Yes
			<input type=\"radio\" name=\"rdoMedTechLicense\" value=\"No\"> No
			</p>

			<p>
			<label>What certifications/licenses do you have?</label><br>
			<textarea name=\"txtMedTechLicense\"></textarea>
			</p>
		";
	}
	if ($sltPosition == "Nurse") {
		echo "
			<p>
			<label>Are you a state-licensed RN or LPN?*</label><br>
			<input type=\"radio\" name=\"rdoNurseLicense\" value=\"Yes\"> Yes
			<input type=\"radio\" name=\"rdoNurseLicense\" value=\"No\"> No
			</p>
		";
	}
	if ($sltPosition == "Phlebotomist") {
		echo "
			<p>
			<label>Can you work a variable schedule?*</label><br>
			<input type=\"radio\" name=\"rdoPhlebSched\" value=\"Yes\"> Yes
			<input type=\"radio\" name=\"rdoPhlebSched\" value=\"No\"> No
			</p>
		";
	}
}
?>