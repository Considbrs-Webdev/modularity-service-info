<?php

declare(strict_types=1);

namespace ModularityServiceInfo\Module;

use ModularityServiceInfo\Helper\CacheBust;
use ModularityServiceInfo\Model\ServiceInfoPost;

/**
 * Class ServiceInfo
 * @package ModularityServiceInfo\Module
 */
class ServiceInfo extends \Modularity\Module
{
    public $slug = 'service-info';
    public $supports = [];

    public function init(): void
    {
        $this->nameSingular = __('Service Information', 'modularity-service-info');
        $this->namePlural = __('Service Information', 'modularity-service-info');
        $this->description = __('A service information module.', 'modularity-service-info');
    }

    /**
     * Data array
     * @return array $data
     */
    public function data(): array
    {
        $data = [];

        // Append field config
        $data = array_merge($data, (array) \Modularity\Helper\FormatObject::camelCase(
            $this->getFields(),
        ));

        $data['postsToShow'] = is_null($data['postsToShow']) ? 5 : (int) $data['postsToShow'];
        $data['showIcons'] = is_null($data['showIcons']) ? true : $data['showIcons'];
        $data['linkToServiceInformationArchive'] = is_null($data['linkToServiceInformationArchive']) ? true : $data['linkToServiceInformationArchive'];
        $data['archiveLink'] = $this->getArchiveLink();
        $data['archiveLinkIcon'] = apply_filters('Modularity/ServiceInformation/Module/ArchiveLink/Icon', 'arrow-right');
        $data['archiveMode'] = is_null($data['archiveMode']) ? false : $data['archiveMode'];
        $data['groupByCategories'] = is_null($data['groupByCategories']) ? false : $data['groupByCategories'];
        $data['showEmptyCategories'] = is_null($data['showEmptyCategories']) ? false : $data['showEmptyCategories'];

        $postsToShow = $data['archiveMode'] ? -1 : $data['postsToShow'];
        $posts = $this->getPosts($postsToShow);

        if ($data['groupByCategories']) {
            $data['posts'] = $this->groupPosts($posts, $data['showEmptyCategories']);
        } else {
            $data['posts'] = $posts;
        }

        $data['translations'] = [
            'noPostsForCategory' => __('No service information available at the moment.', 'modularity-service-info'),
            'archiveLinkText' => __('View all service information', 'modularity-service-info'),
        ];


        return $data;
    }

    /**
     * Blade Template
     * @return string
     */
    public function template(): string
    {
        return 'service-info.blade.php';
    }

    /**
     * Enqueue styles
     * @return void
     */
    public function style(): void
    {
        $styleFile = CacheBust::name('css/modularity-service-info.css');

        if ($styleFile) {
            wp_enqueue_style(
                'modularity-service-info',
                MODULARITYSERVICEINFO_URL . '/assets/dist/' . $styleFile,
                [],
                null
            );
        }
    }

    /**
     * Enqueue scripts
     * @return void
     */
    public function script(): void
    {
        $scriptFile = CacheBust::name('js/modularity-service-info.js');

        if ($scriptFile) {
            wp_enqueue_script(
                'modularity-service-info',
                MODULARITYSERVICEINFO_URL . '/assets/dist/' . $scriptFile,
                [],
                null,
                true
            );
        }
    }

    /**
     * Get service information posts
     * 
     * @param int $postsToShow Number of posts to retrieve
     * @return array
     */
    private function getPosts(int $postsToShow): array
    {
        $args = [
            'post_type'      => 'service_information',
            'posts_per_page' => $postsToShow,
            'post_status'    => 'publish',
            'meta_key'       => 'start_date',
            'orderby'        => 'meta_value',
            'order'          => 'DESC',
        ];

        $query = new \WP_Query($args);

        if (!$query->have_posts()) {
            return [];
        }

        $posts = [];

        foreach ($query->posts as $post) {
            $posts[] = $this->formatPost($post);
        }

        return $posts;
    }

    /**
     * Get the archive link for service information
     * 
     * @return string|null
     */
    private function getArchiveLink(): ?string
    {
        // Get custom archive link from options if available
        $customLink = get_field('service_information_page', 'service-information-settings');
        
        if (!empty($customLink)) {
            return $customLink;
        }
        
        // Fall back to default archive link
        return get_post_type_archive_link('service_information');
    }

