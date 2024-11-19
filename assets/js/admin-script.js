jQuery(document).ready(function ($) {
    // Function to get selected checkbox values
    $('#doaction').on('click', function (e) {
        e.preventDefault(); // Prevent the default form submission (if applicable)

        let trash = $('#bulk-action-selector-top').val();
        if (trash === 'trash') {
            console.log(trash);
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
                        console.log(response);
                        if (response.success) {
                            // Display admin message.
                            const adminMessage = `<div id="message" class="updated notice notice-success is-dismissible">
                                <p>Log deleted succefully</p>
                            </div>`;
                            $('#wpbody-content').prepend(adminMessage);
                            // Delay reloading the page for 5 seconds.
                            setTimeout(function () {
                                location.href =response.data;
                            }, 5000);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log('err', error);
                    }
                });
            } else {
                return;
            }
        }



    })


    // Handles UI Script
    const $checkbox = $('#allow_users_view_logs');
    const $userViewOptions = $('#user-view-options');
    function toggleUserViewOptions() {
        if ($checkbox.is(':checked')) {
            $userViewOptions.show();
        } else {
            $userViewOptions.hide();
        }
    }

    // Attach event listener to the checkbox
    $checkbox.on('change', toggleUserViewOptions);
    toggleUserViewOptions();
})
