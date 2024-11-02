 // Function to get URL parameters
 function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    const results = regex.exec(window.location.search);
    return results === null ? null : decodeURIComponent(results[1].replace(/\+/g, ' '));
}

jQuery(document).ready(function($) {
   
    // Set selectedMonth from the URL parameter 'm'
    const selectedMonth = getUrlParameter('m');

    $('#export_logs').on('click', function() {
       
        $.ajax({
            url: logaction_ajax.ajax_url,
            type: 'GET',
            data: {
                action: 'export_logs',
                m: selectedMonth // Action hook
            },
            xhrFields: {
                responseType: 'blob' // Expect a binary response
           
            },
            success: function(data, status, xhr) {
                // Create a URL for the blob and trigger a download
                const url = window.URL.createObjectURL(data);
                const a = document.createElement('a');
                a.href = url;
                const date = new Date();
                const formattedDate = date.toISOString().slice(0, 10); // YYYY-MM-DD format
                const hours = date.getHours();
                const minutes = date.getMinutes().toString().padStart(2, '0');
                const amPm = hours >= 12 ? 'PM' : 'AM';
                const formattedTime = `${(hours % 12) || 12}_${minutes}-${amPm}`;
                a.download = `logs_export_${formattedDate}_${formattedTime}.csv`;
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Export failed: ', textStatus, errorThrown);
                alert('An error occurred while exporting the logs.');
            }
        });
    });
});
