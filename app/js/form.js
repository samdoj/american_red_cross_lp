jQuery(document).ready(function() {
   
   // $('.selectpicker').selectpicker('toggle');
    $('.selectpicker').selectpicker({
        style: 'btn-info',
        size: 6
    });

    $('form#form').each(function(i) {
        $(this).attr('id', 'form' + i);
        // console.log($(''));
    });

    // form validation
    $("form").submit(function() {
        var form = $(this);
        var formprefix = form.attr('id');

        if($("#" + formprefix + " [name='txtFirstName']").val() == "") {
            alert("First name is required.");
            $("#" + formprefix + " [name='txtFirstName']").focus();
            return false;
        } // end txtFirstName validation

        if($("#" + formprefix + " [name='txtLastName']").val() == "") {
            alert("Last name is required.");
            $("#" + formprefix + " [name='txtLastName']").focus();
            return false;
        } // end txtLastName validation

        if($("#" + formprefix + " [name='txtEmail']").val() == "") {
            alert("Email is required.");
            $("#" + formprefix + " [name='txtEmail']").focus();
            return false;
        } else {
            var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            if(!re.test($("#" + formprefix + " [name='txtEmail']").val())){
                alert("Email is not a valid address.");
                $("#" + formprefix + " [name='txtEmail']").focus();
                return false;
            }
        } // end txtEmail validation

        if($("#" + formprefix + " [name='txtPhone']").val() == "") {
            alert("Phone number is required.");
            $("#" + formprefix + " [name='txtPhone']").focus();
            return false;
        } else {
            var phone = /^\(?(\d{3})\)?[-\. ]?(\d{3})[-\. ]?(\d{4})$/;
            if(!phone.test($("#" + formprefix + " [name='txtPhone']").val())){
                alert("Phone number is not valid. Should be in this format: xxx-xxx-xxxx.");
                $("#" + formprefix + " [name='txtPhone']").focus();
                return false;
            }
            if($("#" + formprefix + " [name='txtPhone']").val().substring(0,1) == '0' || $("#" + formprefix + " [name='txtPhone']").val().substring(0,1) == '1'){
                alert("Phone number cannot start with 0 nor 1.");
                $("#" + formprefix + " [name='txtPhone']").focus();
                return false;
            }
        } // end txtPhone validation

        // Location validation
        if($("#" + formprefix + " [name='location'] :selected").val() == '') {
            alert('Location is required.');
            $("#" + formprefix + " [name='location']").focus();
            return false;
        } // end location validation

        // Position validation
        if($("#" + formprefix + " [name='position'] :selected").val() == '') {
            alert('Position is required.');
            $("#" + formprefix + " [name='position']").focus();
            return false;
        } // end position validation


        // CHECKING EACH POSITION IS SELECTED AND VALIDATING CONDITIONAL QUESTIONS FOR EACH.
        // ACCT MANAGER VALIDATION
        if($("#" + formprefix + " [name='position'] :selected").val() == 'Account+Manager/DRD') {
            // B2B Sales Experience
            if ($("#" + formprefix + " [name='rdoAcctMgrB2B']:checked").length > 0) {
              // one ore more checkboxes are checked
            }
            else{
                alert('Please check "Yes" or "No" for B2B sales experience.');
                $("#" + formprefix + " [name='rdoAcctMgrB2B']").focus();
                return false;
            }
        }

        // DRIVER/PHLEBOTOMIST VALIDATION
        if($("#" + formprefix + " [name='position'] :selected").val() == 'Driver/Phlebotomist') {
            // Phlebotomist Driver CDL
            if ($("#" + formprefix + " [name='rdoDriverPhlebCDL']:checked").length > 0) {
              // one ore more checkboxes are checked
            }
            else{
                alert('Please check "Yes" or "No" for CDL.');
                $("#" + formprefix + " [name='rdoDriverPhlebCDL']").focus();
                return false;
            }
        }

        // MED TECH VALIDATION
        if($("#" + formprefix + " [name='position'] :selected").val() == 'Medical+Technologist') {
            // Medical Technologist State License
            if ($("#" + formprefix + " [name='rdoMedTechLicense']:checked").length > 0) {
              // one ore more checkboxes are checked
            }
            else{
                alert('Please check "Yes" or "No" for state license.');
                $("#" + formprefix + " [name='rdoMedTechLicense']").focus();
                return false;
            }
        }
        
        // NURSE VALIDATION
        if($("#" + formprefix + " [name='position'] :selected").val() == 'Nurse') {
            // State licensed RN or LPN?
            if ($("#" + formprefix + " [name='rdoNurseLicense']:checked").length > 0) {
              // one ore more checkboxes are checked
            }
            else{
                alert('Please check "Yes" or "No" for state licensed RN or LPN.');
                $("#" + formprefix + " [name='rdoNurseLicense']").focus();
                return false;
            }
        }

        // PHLEBOTOMIST VALIDATION
        if($("#" + formprefix + " [name='position'] :selected").val() == 'Phlebotomist') {
            // Phlebotomist Variable Schedule
            if ($("#" + formprefix + " [name='rdoPhlebSched']:checked").length > 0) {
              // one ore more checkboxes are checked
            }
            else{
                alert('Please check "Yes" or "No" for variable schedule.');
                $("#" + formprefix + " [name='rdoPhlebSched']").focus();
                return false;
            }
        }
        // END OF CONDITIONAL VALIDATION

        // Driver's License and Driving Record Validation
        if ($("#" + formprefix + " [name='rdoDriveRecord']:checked").length > 0) {
          // one ore more checkboxes are checked
        }
        else{
            alert('Please check "Yes" or "No" for driver\'s license and driving record.');
            $("#" + formprefix + " [name='rdoDriveRecord']").focus();
            return false;
        }

        // begin resume upload validation
        var ext = $("#" + formprefix + " [name='file_upload']").val().split('.').pop().toLowerCase();
        // if a file was uploaded, check the file extension
        if ( $("#" + formprefix + " [name='file_upload']").val() != '' ) {
            if($.inArray(ext, ['doc','docx','pdf']) == -1) {
                alert('We can only accept documents with a DOC, DOCX, or PDF extension.');
                $("#" + formprefix + " [name='file_upload']").focus();
                // clear uploaded file from field
                $("#" + formprefix + " [name='file_upload']").val('');
                // clear uploaded file name from field label
                var input = $("#" + formprefix + " [name='file_upload']"),
                    numFiles = '',
                    label = '';
                input.trigger('fileselect', [numFiles, label]);
                return false;
            }
        } // end resume upload validation

        // DISABLE SUBMIT BUTTON FOR A BRIEF TIME TO PREVENT MULTIPLE SUBMITS
        var button = $(this).find("input[type='submit']");
    
        setTimeout(function() {
            button.attr("disabled", true);
        },500);
        
        setTimeout(function() {
            button.removeAttr('disabled');
        },6000);

    }); // end $("form").submit(function()

    $('input[type="file"]').change(function(){
      
      var f = this.files[0];  
      var name = f.name;
      
      $(this).closest('.file-container').find('.upload-path').text(name);
      
    });

    // RETRIEVING FORM LOCATION & POSITION DATA
    $("#location").change(function() {
        $("#position").load("get_positions.php?location=" + $("#location").val());
        setTimeout(function() {
            $("#position").selectpicker('refresh');
        }, 500);

        if ($("#position").val() !== '') {
            $('#position-questions').empty();
        }
    });
    $("#position").change(function() {
        $("#position-questions").load("get_positions.php?position=" + $("#position").val());
    });
});