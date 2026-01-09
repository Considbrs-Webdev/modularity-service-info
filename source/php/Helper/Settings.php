<?php

namespace ModularityServiceInfo\Helper;

/**
 * Class Settings
 *
 * Helper functions for retrieving plugin settings used when registering
 * post types and related behavior.
 *
 * @package ModularityServiceInfo\Helper
 */
class Settings
{
    /**
     * Get the configured slug for the service information posts.
     * Falls back to the filter 'Modularity/ServiceInformation/Posts/Slug'
     * with default 'service-information' when no ACF field is set.
     *
     * @return string
     */
    public static function getSlug(): string
    {
        $slug = get_field('slug', 'service-information-settings');

        if (empty($slug)) {
            $slug = apply_filters('Modularity/ServiceInformation/Posts/Slug', 'service-information');
        }

        return sanitize_title((string) $slug);
    }

    /**
     * Get the configured archive page for service information posts.
     * Returns null if no page is set.
     * 
     * @param bool $url Whether to return the URL or the page ID.
     * @return string|int|null
     */
    public static function getArchivePage(bool $url = true)
    {
        $page = get_field('service_information_page', 'service-information-settings');

        if (empty($page)) {
            return null;
        }

        if ($url) {
            return get_permalink($page);
        }

        return $page;
    }
}
