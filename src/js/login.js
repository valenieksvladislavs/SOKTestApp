$(document).ready(function() {
  const validator = $("#loginForm").validate({
    rules: {
      username: {
        required: true
      },
      password: {
        required: true
      }
    },
    messages: {
      username: {
        required: "Please enter a username"
      },
      password: {
        required: "Please provide a password"
      }
    },

    errorElement: "div",
    errorClass: "invalid-feedback",

    highlight: function(element) {
      $(element).addClass("is-invalid").removeClass("is-valid");
    },
    unhighlight: function(element) {
      $(element).removeClass("is-invalid").addClass("is-valid");
    },
    errorPlacement: function(error, element) {
      error.insertAfter(element);
    },
    
    submitHandler: function(form) {
      hideSystemError();
      
      $.ajax({
        url: $(form).attr('action'),
        method: $(form).attr('method'),
        data: $(form).serialize() + '&login=true',
        dataType: 'json',
        success: function(response) {
          if (response.success) {
            window.location.href = location.origin + '/admin';
          } else {
            if (response.errors) {
              if (response.errors.system) {
                showSystemError(response.errors.system);
              }
              
              validator.resetForm();
              
              let fieldErrors = {};
              for (let fieldName in response.errors) {
                if (fieldName !== 'system') {
                  fieldErrors[fieldName] = response.errors[fieldName];
                }
              }
              validator.showErrors(fieldErrors);
            } else {
              showSystemError("Server error or network issue occurred");
            }
          }
        },
        error: function() {
          showSystemError("Server error or network issue occurred");
        }
      });
    }
  });
  
  function showSystemError(messages) {
    if (Array.isArray(messages)) {
      for (let message of messages) {
        $("#systemError").append(`<div>${message}</div>`)
      }
    } else {
      $("#systemError").text(`${messages}`)
    }
    $("#systemError").removeClass("d-none");
  }
  function hideSystemError() {
    $("#systemError")
      .addClass("d-none")
      .text("");
  }
});
