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
            'type' => 'page_link',
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
            'allow_archives' => 0,
            'multiple' => 0,
            'allow_null' => 0,
            'allow_in_bindings' => 0,
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