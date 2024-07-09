(($) => {
  $(document).ready(function () {
    $("#send-referral-btn").on("click", function (e) {
      e.preventDefault();
      console.log("clicked");
      $("#referral-popup").css("display", "block");
    });

    $(".bb-close-referral-popup").on("click", function (e) {
      e.preventDefault();
      $("#referral-popup").css("display", "none");
    });

    // Handle form submission
    $("#send-referral").on("click", function (e) {
      e.preventDefault();

      // Perform form validation
      var isValid = validateForm();

      if (!isValid) {
        return; // If validation fails, do not proceed with the AJAX request
      }

      // Gather form data
      var formData = {
        ref_name: $("#name").val(),
        ref_email: $("#email").val(),
        ref_phoneno: $("#phoneno").val(),
        ref_message: $("#message").val(),
        ref_recipient_id: $("#recipient_id").val(),
        type_of_referral: $("#type_of_referral").val(),
      };

      // AJAX request
      $.ajax({
        type: "POST",
        url: MyAjax.ajaxurl,
        data: {
          action: "save_referral_data",
          nonce: MyAjax.nonce,
          formData: formData,
        },
        success: function (response) {
          // Handle success response (you can update the UI, close the popup, etc.)
          let message = "Please enter valid details.";
          let status = false;
          if (response.success === true) {
            message = response.data;
            status = true;
          }

          const error = `<div class="alert ${
            status ? "alert-success" : "alert-danger"
          }" role="alert">${message}</div>`;
          $("#item-header-content .bb-user-content-wrap").append(error);

          // Remove the success message after 5 seconds
          if (status) {
            setTimeout(function () {
              $(".alert-success").remove();
            }, 5000);
          }
        },
        error: function (error) {
          // Handle error
          console.log(error);
        },
      });

      // Close the popup
      $("#referral-popup").hide();
    });

    // Function to validate form fields
    function validateForm() {
      var isValid = true;

      // Reset previous error messages
      $(".error-message").remove();

      // Validate name
      var name = $("#name").val();
      if (!name) {
        isValid = false;
        $("#name").after(
          '<div class="error-message">Please enter a name.</div>'
        );
      }

      // Validate email
      var email = $("#email").val();
      if (!email || !isValidEmail(email)) {
        isValid = false;
        $("#email").after(
          '<div class="error-message">Please enter a valid email address.</div>'
        );
      }

      // Validate phone number
      var phoneno = $("#phoneno").val();
      if (!phoneno) {
        isValid = false;
        $("#phoneno").after(
          '<div class="error-message">Please enter a phone number.</div>'
        );
      }

      // Validate message
      var message = $("#message").val();
      if (!message) {
        isValid = false;
        $("#message").after(
          '<div class="error-message">Please enter a message.</div>'
        );
      }

      // Validate type of referral
      var type_of_referral = $("#type_of_referral").val();
      if (!type_of_referral) {
        isValid = false;
        $("#type_of_referral").after(
          '<div class="error-message">Please select a type of referral.</div>'
        );
      }

      return isValid;
    }

    // Function to validate email address
    function isValidEmail(email) {
      // Use a simple email validation regex
      var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return emailRegex.test(email);
    }

    // Deactivate Member Popup
    $(".toggle-member").on("change", function () {
      var userId = $(this).data("user-id");
      $("#deactivatePopup-" + userId).css("display", "flex");
    });

    // Change Chapter popup
    $(".change-chapter").on("change", function (e) {
      e.preventDefault();

      $(".change-chapter-popup").css("display", "block");
    });
    $(".change-chapter-cancel").on("click", function (e) {
      e.preventDefault();

      $(".change-chapter-popup").css("display", "none");
    });

    // deactivate member popup
    $(".deactive-member").on("click", function (e) {
      e.preventDefault();

      $(".deactivat-member-popup").css("display", "block");
    });
    $(".deactivate-member-cancel").on("click", function (e) {
      e.preventDefault();

      $(".deactivat-member-popup").css("display", "none");
    });
  });
})(jQuery);

($ => {
  const approveMemberEl = $('.approve-new-member input.approve-member');
  
  if ( approveMemberEl ) {
    approveMemberEl.click( function () {

      if ( !$(this).is(':checked') ) return;

      let memberId = $(this).data('user-id');

      if (memberId == '') return;
      
      $.ajax({
        url: MyAjax.ajaxurl,
        method: "POST",
        dataType: 'json',
        data: {
          action: 'approve_member_ajax',
          nonce: MyAjax.approve_member_nonce,
          user_id: memberId
        },
        success: function (response) {
          console.log(response);
        }
      });
    });
  }
})(jQuery); 