    /**
     * Format date range
     *
     * @param string $startDateRaw
     * @param string $endDateRaw
     * @param mixed $timezone
     * @return string
     */
    private function formatDateRange(string $startDateRaw, string $endDateRaw, $timezone = null): string
    {
        if (empty($startDateRaw)) {
            return '';
        }

        if ($timezone === null) {
            $timezone = wp_timezone();
        }

        if (is_string($timezone)) {
            $timezone = new \DateTimeZone($timezone);
        }

        try {
            $startDateTime = new \DateTime($startDateRaw, $timezone);
            $startTimestamp = $startDateTime->getTimestamp();

            if (!empty($endDateRaw)) {
                $endDateTime = new \DateTime($endDateRaw, $timezone);
                $endTimestamp = $endDateTime->getTimestamp();
            } else {
                $endTimestamp = $startTimestamp;
            }
        } catch (\Exception $e) {
            return '';
        }

        $dateFormat = apply_filters('Modularity/ServiceInfo/DateFormat', get_option('date_format'));
        $timeFormat = apply_filters('Modularity/ServiceInfo/TimeFormat', get_option('time_format'));

        $startDay = wp_date('Y-m-d', $startTimestamp);
        $endDay = wp_date('Y-m-d', $endTimestamp);

        if ($startDay === $endDay) {
            if ($startTimestamp === $endTimestamp) {
                return sprintf(
                    '%s %s',
                    wp_date($dateFormat, $startTimestamp),
                    wp_date($timeFormat, $startTimestamp)
                );
            }

            return sprintf(
                '%s %s &ndash; %s',
                wp_date($dateFormat, $startTimestamp),
                wp_date($timeFormat, $startTimestamp),
                wp_date($timeFormat, $endTimestamp)
            );
        }

        return sprintf(
            '%s %s &ndash; %s %s',
            wp_date($dateFormat, $startTimestamp),
            wp_date($timeFormat, $startTimestamp),
            wp_date($dateFormat, $endTimestamp),
            wp_date($timeFormat, $endTimestamp)
        );
    }

    /**
     * Format a post into a ServiceInfoPost object
     * 
     * @param \WP_Post $post The WordPress post object
     * @return ServiceInfoPost
     */
    private function formatPost(\WP_Post $post): ServiceInfoPost
    {
        // Get the service_category terms
        $terms = get_the_terms($post->ID, 'service_category');
        $iconName = null;

        // Get material_icon from the first term
        if ($terms && !is_wp_error($terms)) {
            $firstTerm = reset($terms);
            $iconGroup = get_field('icon', 'service_category_' . $firstTerm->term_id);
            $iconName = $iconGroup['material_icon'] ?? null;
        }

        // Get start and end raw values
        $startDateRaw = get_field('start_date', $post->ID);
        $endDateRaw = get_field('end_date', $post->ID);

        // Formatted HTML span for date(s)
        $formattedDate = $this->formatDateRange($startDateRaw, $endDateRaw);

        // Create and return ServiceInfoPost object
        return new ServiceInfoPost(
            get_the_title($post->ID),
            $formattedDate,
            $iconName,
            get_permalink($post->ID),
            ($terms && !is_wp_error($terms)) ? $terms : []
        );
    }

    /**
     * Available "magic" methods for modules:
     * init()            What to do on initialization
     * data()            Use to send data to view (return array)
     * style()           Enqueue style only when module is used on page
     * script()          Enqueue script only when module is used on page
     * adminEnqueue()    Enqueue scripts for the module edit/add page in admin
     * template()        Return the view template (blade) the module should use when displayed
     */

    /**
     * Group posts by category
     * 
     * @param array $posts
     * @param bool $showEmptyCategories
     * @return array
     */
    private function groupPosts(array $posts, bool $showEmptyCategories): array
    {
        $grouped = [];

        $terms = get_terms([
            'taxonomy' => 'service_category',
            'hide_empty' => false,
        ]);

        if (!empty($terms) && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                $grouped[$term->name] = [];
            }
        }

        foreach ($posts as $post) {
            if (empty($post->terms)) {
                $grouped[__('Uncategorized', 'modularity-service-info')][] = $post;
                continue;
            }

            foreach ($post->terms as $term) {
                $grouped[$term->name][] = $post;
            }
        }

        if (!$showEmptyCategories) {
            $grouped = array_filter($grouped, function ($posts) {
                return !empty($posts);
            });
        }

        return $grouped;
    }
}

