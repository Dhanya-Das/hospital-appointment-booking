jQuery(document).ready(function($) {
    // Attach a click event handler to the submit button
    $('#submit-appointment').on('click', function(e) {
        e.preventDefault();

        // Get form data
        var formData = $('#appointment-booking-form').serialize();

        // Validate the form
        if (validateForm()) {
            // Make an AJAX request to the server
            $.ajax({
                type: 'POST',
                url: ajax_object.ajaxurl, 
                data: {
                    action: 'insert_appointment', 
                    form_data: formData
                },
                success: function(response) {
                    // Handle the response from the server
                    response = JSON.parse(response);

                    if (response.success) {
                        // Clear the form values
                        $('#appointment-booking-form input, #appointment-booking-form textarea, #appointment-booking-form select').val('');
    
                        // Show a success message
                        var message = $('<div class="alert alert-success">' + response.message + '</div>');
                        var submissionStatus = $('#submission-status');
            
                        submissionStatus.html(message);

                        // Automatically hide the message after 2000 milliseconds (2 seconds)
                        setTimeout(function() {
                            submissionStatus.empty();
                        }, 2000);
                    } else {
                        // Show an error message
                        var message_ = $('<div class="alert alert-danger">' + response.message + '</div>');
                        var submissionStatus_ = $('#submission-status');
            
                        submissionStatus_.html(message_);

                        // Automatically hide the message after 2000 milliseconds (2 seconds)
                        setTimeout(function() {
                            submissionStatus_.empty();
                        }, 2000);
                    }
                }
            });
        }
    });

    function validateForm() {
        // Add your form validation logic here
        
        if ($('#name').val() === '' || $('#emailid').val() === '' || $('#phone').val() === '' || $('#address').val() === '' || $('#doctor').val() === '' || $('#appointment-day').val() === '' || $('#appointment-time').val() === '') {
            alert('Please fill in all required fields.');
            return false;
        }

        return true;
    }
    
    
    // Update record
    
    // Show/hide the edit form when clicking the "Edit" button
    $('.edit-record').click(function() {
        const id = $(this).data('id');
        $('.edit-form[data-id="' + id + '"]').toggle();
    });

    $('.update-button').click(function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        const form = $('.edit-form[data-id="' + id + '"] .edit-appointment-form');
    
        // Perform an AJAX request to update the data
        $.ajax({
            url: ajaxurl, 
            type: 'POST',
            data: {
                action: 'update_appointment', 
                form: form.serialize()
            },
            success: function (response) {
                 // Handle the response from the server
                 response = JSON.parse(response);
    
                 if (response.success) {
                     
                     alert( response.message);
     
                     // Refresh the page after 1 seconds
                     setTimeout(function() {
                         location.reload(); // Refresh the page
                     }, 1000);
                 } else {
                    alert( response.message);
                     // Refresh the page after 1 seconds
                     setTimeout(function() {
                        location.reload(); // Refresh the page
                    }, 1000);
                 }
            }
        });
    });
    

    // Delete record
    $('.delete-record').on('click', function(e) {
        e.preventDefault();
        var recordId = $(this).data('id');
    
        // Send an AJAX request to delete the record
        $.ajax({
            type: 'POST',
            url: ajax_object.ajaxurl, 
            data: {
                action: 'delete_appointment',
                record_id: recordId
            },
            success: function(response) {
                // Handle the response from the server
                response = JSON.parse(response);
    
                if (response.success) {
                    alert( response.message);
                    // Refresh the page after 2 seconds
                    setTimeout(function() {
                        location.reload(); // Refresh the page
                    }, 2000);
                } else {
                    alert( response.message);
                    setTimeout(function() {
                        location.reload(); // Refresh the page
                    }, 2000);
                }
            }
        });
    });
    

    
});
