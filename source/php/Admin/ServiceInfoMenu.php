<?php

namespace ModularityServiceInfo\Admin;

/**
 * Class ServiceInfoMenu
 * 
 * Handles adding the service information archive link to the WordPress menu editor
 * and displays a notification badge with the count of published service info posts.
 * 
 * @package ModularityServiceInfo\Admin
 */
class ServiceInfoMenu
{
    private const ITEM_TYPE = 'service-info';
    private const ITEM_OBJECT = 'service-info-archive';
    private const POST_TYPE = 'service_information';

    /**
     * Constructor - Register all hooks
     */
    public function __construct()
    {
        $this->registerAdminHooks();
        $this->registerFrontendHooks();
    }

    /**
     * Register admin-related hooks
     * 
     * @return void
     */
    private function registerAdminHooks(): void
    {
        // Register custom menu item type for the Customizer
        add_filter('customize_nav_menu_available_item_types', [$this, 'registerCustomizerItemType']);
        add_filter('customize_nav_menu_available_items', [$this, 'addCustomizerItems'], 10, 4);
        
        // Classic menu editor
        add_action('admin_head-nav-menus.php', [$this, 'addServiceInfoMenuMetaBox']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminScripts']);
        
        // Setup the menu item when displayed
        add_filter('wp_setup_nav_menu_item', [$this, 'setupNavMenuItem']);
        
        // Handle AJAX add menu item
        add_action('wp_ajax_add-menu-item', [$this, 'ajaxAddMenuItem'], 0);
        
        // Exclude ACF field groups (unless disabled via filter) - delayed until plugins_loaded
        add_action('plugins_loaded', [$this, 'maybeRegisterAcfExclusion']);
    }

    /**
     * Register frontend-related hooks
     * 
     * @return void
     */
    private function registerFrontendHooks(): void
    {
        // Add notification badge to menu item on frontend
        add_filter('wp_get_nav_menu_items', [$this, 'addNotificationBadgeToMenuItems'], 10, 3);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get service information page URL
     * 
     * @return string|false
     */
    private function getServiceInfoUrl()
    {
        $url = get_field('service_information_page', 'service-information-settings');
        return $url ?: false;
    }

    /**
     * Get count of published service information posts
     * 
     * @return int
     */
    private function getServiceInfoCount(): int
    {
        $count = wp_count_posts(self::POST_TYPE);
        return isset($count->publish) ? (int) $count->publish : 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Customizer Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Register custom item type for the Customizer
     * 
     * @param array $item_types
     * @return array
     */
    public function registerCustomizerItemType($item_types)
    {
        $item_types[] = [
            'title'      => __('Service Information', 'modularity-service-info'),
            'type_label' => __('Service Information', 'modularity-service-info'),
            'type'       => self::ITEM_TYPE,
            'object'     => self::ITEM_OBJECT,
        ];
        
        return $item_types;
    }

    /**
     * Add items for the Customizer
     * 
     * @param array $items
     * @param string $type
     * @param string $object
     * @param int $page
     * @return array
     */
    public function addCustomizerItems($items, $type, $object, $page)
    {
        if ($type !== self::ITEM_TYPE || $object !== self::ITEM_OBJECT) {
            return $items;
        }

        $url = $this->getServiceInfoUrl();
        
        if (!$url) {
            return $items;
        }

        $items[] = [
            'id'         => self::ITEM_OBJECT,
            'title'      => __('Service Information', 'modularity-service-info'),
            'type'       => self::ITEM_TYPE,
            'type_label' => __('Service Information', 'modularity-service-info'),
            'object'     => self::ITEM_OBJECT,
            'url'        => $url,
        ];

        return $items;
    }

    /*
    |--------------------------------------------------------------------------
    | Classic Menu Editor Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Add meta box for service info archive in menu editor
     * 
     * @return void
     */
    public function addServiceInfoMenuMetaBox()
    {
        add_meta_box(
            'add-service-info-archive',
            __('Service Information', 'modularity-service-info'),
            [$this, 'serviceInfoMenuMetaBox'],
            'nav-menus',
            'side',
            'default'
        );
    }

    /**
     * Render the service info menu meta box
     * 
     * @return void
     */
    public function serviceInfoMenuMetaBox()
    {
        global $nav_menu_selected_id;
        
        $url = $this->getServiceInfoUrl();
        
        if (!$url) {
            echo '<p>' . __('No service information page set.', 'modularity-service-info') . '</p>';
            return;
        }

        $title = __('Service Information', 'modularity-service-info');
        ?>
        <div id="posttype-service-info-archive" class="posttypediv">
            <div id="tabs-panel-service-info-archive" class="tabs-panel tabs-panel-active">
                <ul id="service-info-archive-checklist" class="categorychecklist form-no-clear">
                    <li>
                        <label class="menu-item-title">
                            <input type="checkbox" class="menu-item-checkbox" name="menu-item[-1][menu-item-object-id]" value="<?php echo esc_attr(self::ITEM_OBJECT); ?>" />
                            <?php echo esc_html($title); ?>
                        </label>
                        <input type="hidden" class="menu-item-type" name="menu-item[-1][menu-item-type]" value="<?php echo esc_attr(self::ITEM_TYPE); ?>" />
                        <input type="hidden" class="menu-item-object" name="menu-item[-1][menu-item-object]" value="<?php echo esc_attr(self::ITEM_OBJECT); ?>" />
                        <input type="hidden" class="menu-item-title" name="menu-item[-1][menu-item-title]" value="<?php echo esc_attr($title); ?>" />
                        <input type="hidden" class="menu-item-url" name="menu-item[-1][menu-item-url]" value="<?php echo esc_url($url); ?>" />
                    </li>
                </ul>
            </div>
            <p class="button-controls wp-clearfix">
                <span class="add-to-menu">
                    <input type="submit"<?php wp_nav_menu_disabled_check($nav_menu_selected_id); ?> class="button submit-add-to-menu right" value="<?php esc_attr_e('Add to Menu', 'modularity-service-info'); ?>" name="add-post-type-menu-item" id="submit-posttype-service-info-archive" />
                    <span class="spinner"></span>
                </span>
            </p>
        </div>
        <?php
    }

    /**
     * Handle AJAX add menu item request for our custom type
     * 
     * @return void
     */
    public function ajaxAddMenuItem()
    {
        if (!isset($_POST['menu-item'])) {
            return;
        }

        $menu_items = $_POST['menu-item'];
        
        foreach ($menu_items as $key => $item) {
            if (isset($item['menu-item-type']) && $item['menu-item-type'] === self::ITEM_TYPE) {
                // Let WordPress handle it, but make sure we have the right data
                $_POST['menu-item'][$key]['menu-item-type'] = self::ITEM_TYPE;
                $_POST['menu-item'][$key]['menu-item-object'] = self::ITEM_OBJECT;
            }
        }
    }

    /**
     * Enqueue scripts for menu editor
     * 
     * @param string $hook
     * @return void
     */
    public function enqueueAdminScripts($hook)
    {
        if ($hook !== 'nav-menus.php') {
            return;
        }

        $css = '
            .menu-item-settings .field-url { display: block; }
            .menu-item[data-menu-item-object="' . self::ITEM_OBJECT . '"] .field-url { display: none !important; }
        ';
        wp_add_inline_style('nav-menus', $css);

        $hideAcfFields = !apply_filters('Modularity/ServiceInformation/Admin/DisableACFExclusions', true);
        
        $acfHideCode = $hideAcfFields ? "
                            // Hide ACF field groups for service info menu items
                            $(this).find('.acf-field-group-60c325749aeab, .acf-field-group-61dc486660615').hide();
                            $(this).find('[data-key=\"group_60c325749aeab\"], [data-key=\"group_61dc486660615\"]').hide();
                            $(this).find('.acf-fields').each(function() {
                                var \$fields = $(this);
                                \$fields.find('[data-name=\"menu_item_icon\"], [data-name=\"menu_item_style\"]').closest('.acf-field').hide();
                            });" : "";
        
        $js = "
            jQuery(document).ready(function($) {
                function hideServiceInfoUrlFields() {
                    $('#menu-to-edit .menu-item').each(function() {
                        var object = $(this).find('input.menu-item-data-object').val();
                        if (object === '" . self::ITEM_OBJECT . "') {
                            $(this).attr('data-menu-item-object', '" . self::ITEM_OBJECT . "');
                            $(this).find('.field-url').hide();{$acfHideCode}
                        }
                    });
                }
                
                hideServiceInfoUrlFields();
                $(document).on('menu-item-added', hideServiceInfoUrlFields);
                
                // Also observe DOM changes for AJAX additions
                var observer = new MutationObserver(function(mutations) {
                    hideServiceInfoUrlFields();
                });
                
                var menuList = document.getElementById('menu-to-edit');
                if (menuList) {
                    observer.observe(menuList, { childList: true, subtree: true });
                }
            });
        ";
        wp_add_inline_script('nav-menu', $js);
    }

    /**
     * Setup nav menu item
     * 
     * @param object $menu_item
     * @return object
     */
    public function setupNavMenuItem($menu_item)
    {
        if (!isset($menu_item->type) || $menu_item->type !== self::ITEM_TYPE) {
            return $menu_item;
        }

        $menu_item->type_label = __('Service Information', 'modularity-service-info');
        
        // Always update the URL to the current service info page URL
        $url = $this->getServiceInfoUrl();
        if ($url) {
            $menu_item->url = $url;
        }

        return $menu_item;
    }

    /*
    |--------------------------------------------------------------------------
    | ACF Integration Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Maybe register ACF exclusion filter after plugins are loaded
     * 
     * @return void
     */
    public function maybeRegisterAcfExclusion(): void
    {
        if (apply_filters('Modularity/ServiceInformation/Admin/DisableACFExclusions', true)) {
            return;
        }
        
        add_filter('acf/location/rule_match/nav_menu_item', [$this, 'acfLocationRuleMatch'], 10, 4);
    }

    /**
     * Filter ACF location rule match for nav_menu_item
     * 
     * @param bool $match
     * @param array $rule
     * @param array $options
     * @param array $field_group
     * @return bool
     */
    public function acfLocationRuleMatch($match, $rule, $options, $field_group)
    {
        // Only filter the field groups we want to exclude
        $excluded_groups = ['group_60c325749aeab', 'group_61dc486660615'];
        
        if (!in_array($field_group['key'], $excluded_groups)) {
            return $match;
        }

        // Check if the current menu item is our service info type
        if (!empty($options['nav_menu_item'])) {
            $menu_item_id = $options['nav_menu_item'];
            $menu_item_object = get_post_meta($menu_item_id, '_menu_item_object', true);
            
            if ($menu_item_object === self::ITEM_OBJECT) {
                return false; // Don't show this field group
            }
        }

        // Also check via POST data for newly added items
        if (isset($_POST['menu-item-object']) && $_POST['menu-item-object'] === self::ITEM_OBJECT) {
            return false;
        }

        return $match;
    }

    /*
    |--------------------------------------------------------------------------
    | Frontend Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Add notification badge to service info menu items
     * 
     * Filters the menu items returned by wp_get_nav_menu_items() and appends
     * a badge with the count of published service info posts to the title.
     * 
     * @param array $items Array of menu item objects
     * @param object $menu The menu object
     * @param array $args The arguments passed to wp_get_nav_menu_items()
     * @return array
     */
    public function addNotificationBadgeToMenuItems($items, $menu, $args)
    {
        if (empty($items) || is_admin()) {
            return $items;
        }

        $count = $this->getServiceInfoCount();
        
        if ($count < 1) {
            return $items;
        }

        foreach ($items as $item) {
            if (isset($item->type) && $item->type === self::ITEM_TYPE) {
                $badge = sprintf(
                    '<span class="service-info-badge" aria-label="%s">%d</span>',
                    esc_attr(sprintf(
                        _n('%d active service information', '%d active service informations', $count, 'modularity-service-info'),
                        $count
                    )),
                    $count
                );
                
                $item->title .= $badge;
            }
        }

        return $items;
    }
}