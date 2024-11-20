jQuery(document).ready(function ($) {
    $('#progress-loader').hide();
    // Function to get selected checkbox values
    $('#doaction').on('click', function (e) {
        e.preventDefault(); // Prevent the default form submission (if applicable)
        $('#progress-loader').show();
        let trash = $('#bulk-action-selector-top').val();
        if (trash === 'trash') {
            // Collect all checked checkboxes with name "logs[]"
            var selectedLogs = $('input[name="logs[]"]:checked').map(function () {
                return $(this).val(); // Get the value attribute of each selected checkbox
            }).get();

            // Perform further actions, e.g., AJAX request
            if (selectedLogs.length > 0) {
                // Example: Send selected logs via AJAX
                $.ajax({
                    url: logaction_ajax.ajax_url, // WordPress AJAX URL
                    method: 'POST',
                    data: {
                        action: 'delete_selected_logs', // Custom action hook
                        logs: selectedLogs, // Pass the selected log IDs
                        _wpnonce: logaction_ajax.nonce
                    },
                    success: function (response) {
                        $('#progress-loader').hide();
                        if (response.success) {
                            
                            // Display admin message.
                            const adminMessage = `<div id="message" class="updated notice notice-success is-dismissible">
                                <p>Log deleted succefully</p>
                            </div>`;
                            $('#wpbody-content').prepend(adminMessage);
                            // Delay reloading the page for 5 seconds.
                            setTimeout(function () {
                                location.href = response.data;
                            }, 5000);
                        }
                    },
                    error: function (xhr, status, error) {
                        $('#progress-loader').hide();
                        const adminMessage = `<div id="message" class="updated notice notice-error is-dismissible">
                                <p>Failed to delete log ${error}</p>
                            </div>`;
                            $('#wpbody-content').prepend(adminMessage);
                    }
                });
            } else {
                return;
            }
        }



    })



    // Handles ajax function to delete logs from table.
    jQuery(document).ready(function ($) {
        // Attach the click event to the "empty-logs" button
        $('#empty-logs').on('click', function (e) {
            e.preventDefault();
            // Append the modal to the body if it doesn't already exist
            if (!$('#warningModal').length) {
                $('body').append(modalHtml);
            }
            // Show the modal
            $('#warningModal').modal('show');

            // Handle the confirmation button click
            $('#confirm-delete-logs').on('click', function () {
                $('#progress-loader').show();
                // Perform the log deletion (e.g., an AJAX call)
                $.ajax({
                    url: ajaxurl, // WordPress global AJAX URL
                    type: 'POST',
                    data: {
                        action: 'delete_all_logs',
                        _wpnonce: logaction_ajax.nonce, // Pass your nonce for security
                    },
                    success: function (response) {
                        $('#progress-loader').hide();
                        if (response.success) {
                            $('<div class="notice notice-success is-dismissible"><p>' + response.data + '</p></div>').insertBefore('#empty-logs');
                       
                        } else {
                            $('<div class="notice notice-error is-dismissible"><p>' + response.data + '</p></div>').insertBefore('#empty-logs');
                        }
                        // }
                    },
                    error: function () {
                        alert('An error occurred while deleting logs.');
                        $('#progress-loader').hide();
                    },
                });
                $('#progress-loader').hide();
                // Hide the modal after the action
                $('#warningModal').modal('hide');
            });
        });
    });

})
