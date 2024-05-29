jQuery(document).ready(function($) {
    $('#resource-filter').on('submit', function(event) {
        event.preventDefault();

        var filter = $(this);
        $.ajax({
            url: rfajax.ajaxurl,
            type: 'POST',
            data: filter.serialize() + '&action=resource_filter',
            success: function(response) {
                $('#resource-results').html(response);
            }
        });
    });
});
