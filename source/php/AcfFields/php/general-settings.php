<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_694a9d25909d8',
    'title' => __('Service Information Settings', 'modularity-service-info'),
    'fields' => array(
        0 => array(
            'key' => 'field_694a9d267cd58',
            'label' => __('Slug', 'modularity-service-info'),
            'name' => 'slug',
            'aria-label' => '',
            'type' => 'text',
            'instructions' => __('The slug you want for the singular posts, defaults to service-information. Don\'t forget to flush your permalink rules after changing.', 'modularity-service-info'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'maxlength' => '',
            'allow_in_bindings' => 0,
            'placeholder' => __('service-information', 'modularity-service-info'),
            'prepend' => '',
            'append' => '',
        ),
        1 => array(
            'key' => 'field_694a9e4303cda',
            'label' => __('Service information page', 'modularity-service-info'),
            'name' => 'service_information_page',
            'aria-label' => '',
            'type' => 'post_object',
            'instructions' => __('The page where you present service information', 'modularity-service-info'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'post_type' => array(
                0 => 'page',
            ),
            'post_status' => array(
                0 => 'publish',
            ),
            'taxonomy' => '',
            'return_format' => 'id',
            'multiple' => 0,
            'save_custom' => 0,
            'save_post_status' => 'publish',
            'acfe_bidirectional' => array(
                'acfe_bidirectional_enabled' => '0',
            ),
            'allow_null' => 0,
            'allow_in_bindings' => 0,
            'bidirectional' => 0,
            'ui' => 1,
            'bidirectional_target' => array(
            ),
            'save_post_type' => '',
        ),
        2 => array(
            'key' => 'field_6953e2f70b4e1',
            'label' => __('LiteSpeed ESI cache support', 'modularity-service-info'),
            'name' => 'litespeed_esi_cache_support',
            'aria-label' => '',
            'type' => 'true_false',
            'instructions' => __('Try to add support for updating the badge even though the page is cached with LiteSpeed. This will use LiteSpeed ESI to properly update only the badge part of the page.', 'modularity-service-info'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'message' => '',
            'default_value' => 0,
            'allow_in_bindings' => 0,
            'ui_on_text' => '',
            'ui_off_text' => '',
            'ui' => 1,
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'service-information-settings',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
    'show_in_rest' => 0,
    'display_title' => '',
    'acfe_autosync' => array(
        0 => 'json',
    ),
    'acfe_form' => 0,
    'acfe_meta' => '',
    'acfe_note' => '',
));
}