<?php

function ssnt_get_dynamic_field($field_name) {
    return get_option('ssnt_' . $field_name);
}

add_filter( 'bricks/code/echo_function_names', function() {
  return [
    'ssnt_get_dynamic_field',
  ];
} );

add_filter( 'bricks/dynamic_tags_list', 'add_my_tag_to_builder' );
function add_my_tag_to_builder( $tags ) {
  
    $ssnt_dynamic_fields = get_option('ssnt_dynamic_fields', []);
    foreach ($ssnt_dynamic_fields as $field) {
        if ($field['type'] !== 'heading') {
            $tags[] = [
                'name'  => "{".$field['name']."}",
                'label' => $field['label'],
                'group' => 'SSNT Fields',
            ];
        }
    }

  return $tags;
}

add_filter( 'bricks/dynamic_data/render_tag', 'get_my_tag_value', 10, 3 );

function get_my_tag_value( $tag, $post, $context = 'text' ) {
    
    // $tag is the tag name without the curly braces
    
    $ssnt_dynamic_fields = get_option('ssnt_dynamic_fields', []);
    foreach ($ssnt_dynamic_fields as $field) {
        if ( $tag === $field['name']) {
            $value = ssnt_get_dynamic_field( $field['name'] );
            $tag = str_replace( "{".$field['name']."}", $value, $tag );
        }
    }
    return $tag;
}


add_filter( 'bricks/dynamic_data/render_content', 'render_my_tag', 10, 3 );
add_filter( 'bricks/frontend/render_data', 'render_my_tag', 10, 2 );

function render_my_tag( $content, $post, $context='text' ) {
    
    // $content might consists of HTML and other dynamic tags
    
    $ssnt_dynamic_fields = get_option('ssnt_dynamic_fields', []);
    foreach ($ssnt_dynamic_fields as $field) {
            
        if ( strpos( $content, "{".$field['name']."}" ) !== false ) {
            $value = ssnt_get_dynamic_field( $field['name'] );
            $content = str_replace( "{".$field['name']."}", $value, $content );
        }
    }
    return $content;

}