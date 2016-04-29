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

            <label>Are you at least 21 years or older?*</label>
            <div class="radio-btns">
                <input type="radio" name="over21" id="over21_yes" value="Yes"/>
                <label>Yes</label>

                <input type="radio" name="over21" id="over21_no" value="No"/>
                <label>No</label>
            </div>

            <label>Location</label>
            <select class="selectpicker" name="location" id="location">
                <option value="" disabled selected>Nothing Selected</option>
                <option value="San Jose, California">San Jose, California</option>
                <option value="Colorado Springs, Colorado">Colorado Springs, Colorado</option>
                <option value="Louisville, Colorado">Louisville, Colorado</option>
                <option value="Fort Myers, Florida">Fort Myers, Florida</option>
                <option value="Melbourne, Florida">Melbourne, Florida</option>
                <option value="Miramar, Florida">Miramar, Florida</option>
                <option value="Palm Beach Garden, Florida">Palm Beach Garden, Florida</option>
                <option value="Pearl City, Hawaii">Pearl City, Hawaii</option>
                <option value="Columbus, Ohio">Columbus, Ohio</option>
                <option value="Lexington, Ohio">Lexington, Ohio</option>
                <option value="Portland, Oregon">Portland, Oregon</option>
                <option value="North Charleston, South Carolina">North Charleston, South Carolina</option>
                <option value="Redmond, Washington">Redmond, Washington</option>
            </select>

            <label>Years of (driver/delivery service) experience?</label>
            <select class="selectpicker" name="driverExperience" id="driverExperience">
                <option value="" disabled selected>Nothing Selected</option>
                <option value="0 Years">None</option>
                <option value="0-1 Years">0-1 Years</option>
                <option value="1-2 Years">1-2 Years</option>
                <option value="2-3 Years">2-3 Years</option>
                <option value="3-4 Years">3-4 Years</option>
                <option value="4-5 Years">4-5 Years</option>
                <option value="5+ Years">5+ Years</option>
            </select>
        </div><!-- END #contact-info -->

        <div id="questions" class="form-col">
            <label>What salary range are you seeking?</label>
            <input type="text" name="txtSalary" id="salary" class="form-control">

            <label>What type of employment are you seeking?<br>
            <em>(Select all that apply)</em></label>
            <select class="selectpicker" multiple="multiple" name="typeEmployment[]">
                <option value="Full-Time">Full-Time</option>
                <option value="Part-Time">Part-Time</option>
                <option value="Per-Diem">Per-Diem</option>
            </select>

            <label>Do you have a valid driver’s license?</label>
            <div class="radio-btns">
                <input type="radio" name="driverLicense" id="drivers_license_yes" value="Yes"/>
                <label>Yes</label>

                <input type="radio" name="driverLicense" id="drivers_license_no" value="No"/>
                <label>No</label>
            </div>

            <label>Do you have a valid Commercial Driver’s License (CDL)?</label>
            <div class="radio-btns">
                <input type="radio" name="cdl" id="cdl_yes" value="Yes"/>
                <label>Yes</label>

                <input type="radio" name="cdl" id="cdl_no" value="No"/>
                <label>No</label>
            </div>

            <label>Do you have Hazardous Materials Endorsement?</label>
            <div class="radio-btns">
                <input class="conditional-question" type="radio" name="hazMatEndorsement" id="hazMatEndorsement_yes" value="Yes"/>
                <label>Yes</label>

                <input class="conditional-question" type="radio" name="hazMatEndorsement" id="hazMatEndorsement_no" value="No"/>
                <label>No</label>
            </div>

            <div class="conditional-answer">
                <label>Years of hazardous materials experience?</label>
                <select class="selectpicker" name="hazMatExperience" id="hazMatExperience">
                    <option value="" disabled selected>Nothing Selected</option>
                    <option value="0-1 Years">1 Years</option>
                    <option value="1-2 Years">2 Years</option>
                    <option value="1-2 Years">3+ Years</option>
                </select>
            </div>

            <label>Upload your resume</label>
            <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                <span class="input-group-addon btn btn-default btn-file">
                    <span class="fileinput-new">Upload</span>
                    <span class="fileinput-exists">Upload</span>
                    <input type="file" name="file_upload">
                </span>

                <div class="form-control" data-trigger="fileinput">
                    <span class="fileinput-filename"></span>
                </div>
            </div>

            <input type="hidden" name="txtNewsletter">
        </div><!-- END #questions -->
    </div><!-- END #input-wrap -->

    <input class="submit-btn" type="submit" name="btnSubmit" value="APPLY NOW">

    <p class="terms"><em>By submitting my information, I agree to the <a href="http://bayardclient.com/tos.html" target="_blank">Terms &amp; Conditions.</a></em></p>
</form><!-- END #form -->
<!--===========  End of FORM  ===========-->