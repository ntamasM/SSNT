jQuery(document).ready(function($) {
    var file_frame;

    $(document).on('click', '.ssnt_image_upload_button', function(event) {
        event.preventDefault();

        var button = $(this);
        var field = $('#' + button.data('field-id'));

        if (file_frame) {
            file_frame.open();
            return;
        }

        file_frame = wp.media.frames.file_frame = wp.media({
            title: 'Select or Upload an Image',
            button: {
                text: 'Use this image'
            },
            multiple: false
        });

        file_frame.on('select', function() {
            var attachment = file_frame.state().get('selection').first().toJSON();
            field.val(attachment.url);
            field.siblings('.ssnt_image_preview').html('<img src="' + attachment.url + '" style="max-width: 300px;" />');
        });

        file_frame.open();
    });
});