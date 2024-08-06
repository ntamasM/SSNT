<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class SSNT_Dynamic_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() {
        return 'ssnt_dynamic_tag';
    }

    public function get_title() {
        return 'SSNT Dynamic Tag';
    }

    public function get_group() {
        return 'site';
    }

    public function get_categories() {
        return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ];
    }

    protected function register_controls() {
        $this->add_control(
            'field_name',
            [
                'label' => esc_html__( 'Field Name', 'plugin-name' ),
                'type' => \Elementor\Controls_Manager::TEXT,
            ]
        );
    }

    public function render() {
        $field_name = $this->get_settings( 'field_name' );
        if ( ! empty( $field_name ) ) {
            echo ssnt_get_dynamic_field( $field_name );
        }
    }
}
