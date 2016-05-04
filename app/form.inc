<!--==========================================
=                    FORM                    =
===========================================-->
<form id="form" class="form" action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]);?>" method="POST" enctype="multipart/form-data">
    <div class="input-wrap">
        <div id="contact-info" class="form-col">
            <input type="text" label="firstName" name="txtFirstName" class="form-control" placeholder="First Name*">
            <input type="text" label="firstName" name="txtLastName" class="form-control" placeholder="Last Name*">
            <input type="email" label="email" name="txtEmail" class="form-control" placeholder="Email*">
            <input type="tel" label="phone" name="txtPhone" class="form-control" placeholder="Phone*">

            <!-- SPECIAL FORM QUESTIONS-->
            <label>Location*</label>
            <select class="selectpicker" name="location" id="location">
                <option value="" disabled selected>Nothing Selected</option>
                <?php
                // connect to database
                include("login_info.php");

                $sqlLocations = "SELECT * FROM recruiters WHERE status LIKE 'on' GROUP BY city ORDER BY state_name, city ASC";
                $resLocations = mysql_query($sqlLocations);

                while($row = mysql_fetch_array($resLocations)) {
                    $encodedLocation = $row['city'] . "," . $row['state'];
                    $encodedLocation = str_replace(' ', '+', $encodedLocation);
                    $displayLocation = $row['state_name'] . " - " . $row['city'];

                    echo "<option value=\"" . $encodedLocation . "\">" . $displayLocation . "</option>\n";
                }
                ?>
            </select>

            <label>Position*</label>
            <select class="selectpicker" name="position" id="position">
                <option value="" disabled selected>Please select a location first</option>
            </select>

            <p id="position-questions"></p>

            <!-- END SPECIAL FORM QUESTIONS -->
            <p>
            <label>Do you have a current valid driver's license and good driving record?*</label>
            <input class="radio" type="radio" name="rdoDriveRecord" value="Yes"> Yes
            <input class="radio" type="radio" name="rdoDriveRecord" value="No"> No
            </p>

            <label>Resume</label>
            <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                <span class="input-group-addon btn btn-default btn-file">
                    <span class="fileinput-new">BROWSE</span>
                    <span class="fileinput-exists">BROWSE</span>
                    <input type="file" name="file_upload">
                </span>

                <div class="form-control" data-trigger="fileinput">
                    <span class="fileinput-filename"></span>
                </div>
            </div>

            <input type="hidden" name="txtNewsletter">
        </div><!-- END #questions -->
    </div><!-- END #input-wrap -->

    <input class="submit-btn" type="submit" name="btnSubmit" value="CONTACT A RECRUITER NOW">

    <p class="terms">By submitting my information, I agree to the<br><a href="http://bayardclient.com/tos.html" target="_blank">Terms &amp; Conditions.</a></p>
</form><!-- END #form -->
<!--===========  End of FORM  ===========-->