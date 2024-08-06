<?php
// Display settings page content
function ssnt_settings_page() {
    ?>
    <div class="wrap">
        <h1>Site Settings from Ntamas</h1>
        <form action="options.php" method="post">
            <?php
            settings_fields( 'ssnt_settings_group' );
            do_settings_sections( 'ssnt-settings' );
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register settings
function ssnt_register_settings() {

    add_settings_section(
        'ssnt_settings_section',
        '',
        '',
        'ssnt-settings'
    );
    
    $ssnt_dynamic_fields = get_option('ssnt_dynamic_fields');
    if ($ssnt_dynamic_fields) {
        $lastSection = "ssnt_settings_section";
        foreach ($ssnt_dynamic_fields as $field) {
            switch ($field['type']) {
                case "heading":
                    add_settings_section(
                        'ssnt_settings_section_'.esc_html($field['name']),
                        esc_html($field['label']),
                        '',
                        'ssnt-settings'
                    );
                    $lastSection = 'ssnt_settings_section_'.esc_html($field['name']);
                    break;
                case "image":
                    register_setting('ssnt_settings_group', 'ssnt_'.esc_html($field['name']));
                    add_settings_field(
                        'ssnt_'.esc_html($field['name']),
                        esc_html($field['label']),
                        'ssnt_image_upload_callback',
                        'ssnt-settings',
                        $lastSection,
                        array (
                            'label' => esc_html($field['label']),
                            'name' => esc_html($field['name']),
                            'type' => esc_html($field['type']),
                            'required' => esc_html($field['required'])
                        )
                    );
                    break;
                default:
                    register_setting( 'ssnt_settings_group', 'ssnt_'.esc_html($field['name']) );
                    add_settings_field(
                        'ssnt_'.esc_html($field['name']),
                        esc_html($field['label']),
                        'ssnt_my_options_callback',
                        'ssnt-settings',
                        $lastSection,
                        array (
                            'label' => esc_html($field['label']),
                            'name' => esc_html($field['name']),
                            'type' => esc_html($field['type']),
                            'required' => esc_html($field['required']),
                            'size' => esc_html($field['size'])
                        )
                    );
                    break;
            }
            
            
        }
    }
}
add_action( 'admin_init', 'ssnt_register_settings' );

function ssnt_image_upload_callback($args) {
    $option = get_option('ssnt_'.$args['name']);
    ?>
    <input type="hidden" id="ssnt_<?php echo esc_attr($args['name']); ?>" name="ssnt_<?php echo esc_attr($args['name']); ?>" value="<?php echo esc_attr($option); ?>" />
    <input type="button" class="button" id="ssnt_<?php echo esc_attr($args['name']); ?>_button" value="Upload Image" />
    <div id="ssnt_<?php echo esc_attr($args['name']); ?>_preview" style="margin-top: 10px;">
        <?php if ($option) : ?>
            <img src="<?php echo esc_url($option); ?>" style="max-width: 300px;" />
        <?php endif; ?>
    </div>
    <script>
        jQuery(document).ready(function($) {
            var file_frame;
            $('#ssnt_<?php echo esc_attr($args['name']); ?>_button').on('click', function(event) {
                event.preventDefault();
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
                    $('#ssnt_<?php echo esc_attr($args['name']); ?>').val(attachment.url);
                    $('#ssnt_<?php echo esc_attr($args['name']); ?>_preview').html('<img src="'+attachment.url+'" style="max-width: 300px;" />');
                });
                file_frame.open();
            });
        });
    </script>
    <?php
}

function ssnt_my_options_callback($args) {
    $setting = get_option( 'ssnt_'.$args['name'] );
    
    switch ($args['type']) {
        case "plain_text":
            ?>
            <input type="text" <?php echo isset($args['required'])?'required':'' ?> size=<?php echo $args['size'] ?>  name=<?php echo 'ssnt_'.$args['name'] ?> value="<?php echo isset( $setting ) ? esc_attr( $setting ) : ''; ?>">
            <?php
            break;
        case "phone":
            ?>
            <input type="tel" <?php echo isset($args['required'])?'required':'' ?> size=<?php echo $args['size'] ?> pattern="[0-9]{3}[0-9]{3}[0-9]{4}" name=<?php echo 'ssnt_'.$args['name'] ?> value="<?php echo isset( $setting ) ? esc_attr( $setting ) : ''; ?>">
            <?php
            break;
        case "email":
            ?>
            <input type="email" <?php echo isset($args['required'])?'required':'' ?> size=<?php echo $args['size'] ?> pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" name=<?php echo 'ssnt_'.$args['name'] ?> value="<?php echo isset( $setting ) ? esc_attr( $setting ) : ''; ?>">
            <?php
            break;
    }
    
}