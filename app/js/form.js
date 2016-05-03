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
        
        // Age Validation
        if ($("input[name='over21']:checked").length > 0) {
          // one ore more checkboxes are checked
        }
        else{
            alert('Age confirmation is required.');
            $("#" + formprefix + " [name='over21']").focus();
            return false;
        }

        // Location validation
        if($("#" + formprefix + " [name='location'] :selected").val() == '') {
            alert('Location is required.');
            $("#" + formprefix + " [name='location']").focus();
            return false;
        } // end location validation

        // Driver/Delivery service experience validation
        if($("#" + formprefix + " [name='driverExperience'] :selected").val() == '') {
            alert('"Years of (driver/delivery service) experience?" is a required field. Please select an answer from the dropdown menu.');
            $("#" + formprefix + " [name='driverExperience']").focus();
            return false;
        } // end driverExperience validation

        // Salary range validation/
        if($("#" + formprefix + " [name='txtSalary']").val() == "") {
            alert("Salary field is required.");
            $("#" + formprefix + " [name='txtSalary']").focus();
            return false;
        } // end txtSalary validation

        // Type of employment validation
        // Multi-select
        var chkLocation = 0;
        if($("#" + formprefix + " [name='typeEmployment[]'] :selected").val() == null){
            alert('Type of employment is required.');
            $("#" + formprefix + " [name='typeEmployment[]']").focus();
            return false;
        } // end typeEmployment[] validation

        // Driver's License Validation
        if ($("#" + formprefix + " [name='driverLicense']:checked").length > 0) {
          // one ore more checkboxes are checked
        }
        else{
            alert('Please check "Yes" or "No" for driver\'s license.');
            $("#" + formprefix + " [name='driverLicense']").focus();
            return false;
        }

        // CDL Validation
        if ($("#" + formprefix + " [name='cdl']:checked").length > 0) {
          // one ore more checkboxes are checked
        }
        else{
            alert('Please check "Yes" or "No" for commercial driver\'s license (CDL).');
            $("#" + formprefix + " [name='cdl']").focus();
            return false;
        }

        // Hazmat Endorsement Validation
        if ($("#" + formprefix + " [name='hazMatEndorsement']:checked").length > 0) {
          // one ore more checkboxes are checked
        }
        else{
            alert('Please check "Yes" or "No" for hazmat endorsement.');
            $("#" + formprefix + " [name='hazMatEndorsement']").focus();
            return false;
        }

        // Hazmat experience validation
        if ( $("#" + formprefix + " [name='hazMatEndorsement']:checked").val() == 'Yes') {
            if($("#" + formprefix + " [name='hazMatExperience'] :selected").val() == '') {
                alert('"Years of hazardous materials experience?" is a required field. Please select an answer from the dropdown menu.');
                $("#" + formprefix + " [name='hazMatExperience']").focus();
                return false;
            } // end hazMatExperience validation
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

        var button = $(this).find("input[type='submit']");
    
        setTimeout(function() {
            button.attr("disabled", true);
        },500);
        
        setTimeout(function() {
            button.removeAttr('disabled');
        },6000);

    }); // end $("form").submit(function()

    // Hide the field initially
    $("#form0 .conditional-answer").hide();
    $("#form1 .conditional-answer").hide();

    // Show the text field only when the 'yes' option is chosen
    $('#form0 .conditional-question').change(function() {
        if($('#form0  input[name="hazMatEndorsement"]:checked').val() === "Yes") {
            $("#form0 .conditional-answer").show();
        }
        else {
            $("#form0 .conditional-answer").hide();
        }
    });

    $('#form1 .conditional-question').change(function() {
        if($('#form1  input[name="hazMatEndorsement"]:checked').val() === "Yes") {
            $("#form1 .conditional-answer").show();
        }
        else {
            $("#form1 .conditional-answer").hide();
        }
    });

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
        }, 200);

        if ($("#position").val().length > 1 ) {
            $('#position-questions').empty();
        }
    });
    $("#position").change(function() {
        $("#position-questions").load("get_positions.php?position=" + $("#position").val());
    });
});