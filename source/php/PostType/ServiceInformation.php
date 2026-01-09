<?php

namespace ModularityServiceInfo\PostType;

use ModularityServiceInfo\Helper\Settings;

/**
 * Class ServiceInformation
 * 
 * Registers the Service Information custom post type and its taxonomy.
 * 
 * @package ModularityServiceInfo\PostType
 */ 
class ServiceInformation
{
    public const POST_TYPE_NAME = 'service_information';

    public function __construct()
    {
        // Register post type and taxonomy
        add_action('init', [$this, 'registerPostType']);
        add_action('init', [$this, 'registerTaxonomy']);

        // Remove advanced term settings from ACF if needed
        add_filter('acf/load_field_group', [$this, 'removeAdvancedTermSettings']);
    }

    /**
     * Register the Service Information custom post type
     * 
     * @return void
     */
    public function registerPostType(): void
    {
        $slug = Settings::getSlug();
        $archivePage = Settings::getArchivePage();
        
        $labels = [
            'name'                  => __('Service Information', 'modularity-service-info'),
            'singular_name'         => __('Service Information', 'modularity-service-info'),
            'menu_name'             => __('Service Information', 'modularity-service-info'),
            'name_admin_bar'        => __('Service Information', 'modularity-service-info'),
            'add_new'               => __('Add New', 'modularity-service-info'),
            'add_new_item'          => __('Add New Service Information', 'modularity-service-info'),
            'new_item'              => __('New Service Information', 'modularity-service-info'),
            'edit_item'             => __('Edit Service Information', 'modularity-service-info'),
            'view_item'             => __('View Service Information', 'modularity-service-info'),
            'all_items'             => __('All Service Information', 'modularity-service-info'),
            'search_items'          => __('Search Service Information', 'modularity-service-info'),
            'parent_item_colon'     => __('Parent Service Information:', 'modularity-service-info'),
            'not_found'             => __('No service information found.', 'modularity-service-info'),
            'not_found_in_trash'    => __('No service information found in Trash.', 'modularity-service-info'),
            'featured_image'        => __('Featured Image', 'modularity-service-info'),
            'set_featured_image'    => __('Set featured image', 'modularity-service-info'),
            'remove_featured_image' => __('Remove featured image', 'modularity-service-info'),
            'use_featured_image'    => __('Use as featured image', 'modularity-service-info'),
            'archives'              => __('Service Information Archives', 'modularity-service-info'),
            'insert_into_item'      => __('Insert into service information', 'modularity-service-info'),
            'uploaded_to_this_item' => __('Uploaded to this service information', 'modularity-service-info'),
            'filter_items_list'     => __('Filter service information list', 'modularity-service-info'),
            'items_list_navigation' => __('Service information list navigation', 'modularity-service-info'),
            'items_list'            => __('Service information list', 'modularity-service-info'),
        ];

        $args = [
            'labels'             => $labels,
            'description'        => __('Service information for the site', 'modularity-service-info'),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => [
                'slug' => $slug,
                'with_front' => false,
            ],
            'capability_type'    => 'post',
            'has_archive'        => empty($archivePage),
            'hierarchical'       => false,
            'menu_position'      => 20,
            'menu_icon'          => 'dashicons-info',
            'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions'],
            'show_in_nav_menus'  => false,
            'show_in_rest'       => true,
        ];

        register_post_type(self::POST_TYPE_NAME, $args);
    }

    /**
     * Register the Service Information Category taxonomy
     * 
     * @return void
     */
    public function registerTaxonomy(): void
    {
        $labels = [
            'name'                       => __('Service Categories', 'modularity-service-info'),
            'singular_name'              => __('Service Category', 'modularity-service-info'),
            'menu_name'                  => __('Service Categories', 'modularity-service-info'),
            'all_items'                  => __('All Service Categories', 'modularity-service-info'),
            'parent_item'                => __('Parent Service Category', 'modularity-service-info'),
            'parent_item_colon'          => __('Parent Service Category:', 'modularity-service-info'),
            'new_item_name'              => __('New Service Category Name', 'modularity-service-info'),
            'add_new_item'               => __('Add New Service Category', 'modularity-service-info'),
            'edit_item'                  => __('Edit Service Category', 'modularity-service-info'),
            'update_item'                => __('Update Service Category', 'modularity-service-info'),
            'view_item'                  => __('View Service Category', 'modularity-service-info'),
            'separate_items_with_commas' => __('Separate service categories with commas', 'modularity-service-info'),
            'add_or_remove_items'        => __('Add or remove service categories', 'modularity-service-info'),
            'choose_from_most_used'      => __('Choose from the most used', 'modularity-service-info'),
            'popular_items'              => __('Popular Service Categories', 'modularity-service-info'),
            'search_items'               => __('Search Service Categories', 'modularity-service-info'),
            'not_found'                  => __('Not Found', 'modularity-service-info'),
            'no_terms'                   => __('No service categories', 'modularity-service-info'),
            'items_list'                 => __('Service categories list', 'modularity-service-info'),
            'items_list_navigation'      => __('Service categories list navigation', 'modularity-service-info'),
        ];

        $args = [
            'labels'            => $labels,
            'description'       => __('Categories for service information', 'modularity-service-info'),
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud'     => true,
            'show_in_rest'      => true,
            'rewrite'           => ['slug' => 'service-category'],
        ];

        register_taxonomy('service_category', ['service_information'], $args);
    }

    /**
     * Remove advanced term settings ACF field group for specific taxonomy
     *
     * @param array $field_group
     * @return array|false
     */
    public function removeAdvancedTermSettings($field_group) {
        // Target this specific field group
        if ($field_group['key'] !== 'group_63e6002cc129c') {
            return $field_group;
        }

        // Only on taxonomy edit screens
        if (!is_admin() || empty($_GET['taxonomy'])) {
            return $field_group;
        }

        $taxonomy = sanitize_text_field($_GET['taxonomy']);

        // Disable for specific taxonomy
        if ($taxonomy === 'service_category') {
            return false; // ← removes the field group completely
        }

        return $field_group;
    }
}
