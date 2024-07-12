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
                            'type' => esc_html($field['type'])
                        )
                    );
                    break;
            }
            
            
        }
    }
}
add_action( 'admin_init', 'ssnt_register_settings' );

function ssnt_my_options_callback($args) {
    $setting = get_option( 'ssnt_'.$args['name'] );
    
    switch ($args['type']) {
        case "plain_text":
            ?>
            <input type="text" <?php echo $args['required']===true?'required':'' ?> size=<?php echo $args['size'] ?>  name=<?php echo 'ssnt_'.$args['name'] ?> value="<?php echo isset( $setting ) ? esc_attr( $setting ) : ''; ?>">
            <?php
            break;
        case "phone":
            ?>
            <input type="tel" <?php echo $args['required']===true?'required':'' ?> size=<?php echo $args['size'] ?> pattern="[0-9]{3}[0-9]{3}[0-9]{4}" name=<?php echo 'ssnt_'.$args['name'] ?> value="<?php echo isset( $setting ) ? esc_attr( $setting ) : ''; ?>">
            <?php
            break;
        case "email":
            ?>
            <input type="email" <?php echo $args['required']===true?'required':'' ?> size=<?php echo $args['size'] ?> pattern=".+@example\.com" name=<?php echo 'ssnt_'.$args['name'] ?> value="<?php echo isset( $setting ) ? esc_attr( $setting ) : ''; ?>">
            <?php
            break;
    }
    
}





