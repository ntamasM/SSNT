<?php

// Display additional settings page content
function ssnt_additional_settings_page() {
    if (!current_user_can('administrator')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }
    ?>
    <div class="wrap">
        <h1>SSNT Additional Settings</h1>
        <form id="ssnt-settings-form" action="options.php" method="post">
            <?php
            settings_fields('ssnt_additional_settings_group');
            do_settings_sections('ssnt-additional-settings');
            ?>
            <button type="button" id="add-field-button">Add New Field</button>
            <div id="dynamic-fields-container"></div>
            <?php submit_button(); ?>
        </form>
    </div>
    <!-- Include Sortable.js -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var container = document.getElementById('dynamic-fields-container');
            var savedFields = JSON.parse(localStorage.getItem('dynamicFields')) || <?php echo json_encode(get_option('ssnt_dynamic_fields', [])); ?>;

            savedFields.forEach(function(field, index) {
                addField(container, field.label, field.name, field.type, field.required, field.size, index);
            });

            document.getElementById('add-field-button').addEventListener('click', function() {
                addField(container);
            });

            document.getElementById('ssnt-settings-form').addEventListener('submit', function(event) {
                if (hasDuplicateNames()) {
                    event.preventDefault();
                    alert('Field names must be unique. Please correct the duplicates.');
                } else {
                    saveFieldsToLocalStorage();
                }
            });

            // Initialize Sortable on the dynamic fields container
            new Sortable(container, {
                animation: 150,
                handle: '.drag-handle',
                onEnd: saveFieldsToLocalStorage // Save the order when dragging ends
            });
        });

        function addField(container, label = '', name = '', type = 'heading', required = false, size = '', index = null) {
            var fieldIndex = index !== null ? index : container.children.length;
            var fieldHtml = `
                <div class="dynamic-field" draggable="true">
                    <div class="left">
                        <span class="drag-handle dashicon dashicons dashicons-menu"></span>
                        <label>Label: <input type="text" name="ssnt_dynamic_fields[${fieldIndex}][label]" value="${label}" class="field-label" /></label>
                        <label>Name: <input type="text" name="ssnt_dynamic_fields[${fieldIndex}][name]" value="${name}" class="field-name" /></label>
                        <label>Type: 
                            <select name="ssnt_dynamic_fields[${fieldIndex}][type]">
                                <option value="heading" ${type === 'heading' ? 'selected' : ''}>Heading</option>
                                <option value="plain_text" ${type === 'plain_text' ? 'selected' : ''}>Plain Text</option>
                                <option value="phone" ${type === 'phone' ? 'selected' : ''}>Phone</option>
                                <option value="email" ${type === 'email' ? 'selected' : ''}>E-mail</option>
                            </select>
                        </label>
                        <label>Required: <input type="checkbox" name="ssnt_dynamic_fields[${fieldIndex}][required]" ${required ? 'checked' : ''} /></label>
                        <label>Input Field Size: <input type="number" size="10" min="40" max="1000" name="ssnt_dynamic_fields[${fieldIndex}][size]" value="${size}" class="field-size" /></label>
                    </div>
                    <div class="right">
                        <button type="button" class="delete-field-button">❌️</button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', fieldHtml);

            // Add event listeners for new elements
            container.querySelectorAll('.delete-field-button').forEach(function(button) {
                button.addEventListener('click', function() {
                    button.parentElement.parentElement.remove();
                    saveFieldsToLocalStorage();
                });
            });

            container.querySelectorAll('.field-name').forEach(function(nameInput) {
                nameInput.addEventListener('blur', function() {
                    if (nameInput.value === '') {
                        var labelInput = nameInput.closest('.dynamic-field').querySelector('.field-label');
                        nameInput.value = labelToName(labelInput.value);
                    }
                });
            });
        }

        function labelToName(label) {
            return label.toLowerCase().replace(/[^a-z0-9]+/g, '_');
        }

        function saveFieldsToLocalStorage() {
            var container = document.getElementById('dynamic-fields-container');
            var fields = [];
            container.querySelectorAll('.dynamic-field').forEach(function(field, index) {
                fields.push({
                    label: field.querySelector('[name$="[label]"]').value,
                    name: field.querySelector('[name$="[name]"]').value,
                    type: field.querySelector('[name$="[type]"]').value,
                    required: field.querySelector('[name$="[required]"]').checked,
                    size: field.querySelector('[name$="[size]"]').value,
                    index: index
                });
            });
            localStorage.setItem('dynamicFields', JSON.stringify(fields));
        }

        function hasDuplicateNames() {
            var names = [];
            var hasDuplicates = false;
            document.querySelectorAll('.field-name').forEach(function(nameInput) {
                var nameValue = nameInput.value.trim();
                if (names.includes(nameValue)) {
                    hasDuplicates = true;
                } else {
                    names.push(nameValue);
                }
            });
            return hasDuplicates;
        }
    </script>
    <?php
}

// Register additional settings
function ssnt_register_additional_settings() {
    register_setting('ssnt_additional_settings_group', 'ssnt_dynamic_fields', 'ssnt_sanitize_ssnt_dynamic_fields');

    add_settings_section(
        'ssnt_additional_settings_section',
        'Additional Settings',
        'ssnt_additional_settings_section_callback',
        'ssnt-additional-settings'
    );
}

add_action('admin_init', 'ssnt_register_additional_settings');

function ssnt_sanitize_ssnt_dynamic_fields($fields) {
    $sanitized_fields = array();
    foreach ($fields as $field) {
        $sanitized_fields[] = array(
            'label' => sanitize_text_field($field['label']),
            'name' => sanitize_text_field($field['name']),
            'type' => sanitize_text_field($field['type']),
            'required' => isset($field['required']) ? (bool) $field['required'] : false,
            'size' => isset($field['size']) ? absint($field['size']) : '',
        );
    }
    return $sanitized_fields;
}
add_action( 'admin_init', 'ssnt_register_additional_settings' );

function ssnt_additional_settings_section_callback() {
    echo 'Configure your additional settings below:';
}

function ssnt_field_label_callback() {
    $label = get_option( 'ssnt_field_label' );
    ?>
    <input type="text" name="ssnt_field_label" value="<?php echo isset( $label ) ? esc_attr( $label ) : ''; ?>">
    <?php
}

function ssnt_field_name_callback() {
    $name = get_option( 'ssnt_field_name' );
    ?>
    <input type="text" name="ssnt_field_name" value="<?php echo isset( $name ) ? esc_attr( $name ) : ''; ?>">
    <?php
}

function ssnt_field_type_callback() {
    $type = get_option( 'ssnt_field_type' );
    ?>
    <select name="ssnt_field_type">
        <option value="heading" <?php selected( $type, 'heading' ); ?>>Heading</option>
        <option value="plain_text" <?php selected( $type, 'plain_text' ); ?>>Plain Text</option>
        <option value="phone" <?php selected( $type, 'phone' ); ?>>Phone</option>
        <option value="email" <?php selected( $type, 'email' ); ?>>E-mail</option>
    </select>
    <?php
}